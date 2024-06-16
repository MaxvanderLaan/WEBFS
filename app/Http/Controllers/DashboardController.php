<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Help;
use App\Models\PlanningTable;
use App\Models\TableHelp;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;

        $planningIds = Planning::where('user_id', $userId)->pluck('id');
        $tableIds = PlanningTable::whereIn('planning_id', $planningIds)->pluck('table_id');
        $helpIds = TableHelp::whereIn('table_id', $tableIds)->pluck('help_id');
        $helps = Help::where('completed', 0)
            ->whereIn('id', $helpIds)
            ->get();

        return Inertia::render('Dashboard', [
            'helps' => $helps,
        ]);
    }

    public function complete(Request $request){
        $help = Help::find($request->id);
        if ($help) {
            $help->completed = 1;
            $help->save();
        }
        return redirect()->route('dashboard');
    }
}
