<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// =============================================================================
// CONTROLLER IMPORTS
// =============================================================================
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;

// --- Customer Controllers (Dari Kodemu) ---
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Customer\MenuController as CustomerMenu;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController as CustomerOrder;
use App\Http\Controllers\Customer\InvoiceController as CustomerInvoice;
use App\Http\Controllers\Customer\PaymentController as CustomerPayment;
use App\Http\Controllers\Customer\HistoryController as CustomerHistory;

// --- Admin Controllers (Dari Temanmu) ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Admin\UserController; // Asumsi temanmu menaruh ini di folder Admin

// --- Pengelola Controllers (Dari Temanmu) ---
use App\Http\Controllers\Pengelola\DashboardController as PengelolaDashboardController;
use App\Http\Controllers\MenuController as PengelolaMenuController;
// use App\Http\Controllers\CategoryController; // Dinonaktifkan karena tabel categories sudah dihapus di ERD baru
use App\Http\Controllers\PengelolaOrderController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\LaporanFavoritController;
use App\Http\Controllers\NotificationController;

/*
|==========================================================================
| SmartCanteen — Web Routes
|==========================================================================
*/

// =============================================================================
// REDIRECT ROOT
// =============================================================================
Route::get('/', fn () => redirect()->route('login'));

// =============================================================================
// AUTH ROUTES (Guest Only)
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
// GLOBAL AUTH ROUTES (Untuk semua role yang login)
// =============================================================================
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── PROFILE (Fitur baru temanmu) ──
    Route::get('/profile/edit',     [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update',   [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
});

// =============================================================================
// CUSTOMER ROUTES (Sesuai dengan Kodemu)
// =============================================================================
Route::middleware(['auth', 'role:customer'])
     ->prefix('customer')
     ->name('customer.')
     ->group(function (): void {

         Route::get('/home', [CustomerDashboard::class, 'index'])->name('home');
         Route::get('/menu', [CustomerMenu::class, 'index'])->name('menu');
         Route::get('/history', [CustomerHistory::class, 'index'])->name('history');

         // ── CART ──
         Route::prefix('cart')->name('cart.')->group(function (): void {
             Route::get('/',               [CartController::class, 'index'])   ->name('index');
             Route::post('/',              [CartController::class, 'store'])   ->name('store');
             Route::patch('/{item}',       [CartController::class, 'update'])  ->name('update');
             Route::delete('/{item}',      [CartController::class, 'destroy']) ->name('destroy');
             Route::get('/data',           [CartController::class, 'data'])    ->name('data'); // Tambahan untuk fetch data JSON
         });

         // ── ORDER ──
         Route::prefix('order')->name('order.')->group(function (): void {
             Route::get('/checkout',       [CustomerOrder::class, 'showCheckout']) ->name('checkout.show');
             Route::post('/checkout',      [CustomerOrder::class, 'checkout'])     ->name('checkout');
             Route::post('/confirm',       [CustomerOrder::class, 'confirm'])      ->name('confirm'); // Tambahan dari view order.blade.php
             Route::get('/{id}',           [CustomerOrder::class, 'show'])         ->name('show');
         });

         // ── PAYMENT ──
         Route::prefix('payment')->name('payment.')->group(function (): void {
             Route::get('/',               [CustomerPayment::class, 'index'])  ->name('index');
             Route::get('/{orderId}',      [CustomerPayment::class, 'show'])   ->name('show');
             Route::post('/{orderId}',     [CustomerPayment::class, 'upload']) ->name('upload');
         });

     });

// =============================================================================
// ADMIN ROUTES (Gabungan Strukturmu dengan Fungsi Temanmu)
// =============================================================================
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function (): void {

         Route::get('/', fn() => redirect('/admin/dashboard'));
         Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
         Route::get('/verification', [VerificationController::class, 'index'])->name('verification');
         Route::get('/report', fn() => view('admin.report'))->name('report');

         // ── TRANSACTIONS ──
         Route::get('/transactions',                  [TransactionController::class, 'index'])->name('transactions');
         Route::get('/transactions/{payment}/detail', [TransactionController::class, 'detail'])->name('transactions.detail');
         Route::get('/transactions/export',           [TransactionController::class, 'export'])->name('transactions.export');

         // ── KELOLA USER ──
         Route::get('/users',                  [UserController::class, 'index'])->name('users.index');
         Route::post('/users',                 [UserController::class, 'store'])->name('users.store');
         Route::put('/users/{user}',           [UserController::class, 'update'])->name('users.update');
         Route::patch('/users/{user}/suspend', [UserController::class, 'toggleSuspend'])->name('users.suspend');
         Route::delete('/users/{user}',        [UserController::class, 'destroy'])->name('users.destroy');

     });

// =============================================================================
// PENGELOLA ROUTES (Gabungan Strukturmu dengan Fungsi Temanmu)
// =============================================================================
Route::middleware(['auth', 'role:pengelola'])
     ->prefix('pengelola')
     ->name('pengelola.')
     ->group(function (): void {

         Route::get('/', fn() => redirect('/pengelola/dashboard'));
         Route::get('/dashboard', [PengelolaDashboardController::class, 'index'])->name('dashboard');

         // ── KELOLA MENU ──
         Route::get('/menu-management', [PengelolaMenuController::class, 'index'])->name('menu.index');
         Route::post('/menu-management/store', [PengelolaMenuController::class, 'store'])->name('menu.store');
         Route::put('/menu-management/update/{menu}', [PengelolaMenuController::class, 'update'])->name('menu.update');
         Route::delete('/menu-management/delete/{menu}', [PengelolaMenuController::class, 'destroy'])->name('menu.destroy');
         Route::patch('/menu-management/toggle/{menu}', [PengelolaMenuController::class, 'toggle'])->name('menu.toggle');

         // ── PESANAN MASUK ──
         Route::get('/orders', [PengelolaOrderController::class, 'index'])->name('orders.index');
         Route::patch('/orders/{order}/confirm', [PengelolaOrderController::class, 'confirm'])->name('orders.confirm');
         Route::patch('/orders/{order}/process', [PengelolaOrderController::class, 'process'])->name('orders.process');
         Route::patch('/orders/{order}/complete', [PengelolaOrderController::class, 'complete'])->name('orders.complete');

         // ── PROSES PENGIRIMAN ──
         Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.index');
         Route::patch('/delivery/{delivery}/send', [DeliveryController::class, 'send'])->name('delivery.send');
         Route::patch('/delivery/{delivery}/complete', [DeliveryController::class, 'complete'])->name('delivery.complete');

         // ── LAPORAN ──
         Route::get('/report', [LaporanFavoritController::class, 'index'])->name('report.index');
         Route::get('/report/export', [LaporanFavoritController::class, 'exportExcel'])->name('report.export');

         // ── NOTIFICATIONS ──
         Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
         Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
         Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
         Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
         Route::get('/notifications/count', [NotificationController::class, 'unreadCount'])->name('notifications.count');

     });