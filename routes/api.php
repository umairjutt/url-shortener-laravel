<?php

use App\Http\Controllers\Api\ShortLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:create-link')
    ->post('/links', [ShortLinkController::class, 'store']);

Route::get('/links/{slug}', [ShortLinkController::class, 'show']);
Route::get('/links/{slug}/qr', [ShortLinkController::class, 'qr']);
