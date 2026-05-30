<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service' => 'URL Shortener',
        'docs' => '/api/links',
        'health' => '/up',
    ]);
});

Route::get('/{slug}', RedirectController::class)
    ->where('slug', '[A-Za-z0-9_-]+');
