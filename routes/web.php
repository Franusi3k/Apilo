<?php

use App\Http\Controllers\PreviewController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('App');
});

Route::post('/preview', [PreviewController::class, 'preview']);
