<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController as WebOrderController;
use App\Http\Controllers\Shop\OrderController as ShopOrderController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\ShopController as ShopDashboardController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/user-register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/user-register', [RegisterController::class, 'register'])->name('register.post');

    Route::get('/shop-register', [RegisterController::class, 'create'])->name('shop.create');
    Route::post('/shop-register', [RegisterController::class, 'store'])->name('shop.store');
});

Route::prefix('shop')->name('shop.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [ShopDashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('orders', ShopOrderController::class);
    Route::get('/revenue', [ShopDashboardController::class, 'revenue'])->name('revenue');
    Route::get('/settings', [ShopDashboardController::class, 'settings'])->name('settings');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('shops/pending', [AdminController::class, 'pendingShops'])->name('shops.pending');
    Route::post('shops/{shop}/approve', [AdminController::class, 'approveShop'])->name('shops.approve');
    Route::post('shops/{shop}/reject', [AdminController::class, 'rejectShop'])->name('shops.reject');
    Route::resource('users', AdminController::class);
    Route::resource('categories', CategoryController::class)->except(['show']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'show'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'updateInfo'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/prepare', [CheckoutController::class, 'prepare'])->name('checkout.prepare');
    Route::patch('/checkout/items/{productId}', [CheckoutController::class, 'updateQuantity'])->name('checkout.items.update');
    Route::delete('/checkout/items/{productId}', [CheckoutController::class, 'remove'])->name('checkout.items.remove');
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');

    Route::post('/vouchers/preview', [VoucherController::class, 'preview'])->name('vouchers.preview');
    Route::post('/orders', [WebOrderController::class, 'store'])->name('orders.store');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop/{id}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/shops/{shop}/vouchers', [VoucherController::class, 'index'])->name('shops.vouchers.index');
