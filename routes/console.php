<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\AutoCancelExpiredOrders;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reminder:payment')->everyThirtyMinutes();
Schedule::command(AutoCancelExpiredOrders::class)->everyFiveMinutes();