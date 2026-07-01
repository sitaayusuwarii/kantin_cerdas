<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;

class DeliveryController extends Controller
{
   public function index()
{
    $tenant = auth()->user()->tenant;
    abort_if(!$tenant, 403, 'Akun ini tidak memiliki tenant.');

    $period  = request('period', 'today');
    $dateFrom = request('date_from');
    $dateTo   = request('date_to');

    $query = Delivery::with(['order.user', 'order.items.menu'])
        ->whereIn('status', ['diproses', 'selesai_dimasak', 'dikirim', 'selesai'])
        ->whereHas('order.items', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        });

    // Filter tanggal custom override period
    if ($dateFrom && $dateTo) {
        $query->whereDate('created_at', '>=', $dateFrom)
              ->whereDate('created_at', '<=', $dateTo);
    } else {
        match($period) {
            'week'  => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'year'  => $query->whereYear('created_at', now()->year),
            default => $query->whereDate('created_at', today()), // 'today'
        };
    }

    $deliveries = $query->latest()->get();

    return view('pengelola.delivery', compact('deliveries', 'period', 'dateFrom', 'dateTo'));
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

    //  Delivery wajib lewat 'dikirim' dulu
    // Takeaway & dine-in boleh langsung dari 'selesai_dimasak'
    $allowedStatus = $delivery->order->isDelivery()
        ? ['dikirim']
        : ['selesai_dimasak', 'dikirim'];

    abort_if(
        !in_array($delivery->status, $allowedStatus),
        403, 'Pesanan belum dikirim.'
    );

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

    abort_if(
        !in_array($order->status, [Order::STATUS_DIPROSES, Order::STATUS_DIKONFIRMASI]),
        403, 'Status tidak valid.'
    );

    // Tentukan next status berdasarkan order_type
   $next = Order::STATUS_SELESAI_DIMASAK;
   
    $delivery->update([
        'status'    => $next,
        'cooked_at' => now(),
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
    $tenant = auth()->user()->tenant;

    abort_if(!$tenant, 403, 'Akun ini tidak memiliki tenant.');

    $deliveries = Delivery::with(['order.user', 'order.items.menu'])
        ->whereIn('status', ['diproses', 'selesai_dimasak', 'dikirim', 'selesai'])
        ->whereHas('order.items', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })
        ->whereDate('created_at', today())
        ->latest()
        ->get();

    return view('pengelola.delivery-display', compact('deliveries'));
}
    
}