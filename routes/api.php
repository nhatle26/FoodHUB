<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\VoucherController;
use Illuminate\Support\Facades\Route;

Route::prefix('shops/{shop}')->group(function () {
    Route::get('/vouchers', [VoucherController::class, 'index']);
});

Route::post('/vouchers/preview', [VoucherController::class, 'preview']);
Route::post('/orders', [OrderController::class, 'store']);
