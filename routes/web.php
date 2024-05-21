<?php

use App\Http\Controllers\ChangeMenuController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterOrderController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/get-session-message', function () {
    return response()->json(['success' => session('success')]);
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

    Route::get('/register/menu', [RegisterOrderController::class, 'index'])->name('register.menu');
    Route::get('/register/menu/search', [RegisterOrderController::class, 'search'])->name('search.menu.search');
    Route::post('/register/menu/order', [RegisterOrderController::class, 'store'])->name('register.menu.order');

    Route::get('/change/menu', [ChangeMenuController::class, 'index'])->name('change.menu');
    Route::get('/change/menu/search', [ChangeMenuController::class, 'search'])->name('change.menu.search');
    Route::get('/change/menu/create', [ChangeMenuController::class, 'create'])->name('change.menu.create');
    Route::post('/change/menu/make', [ChangeMenuController::class, 'make'])->name('change.menu.make');
    Route::get('/change/menu/edit/{id}', [ChangeMenuController::class, 'edit'])->name('change.menu.edit');
    Route::put('/change/menu/update', [ChangeMenuController::class, 'update'])->name('change.menu.update');
    Route::post('/change/menu/delete', [ChangeMenuController::class, 'delete'])->name('change.menu.delete');
});

Route::get('/locale/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'nl'])) {
        abort(400, 'Invalid locale');
    }

    Session::put('locale', $locale);

    return redirect()->back();
})->name('locale.switch');

require __DIR__.'/auth.php';
