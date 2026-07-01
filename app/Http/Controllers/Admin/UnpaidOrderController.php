<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UnpaidOrderController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', today()->toDateString());

        $unpaidOrders = Order::with(['user', 'items.menu'])
            ->where('status', 'baru')
            ->where('payment_status', 'pending')
            ->whereNull('payment_proof')
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $summary = [
            'total'       => $unpaidOrders->count(),
            'total_nilai' => $unpaidOrders->sum('total_price'),
        ];

        return view('admin.unpaid-orders', compact('unpaidOrders', 'summary', 'date'));
    }

    // Admin bisa cancel manual
   public function cancel(Order $order, Request $request)
{
    $order->update([
        'status'       => 'dibatalkan',
        'cancelled_at' => now(),
    ]);

    $alasan = $request->cancel_reason ?? 'Pembayaran tidak diterima.';

    $chatId = $order->user->telegram_chat_id ?? null;
    if ($chatId) {
        (new \App\Http\Controllers\TelegramController)->sendMessage(
            $chatId,
            "❌ *Pesanan kamu dibatalkan oleh admin.*\n\n" .
            "Pesanan *#{$order->order_number}* dibatalkan.\n" .
            "Alasan: {$alasan}\n\n" .
            "Hubungi kantin jika ada pertanyaan."
        );
    }

    return back()->with('success', 'Pesanan berhasil dibatalkan.');
}

    // Admin kirim reminder manual ke 1 customer
    public function sendReminder(Order $order)
    {
        $chatId = $order->user->telegram_chat_id ?? null;

        if (!$chatId) {
            return back()->with('error', 'Customer belum menghubungkan Telegram.');
        }

        (new \App\Http\Controllers\TelegramController)->sendMessage(
            $chatId,
            "⏰ *Pengingat dari Admin Kantin*\n\n" .
            "Pesanan *#{$order->order_number}* senilai *Rp " . number_format($order->total_price, 0, ',', '.') . "* belum dibayar.\n\n" .
            "Segera selesaikan pembayaran sebelum kantin tutup jam 13:00! 🙏"
        );

        return back()->with('success', 'Reminder berhasil dikirim ke ' . $order->user->name);
    }


    }