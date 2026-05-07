<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// =============================================================================
// CONTROLLER IMPORTS
// =============================================================================
use App\Http\Controllers\Auth\AuthController;

// --- Customer Controllers ---
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\InvoiceController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\HistoryController;

// --- Admin & Pengelola Controllers (Uncomment saat sudah dibuat) ---
// use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
// use App\Http\Controllers\Admin\VerificationController;
// use App\Http\Controllers\Admin\TransactionController;
// use App\Http\Controllers\Admin\ReportController;

/*
|==========================================================================
| SmartCanteen — Web Routes
|==========================================================================
*/

// =============================================================================
// REDIRECT ROOT
// =============================================================================
// Diarahkan ke login. Jika sistem mendeteksi user sudah login, middleware guest
// pada route login secara otomatis akan mengarahkannya ke dashboard masing-masing.
Route::get('/', fn () => redirect()->route('login'));

// =============================================================================
// AUTH ROUTES (Guest Only - Hanya untuk yang belum login)
// =============================================================================
Route::middleware('guest')->group(function (): void {
    
    // ── LOGIN & REGISTER ──
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // ── FORGOT PASSWORD ──
    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
    Route::post('/forgot-password', fn() => back()->with('otp_sent', true))->name('password.send');
    Route::post('/forgot-password/resend', fn() => back()->with('otp_sent', true))->name('password.resend');
    Route::post('/reset-password', fn() => redirect()->route('login')->with('status', 'Password berhasil direset!'))->name('password.reset');

});

// =============================================================================
// LOGOUT (Auth Only)
// =============================================================================
Route::post('/logout', [AuthController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

// =============================================================================
// CUSTOMER ROUTES
// Protected: Login & Role 'customer'
// =============================================================================
Route::middleware(['auth', 'role:customer'])
     ->prefix('customer')
     ->name('customer.')
     ->group(function (): void {

         // ------------------------------------------------------------------
         // DASHBOARD & MENU
         // ------------------------------------------------------------------
         Route::get('/home', [DashboardController::class, 'index'])->name('home');
         Route::get('/menu', [MenuController::class, 'index'])->name('menu');

         // ------------------------------------------------------------------
         // CART — Keranjang Belanja
         // Endpoint AJAX: store, update, destroy mengembalikan JSON
         // ------------------------------------------------------------------
         Route::prefix('cart')->name('cart.')->group(function (): void {
             Route::get('/',               [CartController::class, 'index'])   ->name('index');
             Route::post('/',              [CartController::class, 'store'])   ->name('store');
             Route::patch('/{item}',       [CartController::class, 'update'])  ->name('update');
             Route::delete('/{item}',      [CartController::class, 'destroy']) ->name('destroy');
         });

         // ------------------------------------------------------------------
         // ORDER — Checkout & Detail Pesanan
         // ------------------------------------------------------------------
         Route::prefix('order')->name('order.')->group(function (): void {
             Route::get('/checkout',       [OrderController::class, 'showCheckout']) ->name('checkout.show');
             Route::post('/checkout',      [OrderController::class, 'checkout'])     ->name('checkout');
             Route::get('/{id}',           [OrderController::class, 'show'])         ->name('show');
         });

         // ------------------------------------------------------------------
         // PAYMENT — Pembayaran
         // ------------------------------------------------------------------
         Route::prefix('payment')->name('payment.')->group(function (): void {
             Route::get('/',               [PaymentController::class, 'index'])  ->name('index');
             Route::get('/{orderId}',      [PaymentController::class, 'show'])   ->name('show');
             Route::post('/{orderId}',     [PaymentController::class, 'upload']) ->name('upload');
         });

         // ------------------------------------------------------------------
         // HISTORY — Riwayat Pesanan
         // ------------------------------------------------------------------
         Route::get('/history', [HistoryController::class, 'index'])->name('history');
         
         // ------------------------------------------------------------------
         // INVOICE (Opsional - Bisa diaktifkan nanti jika dibutuhkan terpisah dari payment/history)
         // ------------------------------------------------------------------
         // Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');
         // Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->name('invoice.show');

     });

// =============================================================================
// ADMIN ROUTES
// Protected: Login & Role 'admin'
// =============================================================================
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function (): void {

         Route::get('/', fn() => redirect('/admin/dashboard'));
         Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
         Route::get('/verification', fn() => view('admin.verification'))->name('verification');
         Route::get('/transactions', fn() => view('admin.transactions'))->name('transactions');
         Route::get('/report', fn() => view('admin.report'))->name('report');
         Route::get('/kelola-user', fn() => view('admin.kelola-user'))->name('kelola-user');

     });

// =============================================================================
// PENGELOLA ROUTES
// Protected: Login & Role 'pengelola'
// =============================================================================
Route::middleware(['auth', 'role:pengelola'])
     ->prefix('pengelola')
     ->name('pengelola.')
     ->group(function (): void {

         Route::get('/', fn() => redirect('/pengelola/dashboard'));

         // ── DASHBOARD ──
         Route::get('/dashboard', fn() => view('pengelola.dashboard'))->name('dashboard');

         // ── KELOLA MENU ──
         Route::get('/menu', fn() => view('pengelola.menu-management'))->name('menu.index');
         Route::post('/menu/store', fn() => redirect()->route('pengelola.menu.index')->with('success', 'Menu berhasil ditambahkan!'))->name('menu.store');
         Route::put('/menu/{id}', fn($id) => redirect()->route('pengelola.menu.index')->with('success', 'Menu #' . $id . ' berhasil diperbarui!'))->name('menu.update');
         Route::delete('/menu/{id}', fn($id) => redirect()->route('pengelola.menu.index')->with('success', 'Menu #' . $id . ' berhasil dihapus.'))->name('menu.destroy');
         Route::patch('/menu/{id}/toggle', fn($id) => response()->json(['success' => true]))->name('menu.toggle');

         // ── PESANAN MASUK ──
         Route::get('/orders', fn() => view('pengelola.orders'))->name('orders.index');
         Route::post('/orders/{id}/accept', fn($id) => redirect()->route('pengelola.orders.index')->with('success', 'Pesanan #' . $id . ' diterima!'))->name('orders.accept');

         // ── PROSES PENGIRIMAN ──
         Route::get('/delivery', fn() => view('pengelola.delivery'))->name('delivery.index');
         Route::post('/delivery/{id}/status', fn($id) => redirect()->route('pengelola.delivery.index')->with('success', 'Status pesanan #' . $id . ' diperbarui!'))->name('delivery.updateStatus');

         // ── LAPORAN ──
         Route::get('/report', fn() => view('pengelola.report'))->name('report.index');
         Route::get('/report/export', fn() => redirect()->route('pengelola.report.index')->with('info', 'Mengekspor PDF...'))->name('report.export');

     });