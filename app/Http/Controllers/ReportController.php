<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        abort_if(!$tenant, 403, 'Akun ini tidak memiliki tenant.');


        $period = request('period', 'bulan');

        // ── Tentukan rentang tanggal ──────────────────────────
        if ($period === 'custom') {
            $rawFrom = request('date_from');
            $rawTo   = request('date_to');

            // Fallback ke bulan berjalan kalau input kosong/invalid
            try {
                $dateFrom = $rawFrom ? Carbon::parse($rawFrom)->startOfDay() : now()->startOfMonth();
            } catch (\Exception $e) {
                $dateFrom = now()->startOfMonth();
            }

            try {
                $dateTo = $rawTo ? Carbon::parse($rawTo)->endOfDay() : now()->endOfDay();
            } catch (\Exception $e) {
                $dateTo = now()->endOfDay();
            }

            // Jangan biarkan "sampai" lebih kecil dari "dari"
            if ($dateTo->lt($dateFrom)) {
                [$dateFrom, $dateTo] = [$dateTo->copy()->startOfDay(), $dateFrom->copy()->endOfDay()];
            }

            // Jangan biarkan rentang tembus ke masa depan
            if ($dateTo->gt(now()->endOfDay())) {
                $dateTo = now()->endOfDay();
            }

            $dateRange = [$dateFrom, $dateTo];

            $periodLabel = $dateFrom->isSameDay($dateTo)
                ? $dateFrom->translatedFormat('d F Y')
                : $dateFrom->translatedFormat('d M Y') . ' – ' . $dateTo->translatedFormat('d M Y');
        } else {
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

            // Untuk mengisi ulang form kalau user balik ke custom
            $dateFrom = $dateRange[0]->format('Y-m-d');
            $dateTo   = $dateRange[1]->format('Y-m-d');
        }

        // ✅ Filter by tenant + periode
        $totalTransactions = Order::whereHas('items', fn($q) =>
        $q->where('tenant_id', $tenant->id))
            ->where('status', 'selesai')
            ->whereBetween('orders.created_at', $dateRange)
            ->count();

        $totalRevenue = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
    ->where('order_items.tenant_id', $tenant->id)
    ->where('orders.status', 'selesai')
    ->whereBetween('orders.created_at', $dateRange)
    ->sum('order_items.subtotal');

        // Menu aktif milik tenant ini
        $activeMenus = Menu::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->count();

        // Top Menus
        $topMenus = OrderItem::select(
        'order_items.menu_id',
        DB::raw('SUM(order_items.quantity) as sold'),
        DB::raw('SUM(order_items.subtotal) as revenue')
    )
        ->join('orders', 'orders.id', '=', 'order_items.order_id')
        ->with('menu.category')
        ->where('order_items.tenant_id', $tenant->id)
        ->where('orders.status', 'selesai')
        ->whereBetween('orders.created_at', $dateRange)
        ->groupBy('order_items.menu_id')
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
            'periodLabel',
            'dateFrom',
            'dateTo'
        ));
    }
}