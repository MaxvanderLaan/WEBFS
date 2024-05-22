<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TableController extends Controller{

    public function index()
    {
        $tables = Table::get();
        return Inertia::render('Auth/Table/Index', [
            'tables' => $tables,
        ]);
    }

    public function create()
    {
        return Inertia::render('Auth/Table/Create', [
        ]);
    }

    public function make(Request $request)
    {
        $request->validate([
            'number' => [
                'required',
                'integer',
                Rule::unique('tables')->where(function ($query) {
                    return $query->where('is_archived', false);
                }),
            ],
        ]);
    
        $table = new Table();
        $table->number = $request->number;
        $table->save();
    
        return back()->with('success', 'Table created successfully!');
    }

    public function edit($id)
    {
        $table = Table::find($id);
        if ($table) {
            return Inertia::render('Auth/Table/Edit', [
                'table' => $table,
            ]);
        } else {
            return redirect()->route('admin.table')->with('error', 'Menu not found');
        }
    }

    public function update(Request $request)
    {
        $table = Table::find($request->id);

        if (!$table) {
            return back()->with('error', 'Table not found');
        }

        $request->validate([
            'id' => 'required|exists:tables,id',
            'number' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($table) {
                    if ($table->number != $value) {
                        $existingTable = Table::where('number', $value)->where('is_archived', false)->first();
                        if ($existingTable) {
                            $fail('The ' . $attribute . ' is already in use.');
                        }
                    }
                },
            ],
            'is_archived' => 'required|boolean',
        ]);
    
        $table = Table::find($request->id);
    
        if (!$table) {
            return back()->with('error', 'Table not found');
        }
    
        $table->update([
            'number' => $request->number,
            'is_archived' => $request->is_archived,
        ]);
    
        return back()->with('success', 'Table updated successfully');
    }

    public function delete(Request $request)
    {
        $table = Table::find($request->id);

        if (!$table) {
            return back()->with('error', 'Table not found');
        }

        $table->delete();

        return redirect()->route('admin.table')->with('success', 'Table deleted successfully');
    }
}