<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = AdminNotification::latest()
            ->paginate(20);

        // Mark semua yang dibuka sebagai read
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        $unreadCount = 0; // sudah di-read semua

        return view('admin.notifications', compact('notifications', 'unreadCount'));
    }

    public function markRead(AdminNotification $notification)
    {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    // Endpoint untuk dapat unread count (polling)
    public function unreadCount()
    {
        return response()->json([
            'count' => AdminNotification::whereNull('read_at')->count()
        ]);
    }
}