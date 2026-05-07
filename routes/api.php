<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\TelegramController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/telegram/webhook', [PasswordResetLinkController::class, 'handleTelegram']);
Route::post('/telegram/webhook', [TelegramController::class, 'handle']);
