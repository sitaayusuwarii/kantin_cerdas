<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index()
    {
        // Tandai semua sebagai dibaca saat halaman dibuka → badge lonceng jadi 0
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        $notifications = AdminNotification::latest()->paginate(20);
        $unreadCount   = 0; // sudah semua dibaca

        return view('admin.notifications', compact('notifications', 'unreadCount'));
    }

    public function markRead(AdminNotification $notification)
    {
        // Hapus notif & redirect ke halaman yang sesuai
        $type = $notification->type;
        $notification->delete();

        $redirect = match($type) {
            'payment_new' => route('admin.verification'),
            default       => route('admin.notifications'),
        };

        return redirect($redirect);
    }

    public function markAllRead()
    {
        AdminNotification::query()->delete();
        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => AdminNotification::whereNull('read_at')->count()
        ]);
    }
}