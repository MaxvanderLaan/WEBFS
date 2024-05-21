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
        return Inertia::render('Auth/RegisterMenu/RegisterMenu', [
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
                Log::info('Creating MenuSale with sale_id: ', ['sale_id' => $sale->id]);
            
                $menuSale = new MenuSale();
                $menuSale->sale_id = $sale->id;
                $menuSale->menu_id = $menuSalesData['menuId'];
                $menuSale->amount = $menuSalesData['amount'];
                $menuSale->remark = $menuSalesData['remark'];
                $menuSale->meal_addition_id = $menuSalesData['mealAdditionId'];
                $menuSale->save();
            }
    
            return back()->with('success', 'Order created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }
}