<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PengelolaOrderController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LaporanFavoritController;
use App\Http\Controllers\Pengelola\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Admin\LaporanKeuanganController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\Admin\UnpaidOrderController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\Pengelola\TenantOrderController;
use App\Http\Controllers\Admin\AdminMenuController;


Route::get('/', function () {
    return view('auth/login');
});

Route::middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::patch('/admin/dashboard/target', [AdminDashboardController::class, 'setTarget'])
            ->name('admin.dashboard.setTarget');

        Route::get('/admin/kelola-user', [UserController::class, 'index'])
            ->name('admin.kelola-user');

        Route::get('/admin/laporan-keuangan', [LaporanKeuanganController::class, 'index'])
            ->name('admin.laporan-keuangan');

        // placeholder untuk export (implement terpisah)
        Route::get('/admin/laporan-keuangan/pdf', [LaporanKeuanganController::class, 'exportPdf'])
            ->name('admin.laporan-keuangan.export-pdf');
        Route::get('/admin/laporan-keuangan/excel', [LaporanKeuanganController::class, 'exportExcel'])
            ->name('admin.laporan-keuangan.export-excel');

        Route::get('/transactions',              [TransactionController::class, 'index'])->name('admin.transactions');
        Route::get('/transactions/{payment}/detail', [TransactionController::class, 'detail'])->name('admin.transactions.detail');
        Route::get('/transactions/export',       [TransactionController::class, 'export'])->name('admin.transactions.export');

        Route::get('/admin/verification', [VerificationController::class, 'index'])->name('admin.verification');
        Route::post('/admin/verification/{payment}/verify', [VerificationController::class, 'verify'])->name('admin.verification.verify');
        Route::post('/admin/verification/{payment}/reject', [VerificationController::class, 'reject'])->name('admin.verification.reject');

        // Kelola User
        Route::get('/admin/users',                  [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users',                 [UserController::class, 'store'])->name('admin.users.store');
        Route::put('/admin/users/{user}',           [UserController::class, 'update'])->name('admin.users.update');
        Route::patch('/admin/users/{user}/suspend', [UserController::class, 'toggleSuspend'])->name('admin.users.toggle-suspend');
        Route::delete('/admin/users/{user}',        [UserController::class, 'destroy'])->name('admin.users.destroy');

        //KELOLA TENANT
        Route::resource('admin/tenants', TenantController::class)->names('admin.tenants');
        Route::patch('admin/tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])
            ->name('admin.tenants.toggle-status');

        Route::get('/admin/unpaid-orders',                        [UnpaidOrderController::class, 'index'])->name('admin.unpaid-orders');
        Route::post('/admin/unpaid-orders/{order}/cancel',        [UnpaidOrderController::class, 'cancel'])->name('admin.unpaid-orders.cancel');
        Route::post('/admin/unpaid-orders/{order}/send-reminder', [UnpaidOrderController::class, 'sendReminder'])->name('admin.unpaid-orders.reminder');

        Route::get('/admin/notifications',                              [AdminNotificationController::class, 'index'])->name('admin.notifications');
        Route::post('/admin/notifications/{notification}/read',         [AdminNotificationController::class, 'markRead'])->name('admin.notifications.read');
        Route::post('/admin/notifications/read-all',                    [AdminNotificationController::class, 'markAllRead'])->name('admin.notifications.read-all');
        Route::get('/admin/notifications/unread-count',                 [AdminNotificationController::class, 'unreadCount'])->name('admin.notifications.count');
        Route::post('admin/notifications/{notification}/read', [AdminNotificationController::class, 'markRead'])
        ->name('admin.notifications.mark-read');

        Route::get('/admin/payment-methods',                          [PaymentMethodController::class, 'index'])->name('admin.payment-methods');
        Route::post('/admin/payment-methods',                         [PaymentMethodController::class, 'store'])->name('admin.payment-methods.store');
        Route::put('/admin/payment-methods/{paymentMethod}',          [PaymentMethodController::class, 'update'])->name('admin.payment-methods.update');
        Route::patch('/admin/payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggleActive'])->name('admin.payment-methods.toggle');
        Route::delete('/admin/payment-methods/{paymentMethod}',       [PaymentMethodController::class, 'destroy'])->name('admin.payment-methods.destroy');
    
        Route::get('admin/menus', [\App\Http\Controllers\Admin\AdminMenuController::class, 'index'])->name('admin.menus');
        
        Route::patch('/admin/menu-management/toggle/{menu}', [AdminMenuController::class, 'toggle'])
        ->name('admin.menu-management.toggle');
        Route::delete('/admin/menu-management/{menu}', [AdminMenuController::class, 'destroy']);
        });
        

Route::middleware(['auth', 'role:pengelola'])
    ->group(function () {

        Route::get('/pengelola/dashboard', [DashboardController::class, 'index'])
            ->name('pengelola.dashboard');

        // MENU MANAGEMENT
        Route::get('/pengelola/menu-management', [MenuController::class, 'index'])
            ->name('pengelola.menu-management');

        Route::post('/pengelola/menu-management/store', [MenuController::class, 'store'])
            ->name('pengelola.menu.store');

        Route::put('/pengelola/menu-management/update/{menu}', [MenuController::class, 'update'])
            ->name('pengelola.menu.update');

        Route::delete('/pengelola/menu-management/delete/{menu}', [MenuController::class, 'destroy'])
            ->name('pengelola.menu.delete');

        Route::patch('/pengelola/menu-management/toggle/{menu}', [MenuController::class, 'toggle'])
            ->name('pengelola.menu.toggle');


        // CATEGORY
        Route::get('/pengelola/categories', [CategoryController::class, 'index'])
            ->name('pengelola.categories.index');

        Route::post('/pengelola/categories/store', [CategoryController::class, 'store'])
            ->name('pengelola.categories.store');

        Route::put('/pengelola/categories/update/{id}', [CategoryController::class, 'update'])
            ->name('pengelola.categories.update');

        Route::delete('/pengelola/categories/delete/{id}', [CategoryController::class, 'destroy'])
            ->name('pengelola.categories.delete');

        // ORDER

        Route::get('pengelola/orders', [PengelolaOrderController::class, 'index'])
            ->name('pengelola.orders');

        Route::patch('pengelola/orders/{order}/confirm', [PengelolaOrderController::class, 'confirm'])
            ->name('pengelola.orders.confirm');

        Route::patch('pengelola/orders/{order}/process', [PengelolaOrderController::class, 'process'])
            ->name('pengelola.orders.process');

        Route::patch('pengelola/orders/{order}/complete', [PengelolaOrderController::class, 'complete'])
            ->name('pengelola.orders.complete');

        // UPDATE STATUS ORDER ITEM PER TENANT
        Route::patch('pengelola/order-items/{orderItem}/status', [TenantOrderController::class, 'updateStatus'])
            ->name('pengelola.order-items.update-status');

        Route::get('pengelola/order-items', [TenantOrderController::class, 'index'])
            ->name('pengelola.order-items.index');

        // REPORT
        Route::get('/pengelola/report', [LaporanFavoritController::class, 'index'])
            ->name('pengelola.report');

        Route::get('/pengelola/report/export', [LaporanFavoritController::class, 'exportExcel'])
            ->name('pengelola.report.export');

        // DELIVERY
        Route::get('/pengelola/delivery', [DeliveryController::class, 'index'])
            ->name('pengelola.delivery');

        Route::patch('/pengelola/delivery/{delivery}/send', [DeliveryController::class, 'send'])
            ->name('pengelola.delivery.send');

        Route::patch('/pengelola/delivery/{delivery}/complete', [DeliveryController::class, 'complete'])
            ->name('pengelola.delivery.complete');

        Route::patch('/pengelola/delivery/{delivery}/cooked', [DeliveryController::class, 'cooked'])
            ->name('pengelola.delivery.cooked');

        Route::get('/pengelola/delivery/display', [DeliveryController::class, 'display'])
            ->name('pengelola.delivery.display');

        // NOTIFICATIONS
        Route::get('/pengelola/notifications', [NotificationController::class, 'index'])
            ->name('pengelola.notifications');
        Route::patch('/pengelola/notifications/{notification}/read', [NotificationController::class, 'markRead'])
            ->name('pengelola.notifications.read');
        Route::post('/pengelola/notifications/read-all', [NotificationController::class, 'markAllRead'])
            ->name('pengelola.notifications.read-all');
        Route::delete('/pengelola/notifications/{notification}', [NotificationController::class, 'destroy'])
            ->name('pengelola.notifications.destroy');
        Route::get('/pengelola/notifications/count', [NotificationController::class, 'unreadCount'])
            ->name('pengelola.notifications.count');
        Route::get('/pengelola/notifications/{notification}/open', [NotificationController::class, 'open'])
        ->name('pengelola.notifications.open');
        Route::get('/pengelola/orders/badge-count', [OrderController::class, 'badgeCount']);
    });

// ── PUBLIC (tanpa auth) ──────────────────────────────
Route::get('/menu', [MenuController::class, 'customerMenu'])
    ->name('customer.menu');
Route::get('/cart/data', [CartController::class, 'data'])->name('cart.data');

//CUSTOMER
Route::middleware(['auth', 'role:customer'])
    ->group(function () {
        Route::get('/payment', [PaymentController::class, 'index'])->name('customer.payment');
        Route::post('/payment/upload', [PaymentController::class, 'upload'])->name('customer.payment.upload');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('/home',    [HomeController::class, 'index'])->name('customer.home');
    Route::get('/history', [HistoryController::class, 'index'])->name('customer.history');

    // Cart — dipakai kasir & customer
    Route::get('/cart',                         [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add',                    [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cartItem}',       [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}',    [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update-ajax/{cartItem}', [CartController::class, 'updateAjax'])->name('cart.update-ajax');
    Route::post('/cart/clear',                  [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/remove-ajax/{cartItem}', [CartController::class, 'removeAjax'])->name('cart.remove-ajax');

    // Order — dipakai kasir & customer
    Route::get('/order',          [OrderController::class, 'index'])->name('customer.order');
    Route::post('/order/confirm', [OrderController::class, 'confirm'])->name('customer.order.confirm');

    // Invoice
    Route::get('/invoice',               [InvoiceController::class, 'latest'])->name('customer.invoice');
    Route::get('/invoice/{orderNumber}', [InvoiceController::class, 'show'])->name('customer.invoice.show');

    Route::get('/profile/edit',     [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update',   [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/dashboard', [KasirController::class, 'index'])
        ->name('kasir.dashboard');

    Route::post('/kasir/order', [KasirController::class, 'store'])
        ->name('kasir.order.store');

    Route::get('/kasir/history', [KasirController::class, 'history'])
        ->name('kasir.history');

    Route::get('/kasir/payment/{order:order_number}', [KasirController::class, 'showPayment'])
        ->name('kasir.payment');

    Route::post('/kasir/payment/{order:order_number}/confirm', [KasirController::class, 'confirmPayment'])
        ->name('kasir.payment.confirm');
});


Route::post('/telegram/webhook', [TelegramController::class, 'handle']);

Route::post('/password/resend', [PasswordResetLinkController::class, 'resend'])
    ->name('password.resend');

Route::post('/password/update', [NewPasswordController::class, 'store'])
    ->name('password.update');
require __DIR__ . '/auth.php';
