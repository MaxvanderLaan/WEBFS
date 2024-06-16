<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Help;

class DashboardController extends Controller
{
    public function index()
    {
        $helps = Help::where('completed', 0)->get();

        return Inertia::render('Dashboard', [
            'helps' => $helps,
        ]);
    }
}
