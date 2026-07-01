<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use App\Models\DailyTarget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today     = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $thisWeek  = Carbon::now()->startOfWeek();

        // ── Stat Cards ───────────────────────────────────────────────────

        // Pemasukan hari ini (payment terverifikasi)
        $incomeToday = Payment::where('status', 'terverifikasi')
            ->whereDate('verified_at', $today)
            ->sum('amount');

        $incomeYesterday = Payment::where('status', 'terverifikasi')
            ->whereDate('verified_at', $yesterday)
            ->sum('amount');

        $incomePct = $incomeYesterday > 0
            ? round((($incomeToday - $incomeYesterday) / $incomeYesterday) * 100, 1)
            : 0;

        // Total transaksi bulan ini
        $totalThisMonth = Payment::whereDate('created_at', '>=', $thisMonth)->count();
        $newToday       = Payment::whereDate('created_at', $today)->count();

        // Pending verifikasi
        $pendingCount = Payment::where('status', 'menunggu')->count();

        // Pengguna aktif (status active)
        $activeUsers = User::where('status', 'active')
            ->where('role', 'customer')
            ->count();

        // ── Chart: Pemasukan 7 hari terakhir ────────────────────────────
        $chartData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $amount = Payment::where('status', 'terverifikasi')
                ->whereDate('verified_at', $date)
                ->sum('amount');
            $chartData->push([
                'day'    => $date->isoFormat('ddd'), // Sen, Sel, dst
                'date'   => $date->format('Y-m-d'),
                'amount' => (float) $amount,
            ]);
        }
        $chartMax = $chartData->max('amount') ?: 1;

        // ── Status Breakdown bulan ini ───────────────────────────────────
        $totalMonth = max($totalThisMonth, 1); // hindari div by zero
        $verified   = Payment::where('status', 'terverifikasi')->whereDate('created_at', '>=', $thisMonth)->count();
        $waiting    = Payment::where('status', 'menunggu')->whereDate('created_at', '>=', $thisMonth)->count();
        $rejected   = Payment::where('status', 'ditolak')->whereDate('created_at', '>=', $thisMonth)->count();

        $statusBreakdown = [
            ['label' => 'Lunas',   'count' => $verified, 'pct' => round($verified / $totalMonth * 100), 'color' => 'emerald'],
            ['label' => 'Pending', 'count' => $waiting,  'pct' => round($waiting  / $totalMonth * 100), 'color' => 'amber'],
            ['label' => 'Ditolak', 'count' => $rejected, 'pct' => round($rejected / $totalMonth * 100), 'color' => 'red'],
        ];

        // Perbandingan bulan ini vs bulan lalu
        $totalLastMonth = Payment::whereDate('created_at', '>=', $lastMonth)
            ->whereDate('created_at', '<', $thisMonth)
            ->count();
        $monthGrowthPct = $totalLastMonth > 0
            ? round((($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100)
            : 0;

        // ── Pending list (5 terbaru) ─────────────────────────────────────
        $pendingList = Payment::with(['user:id,full_name,kelas,class,photo', 'order:id,order_number'])
            ->where('status', 'menunggu')
            ->latest()
            ->limit(5)
            ->get();

        // ── Rekap Cepat ──────────────────────────────────────────────────
        $incomeThisWeek = Payment::where('status', 'terverifikasi')
            ->whereDate('verified_at', '>=', $thisWeek)
            ->sum('amount');

        $avgPerTrx = $totalThisMonth > 0
            ? Payment::whereDate('created_at', '>=', $thisMonth)->avg('amount')
            : 0;

        // Hari dengan transaksi terbanyak (bulan ini)
        $busiestDay = Payment::whereDate('created_at', '>=', $thisMonth)
            ->selectRaw("TO_CHAR(created_at, 'Day') as day_name, COUNT(*) as total")
            ->groupBy('day_name')
            ->orderByDesc('total')
            ->first();

        $usersTransacted = Payment::whereDate('created_at', '>=', $thisMonth)
            ->distinct('user_id')
            ->count('user_id');

         // ── Target Harian ────────────────────────────────────────────────
        $dailyTarget  = DailyTarget::where('date', $today->toDateString())->first();
        $targetAmount = $dailyTarget?->target_amount ?? 0;
        $targetPct    = $targetAmount > 0
            ? min(round(($incomeToday / $targetAmount) * 100), 100)
            : 0;

        return view('admin.dashboard', compact(
            'incomeToday', 'incomeYesterday', 'incomePct',
            'totalThisMonth', 'newToday',
            'pendingCount',
            'activeUsers',
            'chartData', 'chartMax',
            'statusBreakdown', 'totalThisMonth', 'monthGrowthPct',
            'pendingList',
            'incomeThisWeek', 'avgPerTrx', 'busiestDay', 'usersTransacted',
            'targetAmount', 'targetPct'
        ));
    }

     public function setTarget(Request $request)
    {
        $request->validate([
            'target_amount' => 'required|numeric|min:0',
        ]);

        DailyTarget::updateOrCreate(
            ['date' => Carbon::today()->toDateString()],
            ['target_amount' => $request->target_amount]
        );

        return back()->with('target_saved', true);
    }
}