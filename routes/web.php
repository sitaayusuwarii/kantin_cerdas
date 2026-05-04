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

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::get('/admin/kelola-user', function () {
            return view('admin.kelola-user');
        })->name('admin.kelola-user');

        Route::get('/admin/report', function () {
            return view('admin.report');
        })->name('admin.report');

        Route::get('/admin/transactions', function () {
            return view('admin.transactions');
        })->name('admin.transactions');

        Route::get('/admin/verification', function () {
            return view('admin.verification');
        })->name('admin.verification');
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

        Route::get('/home', function () {
            return view('customer.home');
        })->name('customer.home');

        Route::get('/order', function () {
            return view('customer.order');
        })->name('customer.order');
        
        Route::get('/payment', function () {
            return view('customer.payment');
        })->name('customer.payment');

        Route::get('/invoice', function () {
            return view('customer.invoice');
        })->name('customer.invoice');

        Route::get('/menu', [MenuController::class, 'customerMenu'])
        ->name('customer.menu');

        Route::get('/history', function () {
            return view('customer.history');
        })->name('customer.history');

        Route::get('/cart', function () {
            return view('customer.cart');
        })->name('customer.cart');

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
