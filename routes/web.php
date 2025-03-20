<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UrlController;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::post('/shorten', [UrlController::class, 'store']);
Route::get('/{shortUrl}', [UrlController::class, 'redirect']);

Route::post('/generate-qrcode', [App\Http\Controllers\QrCodeController::class, 'generate'])
    ->name('generate.qrcode');