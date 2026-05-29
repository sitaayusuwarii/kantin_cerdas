@extends('layouts.pengelola')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional kantin hari ini')

@section('content')

{{-- ── STAT CARDS ───────────────────────────────────────── --}}
@php
$diffLabel = $diffOrders >= 0 ? '+' . $diffOrders . ' dari kemarin' : $diffOrders . ' dari kemarin';
$stats = [
    [
        'label'  => 'Total Pesanan',
        'value'  => $totalToday,
        'sub'    => $diffLabel,
        'icon'   => 'fa-bag-shopping',
        'trend'  => $totalYesterday > 0
                        ? round((($totalToday - $totalYesterday) / $totalYesterday) * 100) . '%'
                        : '-',
        'color'  => 'bg-primary',
    ],
    [
        'label'  => 'Sedang Diproses',
        'value'  => $diproses,
        'sub'    => $diproses > 0 ? $diproses . ' perlu perhatian' : 'Semua lancar',
        'icon'   => 'fa-fire-burner',
        'trend'  => $diproses,
        'color'  => 'bg-amber-500',
    ],
    [
        'label'  => 'Selesai',
        'value'  => $selesai,
        'sub'    => $completionRate . '% completion rate',
        'icon'   => 'fa-circle-check',
        'trend'  => $completionRate . '%',
        'color'  => 'bg-emerald-500',
    ],
    [
        'label'  => 'Pendapatan Hari Ini',
        'value'  => 'Rp ' . number_format($pendapatanHariIni / 1000, 0, ',', '.') . 'rb',
        'sub'    => 'Total transaksi selesai',
        'icon'   => 'fa-coins',
        'trend'  => null,
        'color'  => 'bg-orange-400',
    ],
];
@endphp

<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-7">
    @foreach($stats as $stat)
    <div class="bg-white rounded-3xl border border-orange-100 p-5 shadow-sm hover:shadow-md transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="w-11 h-11 {{ $stat['color'] }} rounded-2xl flex items-center justify-center shadow-sm">
                <i class="fa-solid {{ $stat['icon'] }} text-white text-sm"></i>
            </div>
            <span class="bg-orange-50 text-primary text-[11px] font-semibold px-2.5 py-1 rounded-full">
                {{ $stat['trend'] ?? '-' }}
            </span>
        </div>
        <h3 class="text-2xl font-bold text-darkText">{{ $stat['value'] }}</h3>
        <p class="text-sm font-medium text-gray-700 mt-1">{{ $stat['label'] }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $stat['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── MAIN GRID ────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

    {{-- Recent Orders (xl: 3/5) --}}
    <div class="xl:col-span-3 bg-white rounded-3xl border border-orange-100 shadow-sm overflow-hidden">
        <div class="flex justify-between items-center px-6 py-5 border-b border-orange-100">
            <h2 class="font-semibold text-darkText text-base">Pesanan Terbaru</h2>
            <a href="{{ url('/pengelola/orders') }}"
               class="text-sm text-primary font-semibold hover:opacity-80">
                Lihat semua →
            </a>
        </div>

        @php
        $statusMap = [
            'baru'       => 'bg-orange-50 text-primary',
            'diproses'   => 'bg-amber-50 text-amber-700',
            'selesai'    => 'bg-emerald-50 text-emerald-700',
            'dibatalkan' => 'bg-red-50 text-red-600',
        ];
        $statusLabel = [
            'baru'       => 'Baru',
            'diproses'   => 'Diproses',
            'selesai'    => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
        @endphp

        {{-- Desktop --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-orange-50 border-b border-orange-100">
                        <th class="text-left px-6 py-4 text-xs text-gray-500 uppercase">No. Order</th>
                        <th class="text-left px-4 py-4 text-xs text-gray-500 uppercase">Pelanggan</th>
                        <th class="text-left px-4 py-4 text-xs text-gray-500 uppercase">Menu</th>
                        <th class="text-right px-4 py-4 text-xs text-gray-500 uppercase">Total</th>
                        <th class="text-center px-4 py-4 text-xs text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-50">
                    @forelse($recentOrders as $r)
                    <tr class="hover:bg-orange-50/40">
                        <td class="px-6 py-4 font-semibold text-darkText">#{{ $r->order_number }}</td>
                        <td class="px-4 py-4">
                            <p class="font-medium text-darkText">{{ $r->user->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $r->user->phone ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-4 text-gray-600">
                            {{ $r->items->take(2)->map(fn($i) => $i->menu->name ?? '-')->join(', ') }}
                            @if($r->items->count() > 2)
                                <span class="text-gray-400">+{{ $r->items->count() - 2 }} lainnya</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right font-semibold text-darkText">
                            Rp {{ number_format($r->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="{{ $statusMap[$r->status] ?? 'bg-gray-100 text-gray-600' }}
                                        text-xs px-3 py-1 rounded-full font-semibold">
                                {{ $statusLabel[$r->status] ?? $r->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">
                            Belum ada pesanan hari ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="sm:hidden divide-y divide-orange-50">
            @forelse($recentOrders as $r)
            <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold text-xs text-darkText truncate">
                        #{{ $r->order_number }} · {{ $r->user->name ?? '-' }}
                    </p>
                    <p class="text-[11px] text-gray-400 mt-0.5 truncate">
                        {{ $r->items->first()?->menu->name ?? '-' }}
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-semibold text-xs text-darkText">
                        Rp {{ number_format($r->total_price / 1000, 0) }}rb
                    </p>
                    <span class="{{ $statusMap[$r->status] ?? 'bg-gray-100 text-gray-600' }}
                                text-[10px] font-semibold px-2 py-0.5 rounded-full mt-1 inline-block">
                        {{ $statusLabel[$r->status] ?? $r->status }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-sm">
                Belum ada pesanan hari ini
            </div>
            @endforelse
        </div>
    </div>

    {{-- Top Menu + Mini Chart (xl: 2/5) --}}
    <div class="xl:col-span-2 bg-white rounded-3xl border border-orange-100 shadow-sm">
        <div class="px-6 py-5 border-b border-orange-100">
            <h2 class="font-semibold text-darkText text-base">Menu Terlaris Hari Ini</h2>
        </div>
        <div class="p-5 space-y-5">

            @forelse($topMenus as $m)
            <div>
                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold text-primary">{{ $m['rank'] }}</span>
                        <span>{{ $m['emoji'] }}</span>
                        <div>
                            <p class="text-sm font-semibold text-darkText">{{ $m['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $m['sold'] }} porsi</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-primary">{{ $m['pct'] }}%</span>
                </div>
                <div class="w-full h-2 bg-orange-50 rounded-full overflow-hidden">
                    <div class="bg-primary h-full rounded-full" style="width: {{ $m['pct'] }}%"></div>
                </div>
            </div>
            @empty
            <div class="py-6 text-center text-gray-400 text-sm">
                Belum ada penjualan hari ini
            </div>
            @endforelse

            {{-- Hourly bar chart (dari doc 5, disesuaikan warna orange) --}}
            <div class="mt-5 pt-4 border-t border-orange-100">
                <p class="text-[10px] font-semibold tracking-widest text-gray-400 uppercase mb-3">
                    Pesanan per Jam
                </p>
                @php $maxBar = max(array_merge($hourlyBars, [1])); @endphp
                <div class="flex items-end gap-1 h-16">
                    @foreach($hourlyBars as $b)
                    <div class="flex-1 {{ $b === $maxBar && $b > 0 ? 'bg-primary' : 'bg-orange-100' }}
                                rounded-t-sm hover:bg-orange-300 transition-colors cursor-default"
                         style="height:{{ $maxBar > 0 ? round(($b / $maxBar) * 100) : 0 }}%">
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-[9px] text-gray-400">07:00</span>
                    <span class="text-[9px] text-gray-400">12:00</span>
                    <span class="text-[9px] text-gray-400">18:00</span>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ── QUICK ACTIONS ────────────────────────────────────── --}}
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
    @php
    $actions = [
        ['url'=>'/pengelola/orders',   'icon'=>'fa-bell',       'label'=>'Pesanan Baru',  'badge'=> $badgePesanan],
        ['url'=>'/pengelola/delivery', 'icon'=>'fa-truck-fast', 'label'=>'Pengiriman',     'badge'=> $badgePengiriman],
        ['url'=>'/pengelola/menu',     'icon'=>'fa-utensils',   'label'=>'Kelola Menu',    'badge'=> null],
        ['url'=>'/pengelola/report',   'icon'=>'fa-chart-bar',  'label'=>'Laporan',        'badge'=> null],
    ];
    @endphp
    @foreach($actions as $act)
    <a href="{{ url($act['url']) }}"
       class="bg-white border border-orange-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all flex items-center gap-3">
        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-sm">
            <i class="fa-solid {{ $act['icon'] }} text-white text-sm"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-darkText">{{ $act['label'] }}</p>
            @if($act['badge'])
            <p class="text-xs text-gray-400">{{ $act['badge'] }} item aktif</p>
            @else
            <p class="text-xs text-gray-400">Buka →</p>
            @endif
        </div>
    </a>
    @endforeach
</div>

@endsection