<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // ── STAT CARDS ────────────────────────────────────────
        $totalToday     = Order::whereDate('created_at', $today)->count();
        $totalYesterday = Order::whereDate('created_at', $yesterday)->count();
        $diffOrders     = $totalToday - $totalYesterday;

        $diproses = Order::whereDate('created_at', $today)
            ->where('status', 'diproses')
            ->count();

        $selesai = Order::whereDate('created_at', $today)
            ->where('status', 'selesai')
            ->count();

        $completionRate = $totalToday > 0
            ? round(($selesai / $totalToday) * 100)
            : 0;

        $pendapatanHariIni = OrderItem::whereHas('order', fn($q) => $q
            ->whereDate('created_at', $today)
            ->where('status', 'selesai')
        )->sum('subtotal');

        // ── RECENT ORDERS ─────────────────────────────────────
        $recentOrders = Order::with(['user', 'orderItems.menu'])
            ->whereDate('created_at', $today)
            ->latest()
            ->limit(4)
            ->get();

        // ── TOP MENUS HARI INI ────────────────────────────────
        $topMenusRaw = OrderItem::with('menu')
            ->whereHas('order', fn($q) => $q
                ->whereDate('created_at', $today)
                ->where('status', 'selesai')
            )
            ->selectRaw('menu_id, SUM(quantity) as sold')
            ->groupBy('menu_id')
            ->orderByDesc('sold')
            ->limit(3)
            ->get();

        $maxSold   = $topMenusRaw->max('sold') ?: 1;
        $emojis    = ['🍛', '🍜', '🧋'];
        $barColors = ['bg-forest-500', 'bg-forest-400', 'bg-forest-300'];

        $topMenus = $topMenusRaw->map(function ($m, $i) use ($maxSold, $emojis, $barColors) {
            return [
                'rank'     => $i + 1,
                'emoji'    => $emojis[$i] ?? '🍽️',
                'name'     => $m->menu->name ?? '-',
                'sold'     => $m->sold,
                'pct'      => (int) round(($m->sold / $maxSold) * 100),
                'barColor' => $barColors[$i] ?? 'bg-forest-200',
            ];
        });

        // ── PESANAN PER JAM (07:00 - 18:00) ──────────────────
        $hourlyBars = collect(range(7, 18))->map(function ($hour) use ($today) {
            return Order::whereDate('created_at', $today)
                ->whereRaw('EXTRACT(HOUR FROM created_at) = ?', [$hour])
                ->count();
        })->toArray();

        // ── QUICK ACTION BADGES ───────────────────────────────
        $badgePesanan   = Order::where('status', 'baru')->count();
        $badgePengiriman = \App\Models\Delivery::where('status', 'pending')->count();

        return view('pengelola.dashboard', compact(
            'totalToday', 'totalYesterday', 'diffOrders',
            'diproses', 'selesai', 'completionRate',
            'pendapatanHariIni',
            'recentOrders',
            'topMenus',
            'hourlyBars',
            'badgePesanan', 'badgePengiriman'
        ));
    }
}