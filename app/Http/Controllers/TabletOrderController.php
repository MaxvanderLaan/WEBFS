<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MealAddition;
use App\Models\Menu;
use App\Models\MenuOffer;
use App\Models\Planning;
use App\Models\PlanningTable;
use App\Models\Sale;
use App\Models\Table;
use App\Models\MenuSale;
use App\Models\TableCustomer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TabletOrderController extends Controller
{
    public function start()
    {
        $tables = Table::all();
        return Inertia::render('Auth/Tablet/Start', [
            'tables' => $tables,
        ]);
    }

    public function register(Request $request)
    {
        $rules = [
            'tableId' => 'required|exists:tables,id',
            'customers' => 'required|array|max:8',
            'customers.*.name' => 'required|string|max:255',
            'customers.*.birthday' => 'required|date',
            'customers.*.deluxe' => 'required|boolean',
        ];
    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        $customers = $request->input('customers');
        $tableId = $request->input('tableId');
    
        foreach ($customers as $customerData) {
            // Create customer
            $customer = Customer::create([
                'name' => $customerData['name'],
                'birthday' => $customerData['birthday'],
            ]);
    
            TableCustomer::create([
                'customer_id' => $customer->id,
                'table_id' => $tableId,
                'deluxe' => $customerData['deluxe'],
            ]);
        }
    
        return redirect()->route('tablet.order.index');
    }
    
    public function index(){
        return Inertia::render('Auth/Tablet/Index');
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
    //     $rules = [
    //         'items' => 'required|array|min:1',
    //         'description' => 'required|string|max:255',
    //         'remarks' => 'nullable|array',
    //         'meal_additions' => 'required|array',
    //         'quantities' => 'required|array|min:1',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);

    //     $validator->after(function ($validator) use ($request) {
    //         foreach ($request->items as $itemId) {
    //             if (empty($request->meal_additions[$itemId])) {
    //                 $validator->errors()->add('meal_additions.' . $itemId, 'Meal addition is required for each item.');
    //             }
    //         }
    //     });

    //     if ($validator->fails()) {
    //         return redirect()->back()
    //             ->withErrors($validator)
    //             ->withInput();
    //     }

    //     //create sale
    //     $sale = new Sale();
    //     $sale->description = $request->description;
    //     $sale->created_at = Carbon::now();
    //     $sale->updated_at = Carbon::now();
    //     $sale->save();

    //     //create menu_sale and link it to sale
    //     foreach ($request->items as $itemId) {
    //         $menu_sales = new MenuSale();
    //         $menu_sales->amount = $request->quantities[$itemId] ?? 0;
    //         $menu_sales->remark = $request->remarks[$itemId] ?? '';
    //         $menu_sales->sale_id = $sale->id;
    //         $menu_sales->menu_id = $itemId;
    //         $menu_sales->meal_addition_id = $request->meal_additions[$itemId] ?? null;
    //         $menu_sales->save();
    //     }

    //     return back()->with('success', 'Order created successfully!');
    // }
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
