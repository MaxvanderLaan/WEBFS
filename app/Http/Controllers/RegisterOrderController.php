<?php

namespace App\Http\Controllers;

use App\Models\MealAddition;
use App\Models\Menu;
use App\Models\MenuSale;
use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class RegisterOrderController extends Controller{

    public function index()
    {
        $menus = Menu::with('mealType')->get();
        $mealAdditions = MealAddition::get();
        return Inertia::render('Auth/Search/Search', [
            'menus' => $menus,
            'mealAdditions' => $mealAdditions,
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

    public function store(Request $request)
    {
        Log::info('Store method called with request: ', $request->all());
        try {
            $data = $request->validate([
                'menuSales' => 'required|array',
                'menuSales.*.menuId' => 'required|exists:menus,id',
                'menuSales.*.amount' => 'required|integer|min:1',
                'menuSales.*.remark' => 'nullable|string',
                'menuSales.*.mealAdditionId' => 'required',
            ]);
    
            $sale = Sale::create([
                'description' => '',
            ]);
    
            foreach ($data['menuSales'] as $menuSalesData) {
                $menuSale = MenuSale::create([
                    'sale_id' => $sale->id,
                    'menu_id' => $menuSalesData['menuId'],
                    'amount' => $menuSalesData['amount'],
                    'remark' => $menuSalesData['remark'],
                ]);
    
                if (isset($menuSalesData['mealAdditionId'])) {
                    $mealAddition = MealAddition::find($menuSalesData['mealAdditionId']);
                    $menuSale->mealAddition()->save($mealAddition);
                }
            }
    
            return response()->json(['message' => 'Order created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create order: ' . $e->getMessage()], 500);
        }
    }
}