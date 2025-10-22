<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('api')->name('api.')->group(function () {

    // Endpoint responsible for sending the order
    Route::post('/send', [OrderController::class, 'send'])->name('order.send');

    // Enpoint responsible for fetching the product by SKU
    Route::get('product/{sku}', [ProductController::class, 'getProductBySku'])->name('product.show');

});
