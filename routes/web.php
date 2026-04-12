<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UrlController;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::post('/shorten', [UrlController::class, 'store']);

Route::get('/favicon.ico', fn () => response()->file(public_path('favicon.ico'), [
    'Content-Type' => 'image/x-icon',
]));
Route::get('/favicon.png', fn () => response()->file(public_path('favicon.png'), [
    'Content-Type' => 'image/png',
]));

Route::get('/{shortUrl}', [UrlController::class, 'redirect']);

Route::post('/generate-qrcode', [App\Http\Controllers\QrCodeController::class, 'generate'])
    ->name('generate.qrcode');