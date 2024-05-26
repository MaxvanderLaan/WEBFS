<?php

namespace App\Http\Controllers;

use App\Models\MealAddition;
use App\Models\Menu;
use App\Models\Planning;
use App\Models\PlanningTable;
use App\Models\Sale;
use App\Models\Table;
use App\Models\MenuSale;
use App\Models\TableCustomer;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TabletOrderController extends Controller
{
    public function index()
    {
        $currentDate = Carbon::today();

        $user = auth()->user();
        $plannings = Planning::where('user_id', $user->id)->whereDate('start_time', $currentDate)->first();
        $planning_tables = PlanningTable::where('planning_id', $plannings->id)->first();
        $table = Table::where('id', $planning_tables->table_id)->first();;

        return Inertia::render('Auth/Tablet/Index', [
            'table' => $table,
        ]);
    }

    public function create()
    {
        $menu = Menu::get();
        $meal_additions = MealAddition::get();

        return Inertia::render('Auth/Tablet/Create', [
            'menu' => $menu,
            'meal_additions' => $meal_additions
        ]);
    }

    public function make(Request $request)
    {
        $rules = [
            'items' => 'required|array|min:1',
            'description' => 'required|string|max:255',
            'remarks' => 'nullable|array',
            'meal_additions' => 'required|array',
            'quantities' => 'required|array|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            foreach ($request->items as $itemId) {
                if (empty($request->meal_additions[$itemId])) {
                    $validator->errors()->add('meal_additions.' . $itemId, 'Meal addition is required for each item.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        //create sale
        $sale = new Sale();
        $sale->description = $request->description;
        $sale->created_at = Carbon::now();
        $sale->updated_at = Carbon::now();
        $sale->save();

        //create menu_sale and link it to sale
        foreach ($request->items as $itemId) {
            $menu_sales = new MenuSale();
            $menu_sales->amount = $request->quantities[$itemId] ?? 0;
            $menu_sales->remark = $request->remarks[$itemId] ?? '';
            $menu_sales->sale_id = $sale->id;
            $menu_sales->menu_id = $itemId;
            $menu_sales->meal_addition_id = $request->meal_additions[$itemId] ?? null;
            $menu_sales->save();
        }

        return back()->with('success', 'Order created successfully!');
    }
}
