<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
{
    $tenant = auth()->user()->tenant;
    abort_if(!$tenant, 403, 'Akun ini tidak memiliki tenant.');

    $period = request('period', 'bulan');

    $dateRange = match($period) {
        'hari'   => [now()->startOfDay(),   now()->endOfDay()],
        'minggu' => [now()->startOfWeek(),  now()->endOfWeek()],
        default  => [now()->startOfMonth(), now()->endOfMonth()], // 'bulan'
    };

    $periodLabel = match($period) {
        'hari'   => 'Hari ini, ' . now()->translatedFormat('d F Y'),
        'minggu' => now()->startOfWeek()->translatedFormat('d M') . ' – ' . now()->endOfWeek()->translatedFormat('d M Y'),
        default  => now()->translatedFormat('F Y'),
    };

    // ✅ Filter by tenant + periode
    $totalTransactions = Order::whereHas('items.menu', fn($q) =>
            $q->where('tenant_id', $tenant->id))
        ->whereBetween('created_at', $dateRange)
        ->count();

    $totalRevenue = OrderItem::whereHas('menu', fn($q) =>
            $q->where('tenant_id', $tenant->id))
        ->whereBetween('created_at', $dateRange)
        ->sum('subtotal');

    // Top Menus
    $topMenus = OrderItem::select('menu_id',
            DB::raw('SUM(quantity) as sold'),
            DB::raw('SUM(subtotal) as revenue'))
        ->with('menu.category')
        ->whereHas('menu', fn($q) => $q->where('tenant_id', $tenant->id))
        ->whereBetween('created_at', $dateRange)
        ->groupBy('menu_id')
        ->orderByDesc('sold')
        ->take(5)
        ->get();

    $bestMenu = $topMenus->first()?->menu;
    if ($bestMenu) $bestMenu->total_sold = $topMenus->first()->sold;

    // Tambah metadata untuk blade
    $emojis = ['🥇','🥈','🥉','🍽️','🍴'];
    $bars   = ['bg-amber-500','bg-forest-500','bg-teal-500','bg-blue-400','bg-red-400'];
    $maxSold = $topMenus->max('sold') ?: 1;

    $topMenus = $topMenus->map(function ($m, $i) use ($emojis, $bars, $maxSold) {
        $m['r']   = $i + 1;
        $m['e']   = $emojis[$i] ?? '🍽️';
        $m['bar'] = $bars[$i] ?? 'bg-gray-400';
        $m['pct'] = round(($m->sold / $maxSold) * 100);
        $m['sold'] = $m->sold;
        $m['rev']  = $m->revenue;
        return $m;
    });

    return view('pengelola.report', compact(
        'totalTransactions',
        'totalRevenue',
        'activeMenus',
        'bestMenu',
        'topMenus',
        'period',
        'periodLabel'
    ));
}
}