<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterOrderController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/menu', [ContactController::class, 'index'])->name('menu');
Route::get('/news', [ContactController::class, 'index'])->name('news');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/search', [RegisterOrderController::class, 'index'])->name('search');
    Route::get('/search/menu', [RegisterOrderController::class, 'search'])->name('search.menu');

    Route::post('/register/order', [RegisterOrderController::class, 'store'])->name('register.order');
});

require __DIR__.'/auth.php';
