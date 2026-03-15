<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api.key')->group(function () {
    Route::post('/orders', [OrderController::class, 'createOrder']);
    Route::get('/orders/{orderNumber}', [OrderController::class, 'getOrderDetail']);
});
