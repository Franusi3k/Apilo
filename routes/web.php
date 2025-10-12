<?php

use App\Http\Controllers\PreviewController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('App');
});

Route::post('/preview', [PreviewController::class, 'preview'])->name('preview.file');;

Route::get('product/{sku}', [ProductController::class, 'getProductBySku'])->name('product.show');