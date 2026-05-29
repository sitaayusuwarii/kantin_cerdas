<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Delivery;

class PengelolaOrderController extends Controller
{
    const VISIBLE_STATUSES = [
        'baru',
        'pembayaran_terverifikasi', // data lama
        'dikonfirmasi',
        'diproses',
        'selesai_dimasak',
        'dikirim',
        'selesai',
    ];

    public function index()
    {
        $orders = Order::with(['user', 'items.menu.category'])
            ->whereIn('status', self::VISIBLE_STATUSES)
            ->today()
            ->latest()
            ->get();

        $totalOrders     = $orders->count();
        $newOrders       = $orders->whereIn('status', ['baru', 'pembayaran_terverifikasi'])->count();
        $confirmedOrders = $orders->where('status', 'dikonfirmasi')->count();
        $processedOrders = $orders->where('status', 'diproses')->count();
        $readyOrders     = $orders->where('status', 'selesai_dimasak')->count();
        $completedOrders = $orders->where('status', 'selesai')->count();

        return view('pengelola.orders', compact(
            'orders',
            'totalOrders',
            'newOrders',
            'confirmedOrders',
            'processedOrders',
            'readyOrders',
            'completedOrders'
        ));
    }

    public function confirm(Order $order)
    {
        abort_if(
            !in_array($order->status, ['baru', 'pembayaran_terverifikasi']),
            403, 'Pesanan tidak valid untuk dikonfirmasi.'
        );

        $order->update([
            'status'       => 'dikonfirmasi',
            'confirmed_at' => now(),
        ]);

        $chatId = $order->user->telegram_chat_id ?? null;
        if ($chatId) {
            (new TelegramController)->sendMessage(
                $chatId,
                "✅ *Pesanan dikonfirmasi!*\n\nPesanan #{$order->order_number} sudah diterima kantin."
            );
        }

        return back()->with('success', 'Pesanan dikonfirmasi.');
    }

    public function process(Order $order)
    {
        abort_if($order->status !== 'dikonfirmasi', 403, 'Pesanan belum dikonfirmasi.');

        $order->update([
            'status'       => 'diproses',
            'processed_at' => now(),
        ]);

        // Buat delivery record di sini — saat masuk dapur
        Delivery::create([
            'order_id'     => $order->id,
            'status'       => 'diproses',
            'processed_at' => now(),
        ]);

        $chatId = $order->user->telegram_chat_id ?? null;
        if ($chatId) {
            (new TelegramController)->sendMessage(
                $chatId,
                "👨‍🍳 *Pesanan sedang dimasak!*\n\nPesanan #{$order->order_number} sedang disiapkan dapur."
            );
        }

        return back()->with('success', 'Pesanan sedang diproses.');
    }

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