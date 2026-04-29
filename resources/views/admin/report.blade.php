@extends('layouts.admin')

@section('title', 'Laporan Keuangan — SmartCanteen Admin')
@section('page-title', 'Laporan Keuangan')
@section('page-subtitle', 'Ringkasan & analisis keuangan kantin sekolah')

@section('content')

{{-- ===== PERIOD SELECTOR ===== --}}
<div class="glass-card rounded-2xl p-4 mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-3">
    <div class="flex items-center gap-2">
        <i class="fa-solid fa-calendar-range text-primary-400 text-sm"></i>
        <span class="text-slate-300 text-sm font-medium">Periode:</span>
    </div>
    <div class="flex flex-wrap gap-2">
        @foreach(['Hari Ini', 'Minggu Ini', 'Bulan Ini', 'Tahun Ini'] as $p)
        <button class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
            {{ $p === 'Bulan Ini' ? 'bg-primary-500/20 border border-primary-500/40 text-primary-300' : 'bg-slate-800 border border-border text-slate-400 hover:text-white hover:border-slate-600' }}">
            {{ $p }}
        </button>
        @endforeach
        <div class="flex items-center gap-2 bg-slate-800 border border-border rounded-lg px-3 py-1.5">
            <input type="date" value="{{ date('Y-m-01') }}" class="bg-transparent text-slate-300 text-xs outline-none">
            <span class="text-slate-600">—</span>
            <input type="date" value="{{ date('Y-m-d') }}" class="bg-transparent text-slate-300 text-xs outline-none">
        </div>
    </div>
    <div class="sm:ml-auto flex gap-2">
        <button class="flex items-center gap-2 px-3 py-2 rounded-xl bg-primary-500/10 border border-primary-500/20 text-primary-400 hover:bg-primary-500/20 transition-all text-xs font-semibold">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </button>
        <button class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 hover:border-primary-500 hover:text-primary-400 transition-all text-xs font-semibold">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </button>
    </div>
</div>

{{-- ===== HERO INCOME CARDS ===== --}}
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
            <p class="text-white font-bold text-3xl leading-none">Rp 48,3<span class="text-xl text-slate-300">jt</span></p>
            <p class="text-slate-500 text-xs mt-2">Bulan April 2025</p>
            <div class="mt-4 flex items-center gap-2">
                <div class="flex-1 h-1.5 bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-primary-500 to-violet-500 rounded-full" style="width: 78%"></div>
                </div>
                <span class="text-primary-400 text-xs font-semibold font-mono">78%</span>
            </div>
            <p class="text-slate-600 text-xs mt-1">dari target Rp 62jt</p>
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
            <p class="text-white font-bold text-3xl leading-none">248</p>
            <p class="text-slate-500 text-xs mt-2">Transaksi berhasil</p>
            <p class="text-emerald-400 text-xs font-semibold mt-4 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up"></i> +18% dari bulan lalu
            </p>
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
            <p class="text-white font-bold text-3xl leading-none">Rp 38,5<span class="text-xl text-slate-300">rb</span></p>
            <p class="text-slate-500 text-xs mt-2">Per transaksi bulan ini</p>
            <p class="text-violet-400 text-xs font-semibold mt-4 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up"></i> +Rp 3.200 dari April
            </p>
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
                <p class="text-slate-500 text-xs mt-0.5">Januari – April 2025 (dalam jutaan Rp)</p>
            </div>
            <div class="flex gap-3">
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                    <span class="text-slate-500 text-xs">Pemasukan</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-slate-600"></div>
                    <span class="text-slate-500 text-xs">Target</span>
                </div>
            </div>
        </div>

        @php
            $months = ['Jan', 'Feb', 'Mar', 'Apr'];
            $income  = [38500000, 42300000, 51200000, 48300000];
            $target  = [45000000, 48000000, 55000000, 62000000];
            $maxVal  = max(array_merge($income, $target));
        @endphp

        <div class="flex items-end gap-4 h-40 mb-4">
            @foreach($months as $i => $month)
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="w-full relative flex gap-1 items-end" style="height: 128px;">
                    {{-- Target bar (faded) --}}
                    <div class="flex-1 rounded-t-md bg-slate-700/40"
                         style="height: {{ round(($target[$i] / $maxVal) * 128) }}px"></div>
                    {{-- Income bar --}}
                    <div class="flex-1 rounded-t-md bg-gradient-to-t from-primary-700 to-primary-400 relative group"
                         style="height: {{ round(($income[$i] / $maxVal) * 128) }}px">
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 border border-border text-white text-xs px-2 py-1 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                            Rp {{ number_format($income[$i] / 1000000, 1, ',', '.') }}jt
                        </div>
                    </div>
                </div>
                <span class="text-slate-500 text-xs font-medium">{{ $month }}</span>
            </div>
            @endforeach
        </div>

        <div class="border-t border-border/50 pt-3 grid grid-cols-4 gap-2">
            @foreach($months as $i => $month)
            <div class="text-center">
                <p class="text-white font-bold text-sm font-mono">{{ number_format($income[$i] / 1000000, 1, ',', '.') }}jt</p>
                <p class="text-slate-600 text-xs">{{ $month }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Breakdown by Category --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="text-white font-bold text-base mb-1">Breakdown Metode</h2>
        <p class="text-slate-500 text-xs mb-5">Distribusi metode pembayaran</p>

        @php
            $methods = [
                ['label' => 'Transfer Bank', 'pct' => 65, 'amount' => 'Rp 31,4jt', 'color' => 'primary'],
                ['label' => 'E-Wallet',      'pct' => 28, 'amount' => 'Rp 13,5jt', 'color' => 'violet'],
                ['label' => 'Lainnya',       'pct' => 7,  'amount' => 'Rp 3,4jt',  'color' => 'slate'],
            ];
        @endphp

        <div class="space-y-4 mb-6">
            @foreach($methods as $m)
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-{{ $m['color'] }}-500"></div>
                        <span class="text-slate-300 text-sm font-medium">{{ $m['label'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-slate-400 text-xs">{{ $m['amount'] }}</span>
                        <span class="text-{{ $m['color'] }}-400 font-mono font-semibold text-xs">{{ $m['pct'] }}%</span>
                    </div>
                </div>
                <div class="h-2.5 bg-slate-700/50 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $m['color'] }}-500 rounded-full transition-all" style="width: {{ $m['pct'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Donut Chart (CSS) --}}
        <div class="flex items-center justify-center py-2">
            <div class="relative w-28 h-28">
                <svg viewBox="0 0 36 36" class="w-28 h-28 -rotate-90">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#1e293b" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#4f46e5" stroke-width="3"
                        stroke-dasharray="65 35" stroke-linecap="round"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#7c3aed" stroke-width="3"
                        stroke-dasharray="28 72" stroke-dashoffset="-65" stroke-linecap="round"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#475569" stroke-width="3"
                        stroke-dasharray="7 93" stroke-dashoffset="-93" stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <p class="text-white font-bold text-base leading-none">248</p>
                    <p class="text-slate-500 text-xs">trx</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== MONTHLY COMPARISON TABLE ===== --}}
<div class="glass-card rounded-2xl overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-border flex items-center justify-between">
        <h2 class="text-white font-bold text-base">Ringkasan Bulanan</h2>
        <span class="text-slate-500 text-xs">Tahun 2025</span>
    </div>

    {{-- Desktop table --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Bulan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Transaksi</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemasukan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Target</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Capaian</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Growth</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @php
                    $monthlyData = [
                        ['month'=>'Januari',  'trx'=>195, 'income'=>38500000, 'target'=>45000000, 'pct'=>86, 'growth'=>null,  'gdir'=>'up'],
                        ['month'=>'Februari', 'trx'=>212, 'income'=>42300000, 'target'=>48000000, 'pct'=>88, 'growth'=>'+9.9%','gdir'=>'up'],
                        ['month'=>'Maret',    'trx'=>241, 'income'=>51200000, 'target'=>55000000, 'pct'=>93, 'growth'=>'+21.0%','gdir'=>'up'],
                        ['month'=>'April',    'trx'=>248, 'income'=>48300000, 'target'=>62000000, 'pct'=>78, 'growth'=>'-5.7%','gdir'=>'down'],
                    ];
                @endphp
                @foreach($monthlyData as $row)
                <tr class="table-row {{ $loop->last ? 'bg-primary-500/5' : '' }}">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-slate-700 flex items-center justify-center">
                                <i class="fa-solid fa-calendar text-slate-400 text-xs"></i>
                            </div>
                            <span class="text-white font-semibold text-sm">{{ $row['month'] }}</span>
                            @if($loop->last)
                            <span class="text-xs font-semibold text-primary-400 bg-primary-500/10 border border-primary-500/20 px-1.5 py-0.5 rounded">Aktif</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 text-slate-300 text-sm">{{ $row['trx'] }}</td>
                    <td class="px-5 py-4">
                        <span class="text-white font-bold font-mono text-sm">Rp {{ number_format($row['income'], 0, ',', '.') }}</span>
                    </td>
                    <td class="px-5 py-4 text-slate-400 text-sm font-mono">Rp {{ number_format($row['target'], 0, ',', '.') }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-16 h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-{{ $row['pct'] >= 90 ? 'emerald' : ($row['pct'] >= 75 ? 'amber' : 'red') }}-500 rounded-full" style="width: {{ $row['pct'] }}%"></div>
                            </div>
                            <span class="text-{{ $row['pct'] >= 90 ? 'emerald' : ($row['pct'] >= 75 ? 'amber' : 'red') }}-400 text-xs font-semibold font-mono">{{ $row['pct'] }}%</span>
                        </div>
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
                    <td class="px-5 py-4 text-white font-bold">896</td>
                    <td class="px-5 py-4 text-emerald-400 font-bold font-mono text-sm">Rp 180.300.000</td>
                    <td class="px-5 py-4 text-slate-400 font-mono text-sm">Rp 210.000.000</td>
                    <td class="px-5 py-4">
                        <span class="text-amber-400 font-bold font-mono text-sm">86%</span>
                    </td>
                    <td class="px-5 py-4 text-emerald-400 text-xs font-semibold font-mono">+8.3% rata²</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="sm:hidden p-4 space-y-3">
        @foreach($monthlyData as $row)
        <div class="p-4 rounded-xl bg-slate-800/40 border border-border {{ $loop->last ? 'border-primary-500/30 bg-primary-500/5' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <p class="text-white font-bold">{{ $row['month'] }}
                    @if($loop->last)<span class="ml-2 text-xs text-primary-400 font-normal">Aktif</span>@endif
                </p>
                @if($row['growth'])
                <span class="text-xs font-mono font-semibold {{ $row['gdir'] === 'up' ? 'text-emerald-400' : 'text-red-400' }}">{{ $row['growth'] }}</span>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <p class="text-slate-500">Transaksi</p>
                    <p class="text-white font-semibold">{{ $row['trx'] }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Pemasukan</p>
                    <p class="text-white font-semibold font-mono">{{ number_format($row['income'] / 1000000, 1, ',', '.') }}jt</p>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-500">Capaian target</span>
                    <span class="text-{{ $row['pct'] >= 90 ? 'emerald' : ($row['pct'] >= 75 ? 'amber' : 'red') }}-400 font-mono font-semibold">{{ $row['pct'] }}%</span>
                </div>
                <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $row['pct'] >= 90 ? 'emerald' : ($row['pct'] >= 75 ? 'amber' : 'red') }}-500 rounded-full" style="width: {{ $row['pct'] }}%"></div>
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
        @php
            $notes = [
                ['icon' => 'fa-arrow-down', 'color' => 'amber', 'title' => 'Pemasukan April Turun', 'desc' => 'Turun 5.7% dibanding Maret. Kemungkinan karena libur sekolah.'],
                ['icon' => 'fa-circle-check', 'color' => 'emerald', 'title' => 'Target Maret Tercapai', 'desc' => '93% capaian — bulan terbaik sejak Januari 2025.'],
                ['icon' => 'fa-users', 'color' => 'primary', 'title' => 'Pengguna Meningkat', 'desc' => '+24 pengguna baru aktif bertransaksi bulan ini.'],
            ];
        @endphp
        @foreach($notes as $n)
        <div class="p-4 rounded-xl bg-{{ $n['color'] }}-500/5 border border-{{ $n['color'] }}-500/15">
            <div class="flex items-center gap-2 mb-2">
                <i class="fa-solid {{ $n['icon'] }} text-{{ $n['color'] }}-400 text-sm"></i>
                <p class="text-white text-sm font-semibold">{{ $n['title'] }}</p>
            </div>
            <p class="text-slate-400 text-xs leading-relaxed">{{ $n['desc'] }}</p>
        </div>
        @endforeach
    </div>
</div>

@endsection
