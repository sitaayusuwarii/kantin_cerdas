<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Admin\DashboardController;
// use App\Http\Controllers\Admin\VerificationController;
// use App\Http\Controllers\Admin\TransactionController;
// use App\Http\Controllers\Admin\ReportController;
/*
|==========================================================================
| SmartCanteen — Web Routes
|==========================================================================
| Berisi semua route untuk dua role:
|   1. Customer   → /home, /menu, /cart, /order, /invoice, /payment, /history
|   2. Admin      → /admin/dashboard, /menu, /orders, /delivery, /report
|
| Untuk production, aktifkan middleware auth & role di masing-masing group.
|==========================================================================
*/


// =============================================
// ROOT
// =============================================
Route::get('/', function () {
    return redirect('/home');
});


// =============================================
// CUSTOMER ROUTES
// =============================================
// Production: ->middleware(['auth', 'role:customer'])
Route::group([], function () {

    // HOME / DASHBOARD
    Route::get('/home', function () {
        return view('customer.home');
    })->name('customer.home');

    // MENU KANTIN
    Route::get('/menu', function () {
        return view('customer.menu');
    })->name('customer.menu');

    // ✅ KERANJANG — halaman penuh review cart
    Route::get('/cart', function () {
        return view('customer.cart');
    })->name('customer.cart');

    // DETAIL PESANAN / CHECKOUT
    Route::get('/order', function () {
        return view('customer.order');
    })->name('customer.order');

    // POST: Konfirmasi Pesanan (dummy)
    Route::post('/order/confirm', function () {
        return redirect('/history')->with('success', 'Pesanan berhasil dikonfirmasi!');
    })->name('customer.order.confirm');

    // TAGIHAN / INVOICE
    Route::get('/invoice', function () {
        return view('customer.invoice');
    })->name('customer.invoice');

    // Tagihan berdasarkan ID
    Route::get('/invoice/{id}', function ($id) {
        return view('customer.invoice', compact('id'));
    })->name('customer.invoice.show');

    // UPLOAD BUKTI PEMBAYARAN
    Route::get('/payment', function () {
        return view('customer.payment');
    })->name('customer.payment');

    // POST: Upload bukti bayar (dummy)
    Route::post('/payment/upload', function () {
        return redirect('/history')->with('success', 'Bukti pembayaran berhasil dikirim! Kami akan memverifikasi dalam 1x24 jam.');
    })->name('customer.payment.upload');

    // RIWAYAT PESANAN
    Route::get('/history', function () {
        return view('customer.history');
    })->name('customer.history');

});


// ========================
// ADMIN ROUTES
// ========================
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/', fn() => redirect('/admin/dashboard'));

    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Verifikasi Pembayaran
    Route::get('/verification', function () {
        return view('admin.verification');
    })->name('verification');

    // Monitoring Transaksi
    Route::get('/transactions', function () {
        return view('admin.transactions');
    })->name('transactions');

    // Laporan
    Route::get('/report', function () {
        return view('admin.report');
    })->name('report');

    // Kelola User
    Route::get('/kelola-user', function () {
        return view('admin.kelola-user');
    })->name('kelola-user');
});

// ──────────────────────────────────────────────────────
// PENGELOLA ROUTES
// Production: ->middleware(['auth', 'role:pengelola'])
// ──────────────────────────────────────────────────────
Route::prefix('pengelola')->name('pengelola.')->group(function () {

    // Redirect /pengelola → /pengelola/dashboard
    Route::get('/', fn() => redirect('/pengelola/dashboard'));

    // ── DASHBOARD ──────────────────────────────────
    Route::get('/dashboard', function () {
        return view('pengelola.dashboard');
    })->name('dashboard');

    // ── KELOLA MENU ────────────────────────────────
    Route::get('/menu', function () {
        return view('pengelola.menu-management');
    })->name('menu.index');

    Route::post('/menu/store', function () {
        return redirect()->route('pengelola.menu.index')
                         ->with('success', 'Menu berhasil ditambahkan!');
    })->name('menu.store');

    Route::put('/menu/{id}', function ($id) {
        return redirect()->route('pengelola.menu.index')
                         ->with('success', 'Menu #' . $id . ' berhasil diperbarui!');
    })->name('menu.update');

    Route::delete('/menu/{id}', function ($id) {
        return redirect()->route('pengelola.menu.index')
                         ->with('success', 'Menu #' . $id . ' berhasil dihapus.');
    })->name('menu.destroy');

    Route::patch('/menu/{id}/toggle', function ($id) {
        return response()->json(['success' => true]);
    })->name('menu.toggle');

    // ── PESANAN MASUK ──────────────────────────────
    Route::get('/orders', function () {
        return view('pengelola.orders');
    })->name('orders.index');

    Route::post('/orders/{id}/accept', function ($id) {
        return redirect()->route('pengelola.orders.index')
                         ->with('success', 'Pesanan #' . $id . ' diterima!');
    })->name('orders.accept');

    // ── PROSES PENGIRIMAN ──────────────────────────
    Route::get('/delivery', function () {
        return view('pengelola.delivery');
    })->name('delivery.index');

    Route::post('/delivery/{id}/status', function ($id) {
        return redirect()->route('pengelola.delivery.index')
                         ->with('success', 'Status pesanan #' . $id . ' diperbarui!');
    })->name('delivery.updateStatus');

    // ── LAPORAN FAVORIT ────────────────────────────
    Route::get('/report', function () {
        return view('pengelola.report');
    })->name('report.index');

    Route::get('/report/export', function () {
        return redirect()->route('pengelola.report.index')
                         ->with('info', 'Mengekspor PDF...');
    })->name('report.export');

});

/*
|--------------------------------------------------------------------------
| VERSI PRODUCTION — Dengan Controller & Middleware (Uncomment saat siap)
|--------------------------------------------------------------------------

use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\InvoiceController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\HistoryController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\MenuController      as AdminMenu;
use App\Http\Controllers\Admin\OrderController     as AdminOrder;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\ReportController;

// --- Customer ---
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/home',             [DashboardController::class, 'index'])->name('customer.home');
    Route::get('/menu',             [MenuController::class, 'index'])->name('customer.menu');
    Route::get('/cart',             [CartController::class, 'index'])->name('customer.cart');
    Route::get('/order',            [OrderController::class, 'index'])->name('customer.order');
    Route::post('/order/confirm',   [OrderController::class, 'confirm'])->name('customer.order.confirm');
    Route::get('/invoice',          [InvoiceController::class, 'index'])->name('customer.invoice');
    Route::get('/invoice/{id}',     [InvoiceController::class, 'show'])->name('customer.invoice.show');
    Route::get('/payment',          [PaymentController::class, 'index'])->name('customer.payment');
    Route::post('/payment/upload',  [PaymentController::class, 'upload'])->name('customer.payment.upload');
    Route::get('/history',          [HistoryController::class, 'index'])->name('customer.history');
});

// --- Admin / Pengelola ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                    [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/menu',                         [AdminMenu::class, 'index'])->name('menu.index');
    Route::post('/menu/store',                  [AdminMenu::class, 'store'])->name('menu.store');
    Route::put('/menu/{id}',                    [AdminMenu::class, 'update'])->name('menu.update');
    Route::delete('/menu/{id}',                 [AdminMenu::class, 'destroy'])->name('menu.destroy');
    Route::patch('/menu/{id}/toggle-status',    [AdminMenu::class, 'toggleStatus'])->name('menu.toggleStatus');
    Route::get('/orders',                       [AdminOrder::class, 'index'])->name('orders.index');
    Route::post('/orders/{id}/accept',          [AdminOrder::class, 'accept'])->name('orders.accept');
    Route::get('/delivery',                     [DeliveryController::class, 'index'])->name('delivery.index');
    Route::post('/delivery/{id}/update-status', [DeliveryController::class, 'updateStatus'])->name('delivery.updateStatus');
    Route::get('/report',                       [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/export',                [ReportController::class, 'export'])->name('report.export');
});

|--------------------------------------------------------------------------
*/
