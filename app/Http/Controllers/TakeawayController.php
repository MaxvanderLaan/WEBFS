<?php

namespace App\Http\Controllers;

use App\Models\MealAddition;
use App\Models\Menu;
use App\Models\MenuSale;
use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Builder\Builder;

class TakeawayController extends Controller{

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

        $mealAdditions = MealAddition::get();
        return Inertia::render('Takeaway/Index', [
            'menus' => $menus,
            'mealAdditions' => $mealAdditions,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
    
        $menus = Menu::with(['mealType', 'menuOffers'])
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

    public function store(Request $request)
    {
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

        $qrCodeUris = [];

        $sale->menuSales->chunk(10)->each(function ($menuSalesChunk) use (&$qrCodeUris, $sale) {
            $qrCodeData = [
                'sale_id' => $sale->id,
                'menuSales' => $menuSalesChunk->values()->map(function ($menuSale) {
                    return [
                        'menu_number' => $menuSale->menu->number,
                        'menu_addition' => $menuSale->menu->addition,
                        'menu_name' => $menuSale->menu->name,
                    ];
                }),
            ];
    
            $qrCode = new QrCode(json_encode($qrCodeData));
            $writer = new PngWriter();
            $qrCodeUri = $writer->write($qrCode)->getDataUri();
    
            // Add the generated QR code URI to the array
            $qrCodeUris[] = $qrCodeUri;
        });
    
        return Inertia::render('Takeaway/QrCode', [
            'qr_codes' => $qrCodeUris,
        ]);
    }
}