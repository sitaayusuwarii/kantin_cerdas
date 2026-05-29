<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with([
                'order.user',
                'order.items.menu'
            ])
            // ↓ hanya tampilkan yang sudah masuk pipeline pengiriman
            ->whereIn('status', ['diproses', 'selesai_dimasak', 'dikirim', 'selesai'])
            // ↓ yang paling lama menunggu muncul paling atas
            ->latest()
            ->get();

        return view('pengelola.delivery', compact('deliveries'));
    }

    public function send(Delivery $delivery)
{
    $delivery->load('order.user'); 

    abort_if($delivery->status !== 'selesai_dimasak', 403, 'Status pengiriman tidak valid.');

    $delivery->update([
        'status'  => 'dikirim',
        'sent_at' => now(),
    ]);

    $delivery->order->update(['status' => 'dikirim']);

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

public function cooked(Delivery $delivery): RedirectResponse
{
    $delivery->load('order');
    $order = $delivery->order;

    // Pastikan order statusnya diproses dulu sebelum nextStatus() dipanggil
    abort_if(
    !in_array($order->status, [Order::STATUS_DIPROSES, Order::STATUS_DIKONFIRMASI]),
    403, 'Status tidak valid.'
);

    $next = $order->nextStatus(); // 'selesai_dimasak'

    $delivery->update([
        'status'    => $next,
        'cooked_at' => now(),   // pastikan kolom ini ada, atau hapus baris ini
    ]);

    $order->update([
        'status'       => $next,
        'completed_at' => $next === Order::STATUS_SELESAI ? now() : null,
    ]);

    $msg = $next === Order::STATUS_SELESAI
        ? 'Pesanan langsung selesai!'
        : 'Pesanan siap dikirim!';

    return back()->with('success', $msg);
}

public function display()
{
    $deliveries = Delivery::with(['order.user', 'order.items.menu'])
        ->whereIn('status', ['diproses', 'selesai_dimasak', 'dikirim', 'selesai'])
        ->latest()
        ->get();

    return view('pengelola.delivery-display', compact('deliveries'));
}
    
}