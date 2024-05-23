<?php

namespace App\Http\Controllers;

use App\Models\MealType;
use App\Models\Menu;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ChangeMenuController extends Controller{

    public function index()
    {
        $menus = Menu::with('mealType')->get();
        return Inertia::render('Auth/ChangeMenu/ChangeMenu', [
            'menus' => $menus,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
    
        $menus = Menu::with('mealType')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('number', $query)
            ->orWhereHas('mealType', function ($q) use ($query) {
                $q->where('type', 'LIKE', "%{$query}%");
            })
            ->get();

        return response()->json($menus);
    }

    public function edit($id)
    {
        $menu = Menu::with('mealType')->find($id);
        $mealTypes = MealType::get();
        if ($menu) {
            return Inertia::render('Auth/ChangeMenu/ChangeMenuEdit', [
                'menu' => $menu,
                'mealTypes' => $mealTypes,
            ]);
        } else {
            return redirect()->route('change.menu')->with('error', 'Menu not found');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:menus,id',
            'number' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'addition' => 'nullable|string|max:1',
            'price' => 'required|numeric',
            'is_archived' => 'required|boolean',
            'meal_type_id' => 'required|integer|exists:meal_types,id',
        ]);
    
        $menu = Menu::find($request->id);
    
        if (!$menu) {
            return back()->with('error', 'Menu not found');
        }
    
        $menu->update([
            'number' => $request->number,
            'name' => $request->name,
            'description' => $request->description,
            'addition' => $request->addition,
            'price' => $request->price,
            'is_archived' => $request->is_archived,
            'meal_type_id' => $request->meal_type_id,
        ]);
    
        return back()->with('success', 'Menu updated successfully');
    }

    public function create()
    {
        $mealTypes = MealType::get();
        return Inertia::render('Auth/ChangeMenu/ChangeMenuCreate', [
            'mealTypes' => $mealTypes,
        ]);
    }

    public function make(Request $request)
    {
        $request->validate([
            'number' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'addition' => 'nullable|string|max:1',
            'price' => 'required|numeric',
            'meal_type_id' => 'required|integer|exists:meal_types,id',
        ]);
    
        $menu = new Menu([
            'number' => $request->number,
            'name' => $request->name,
            'description' => $request->description,
            'addition' => $request->addition,
            'price' => $request->price,
        ]);
    
        $menu->meal_type_id = $request->meal_type_id;
        $menu->save();
    
        return back()->with('success', 'Menu updated successfully');
    }

    public function delete(Request $request)
    {
        $menu = Menu::find($request->id);

        if (!$menu) {
            return back()->with('error', 'Menu not found');
        }

        $menu->delete();

        return redirect()->route('change.menu')->with('success', 'Menu deleted successfully');
    }
}