<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});


// --- GRUP UNTUK USER YANG BELUM LOGIN (GUEST) ---
Route::middleware('guest')->group(function () {
    
    // Halaman Input Nomor HP (Step 1)
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Proses Kirim OTP ke Telegram
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.phone');

    // Proses Verifikasi OTP & Update Password Baru (Step 2)
    // Kita gunakan POST karena di halaman forgot-password.blade.php kamu 
    // semua input ada di satu form saat session otp_sent aktif.
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// --- GRUP UNTUK USER YANG SUDAH LOGIN (AUTH) ---
Route::middleware('auth')->group(function () {
    
    // Konfirmasi password sebelum akses area sensitif
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Update password dari halaman profil
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});