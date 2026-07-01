<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Delivery;

class PengelolaOrderController extends Controller
{
    const VISIBLE_STATUSES = [
    'pembayaran_terverifikasi',
    'dikonfirmasi',
    'diproses',
    'selesai_dimasak',
    'dikirim',
    'selesai',
];

    // Ambil tenant milik pengelola yang login
    private function getTenant()
    {
        return Tenant::where('user_id', Auth::id())->firstOrFail();
    }

    public function index()
    {
        $tenant = $this->getTenant();

        // Hanya order yang mengandung item milik tenant ini
        $orders = Order::with([
                        'user',
                        'items' => fn($q) => $q->where('tenant_id', $tenant->id)
                                              ->with('menu.category'),
                    ])
                    ->whereHas('items', fn($q) => $q->where('tenant_id', $tenant->id))
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
            'tenant',
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
        $tenant = $this->getTenant();

        // Pastikan order ini ada item milik tenant ini
        abort_if(
            !$order->items()->where('tenant_id', $tenant->id)->exists(),
            403, 'Akses ditolak.'
        );

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
        $tenant = $this->getTenant();

        abort_if(
            !$order->items()->where('tenant_id', $tenant->id)->exists(),
            403, 'Akses ditolak.'
        );

        abort_if($order->status !== 'dikonfirmasi', 403, 'Pesanan belum dikonfirmasi.');

        $order->update([
            'status'       => 'diproses',
            'processed_at' => now(),
        ]);

        // Update tenant_status item milik tenant ini → diproses
        $order->items()
              ->where('tenant_id', $tenant->id)
              ->update(['tenant_status' => 'diproses']);

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
        $tenant = $this->getTenant();

        abort_if(
            !$order->items()->where('tenant_id', $tenant->id)->exists(),
            403, 'Akses ditolak.'
        );

        abort_if($order->status !== 'diproses', 403, 'Pesanan belum diproses.');

        // Update tenant_status item milik tenant ini → selesai_dimasak
        $order->items()
              ->where('tenant_id', $tenant->id)
              ->update(['tenant_status' => 'selesai_dimasak']);

        // Cek apakah SEMUA item di order sudah selesai_dimasak
        $allDone = $order->items()
                         ->where('tenant_status', '!=', 'selesai_dimasak')
                         ->doesntExist();

        // Jika semua tenant sudah selesai masak, update status order
        if ($allDone) {
            $order->update([
                'status'       => 'selesai_dimasak',
                'completed_at' => now(),
            ]);
        }

        return back()->with('success', 'Pesanan selesai dimasak.');
    }
}