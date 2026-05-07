<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Payment;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    // Share jumlah payment menunggu ke semua view
    View::composer('layouts.admin', function ($view) {
        $view->with('pendingPaymentCount', Payment::where('status', 'menunggu')->count());
    });
}
}
