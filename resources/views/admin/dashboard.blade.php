@extends('layouts.admin')

@section('title', 'Dashboard — SmartCanteen Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan sistem hari ini · ' . now()->isoFormat('dddd, D MMMM Y'))

@section('content')

{{-- ===== STAT CARDS ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    {{-- Total Pemasukan Hari Ini --}}
    <div class="relative glass-card rounded-2xl p-5 stat-card overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-emerald-500/10 -translate-y-6 translate-x-6 blur-xl"></div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                <i class="fa-solid fa-money-bill-wave text-emerald-400 text-base"></i>
            </div>
            <span class="text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full">+12.4%</span>
        </div>
        <p class="text-slate-400 text-xs font-medium mb-1">Pemasukan Hari Ini</p>
        <p class="text-white font-bold text-2xl leading-none">Rp 4,85<span class="text-lg">jt</span></p>
        <p class="text-slate-600 text-xs mt-1">vs kemarin Rp 4,31jt</p>
    </div>

    {{-- Total Transaksi --}}
    <div class="relative glass-card rounded-2xl p-5 stat-card overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-primary-500/10 -translate-y-6 translate-x-6 blur-xl"></div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-primary-500/10 border border-primary-500/20 flex items-center justify-center">
                <i class="fa-solid fa-receipt text-primary-400 text-base"></i>
            </div>
            <span class="text-xs font-semibold text-primary-400 bg-primary-500/10 border border-primary-500/20 px-2 py-0.5 rounded-full">+8 baru</span>
        </div>
        <p class="text-slate-400 text-xs font-medium mb-1">Total Transaksi</p>
        <p class="text-white font-bold text-2xl leading-none">248</p>
        <p class="text-slate-600 text-xs mt-1">Sepanjang bulan ini</p>
    </div>

    {{-- Pembayaran Pending --}}
    <div class="relative glass-card rounded-2xl p-5 stat-card overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-amber-500/10 -translate-y-6 translate-x-6 blur-xl"></div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                <i class="fa-solid fa-clock text-amber-400 text-base"></i>
            </div>
            <span class="text-xs font-semibold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-full">Perlu aksi</span>
        </div>
        <p class="text-slate-400 text-xs font-medium mb-1">Pembayaran Pending</p>
        <p class="text-white font-bold text-2xl leading-none">5</p>
        <p class="text-slate-600 text-xs mt-1">Menunggu verifikasi</p>
    </div>

    {{-- Total Pengguna Aktif --}}
    <div class="relative glass-card rounded-2xl p-5 stat-card overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-violet-500/10 -translate-y-6 translate-x-6 blur-xl"></div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                <i class="fa-solid fa-users text-violet-400 text-base"></i>
            </div>
            <span class="text-xs font-semibold text-violet-400 bg-violet-500/10 border border-violet-500/20 px-2 py-0.5 rounded-full">Aktif</span>
        </div>
        <p class="text-slate-400 text-xs font-medium mb-1">Pengguna Aktif</p>
        <p class="text-white font-bold text-2xl leading-none">312</p>
        <p class="text-slate-600 text-xs mt-1">Siswa & guru terdaftar</p>
    </div>
</div>

{{-- ===== CHART + RECENT ACTIVITY ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">

    {{-- Bar Chart Pemasukan 7 Hari --}}
    <div class="xl:col-span-2 glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-white font-bold text-base">Pemasukan Harian</h2>
                <p class="text-slate-500 text-xs mt-0.5">7 hari terakhir</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                <span class="text-slate-400 text-xs">Pemasukan (Rp)</span>
            </div>
        </div>

        {{-- Chart --}}
        <div class="flex items-end gap-2 h-36 mb-3" id="chart-bars">
            @php
                $days = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
                $values = [3200000, 4100000, 3800000, 5200000, 4850000, 2100000, 1500000];
                $max = max($values);
            @endphp
            @foreach($days as $i => $day)
            <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full relative group cursor-pointer">
                    <div class="w-full rounded-t-lg bg-primary-500/20 hover:bg-primary-500/30 transition-colors relative overflow-hidden"
                         style="height: {{ round(($values[$i] / $max) * 128) }}px">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary-600 to-primary-400 opacity-80"></div>
                        {{-- Tooltip --}}
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 border border-border text-white text-xs px-2 py-1 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                            Rp {{ number_format($values[$i], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <span class="text-slate-500 text-xs font-medium">{{ $day }}</span>
            </div>
            @endforeach
        </div>

        {{-- Y-axis labels --}}
        <div class="flex justify-between text-xs text-slate-700 font-mono border-t border-border/50 pt-2">
            <span>Rp 0</span>
            <span>Rp 2,6jt</span>
            <span>Rp 5,2jt</span>
        </div>
    </div>

    {{-- Status Breakdown --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="text-white font-bold text-base mb-1">Status Pembayaran</h2>
        <p class="text-slate-500 text-xs mb-5">Distribusi bulan ini</p>

        @php
            $statuses = [
                ['label' => 'Lunas', 'count' => 231, 'pct' => 93, 'color' => 'emerald'],
                ['label' => 'Pending', 'count' => 5, 'pct' => 2, 'color' => 'amber'],
                ['label' => 'Ditolak', 'count' => 12, 'pct' => 5, 'color' => 'red'],
            ];
        @endphp

        <div class="space-y-4">
            @foreach($statuses as $s)
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-{{ $s['color'] }}-500"></div>
                        <span class="text-slate-300 text-sm font-medium">{{ $s['label'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-slate-500 text-xs">{{ $s['count'] }}</span>
                        <span class="text-{{ $s['color'] }}-400 text-xs font-semibold font-mono">{{ $s['pct'] }}%</span>
                    </div>
                </div>
                <div class="h-2 bg-slate-700/50 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $s['color'] }}-500 rounded-full" style="width: {{ $s['pct'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 rounded-xl bg-slate-800/60 border border-border">
            <p class="text-slate-500 text-xs mb-1">Total Transaksi Bulan Ini</p>
            <p class="text-white font-bold text-xl">248 <span class="text-slate-500 font-normal text-sm">transaksi</span></p>
            <p class="text-emerald-400 text-xs mt-1 font-medium">
                <i class="fa-solid fa-arrow-trend-up mr-1"></i> Naik 18% dari bulan lalu
            </p>
        </div>
    </div>
</div>

{{-- ===== RECENT PENDING + QUICK ACTIONS ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

    {{-- Recent Pending Verifications --}}
    <div class="xl:col-span-2 glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-white font-bold text-base">Pending Verifikasi</h2>
                <p class="text-slate-500 text-xs mt-0.5">Membutuhkan perhatian segera</p>
            </div>
            <a href="{{ route('admin.verification') }}" class="text-xs text-primary-400 hover:text-primary-300 font-semibold flex items-center gap-1 transition-colors">
                Lihat Semua <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="space-y-3">
            @php
                $pending = [
                    ['id' => 'TRX-0821', 'name' => 'Budi Santoso', 'class' => 'XI IPA 2', 'amount' => 45000, 'time' => '08:24'],
                    ['id' => 'TRX-0822', 'name' => 'Sari Dewi', 'class' => 'X IPS 1', 'amount' => 32000, 'time' => '08:31'],
                    ['id' => 'TRX-0823', 'name' => 'Agus Pratama', 'class' => 'XII IPA 1', 'amount' => 67000, 'time' => '08:45'],
                    ['id' => 'TRX-0824', 'name' => 'Rina Melati', 'class' => 'X IPA 3', 'amount' => 28000, 'time' => '09:02'],
                    ['id' => 'TRX-0825', 'name' => 'Doni Kurniawan', 'class' => 'XI IPS 2', 'amount' => 53000, 'time' => '09:15'],
                ];
            @endphp

            @foreach($pending as $item)
            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/40 border border-amber-500/10 hover:border-amber-500/30 transition-all group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ substr($item['name'], 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate">{{ $item['name'] }}</p>
                    <p class="text-slate-500 text-xs">{{ $item['class'] }} · {{ $item['id'] }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-white text-sm font-bold font-mono">Rp {{ number_format($item['amount'], 0, ',', '.') }}</p>
                    <p class="text-slate-600 text-xs">{{ $item['time'] }} WIB</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="badge px-2 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400">PENDING</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="space-y-4">
        <div class="glass-card rounded-2xl p-5">
            <h2 class="text-white font-bold text-base mb-4">Rekap Cepat</h2>
            <div class="space-y-3">
                @php
                    $recaps = [
                        ['label' => 'Pemasukan Minggu Ini', 'value' => 'Rp 24,3jt', 'icon' => 'fa-wallet', 'color' => 'emerald'],
                        ['label' => 'Rata-rata per Transaksi', 'value' => 'Rp 38.500', 'icon' => 'fa-calculator', 'color' => 'primary'],
                        ['label' => 'Transaksi Terbanyak', 'value' => 'Jumat', 'icon' => 'fa-calendar-day', 'color' => 'violet'],
                        ['label' => 'Pengguna Bertransaksi', 'value' => '187 siswa', 'icon' => 'fa-user-check', 'color' => 'sky'],
                    ];
                @endphp
                @foreach($recaps as $r)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/40">
                    <div class="w-8 h-8 rounded-lg bg-{{ $r['color'] }}-500/10 border border-{{ $r['color'] }}-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid {{ $r['icon'] }} text-{{ $r['color'] }}-400 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-slate-500 text-xs">{{ $r['label'] }}</p>
                        <p class="text-white text-sm font-bold">{{ $r['value'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="glass-card rounded-2xl p-5">
            <h2 class="text-white font-bold text-sm mb-3">Aksi Cepat</h2>
            <div class="space-y-2">
                <a href="{{ route('admin.verification') }}" class="flex items-center gap-3 w-full p-3 rounded-xl bg-primary-500/10 border border-primary-500/20 hover:bg-primary-500/20 transition-all text-primary-300 text-sm font-medium">
                    <i class="fa-solid fa-circle-check text-primary-400"></i>
                    Verifikasi Pembayaran
                    <span class="ml-auto notif-badge text-white text-xs font-bold px-1.5">5</span>
                </a>
                <a href="{{ route('admin.report') }}" class="flex items-center gap-3 w-full p-3 rounded-xl bg-slate-800/40 hover:bg-slate-700/40 transition-all text-slate-300 text-sm font-medium">
                    <i class="fa-solid fa-file-invoice-dollar text-slate-400"></i>
                    Unduh Laporan
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
