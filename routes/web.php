<?php

use App\Http\Controllers\ChangeMenuController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterMenuOfferController;
use App\Http\Controllers\RegisterOrderController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TabletOrderController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::get('/get-session-message', function () {
    return response()->json(['success' => session('success')]);
});

Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/news', [NewsController::class, 'index'])->name('news');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/tablet/start', [TabletOrderController::class, 'start'])->name('tablet.start');
    Route::post('/tablet/register', [TabletOrderController::class, 'register'])->name('tablet.register');
    Route::get('/tablet/create/{saleId}', [TabletOrderController::class, 'create'])->name('tablet.create');
    Route::post('/tablet/make', [TabletOrderController::class, 'make'])->name('tablet.make');
    Route::get('/tablet/{saleId}', [TabletOrderController::class, 'index'])->name('tablet.index');
    Route::get('/tablet/checkout/{saleId}', [TabletOrderController::class, 'checkout'])->name('checkout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/register/menu', [RegisterOrderController::class, 'index'])->name('register.menu');
    Route::get('/register/menu/search', [RegisterOrderController::class, 'search'])->name('register.menu.search');
    Route::post('/register/menu/order', [RegisterOrderController::class, 'store'])->name('register.menu.order');

    Route::get('/register/menu/offer', [RegisterMenuOfferController::class, 'index'])->name('register.menu.offer');
    Route::get('/register/menu/offer/create', [RegisterMenuOfferController::class, 'create'])->name('register.menu.offer.create');
    Route::post('/register/menu/offer/make', [RegisterMenuOfferController::class, 'make'])->name('register.menu.offer.make');

    Route::get('/change/menu', [ChangeMenuController::class, 'index'])->name('change.menu');
    Route::get('/change/menu/search', [ChangeMenuController::class, 'search'])->name('change.menu.search');
    Route::get('/change/menu/create', [ChangeMenuController::class, 'create'])->name('change.menu.create');
    Route::post('/change/menu/make', [ChangeMenuController::class, 'make'])->name('change.menu.make');
    Route::get('/change/menu/edit/{id}', [ChangeMenuController::class, 'edit'])->name('change.menu.edit');
    Route::put('/change/menu/update', [ChangeMenuController::class, 'update'])->name('change.menu.update');
    Route::post('/change/menu/delete', [ChangeMenuController::class, 'delete'])->name('change.menu.delete');

    Route::get('/admin/table', [TableController::class, 'index'])->name('admin.table');
    Route::get('/admin/table/create', [TableController::class, 'create'])->name('admin.table.create');
    Route::post('/admin/table/make', [TableController::class, 'make'])->name('admin.table.make');
    Route::get('/admin/table/edit/{id}', [TableController::class, 'edit'])->name('admin.table.edit');
    Route::put('/admin/table/update', [TableController::class, 'update'])->name('admin.table.update');
    Route::post('/admin/table/delete', [TableController::class, 'delete'])->name('admin.table.delete');

    Route::get('/admin/planning', [PlanningController::class, 'index'])->name('admin.planning');
    Route::get('/admin/planning/create', [PlanningController::class, 'create'])->name('admin.planning.create');
    Route::post('/admin/planning/make', [PlanningController::class, 'make'])->name('admin.planning.make');
    Route::get('/admin/planning/edit/{id}', [PlanningController::class, 'edit'])->name('admin.planning.edit');
    Route::put('/admin/planning/update', [PlanningController::class, 'update'])->name('admin.planning.update');
    Route::post('/admin/planning/delete', [PlanningController::class, 'delete'])->name('admin.planning.delete');
});

Route::get('/locale/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'nl'])) {
        abort(400, 'Invalid locale');
    }

    Session::put('locale', $locale);

    return redirect()->back();
})->name('locale.switch');

require __DIR__.'/auth.php';
