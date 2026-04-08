<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/shop/register', [RegisterController::class, 'create'])->name('shop.create');
Route::post('/shop/register', [RegisterController::class, 'store'])->name('shop.store');
