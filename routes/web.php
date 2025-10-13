<?php

use App\Http\Controllers\PreviewController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('App');
});

// Endpoint for previewing CSV files
Route::post('/preview', [PreviewController::class, 'preview'])->name('preview.file');;

require __DIR__.'/api.php';