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
        $menuOffers = MenuOffer::with('menu')->get()->map(function ($offer) {
            $offer->price = $offer->price;
            return $offer;
        });
    
        return Inertia::render('Auth/Register/Menu/Offer/Index', [
            'menuOffers' => $menuOffers,
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
        $nextWeekStart = now()->addWeek()->startOfWeek();
        $nextWeekEnd = now()->addWeek()->endOfWeek();
        $menus = Menu::with('mealType')->where('is_archived', false)->get();
        $mealAdditions = MealAddition::get();
        $menuOffers = MenuOffer::whereBetween('start_date', [$nextWeekStart, $nextWeekEnd])
                                ->whereBetween('end_date', [$nextWeekStart, $nextWeekEnd])
                                ->get();
        return Inertia::render('Auth/Register/Menu/Offer/Create', [
            'menus' => $menus,
            'mealAdditions' => $mealAdditions,
            'menuOffers' => $menuOffers,
        ]);
    }

    public function make(Request $request)
    {
        $nextWeekStart = now()->addWeek()->startOfWeek();
        $nextWeekEnd = now()->addWeek()->endOfWeek();
    
        $validatedData = $request->validate([
            'menuOffers' => 'array',
            'menuOffers.*.id' => 'sometimes|integer|exists:menu_offers,id',
            'menuOffers.*.menu_id' => 'required|integer|exists:menus,id',
            'menuOffers.*.discount' => 'required|numeric|between:1,100',
            'menuOffers.*.start_date' => 'required|date|after_or_equal:' . $nextWeekStart,
            'menuOffers.*.end_date' => 'required|date|after_or_equal:menuOffers.*.start_date|before_or_equal:' . $nextWeekEnd,
        ]);
    
        $ids = collect($validatedData['menuOffers'])->pluck('id')->filter()->all();
    
        MenuOffer::whereBetween('start_date', [$nextWeekStart, $nextWeekEnd])
                 ->whereBetween('end_date', [$nextWeekStart, $nextWeekEnd])
                 ->whereNotIn('id', $ids)
                 ->delete();
    
        foreach ($validatedData['menuOffers'] as $menuOfferData) {
            MenuOffer::updateOrCreate(
                ['id' => $menuOfferData['id'] ?? null],
                [
                    'menu_id' => $menuOfferData['menu_id'],
                    'discount' => $menuOfferData['discount'],
                    'start_date' => $nextWeekStart,
                    'end_date' => $nextWeekEnd,
                ]
            );
        }
    
        return back()->with('success', 'Menu offers created or updated successfully!');
    }
}