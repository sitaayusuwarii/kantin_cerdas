@extends('layouts.admin')

@section('title', 'Dashboard — SmartCanteen Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan sistem hari ini · ' . now()->isoFormat('dddd, D MMMM Y'))

@section('content')

{{-- ===== STAT CARDS ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    {{-- Pemasukan Hari Ini --}}
    <div class="relative glass-card rounded-2xl p-5 stat-card overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-emerald-500/10 -translate-y-6 translate-x-6 blur-xl"></div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                <i class="fa-solid fa-money-bill-wave text-emerald-400 text-base"></i>
            </div>
            @if($incomePct >= 0)
                <span class="text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full">
                    +{{ $incomePct }}%
                </span>
            @else
                <span class="text-xs font-semibold text-red-400 bg-red-500/10 border border-red-500/20 px-2 py-0.5 rounded-full">
                    {{ $incomePct }}%
                </span>
            @endif
        </div>
        <p class="text-stone-500 text-xs font-medium mb-1">Pemasukan Hari Ini</p>
        <p class="text-stone-800 font-bold text-2xl leading-none">
            Rp {{ number_format($incomeToday, 0, ',', '.') }}
        </p>
        <p class="text-stone-400 text-xs mt-1">
           vs kemarin Rp {{ number_format($incomeYesterday, 0, ',', '.') }}
        </p>
    </div>

    {{-- Total Transaksi --}}
    <div class="relative glass-card rounded-2xl p-5 stat-card overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-primary-500/10 -translate-y-6 translate-x-6 blur-xl"></div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-primary-500/10 border border-primary-500/20 flex items-center justify-center">
                <i class="fa-solid fa-receipt text-primary-400 text-base"></i>
            </div>
            <span class="text-xs font-semibold text-primary-400 bg-primary-500/10 border border-primary-500/20 px-2 py-0.5 rounded-full">
                +{{ $newToday }} baru
            </span>
        </div>
        <p class="text-stone-500 text-xs font-medium mb-1">Total Transaksi</p>
        <p class="text-stone-800 font-bold text-2xl leading-none">{{ number_format($totalThisMonth) }}</p>
        <p class="text-stone-400 text-xs mt-1">Sepanjang bulan ini</p>
    </div>

    {{-- Pembayaran Pending --}}
    <div class="relative glass-card rounded-2xl p-5 stat-card overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-amber-500/10 -translate-y-6 translate-x-6 blur-xl"></div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                <i class="fa-solid fa-clock text-amber-400 text-base"></i>
            </div>
            @if($pendingCount > 0)
            <span class="text-xs font-semibold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-full">Perlu aksi</span>
            @else
            <span class="text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full">Semua clear</span>
            @endif
        </div>
        <p class="text-stone-500 text-xs font-medium mb-1">Pembayaran Pending</p>
        <p class="text-stone-800 font-bold text-2xl leading-none">{{ $pendingCount }}</p>
        <p class="text-stone-400 text-xs mt-1">Menunggu verifikasi</p>
    </div>

    {{-- Pengguna Aktif --}}
    <div class="relative glass-card rounded-2xl p-5 stat-card overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-violet-500/10 -translate-y-6 translate-x-6 blur-xl"></div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                <i class="fa-solid fa-users text-violet-400 text-base"></i>
            </div>
            <span class="text-xs font-semibold text-violet-400 bg-violet-500/10 border border-violet-500/20 px-2 py-0.5 rounded-full">Aktif</span>
        </div>
        <p class="text-stone-500 text-xs font-medium mb-1">Pengguna Aktif</p>
        <p class="text-stone-800 font-bold text-2xl leading-none">{{ number_format($activeUsers) }}</p>
        <p class="text-stone-400 text-xs mt-1">Customer terdaftar</p>
    </div>
</div>

{{-- ===== WIDGET TARGET HARIAN ===== --}}
<div class="glass-card rounded-2xl p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-stone-800 font-bold text-base">Target Pendapatan Harian</h2>
            <p class="text-stone-400 text-xs mt-0.5">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        @if(session('target_saved'))
            <span class="text-xs text-emerald-500 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full font-semibold">
                ✓ Target tersimpan
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-5">

        {{-- Pendapatan Hari Ini --}}
        <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200/50">
            <p class="text-xs text-stone-400 mb-1">Pendapatan Hari Ini</p>
            <p class="text-xl font-bold text-stone-800">
                Rp {{ number_format($incomeToday, 0, ',', '.') }}
            </p>
        </div>

        {{-- Target Harian --}}
        <div class="p-4 rounded-xl bg-orange-50/60 border border-orange-200/50">
            <p class="text-xs text-stone-400 mb-1">Target Harian</p>
            <p class="text-xl font-bold text-stone-800">
                {{ $targetAmount > 0 ? 'Rp ' . number_format($targetAmount, 0, ',', '.') : '—' }}
            </p>
        </div>

        {{-- Progress --}}
        <div class="p-4 rounded-xl border
            {{ $targetPct >= 100 ? 'bg-emerald-50/60 border-emerald-200/50' : ($targetPct >= 50 ? 'bg-amber-50/60 border-amber-200/50' : 'bg-red-50/60 border-red-200/50') }}">
            <p class="text-xs text-stone-400 mb-1">Progres Target</p>
            <p class="text-xl font-bold
                {{ $targetPct >= 100 ? 'text-emerald-600' : ($targetPct >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                {{ $targetAmount > 0 ? $targetPct . '%' : '—' }}
            </p>
        </div>

        {{-- Jumlah Pesanan --}}
        <div class="p-4 rounded-xl bg-violet-50/60 border border-violet-200/50">
            <p class="text-xs text-stone-400 mb-1">Pesanan Hari Ini</p>
            <p class="text-xl font-bold text-stone-800">{{ $newToday }} pesanan</p>
        </div>
    </div>

    {{-- Progress Bar --}}
    @if($targetAmount > 0)
    <div class="mb-5">
        <div class="flex justify-between text-xs text-stone-400 mb-1.5">
            <span>Rp 0</span>
            <span>Rp {{ number_format($targetAmount, 0, ',', '.') }}</span>
        </div>
        <div class="h-3 bg-stone-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500
                {{ $targetPct >= 100 ? 'bg-emerald-500' : ($targetPct >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                style="width: {{ $targetPct }}%">
            </div>
        </div>
        <div class="flex justify-between text-xs mt-1.5">
            <span class="{{ $targetPct >= 100 ? 'text-emerald-500' : ($targetPct >= 50 ? 'text-amber-500' : 'text-red-400') }} font-semibold">
                @if($targetPct >= 100)
                    🎉 Target tercapai!
                @elseif($targetPct >= 50)
                    🔥 Setengah jalan, terus semangat!
                @else
                    💪 Yuk kejar target!
                @endif
            </span>
            <span class="text-stone-400">
                Kurang Rp {{ number_format(max($targetAmount - $incomeToday, 0), 0, ',', '.') }}
            </span>
        </div>
    </div>
    @endif

    {{-- Form Set Target --}}
    <form action="{{ route('admin.dashboard.setTarget') }}" method="POST"
          class="flex items-center gap-3">
        @csrf @method('PATCH')
        <div class="relative flex-1 max-w-xs">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm font-medium">Rp</span>
            <input type="number"
                   name="target_amount"
                   value="{{ $targetAmount > 0 ? $targetAmount : '' }}"
                   placeholder="Masukkan target hari ini"
                   min="0"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-200 bg-white
                          text-sm text-stone-700 focus:outline-none focus:border-orange-400
                          focus:ring-2 focus:ring-orange-100 transition">
        </div>
        <button type="submit"
                class="px-4 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600
                       text-white text-sm font-semibold transition-colors flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Simpan Target
        </button>
    </form>
</div>

{{-- ===== CHART + STATUS BREAKDOWN ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">

    {{-- Bar Chart Pemasukan 7 Hari --}}
    <div class="xl:col-span-2 glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-stone-800 font-bold text-base">Pemasukan Harian</h2>
                <p class="text-stone-400 text-xs mt-0.5">7 hari terakhir</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                <span class="text-stone-500 text-xs">Pemasukan (Rp)</span>
            </div>
        </div>

        {{-- Chart --}}
        <div class="flex items-end gap-2 h-36 mb-3">
            @foreach($chartData as $d)
            @php $barH = $chartMax > 0 ? round(($d['amount'] / $chartMax) * 128) : 4; @endphp
            <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full relative group cursor-pointer">
                    <div class="w-full rounded-t-lg bg-primary-500/20 hover:bg-primary-500/30 transition-colors relative overflow-hidden"
                         style="height: {{ max($barH, 4) }}px">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary-600 to-primary-400 opacity-80"></div>
                        {{-- Tooltip --}}
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-white border border-orange-100 text-white text-xs px-2 py-1 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                            Rp {{ number_format($d['amount'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <span class="text-stone-400 text-xs font-medium">{{ $d['day'] }}</span>
            </div>
            @endforeach
        </div>

        {{-- Y-axis labels --}}
        @php $halfMax = $chartMax / 2; @endphp
        <div class="flex justify-between text-xs text-slate-700 font-mono border-t border-border/50 pt-2">
            <span>Rp 0</span>
            <span>Rp {{ number_format($halfMax / 1_000_000, 1, ',', '.') }}jt</span>
            <span>Rp {{ number_format($chartMax / 1_000_000, 1, ',', '.') }}jt</span>
        </div>
    </div>

    {{-- Status Breakdown --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="text-stone-800 font-bold text-base mb-1">Status Pembayaran</h2>
        <p class="text-stone-400 text-xs mb-5">Distribusi bulan ini</p>

        <div class="space-y-4">
            @foreach($statusBreakdown as $s)
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-{{ $s['color'] }}-500"></div>
                        <span class="text-stone-600 text-sm font-medium">{{ $s['label'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-stone-400 text-xs">{{ $s['count'] }}</span>
                        <span class="text-{{ $s['color'] }}-400 text-xs font-semibold font-mono">{{ $s['pct'] }}%</span>
                    </div>
                </div>
                <div class="h-2 bg-orange-100/50 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $s['color'] }}-500 rounded-full transition-all" style="width: {{ $s['pct'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 rounded-xl bg-orange-50/60 border border-border">
            <p class="text-stone-400 text-xs mb-1">Total Transaksi Bulan Ini</p>
            <p class="text-stone-800 font-bold text-xl">
                {{ number_format($totalThisMonth) }}
                <span class="text-stone-400 font-normal text-sm">transaksi</span>
            </p>
            @if($monthGrowthPct >= 0)
            <p class="text-emerald-400 text-xs mt-1 font-medium">
                <i class="fa-solid fa-arrow-trend-up mr-1"></i> Naik {{ $monthGrowthPct }}% dari bulan lalu
            </p>
            @else
            <p class="text-red-400 text-xs mt-1 font-medium">
                <i class="fa-solid fa-arrow-trend-down mr-1"></i> Turun {{ abs($monthGrowthPct) }}% dari bulan lalu
            </p>
            @endif
        </div>
    </div>
</div>

{{-- ===== PENDING LIST + QUICK ACTIONS ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

    {{-- Pending Verifications --}}
    <div class="xl:col-span-2 glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-stone-800 font-bold text-base">Pending Verifikasi</h2>
                <p class="text-stone-400 text-xs mt-0.5">Membutuhkan perhatian segera</p>
            </div>
            <a href="{{ route('admin.verification') }}"
               class="text-xs text-primary-400 hover:text-primary-300 font-semibold flex items-center gap-1 transition-colors">
                Lihat Semua <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if($pendingList->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center mb-3">
                <i class="fa-solid fa-circle-check text-emerald-400 text-xl"></i>
            </div>
            <p class="text-stone-800 font-semibold text-sm">Semua Beres!</p>
            <p class="text-stone-400 text-xs mt-1">Tidak ada pembayaran yang menunggu verifikasi</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($pendingList as $payment)
            <div class="flex items-center gap-3 p-3 rounded-xl bg-orange-50/40 border border-amber-500/10 hover:border-amber-500/30 transition-all">
                <div class="w-9 h-9 rounded-xl flex-shrink-0 overflow-hidden">
                    @if($payment->user->photo)
                        <img src="{{ asset('storage/' . $payment->user->photo) }}"
                            alt="{{ $payment->user->full_name }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($payment->user->full_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-stone-800 text-sm font-semibold truncate">{{ $payment->user->full_name }}</p>
                    <p class="text-stone-400 text-xs">
                        {{ $payment->user->kelas ?? $payment->user->class ?? '-' }}
                        · {{ $payment->order->order_number ?? '-' }}
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-stone-800 text-sm font-bold font-mono">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    <p class="text-stone-400 text-xs">{{ $payment->created_at->format('H:i') }} WIB</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="badge px-2 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs">PENDING</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Rekap Cepat + Aksi Cepat --}}
    <div class="space-y-4">
        <div class="glass-card rounded-2xl p-5">
            <h2 class="text-stone-800 font-bold text-base mb-4">Rekap Cepat</h2>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-orange-50/40">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-wallet text-emerald-400 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-stone-400 text-xs">Pemasukan Minggu Ini</p>
                        <p class="text-stone-800 text-sm font-bold">
                            Rp {{ number_format($incomeThisWeek / 1_000_000, 2, ',', '.') }}jt
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-xl bg-orange-50/40">
                    <div class="w-8 h-8 rounded-lg bg-primary-500/10 border border-primary-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-calculator text-primary-400 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-stone-400 text-xs">Rata-rata per Transaksi</p>
                        <p class="text-stone-800 text-sm font-bold">
                            Rp {{ number_format($avgPerTrx, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-xl bg-orange-50/40">
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 border border-violet-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-calendar-day text-violet-400 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-stone-400 text-xs">Hari Transaksi Terbanyak</p>
                        <p class="text-stone-800 text-sm font-bold">
                            {{ $busiestDay ? trim($busiestDay->day_name) : '-' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-xl bg-orange-50/40">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 border border-sky-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user-check text-sky-400 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-stone-400 text-xs">Pengguna Bertransaksi</p>
                        <p class="text-stone-800 text-sm font-bold">{{ number_format($usersTransacted) }} siswa</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-5">
            <h2 class="text-stone-800 font-bold text-sm mb-3">Aksi Cepat</h2>
            <div class="space-y-2">
                <a href="{{ route('admin.verification') }}"
                   class="flex items-center gap-3 w-full p-3 rounded-xl bg-primary-500/10 border border-primary-500/20 hover:bg-primary-500/20 transition-all text-primary-300 text-sm font-medium">
                    <i class="fa-solid fa-circle-check text-primary-400"></i>
                    Verifikasi Pembayaran
                    @if($pendingCount > 0)
                    <span class="ml-auto notif-badge text-white text-xs font-bold px-1.5">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.laporan-keuangan') }}"
                   class="flex items-center gap-3 w-full p-3 rounded-xl bg-orange-50/40 hover:bg-orange-100/40 transition-all text-stone-600 text-sm font-medium">
                    <i class="fa-solid fa-file-invoice-dollar text-stone-500"></i>
                    Unduh Laporan
                </a>
            </div>
        </div>
    </div>
</div>

@endsection