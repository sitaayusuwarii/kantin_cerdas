<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Delivery; 

class PengelolaOrderController extends Controller
{
    // Status yang relevan untuk pengelola kantin
    // (hanya setelah admin verifikasi pembayaran)
    const VISIBLE_STATUSES = [
        'pembayaran_terverifikasi',
        'dikonfirmasi',
        'diproses',
        'selesai',
    ];

    public function index()
    {
        $orders = Order::with([
                'user',
                'items.menu.category'
            ])
            ->whereIn('status', self::VISIBLE_STATUSES) // ← hanya order terverifikasi
            ->latest()
            ->get();

        // Stats hanya untuk order yang sudah masuk ke pengelola
        $totalOrders      = Order::whereIn('status', self::VISIBLE_STATUSES)->count();
        $newOrders = Order::where('status', 'pembayaran_terverifikasi')->count();
        $confirmedOrders  = Order::where('status', 'dikonfirmasi')->count();
        $processedOrders  = Order::where('status', 'diproses')->count();
        $completedOrders  = Order::where('status', 'selesai')->count();

        return view('pengelola.orders', compact(
            'orders',
            'totalOrders',
            'newOrders',
            'confirmedOrders',
            'processedOrders',
            'completedOrders'
        ));
    }

    // Pengelola konfirmasi → mulai diproses dapur
   public function confirm(Order $order)
{
    abort_if($order->status !== 'pembayaran_terverifikasi', 403, 'Pesanan tidak valid untuk dikonfirmasi.');

    $order->update([
        'status'       => 'dikonfirmasi',
        'confirmed_at' => now(),
    ]);

    Delivery::create([
        'order_id'     => $order->id,
        'status'       => 'diproses',
        'processed_at' => now(),
    ]);

    // Kirim notif ke customer via Telegram
    $chatId = $order->user->telegram_chat_id;
    if ($chatId) {
        (new \App\Http\Controllers\TelegramController)->sendMessage(
            $chatId,
            "👨‍🍳 *Pesanan kamu sedang diproses!*\n\nPesanan #{$order->order_number} sudah diterima kantin dan sedang disiapkan."
        );
    }

    return back()->with('success', 'Pesanan dikonfirmasi dan masuk antrian pengiriman.');
}

    // Pengelola mulai memproses (masak/siapkan)
    public function process(Order $order)
    {
        abort_if($order->status !== 'dikonfirmasi', 403, 'Pesanan belum dikonfirmasi.');

        $order->update([
            'status'       => 'diproses',
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Pesanan sedang diproses.');
    }

    // Pengelola tandai selesai
    public function complete(Order $order)
    {
        abort_if($order->status !== 'diproses', 403, 'Pesanan belum diproses.');

        $order->update([
            'status'       => 'selesai',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Pesanan selesai.');
    }
}