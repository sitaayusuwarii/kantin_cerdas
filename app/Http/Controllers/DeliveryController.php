<?php

namespace App\Http\Controllers;

use App\Models\Delivery;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with([
                'order.user',
                'order.items.menu'
            ])
            // ↓ hanya tampilkan yang sudah masuk pipeline pengiriman
            ->whereIn('status', ['diproses', 'dikirim', 'selesai'])
            // ↓ yang paling lama menunggu muncul paling atas
            ->oldest()
            ->get();

        return view('pengelola.delivery', compact('deliveries'));
    }

    public function send(Delivery $delivery)
{
    $delivery->load('order.user'); 

    abort_if($delivery->status !== 'diproses', 403, 'Status pengiriman tidak valid.');

    $delivery->update([
        'status'  => 'dikirim',
        'sent_at' => now(),
    ]);

    // Notifikasi Telegram
    $chatId = $delivery->order->user->telegram_chat_id ?? null;
    if ($chatId) {
        (new \App\Http\Controllers\TelegramController)->sendMessage(
            $chatId,
            "🛵 *Pesanan kamu sedang diantar!*\n\nPesanan #{$delivery->order->order_number} sedang dalam perjalanan.\nSiapkan diri untuk menerima pesanan ya! 😊"
        );
    }

    return back()->with('success', 'Pesanan sedang dikirim.');
}

public function complete(Delivery $delivery)
{
    $delivery->load('order.user');
    
    abort_if($delivery->status !== 'dikirim', 403, 'Pesanan belum dikirim.');

    $delivery->update([
        'status'       => 'selesai',
        'completed_at' => now(),
    ]);

    $delivery->order->update([
        'status'       => 'selesai',
        'completed_at' => now(),
    ]);

    // Notifikasi Telegram
    $chatId = $delivery->order->user->telegram_chat_id ?? null;
    if ($chatId) {
        (new \App\Http\Controllers\TelegramController)->sendMessage(
            $chatId,
            "✅ *Pesanan kamu telah selesai!*\n\nPesanan #{$delivery->order->order_number} sudah diterima.\nTerima kasih sudah memesan di Kantin! 🍽"
        );
    }

    return back()->with('success', 'Pesanan selesai.');
}

    
}