<?php

use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VariationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('categories/delete_selected', [CategoryController::class, 'delete_selected'])->name('categories.delete_selected');
    Route::resource('categories', CategoryController::class);

    Route::get('size-variation/{id}/delete/', [MenuItemController::class, 'delete_variation'])->name('menu_items.delete_variation');
    Route::post('menu-items/delete_selected', [MenuItemController::class, 'delete_selected'])->name('menu-items.delete_selected');
    Route::post('menu-items/update-status/{id}', [MenuItemController::class, 'update_status'])->name('menu-items.update_status');
    Route::resource('menu-items', MenuItemController::class);

    Route::post('variations/delete_selected', [VariationController::class, 'delete_selected'])->name('variations.delete_selected');
    Route::resource('variations', VariationController::class);
    Route::get('variation_values/{id}', [VariationController::class, 'variation_values'])->name('get_variation_values');
    Route::get('variation/{variation}/delete/{id}', [VariationController::class, 'delete_variation_values'])->name('variation_values.delete');

    // Route::post('advertisements/delete_selected', [AdvertisementController::class, 'delete_selected'])->name('advertisements.delete_selected');
    // Route::resource('advertisements', AdvertisementController::class);

    Route::post('branches/delete_selected', [BranchController::class, 'delete_selected'])->name('branches.delete_selected');
    Route::resource('branches', BranchController::class);

    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::post('settings', [SettingController::class, 'save']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/change-language/{lang?}', function (Request $request, $lang = 'en') {
    session()->put('locale', $lang);
    return redirect()->back();
})->name('change_language');

require __DIR__ . '/auth.php';


Route::get('{code}/{type}/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::post('{code}/{type}/checkout', [CheckoutController::class, 'make_order'])->name('make_order');
Route::get('{code}/{type}/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('{code}/{type}/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

Route::prefix('{code}/{type}/customer')->name('customer.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');

    Route::get('login', [CustomerController::class, 'login'])->name('login');
    Route::post('login', [CustomerController::class, 'store']);

    Route::get('register', [CustomerController::class, 'register'])->name('register');
    Route::post('register', [CustomerController::class, 'register_store']);

    Route::post('logout', [CustomerController::class, 'logout'])->name('logout');
});

Route::post('remove_address/{id}', [CustomerController::class, 'remove_address'])->name('remove_address');

Route::get('/{code}/{type}', [MainController::class, 'menu'])->name('menu');
