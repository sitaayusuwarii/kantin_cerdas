@extends('layouts.admin')

@section('title', 'Laporan Keuangan — SmartCanteen Admin')
@section('page-title', 'Laporan Keuangan')
@section('page-subtitle', 'Ringkasan & analisis keuangan kantin sekolah')

@section('content')

{{-- ===== PERIOD SELECTOR ===== --}}
<form method="GET" action="{{ route('admin.laporan-keuangan') }}" id="period-form">
<div class="glass-card rounded-2xl p-4 mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-3">
    <div class="flex items-center gap-2">
        <i class="fa-solid fa-calendar-range text-primary-400 text-sm"></i>
        <span class="text-slate-300 text-sm font-medium">Periode:</span>
    </div>
    <div class="flex flex-wrap gap-2">
        @foreach([
            'hari_ini'   => 'Hari Ini',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini'  => 'Bulan Ini',
            'tahun_ini'  => 'Tahun Ini',
        ] as $key => $label)
        <button type="submit" name="period" value="{{ $key }}"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
            {{ $period === $key ? 'bg-primary-500/20 border border-primary-500/40 text-primary-300' : 'bg-slate-800 border border-border text-slate-400 hover:text-white hover:border-slate-600' }}">
            {{ $label }}
        </button>
        @endforeach

        {{-- Custom Date Range --}}
        <div class="flex items-center gap-2 bg-slate-800 border border-border rounded-lg px-3 py-1.5
            {{ $period === 'custom' ? 'border-primary-500/40' : '' }}">
            <input type="date" name="start_date"
                value="{{ $period === 'custom' ? $startDate->toDateString() : now()->startOfMonth()->toDateString() }}"
                class="bg-transparent text-slate-300 text-xs outline-none"
                onchange="document.getElementById('period-form').submit()">
            <span class="text-slate-600">—</span>
            <input type="date" name="end_date"
                value="{{ $period === 'custom' ? $endDate->toDateString() : now()->toDateString() }}"
                class="bg-transparent text-slate-300 text-xs outline-none"
                onchange="document.getElementById('period-form').submit()">
        </div>
    </div>

    <div class="sm:ml-auto flex gap-2">
        <a href="{{ route('admin.laporan-keuangan.export-pdf', request()->query()) }}"
           class="flex items-center gap-2 px-3 py-2 rounded-xl bg-primary-500/10 border border-primary-500/20 text-primary-400 hover:bg-primary-500/20 transition-all text-xs font-semibold">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('admin.laporan-keuangan.export-excel', request()->query()) }}"
           class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 hover:border-primary-500 hover:text-primary-400 transition-all text-xs font-semibold">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
    </div>
</div>
</form>

{{-- ===== HERO INCOME CARDS ===== --}}
@php
    $periodLabel = match($period) {
        'hari_ini'   => 'Hari Ini',
        'minggu_ini' => 'Minggu ' . $startDate->format('d M'),
        'tahun_ini'  => 'Tahun ' . $startDate->format('Y'),
        'custom'     => $startDate->format('d M') . ' – ' . $endDate->format('d M Y'),
        default      => $startDate->locale('id')->isoFormat('MMMM YYYY'),
    };

    $incomeGrowth    = $lastIncome > 0 ? (($totalIncome - $lastIncome) / $lastIncome) * 100 : null;
    $trxGrowth       = $lastTransactions > 0 ? (($totalTransactions - $lastTransactions) / $lastTransactions) * 100 : null;
    $avgGrowthAmount = $avgPerTransaction - $lastAvg;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Total Income --}}
    <div class="relative glass-card rounded-2xl p-6 overflow-hidden col-span-1 sm:col-span-1">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/40 to-violet-900/20"></div>
        <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-primary-500/10 -translate-y-10 translate-x-10 blur-2xl"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-10 h-10 rounded-xl bg-primary-500/20 border border-primary-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-primary-400 text-sm"></i>
                </div>
                <span class="text-slate-400 text-xs font-medium">Total Pemasukan</span>
            </div>
            @php
                $incomeJuta = $totalIncome / 1000000;
                $incomeRibu = $totalIncome / 1000;
            @endphp
            @if($totalIncome >= 1000000)
                <p class="text-white font-bold text-3xl leading-none">
                    Rp {{ number_format($incomeJuta, 1, ',', '.') }}<span class="text-xl text-slate-300">jt</span>
                </p>
            @else
                <p class="text-white font-bold text-3xl leading-none">
                    Rp {{ number_format($incomeRibu, 0, ',', '.') }}<span class="text-xl text-slate-300">rb</span>
                </p>
            @endif
            <p class="text-slate-500 text-xs mt-2">{{ $periodLabel }}</p>

            @if($incomeGrowth !== null)
            <div class="mt-4 flex items-center gap-2">
                <span class="{{ $incomeGrowth >= 0 ? 'text-emerald-400' : 'text-red-400' }} text-xs font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-{{ $incomeGrowth >= 0 ? 'up' : 'down' }}"></i>
                    {{ ($incomeGrowth >= 0 ? '+' : '') . number_format($incomeGrowth, 1) }}% dari periode sebelumnya
                </span>
            </div>
            @endif
        </div>
    </div>

    {{-- Total Transaksi --}}
    <div class="relative glass-card rounded-2xl p-6 overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-emerald-500/10 -translate-y-6 translate-x-6 blur-xl"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-receipt text-emerald-400 text-sm"></i>
                </div>
                <span class="text-slate-400 text-xs font-medium">Total Transaksi</span>
            </div>
            <p class="text-white font-bold text-3xl leading-none">{{ number_format($totalTransactions) }}</p>
            <p class="text-slate-500 text-xs mt-2">Transaksi berhasil</p>
            @if($trxGrowth !== null)
            <p class="{{ $trxGrowth >= 0 ? 'text-emerald-400' : 'text-red-400' }} text-xs font-semibold mt-4 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-{{ $trxGrowth >= 0 ? 'up' : 'down' }}"></i>
                {{ ($trxGrowth >= 0 ? '+' : '') . number_format($trxGrowth, 1) }}% dari periode sebelumnya
            </p>
            @endif
        </div>
    </div>

    {{-- Rata-rata --}}
    <div class="relative glass-card rounded-2xl p-6 overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-violet-500/10 -translate-y-6 translate-x-6 blur-xl"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-calculator text-violet-400 text-sm"></i>
                </div>
                <span class="text-slate-400 text-xs font-medium">Rata-rata / Transaksi</span>
            </div>
            @php
                $avgRibu = $avgPerTransaction / 1000;
            @endphp
            <p class="text-white font-bold text-3xl leading-none">
                Rp {{ number_format($avgRibu, 1, ',', '.') }}<span class="text-xl text-slate-300">rb</span>
            </p>
            <p class="text-slate-500 text-xs mt-2">Per transaksi periode ini</p>
            @if($avgGrowthAmount !== 0)
            <p class="{{ $avgGrowthAmount >= 0 ? 'text-violet-400' : 'text-red-400' }} text-xs font-semibold mt-4 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-{{ $avgGrowthAmount >= 0 ? 'up' : 'down' }}"></i>
                {{ ($avgGrowthAmount >= 0 ? '+' : '') }}Rp {{ number_format(abs($avgGrowthAmount), 0, ',', '.') }} dari periode sebelumnya
            </p>
            @endif
        </div>
    </div>
</div>

{{-- ===== CHART + BREAKDOWN ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">

    {{-- Monthly Trend Chart --}}
    <div class="xl:col-span-2 glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-white font-bold text-base">Tren Pemasukan</h2>
                <p class="text-slate-500 text-xs mt-0.5">Januari – {{ end($months) }} {{ $currentYear }} (dalam jutaan Rp)</p>
            </div>
            <div class="flex gap-3">
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                    <span class="text-slate-500 text-xs">Pemasukan</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-slate-600"></div>
                    <span class="text-slate-500 text-xs">Transaksi</span>
                </div>
            </div>
        </div>

        @php $maxVal = max(array_merge($incomeByMonth, [1])); @endphp

        <div class="flex items-end gap-2 h-40 mb-4">
            @foreach($months as $i => $month)
            @php
                $barHeight = (int) round(($incomeByMonth[$i] / $maxVal) * 128);
                $barHeight = max($barHeight, 2); // min visible bar
                $incomeJutaDisplay = number_format($incomeByMonth[$i] / 1000000, 1, ',', '.');
            @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="w-full relative flex gap-1 items-end" style="height: 128px;">
                    {{-- Income bar --}}
                    <div class="flex-1 rounded-t-md bg-gradient-to-t from-primary-700 to-primary-400 relative group"
                         style="height: {{ $barHeight }}px">
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 border border-border text-white text-xs px-2 py-1 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                            Rp {{ $incomeJutaDisplay }}jt
                        </div>
                    </div>
                </div>
                <span class="text-slate-500 text-xs font-medium">{{ $month }}</span>
            </div>
            @endforeach
        </div>

        <div class="border-t border-border/50 pt-3 grid grid-cols-{{ count($months) }} gap-2">
            @foreach($months as $i => $month)
            <div class="text-center">
                <p class="text-white font-bold text-sm font-mono">{{ number_format($incomeByMonth[$i] / 1000000, 1, ',', '.') }}jt</p>
                <p class="text-slate-600 text-xs">{{ $month }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Breakdown by Payment Method --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="text-white font-bold text-base mb-1">Breakdown Metode</h2>
        <p class="text-slate-500 text-xs mb-5">Distribusi metode pembayaran</p>

        <div class="space-y-4 mb-6">
            @foreach($methods as $m)
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-{{ $m['color'] }}-500"></div>
                        <span class="text-slate-300 text-sm font-medium">{{ $m['label'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @php
                            $amtJuta = $m['amount'] / 1000000;
                            $amtRibu = $m['amount'] / 1000;
                        @endphp
                        <span class="text-slate-400 text-xs">
                            {{ $m['amount'] >= 1000000
                                ? 'Rp ' . number_format($amtJuta, 1, ',', '.') . 'jt'
                                : 'Rp ' . number_format($amtRibu, 0, ',', '.') . 'rb' }}
                        </span>
                        <span class="text-{{ $m['color'] }}-400 font-mono font-semibold text-xs">{{ $m['pct'] }}%</span>
                    </div>
                </div>
                <div class="h-2.5 bg-slate-700/50 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $m['color'] }}-500 rounded-full transition-all" style="width: {{ $m['pct'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Donut Chart (SVG) --}}
        @php
            $offset = 0;
            $donutColors = ['primary' => '#4f46e5', 'violet' => '#7c3aed', 'slate' => '#475569'];
        @endphp
        <div class="flex items-center justify-center py-2">
            <div class="relative w-28 h-28">
                <svg viewBox="0 0 36 36" class="w-28 h-28 -rotate-90">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#1e293b" stroke-width="3"/>
                    @foreach($methods as $m)
                    @if($m['pct'] > 0)
                    <circle cx="18" cy="18" r="15.9" fill="none"
                        stroke="{{ $donutColors[$m['color']] ?? '#475569' }}"
                        stroke-width="3"
                        stroke-dasharray="{{ $m['pct'] }} {{ 100 - $m['pct'] }}"
                        stroke-dashoffset="{{ -$offset }}"
                        stroke-linecap="round"/>
                    @php $offset += $m['pct']; @endphp
                    @endif
                    @endforeach
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <p class="text-white font-bold text-base leading-none">{{ number_format($totalTransactions) }}</p>
                    <p class="text-slate-500 text-xs">trx</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== MONTHLY TABLE ===== --}}
<div class="glass-card rounded-2xl overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-border flex items-center justify-between">
        <h2 class="text-white font-bold text-base">Ringkasan Bulanan</h2>
        <span class="text-slate-500 text-xs">Tahun {{ $currentYear }}</span>
    </div>

    {{-- Desktop table --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Bulan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Transaksi</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemasukan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Growth</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @foreach($monthlyData as $i => $row)
                @php
                    $isLast = $i === count($monthlyData) - 1;
                @endphp
                <tr class="table-row {{ $isLast ? 'bg-primary-500/5' : '' }}">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-slate-700 flex items-center justify-center">
                                <i class="fa-solid fa-calendar text-slate-400 text-xs"></i>
                            </div>
                            <span class="text-white font-semibold text-sm">{{ $row['month'] }}</span>
                            @if($isLast)
                            <span class="text-xs font-semibold text-primary-400 bg-primary-500/10 border border-primary-500/20 px-1.5 py-0.5 rounded">Aktif</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 text-slate-300 text-sm">{{ number_format($row['trx']) }}</td>
                    <td class="px-5 py-4">
                        <span class="text-white font-bold font-mono text-sm">Rp {{ number_format($row['income'], 0, ',', '.') }}</span>
                    </td>
                    <td class="px-5 py-4">
                        @if($row['growth'])
                        <span class="text-xs font-semibold font-mono flex items-center gap-1 {{ $row['gdir'] === 'up' ? 'text-emerald-400' : 'text-red-400' }}">
                            <i class="fa-solid fa-arrow-trend-{{ $row['gdir'] }} text-xs"></i>
                            {{ $row['growth'] }}
                        </span>
                        @else
                        <span class="text-slate-600 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-border bg-slate-800/30">
                    <td class="px-5 py-4 text-white font-bold text-sm">TOTAL YTD</td>
                    <td class="px-5 py-4 text-white font-bold">{{ number_format($ytdTrx) }}</td>
                    <td class="px-5 py-4 text-emerald-400 font-bold font-mono text-sm">Rp {{ number_format($ytdIncome, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-slate-400 text-xs font-semibold font-mono">—</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="sm:hidden p-4 space-y-3">
        @foreach($monthlyData as $i => $row)
        @php $isLast = $i === count($monthlyData) - 1; @endphp
        <div class="p-4 rounded-xl bg-slate-800/40 border border-border {{ $isLast ? 'border-primary-500/30 bg-primary-500/5' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <p class="text-white font-bold">{{ $row['month'] }}
                    @if($isLast)<span class="ml-2 text-xs text-primary-400 font-normal">Aktif</span>@endif
                </p>
                @if($row['growth'])
                <span class="text-xs font-mono font-semibold {{ $row['gdir'] === 'up' ? 'text-emerald-400' : 'text-red-400' }}">{{ $row['growth'] }}</span>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <p class="text-slate-500">Transaksi</p>
                    <p class="text-white font-semibold">{{ number_format($row['trx']) }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Pemasukan</p>
                    <p class="text-white font-semibold font-mono">{{ number_format($row['income'] / 1000000, 1, ',', '.') }}jt</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ===== ANOMALY / NOTES ===== --}}
<div class="glass-card rounded-2xl p-5">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
            <i class="fa-solid fa-triangle-exclamation text-amber-400 text-sm"></i>
        </div>
        <div>
            <h2 class="text-white font-bold text-base">Catatan Keuangan</h2>
            <p class="text-slate-500 text-xs">Otomatis terdeteksi sistem</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($notes as $n)
        <div class="p-4 rounded-xl bg-{{ $n['color'] }}-500/5 border border-{{ $n['color'] }}-500/15">
            <div class="flex items-center gap-2 mb-2">
                <i class="fa-solid {{ $n['icon'] }} text-{{ $n['color'] }}-400 text-sm"></i>
                <p class="text-white text-sm font-semibold">{{ $n['title'] }}</p>
            </div>
            <p class="text-slate-400 text-xs leading-relaxed">{{ $n['desc'] }}</p>
        </div>
        @empty
        <div class="col-span-3 text-center text-slate-500 text-sm py-4">Tidak ada catatan untuk periode ini.</div>
        @endforelse
    </div>
</div>

@endsection