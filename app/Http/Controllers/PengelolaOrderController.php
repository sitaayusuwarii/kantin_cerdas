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

   public function index(Request $request)
{
    $tenant = $this->getTenant();

    $from = $request->filled('from')
        ? \Carbon\Carbon::parse($request->query('from'))->startOfDay()
        : today()->startOfDay();

    $to = $request->filled('to')
        ? \Carbon\Carbon::parse($request->query('to'))->endOfDay()
        : today()->endOfDay();

    $status = $request->query('status', 'semua');

    // Query dasar, dipakai ulang untuk counting & listing
    $baseQuery = Order::whereHas('items', fn($q) => $q->where('tenant_id', $tenant->id))
                ->whereIn('status', self::VISIBLE_STATUSES)
                ->whereBetween('created_at', [$from, $to]);

    // Hitung badge count via SQL aggregate — TIDAK load semua row ke PHP
    $counts = (clone $baseQuery)
        ->selectRaw('status, count(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

    $totalOrders     = $counts->sum();
    $newOrders       = $counts->only(['baru', 'pembayaran_terverifikasi'])->sum();
    $confirmedOrders = $counts->get('dikonfirmasi', 0);
    $processedOrders = $counts->get('diproses', 0);
    $readyOrders     = $counts->get('selesai_dimasak', 0);
    $completedOrders = $counts->get('selesai', 0);

    // Query listing: filter status di server + pagination
    $listQuery = (clone $baseQuery)->with([
        'user',
        'items' => fn($q) => $q->where('tenant_id', $tenant->id)->with('menu.category'),
    ]);

    if ($status !== 'semua') {
        $status === 'baru'
            ? $listQuery->whereIn('status', ['baru', 'pembayaran_terverifikasi'])
            : $listQuery->where('status', $status);
    }

    $orders = $listQuery->latest()->paginate(20)->withQueryString();

    return view('pengelola.orders', compact(
        'orders', 'tenant',
        'totalOrders', 'newOrders', 'confirmedOrders',
        'processedOrders', 'readyOrders', 'completedOrders'
    ) + [
        'filterFrom'   => $from->format('Y-m-d'),
        'filterTo'     => $to->format('Y-m-d'),
        'activeStatus' => $status,
    ]);
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