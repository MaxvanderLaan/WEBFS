<?php

namespace App\Http\Controllers;

use App\Models\MealAddition;
use App\Models\Menu;
use App\Models\MenuOffer;
use Inertia\Inertia;
use Illuminate\Http\Request;

class RegisterMenuOfferController extends Controller{

    public function index()
    {
        $menus = Menu::with('mealType')->where('is_archived', false)->get();
        $mealAdditions = MealAddition::get();
        return Inertia::render('Auth/Register/Menu/Offer/Index', [
            'menus' => $menus,
            'mealAdditions' => $mealAdditions,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
    
        $menus = Menu::with('mealType')
            ->where('is_archived', false)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('number', $query)
                  ->orWhereHas('mealType', function ($q) use ($query) {
                      $q->where('type', 'LIKE', "%{$query}%");
                  });
            })
            ->get();
    
        return response()->json($menus);
    }

    public function create()
    {
        $menus = Menu::with('mealType')->where('is_archived', false)->get();
        $mealAdditions = MealAddition::get();
        return Inertia::render('Auth/Register/Menu/Offer/Create', [
            'menus' => $menus,
            'mealAdditions' => $mealAdditions,
        ]);
    }

    public function make(Request $request)
    {
        $nextWeekStart = now()->addWeek()->startOfWeek();
        $nextWeekEnd = now()->addWeek()->endOfWeek();
    
        $validatedData = $request->validate([
            'menuOffers' => 'required|array',
            'menuOffers.*.menuId' => 'required|integer|exists:menus,id',
            'menuOffers.*.discount' => 'required|numeric|between:1,100',
            'menuOffers.*.startDate' => 'required|date|after_or_equal:' . $nextWeekStart,
            'menuOffers.*.endDate' => 'required|date|after_or_equal:menuOffers.*.start_date|before_or_equal:' . $nextWeekEnd,
        ]);
    
        foreach ($validatedData['menuOffers'] as $menuOfferData) {
            $menuOffer = new MenuOffer();
            $menuOffer->menu_id = $menuOfferData['menuId'];
            $menuOffer->discount = $menuOfferData['discount'];
            $menuOffer->start_date = $menuOfferData['startDate'];
            $menuOffer->end_date = $menuOfferData['endDate'];
            $menuOffer->save();
        }
    
        return back()->with('success', 'Menu offers created successfully!');
    }

    public function edit(Request $request)
    {

    }
}