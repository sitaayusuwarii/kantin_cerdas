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

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/admin/kelola-user', function () {
            return view('admin.kelola-user');
        })->name('admin.kelola-user');

        Route::get('/admin/report', function () {
            return view('admin.report');
        })->name('admin.report');

        Route::get('/transactions',              [TransactionController::class, 'index'])->name('admin.transactions');
        Route::get('/transactions/{payment}/detail', [TransactionController::class, 'detail'])->name('admin.transactions.detail');
        Route::get('/transactions/export',       [TransactionController::class, 'export'])->name('admin.transactions.export');

        Route::get('/admin/verification', [VerificationController::class, 'index'])->name('admin.verification');

        // Kelola User
        Route::get('/users',                  [UserController::class, 'index'])->name('users.index');
        Route::post('/users',                 [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',           [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/suspend', [UserController::class, 'toggleSuspend'])->name('users.suspend');
        Route::delete('/users/{user}',        [UserController::class, 'destroy'])->name('users.destroy');
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
});

Route::middleware(['auth', 'role:customer'])
    ->group(function () {

       Route::get('/home', [HomeController::class, 'index'])->name('customer.home');

        //ORDER
        Route::get('/order',          [OrderController::class, 'index'])->name('customer.order');
        Route::post('/order/confirm', [OrderController::class, 'confirm'])->name('customer.order.confirm');
        
        Route::get('/payment', function () {
            return view('customer.payment');
        })->name('customer.payment');

        Route::get('/invoice',          [InvoiceController::class, 'latest'])->name('customer.invoice');
        Route::get('/invoice/{orderNumber}', [InvoiceController::class, 'show'])->name('customer.invoice.show');

        Route::get('/menu', [MenuController::class, 'customerMenu'])
        ->name('customer.menu');

        Route::get('/history', [HistoryController::class, 'index'])->name('customer.history');

        Route::get('/cart',                         [CartController::class, 'index'])->name('cart');
        Route::post('/cart/add',                    [CartController::class, 'add'])->name('cart.add');
        Route::put('/cart/update/{cartItem}',       [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/remove/{cartItem}',    [CartController::class, 'remove'])->name('cart.remove');
        Route::get('/cart/data',                    [CartController::class, 'data'])->name('cart.data');
        Route::post('/cart/update-ajax/{cartItem}', [CartController::class, 'updateAjax'])->name('cart.update-ajax');
        Route::post('/cart/clear',                  [CartController::class, 'clear'])->name('cart.clear');
        Route::post('/cart/remove-ajax/{cartItem}', [CartController::class, 'removeAjax'])->name('cart.remove-ajax');
        Route::post('/cart/remove-ajax/{id}', [CartController::class, 'removeAjax']);

    });

    Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit',     [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('/profile/update',   [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])
        ->name('profile.photo.delete');
});


Route::post('/password/resend', [PasswordResetLinkController::class, 'resend'])
    ->name('password.resend');

Route::post('/password/update', [NewPasswordController::class, 'store'])
    ->name('password.update');
require __DIR__.'/auth.php';
