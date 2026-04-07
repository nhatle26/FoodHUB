<?php

use App\Http\Controllers\ShopRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/shop/register', [ShopRegistrationController::class, 'create'])->name('shop.create');
Route::post('/shop/register', [ShopRegistrationController::class, 'store'])->name('shop.store');
