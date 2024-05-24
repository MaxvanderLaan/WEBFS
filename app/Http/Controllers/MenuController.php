<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Inertia\Inertia;

class MenuController extends Controller{

    public function index()
    {
        $now = now();
        $startOfThisWeek = $now->startOfWeek()->startOfDay();
        $endOfThisWeek = $now->copy()->endOfWeek();
    
        $menus = Menu::with(['mealType', 'menuOffers' => function ($query) use ($startOfThisWeek, $endOfThisWeek) {
            $query->whereBetween('end_date', [$startOfThisWeek, $endOfThisWeek]);
        }])
        ->where('is_archived', false)
        ->get();
    
        return Inertia::render('Menus/Menu', ['menus' => $menus]);
    }
}