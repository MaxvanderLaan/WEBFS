<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class MenuController extends Controller{

    public function index()
    {
        return Inertia::render('Menus/Menu');
    }
}