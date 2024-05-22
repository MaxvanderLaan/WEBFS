<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanningController extends Controller
{
    public function index()
    {
        $plannings = Planning::with('user', 'table') ->get();
        return Inertia::render('Auth/Planning/Index', [
            'plannings' => $plannings,
        ]);
    }

    public function create()
    {
        $users = User::get();
        $tables = Table::get();
        return Inertia::render('Auth/Planning/Create', [
            'users' => $users,
            'tables' => $tables,
        ]);
    }

    public function make(Request $request)
    {
        $today = now()->format('Y-m-d');
        $oneWeekFromNow = now()->addWeek();
    
        $request->validate([
            'start_date' => [
                'required',
                'date',
                'after_or_equal:' . $today,
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
                'before:' . $oneWeekFromNow->format('Y-m-d'),
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
            ],
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request) {
                    if (Planning::where('user_id', $value)
                        ->where(function ($query) use ($request) {
                            $query->whereBetween('start_time', [$request->start_date . ' ' . $request->start_time, $request->end_date . ' ' . $request->end_time])
                                ->orWhereBetween('end_time', [$request->start_date . ' ' . $request->start_time, $request->end_date . ' ' . $request->end_time])
                                ->orWhere(function ($query) use ($request) {
                                    $query->where('start_time', '<=', $request->start_date . ' ' . $request->start_time)
                                        ->where('end_time', '>=', $request->end_date . ' ' . $request->end_time);
                                });
                        })
                        ->exists()) {
                        $fail('The user is already planned within this date range.');
                    }
                },
            ],
            'table_ids' => 'required|array',
            'table_ids.*' => 'exists:tables,id',
        ]);
    
        foreach ($request->table_ids as $table_id) {
            $planning = new Planning;
            $planning->start_time = $request->start_date . ' ' . $request->start_time;
            $planning->end_time = $request->end_date . ' ' . $request->end_time;
            $planning->user_id = $request->user_id;
            $planning->table_id = $table_id;
            $planning->save();
        }
    
        return back()->with('success', 'Planning created successfully');
    }

    public function edit($id)
    {

    }

    public function update(Request $request)
    {

    }

    public function delete(Request $request)
    {

    }
}