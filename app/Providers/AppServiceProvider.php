<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Payment;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $view->with('pendingPaymentCount', Payment::where('status', 'menunggu')->count());
            $view->with('adminUnreadCount', AdminNotification::whereNull('read_at')->count());
        });

        
    }
}