<?php

use App\UserInterface\Controllers\Api\V1\OrderController;
use App\UserInterface\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);

Route::middleware('throttle:orders-create')
    ->post('/orders', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{order}', [OrderController::class, 'show']);
Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
