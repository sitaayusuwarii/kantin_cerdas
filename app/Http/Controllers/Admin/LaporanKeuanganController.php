<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\LaporanKeuanganExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanKeuanganController extends Controller
{
    private function getData(Request $request): array
    {
        $period = $request->get('period', 'bulan_ini');
        [$startDate, $endDate] = $this->resolveDates($period, $request);

        $totalIncome = DB::table('payments')
            ->where('status', 'terverifikasi')
            ->whereBetween('verified_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->sum('amount');

        $totalTransactions = DB::table('payments')
            ->where('status', 'terverifikasi')
            ->whereBetween('verified_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->count();

        $avgPerTransaction = $totalTransactions > 0 ? round($totalIncome / $totalTransactions) : 0;

        $currentYear  = now()->year;
        $currentMonth = now()->month;
        $monthlyData  = [];
        $prevIncome   = null;

        for ($m = 1; $m <= $currentMonth; $m++) {
            $monthStart = Carbon::create($currentYear, $m, 1)->startOfMonth();
            $monthEnd   = Carbon::create($currentYear, $m, 1)->endOfMonth();

            $income = DB::table('payments')
                ->where('status', 'terverifikasi')
                ->whereBetween('verified_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $trx = DB::table('payments')
                ->where('status', 'terverifikasi')
                ->whereBetween('verified_at', [$monthStart, $monthEnd])
                ->count();

            $growth = null;
            $gdir   = null;
            if ($prevIncome !== null && $prevIncome > 0) {
                $pct    = (($income - $prevIncome) / $prevIncome) * 100;
                $growth = ($pct >= 0 ? '+' : '') . number_format($pct, 1) . '%';
                $gdir   = $pct >= 0 ? 'up' : 'down';
            }

            $monthlyData[] = [
                'month'  => $monthStart->locale('id')->isoFormat('MMMM'),
                'trx'    => $trx,
                'income' => $income,
                'growth' => $growth,
                'gdir'   => $gdir,
            ];

            $prevIncome = $income;
        }

        return compact('period', 'startDate', 'endDate', 'totalIncome', 'totalTransactions', 'avgPerTransaction', 'monthlyData', 'currentYear');
    }

    private function resolveDates(string $period, Request $request): array
    {
        return match ($period) {
            'hari_ini'   => [Carbon::today(), Carbon::today()],
            'minggu_ini' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'tahun_ini'  => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'custom'     => [
                Carbon::parse($request->get('start_date', now()->startOfMonth())),
                Carbon::parse($request->get('end_date', now())),
            ],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    public function index(Request $request)
    {
        // ── Determine active period ──────────────────────────────────────────
        $period = $request->input('period', 'bulan_ini');

        [$startDate, $endDate] = match ($period) {
            'hari_ini'   => [Carbon::today(),               Carbon::today()->endOfDay()],
            'minggu_ini' => [Carbon::now()->startOfWeek(),  Carbon::now()->endOfWeek()],
            'tahun_ini'  => [Carbon::now()->startOfYear(),  Carbon::now()->endOfYear()],
            default      => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()], // bulan_ini
        };

        // Custom date range overrides period
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
            $period    = 'custom';
        }

        // ── Hero KPI Cards ───────────────────────────────────────────────────

        // Total income: sum of verified payments in the period
        $totalIncome = DB::table('payments')
            ->where('status', 'terverifikasi')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->sum('amount');

        // Total transactions (orders that are not cancelled)
        $totalTransactions = DB::table('orders')
            ->whereNotIn('status', ['dibatalkan'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Average per transaction
        $avgPerTransaction = $totalTransactions > 0
            ? round($totalIncome / $totalTransactions)
            : 0;

        // Same period last month for growth comparison
        $lastPeriodStart  = (clone $startDate)->subMonth();
        $lastPeriodEnd    = (clone $endDate)->subMonth();

        $lastIncome = DB::table('payments')
            ->where('status', 'terverifikasi')
            ->whereBetween('verified_at', [$lastPeriodStart, $lastPeriodEnd])
            ->sum('amount');

        $lastTransactions = DB::table('orders')
            ->whereNotIn('status', ['dibatalkan'])
            ->whereBetween('created_at', [$lastPeriodStart, $lastPeriodEnd])
            ->count();

        $lastAvg = $lastTransactions > 0
            ? round($lastIncome / $lastTransactions)
            : 0;

        // ── Monthly Trend (current year) ─────────────────────────────────────
        $currentYear = Carbon::now()->year;

        $monthlyIncome = DB::table('payments')
            ->where('status', 'terverifikasi')
            ->whereYear('verified_at', $currentYear)
            ->select(
                DB::raw('EXTRACT(MONTH FROM verified_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw('EXTRACT(MONTH FROM verified_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthlyTransactions = DB::table('orders')
            ->whereNotIn('status', ['dibatalkan'])
            ->whereYear('created_at', $currentYear)
            ->select(
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Build month-by-month summary (only months up to current)
        $currentMonth = Carbon::now()->month;
        $monthNames   = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        $months       = [];
        $incomeByMonth = [];
        $trxByMonth   = [];

        for ($m = 1; $m <= $currentMonth; $m++) {
            $months[]       = $monthNames[$m - 1];
            $incomeByMonth[] = (float) ($monthlyIncome[$m]->total ?? 0);
            $trxByMonth[]   = (int)   ($monthlyTransactions[$m]->total ?? 0);
        }

        $maxChartVal = max(array_merge($incomeByMonth, [1])); // avoid division by zero

        // ── Full Monthly Table ───────────────────────────────────────────────
        $monthlyData = [];
        $prevIncome  = null;

        for ($m = 1; $m <= $currentMonth; $m++) {
            $income = (float) ($monthlyIncome[$m]->total ?? 0);
            $trx    = (int)   ($monthlyTransactions[$m]->total ?? 0);
            $target = 0; // No target table in DB; set 0 or hardcode per business needs

            $growth = null;
            $gdir   = 'up';
            if ($prevIncome !== null && $prevIncome > 0) {
                $pct    = (($income - $prevIncome) / $prevIncome) * 100;
                $growth = ($pct >= 0 ? '+' : '') . number_format($pct, 1) . '%';
                $gdir   = $pct >= 0 ? 'up' : 'down';
            }

            $monthlyData[] = [
                'month'  => Carbon::create($currentYear, $m, 1)->locale('id')->isoFormat('MMMM'),
                'trx'    => $trx,
                'income' => $income,
                'target' => $target,
                'pct'    => 0, // calculated below after targets set
                'growth' => $growth,
                'gdir'   => $gdir,
            ];
            $prevIncome = $income;
        }

        // ── Payment Method Breakdown ─────────────────────────────────────────
        $paymentBreakdown = DB::table('payments')
            ->where('status', 'terverifikasi')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->select('method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        // Group into Transfer Bank / E-Wallet / Lainnya
        $transferMethods = ['transfer_bri', 'transfer_bca', 'transfer_mandiri'];
        $ewalletMethods  = ['gopay', 'ovo', 'dana'];

        $breakdown = [
            'Transfer Bank' => ['total' => 0, 'count' => 0, 'color' => 'primary'],
            'E-Wallet'      => ['total' => 0, 'count' => 0, 'color' => 'violet'],
            'Lainnya'       => ['total' => 0, 'count' => 0, 'color' => 'slate'],
        ];

        foreach ($paymentBreakdown as $row) {
            if (in_array($row->method, $transferMethods)) {
                $breakdown['Transfer Bank']['total'] += $row->total;
                $breakdown['Transfer Bank']['count'] += $row->count;
            } elseif (in_array($row->method, $ewalletMethods)) {
                $breakdown['E-Wallet']['total'] += $row->total;
                $breakdown['E-Wallet']['count'] += $row->count;
            } else {
                $breakdown['Lainnya']['total'] += $row->total;
                $breakdown['Lainnya']['count'] += $row->count;
            }
        }

        $totalBreakdown = array_sum(array_column($breakdown, 'total'));

        $methods = [];
        foreach ($breakdown as $label => $data) {
            $pct = $totalBreakdown > 0
                ? round(($data['total'] / $totalBreakdown) * 100)
                : 0;
            $methods[] = [
                'label'  => $label,
                'pct'    => $pct,
                'amount' => $data['total'],
                'color'  => $data['color'],
            ];
        }

        // ── YTD Totals ───────────────────────────────────────────────────────
        $ytdIncome = array_sum($incomeByMonth);
        $ytdTrx    = array_sum($trxByMonth);

        // ── Auto-detected Financial Notes ───────────────────────────────────
        $notes = $this->generateNotes($monthlyData, $totalTransactions, $lastTransactions, $startDate);

        return view('admin.laporan-keuangan', compact(
            'period',
            'startDate',
            'endDate',
            'totalIncome',
            'totalTransactions',
            'avgPerTransaction',
            'lastIncome',
            'lastTransactions',
            'lastAvg',
            'months',
            'incomeByMonth',
            'trxByMonth',
            'maxChartVal',
            'monthlyData',
            'methods',
            'ytdIncome',
            'ytdTrx',
            'notes',
            'currentYear',
        ));
    }

    // ── Helper: Auto-detect financial anomalies / highlights ────────────────
    private function generateNotes(array $monthlyData, int $currentTrx, int $lastTrx, Carbon $startDate): array
    {
        $notes = [];

        // Check last two months trend
        $count = count($monthlyData);
        if ($count >= 2) {
            $prev    = $monthlyData[$count - 2];
            $current = $monthlyData[$count - 1];

            if ($current['income'] < $prev['income']) {
                $drop  = abs((($current['income'] - $prev['income']) / max($prev['income'], 1)) * 100);
                $notes[] = [
                    'icon'  => 'fa-arrow-down',
                    'color' => 'amber',
                    'title' => 'Pemasukan ' . $current['month'] . ' Turun',
                    'desc'  => 'Turun ' . number_format($drop, 1) . '% dibanding ' . $prev['month'] . '.',
                ];
            } elseif ($current['income'] > $prev['income']) {
                $rise  = abs((($current['income'] - $prev['income']) / max($prev['income'], 1)) * 100);
                $notes[] = [
                    'icon'  => 'fa-arrow-up',
                    'color' => 'emerald',
                    'title' => 'Pemasukan ' . $current['month'] . ' Meningkat',
                    'desc'  => 'Naik ' . number_format($rise, 1) . '% dibanding ' . $prev['month'] . '.',
                ];
            }
        }

        // Transaction growth
        if ($lastTrx > 0) {
            $trxGrowth = (($currentTrx - $lastTrx) / $lastTrx) * 100;
            if ($trxGrowth >= 5) {
                $notes[] = [
                    'icon'  => 'fa-receipt',
                    'color' => 'emerald',
                    'title' => 'Jumlah Transaksi Meningkat',
                    'desc'  => '+' . number_format($trxGrowth, 1) . '% transaksi dibanding periode sebelumnya.',
                ];
            } elseif ($trxGrowth <= -5) {
                $notes[] = [
                    'icon'  => 'fa-receipt',
                    'color' => 'red',
                    'title' => 'Jumlah Transaksi Menurun',
                    'desc'  => number_format($trxGrowth, 1) . '% transaksi dibanding periode sebelumnya.',
                ];
            }
        }

        // New users this period
        $newUsers = DB::table('users')
            ->where('role', 'customer')
            ->whereBetween('created_at', [
                $startDate->copy()->startOfMonth(),
                $startDate->copy()->endOfMonth(),
            ])
            ->count();

        if ($newUsers > 0) {
            $notes[] = [
                'icon'  => 'fa-users',
                'color' => 'primary',
                'title' => 'Pengguna Baru',
                'desc'  => '+' . $newUsers . ' pengguna baru terdaftar pada periode ini.',
            ];
        }

        // Fallback if no notes generated
        if (empty($notes)) {
            $notes[] = [
                'icon'  => 'fa-circle-check',
                'color' => 'emerald',
                'title' => 'Tidak Ada Anomali',
                'desc'  => 'Keuangan periode ini berjalan normal tanpa anomali terdeteksi.',
            ];
        }

        return $notes;
    }

     // ── Export Excel ──────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $data = $this->getData($request);

        $filename = 'laporan-keuangan-' . $data['startDate']->format('Y-m-d') . '-sd-' . $data['endDate']->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new LaporanKeuanganExport(
                $data['startDate'],
                $data['endDate'],
                $data['monthlyData'],
                $data['totalIncome'],
                $data['totalTransactions'],
            ),
            $filename
        );
    }

    // ── Export PDF ────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $data = $this->getData($request);

        $pdf = Pdf::loadView('admin.laporan-keuangan-pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'laporan-keuangan-' . $data['startDate']->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

}