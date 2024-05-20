<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisterOrderController extends Controller{

    public function index()
    {
        $menus = Menu::with('mealTypes')->get();

        return Inertia::render('Auth/Search/Search', [
            'menus' => $menus
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        $menus = Menu::with('mealTypes')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('id', $query)
            ->orWhereHas('mealTypes', function ($q) use ($query) {
                $q->where('type', 'LIKE', "%{$query}%");
            })
            ->get();

        return response()->json($menus);
    }
}