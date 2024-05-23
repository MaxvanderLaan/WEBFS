<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MenuOffer;

class WelcomeController extends Controller{

    public function index()
    {
        $now = now();
        $startOfWeek = $now->startOfWeek();
        $endOfNextWeek = $now->copy()->addWeek()->endOfWeek();

        $menuOffers = MenuOffer::with('menu', 'menu.mealType')
            ->whereBetween('start_date', [$startOfWeek, $endOfNextWeek])
            ->get();
        return Inertia::render('Welcome', [
            'menuOffers' => $menuOffers,
        ]);
    }
}