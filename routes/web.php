<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'KMV Traders API running'
    ]);
});

Route::get('/order-status/{orderNumber}', [OrderController::class, 'getOrderByOrderNumber']);
