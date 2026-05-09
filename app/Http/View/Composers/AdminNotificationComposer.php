<?php

namespace App\Http\View\Composers;

use App\Models\AdminNotification;
use Illuminate\View\View;

class AdminNotificationComposer
{
    public function compose(View $view): void
    {
        $view->with('adminUnreadCount', AdminNotification::whereNull('read_at')->count());
    }
}