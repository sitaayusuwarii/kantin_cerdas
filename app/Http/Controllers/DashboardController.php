<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Statistik pesanan ─────────────────────────────
        $orderStats = Order::forUser($user->id)
                           ->selectRaw("status, count(*) as total")
                           ->groupBy('status')
                           ->pluck('total', 'status');

        // ── Pesanan aktif (sedang berjalan) ───────────────
        $activeOrder = Order::with('items.menu')
                            ->forUser($user->id)
                            ->active()
                            ->latest()
                            ->first();

        // ── Tagihan belum lunas ────────────────────────────
        $unpaidOrder = Order::with('items')
                            ->forUser($user->id)
                            ->whereIn('status', ['selesai', 'dikonfirmasi', 'diproses'])
                            ->whereDoesntHave('payments', fn($q) =>
                                $q->where('status', 'terverifikasi')
                            )
                            ->latest()
                            ->first();

        // ── Top 3 menu favorit ────────────────────────────
        $favoriteMenus = Menu::featured()
                             ->with('category')
                             ->orderByDesc('total_sold')
                             ->limit(3)
                             ->get();

        return view('customer.home', compact(
            'user',
            'orderStats',
            'activeOrder',
            'unpaidOrder',
            'favoriteMenus',
        ));
    }
}
