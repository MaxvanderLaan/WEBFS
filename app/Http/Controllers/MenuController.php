<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Inertia\Inertia;

class MenuController extends Controller{

    public function index()
    {
        $now = now();
        $startOfNextWeek = $now->startOfWeek()->addWeek()->startOfDay();
        $endOfNextWeek = $now->copy()->endOfWeek()->addWeek();
    
        $menus = Menu::with(['mealType', 'menuOffers' => function ($query) use ($startOfNextWeek, $endOfNextWeek) {
            $query->whereBetween('end_date', [$startOfNextWeek, $endOfNextWeek]);
        }])
        ->where('is_archived', false)
        ->get();
    
        return Inertia::render('Menus/Menu', ['menus' => $menus]);
    }
}