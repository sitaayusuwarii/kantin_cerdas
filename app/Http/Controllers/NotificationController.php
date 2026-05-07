<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        // Tandai semua sebagai dibaca saat halaman dibuka
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('pengelola.notifications', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        $notification->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back();
    }

    // Untuk badge count di topbar (AJAX)
    public function unreadCount()
    {
        return response()->json([
            'count' => Notification::where('user_id', auth()->id())
                        ->unread()->count()
        ]);
    }
}