<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Help;
use App\Models\MealAddition;
use App\Models\Menu;
use App\Models\Sale;
use App\Models\Table;
use App\Models\MenuSale;
use App\Models\TableCustomer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use App\Events\HelpRequestCreated;

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

        $sale = Sale::create([
            'description' => 'sale for table ' . $tableId,
            'table_id' => $tableId,
        ]);
        return redirect()->route('tablet.index', ['saleId' => $sale->id]);
    }

    public function index(int $saleId)
    {
        $menu_sales = MenuSale::where('sale_id', $saleId)->get();
        $recentOrderTime = $this->getMostRecentOrder($menu_sales);
        $timesOrdered = $this->getTimesOrdered($menu_sales);
        $sale = Sale::where('id', $saleId)->first();

        return Inertia::render('Auth/Tablet/Index', [
            'saleId' => $saleId,
            'success' => session('success'),
            'tableId' => $sale->table_id,
            'recentOrderTime' => $recentOrderTime ? $recentOrderTime->toIso8601String() : null,
            'timesOrdered' => $timesOrdered,
        ]);
    }
    
    public function create(int $saleId)
    {
        $now = now();
        $startOfThisWeek = $now->startOfWeek()->startOfDay();
        $endOfThisWeek = $now->copy()->endOfWeek();

        $menus = Menu::with(['mealType', 'menuOffers' => function ($query) use ($startOfThisWeek, $endOfThisWeek) {
            $query->whereBetween('end_date', [$startOfThisWeek, $endOfThisWeek]);
        }])
        ->where('is_archived', false)
        ->get();

        $mealAdditions = MealAddition::get();
        return Inertia::render('Auth/Tablet/Create', [
            'menus' => $menus,
            'mealAdditions' => $mealAdditions,
            'saleId' => $saleId,
        ]);
    }

    public function make(Request $request)
    {
        try {
            $data = $request->validate([
                'menuSales' => 'required|array',
                'menuSales.*.menuId' => 'required|exists:menus,id',
                'menuSales.*.amount' => 'required|integer|min:1',
                'menuSales.*.remark' => 'nullable|string',
                'menuSales.*.mealAdditionId' => 'required',
            ]);
    
            $sale = Sale::findOrFail($request->saleId);
    
            foreach ($data['menuSales'] as $menuSalesData) {       
                $menuSale = new MenuSale();
                $menuSale->sale_id = $sale->id;
                $menuSale->menu_id = $menuSalesData['menuId'];
                $menuSale->amount = $menuSalesData['amount'];
                $menuSale->remark = $menuSalesData['remark'];
                $menuSale->meal_addition_id = $menuSalesData['mealAdditionId'];
                $menuSale->save();
            }
    
            return redirect()->route('tablet.index', ['saleId' => $sale->id])
                             ->with('success', 'Order created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function checkout(int $saleId)
    {
        $menu_sales = MenuSale::where('sale_id', $saleId)->get();
        $menus = Menu::with('mealType', 'menuOffers')->get();
        $mealAdditions = MealAddition::get();
    
        return Inertia::render('Auth/Tablet/Checkout', [
            'menuSales' => $menu_sales,
            'menus' => $menus,
            'mealAdditions' => $mealAdditions,
        ]);
    }

    public function askHelpForm(int $saleId)
    {
        return Inertia::render('Auth/Tablet/AskHelpForm', [
            'saleId' => $saleId,
        ]);
    }

    public function askHelpStore(Request $request){
        
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);
        
        $help = Help::create([
            'message' => $validated['message'],
            'completed' => false,
        ]);

        event(new HelpRequestCreated($help));
    
        return redirect()->route('tablet.index', ['saleId' => $request->saleId])->with('success', 'Help Request created successfully');
    }

    private function getMostRecentOrder(Collection $menu_sales)
    {
        $closestTime = null;
    
        foreach ($menu_sales as $menu_sale) {
            $createdAt = $menu_sale->created_at;
    
            if (is_null($closestTime) || $createdAt > $closestTime) {
                $closestTime = $createdAt;
            }
        }
    
        return $closestTime;
    }

    private function getTimesOrdered(Collection $menu_sales){
        $uniqueTimestamps = $menu_sales->pluck('created_at')->unique();
        return $uniqueTimestamps->count();
    }
}
