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
                        : null,
        'up'     => $totalToday >= $totalYesterday,
        'accent' => 'forest',
    ],
    [
        'label'  => 'Sedang Diproses',
        'value'  => $diproses,
        'sub'    => $diproses > 0 ? $diproses . ' perlu perhatian' : 'Semua lancar',
        'icon'   => 'fa-fire-burner',
        'trend'  => null,
        'up'     => null,
        'accent' => 'amber',
    ],
    [
        'label'  => 'Selesai',
        'value'  => $selesai,
        'sub'    => $completionRate . '% completion rate',
        'icon'   => 'fa-circle-check',
        'trend'  => $completionRate . '%',
        'up'     => true,
        'accent' => 'green',
    ],
    [
        'label'  => 'Pendapatan Hari Ini',
        'value'  => 'Rp ' . number_format($pendapatanHariIni / 1000, 0, ',', '.') . 'rb',
        'sub'    => 'Total transaksi selesai',
        'icon'   => 'fa-coins',
        'trend'  => null,
        'up'     => true,
        'accent' => 'teal',
    ],
];
$accentMap = [
    'forest' => ['bg'=>'bg-forest-700', 'text'=>'text-forest-50',   'ring'=>'bg-forest-100',  'rtxt'=>'text-forest-700'],
    'amber'  => ['bg'=>'bg-amber-600',  'text'=>'text-amber-50',    'ring'=>'bg-amber-100',   'rtxt'=>'text-amber-700'],
    'green'  => ['bg'=>'bg-emerald-600','text'=>'text-emerald-50',  'ring'=>'bg-emerald-100', 'rtxt'=>'text-emerald-700'],
    'teal'   => ['bg'=>'bg-teal-600',   'text'=>'text-teal-50',     'ring'=>'bg-teal-100',    'rtxt'=>'text-teal-700'],
];
@endphp

<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-7">
    @foreach($stats as $i => $s)
    @php $a = $accentMap[$s['accent']]; @endphp
    <div class="stat-card bg-cream-50 rounded-2xl p-5 shadow-sm border border-cream-200"
         style="animation-delay:{{ $i * 70 }}ms">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 {{ $a['bg'] }} rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                <i class="fa-solid {{ $s['icon'] }} {{ $a['text'] }} text-sm"></i>
            </div>
            @if($s['trend'])
            <span class="{{ $a['ring'] }} {{ $a['rtxt'] }} text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5">
                @if($s['up'])<i class="fa-solid fa-arrow-trend-up text-[9px]"></i>@endif
                {{ $s['trend'] }}
            </span>
            @else
            <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                <i class="fa-solid fa-exclamation text-[9px]"></i>
            </span>
            @endif
        </div>
        <p class="font-display font-bold text-2xl sm:text-3xl text-forest-900 leading-none">
            {{ $s['value'] }}
        </p>
        <p class="text-forest-600 text-xs font-medium mt-1.5">{{ $s['label'] }}</p>
        <p class="text-forest-400 text-[10px] mt-0.5">{{ $s['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── MAIN GRID ────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

    {{-- Recent Orders (xl: 3/5) --}}
    <div class="xl:col-span-3 bg-cream-50 rounded-2xl shadow-sm border border-cream-200 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200">
            <h2 class="font-display font-semibold text-forest-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-bell text-forest-500 text-sm"></i>
                Pesanan Terbaru
            </h2>
            <a href="{{ url('/pengelola/orders') }}"
               class="text-xs text-forest-600 hover:text-forest-800 font-semibold transition-colors">
                Lihat semua →
            </a>
        </div>

        @php
        $statusMap = [
            'baru'       => 'bg-forest-100 text-forest-700',
            'diproses'   => 'bg-amber-100 text-amber-700',
            'selesai'    => 'bg-emerald-100 text-emerald-700',
            'dibatalkan' => 'bg-red-100 text-red-700',
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
                    <tr class="bg-cream-100/70">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">No. Order</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Pelanggan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Menu</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Total</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200">
                    @forelse($recentOrders as $r)
                    <tr class="hover:bg-cream-100/60 transition-colors">
                        <td class="px-6 py-3.5 font-display font-semibold text-sm text-forest-800">
                            #{{ $r->order_number }}
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-xs text-forest-900">{{ $r->user->name ?? '-' }}</p>
                            <p class="text-[10px] text-forest-400">{{ $r->user->phone ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-forest-600">
                            {{ $r->items->take(2)->map(fn($i) => $i->menu->name ?? '-')->join(', ') }}
                            @if($r->items->count() > 2)
                                <span class="text-forest-400">+{{ $r->items->count() - 2 }} lainnya</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right font-display font-semibold text-sm text-forest-900">
                            Rp {{ number_format($r->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1
                                {{ $statusMap[$r->status] ?? 'bg-gray-100 text-gray-700' }}
                                text-[10px] font-bold px-2.5 py-1 rounded-full">
                                {{ $statusLabel[$r->status] ?? $r->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-forest-400 text-sm">
                            Belum ada pesanan hari ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="sm:hidden divide-y divide-cream-200">
            @forelse($recentOrders as $r)
            <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-display font-semibold text-xs text-forest-800 truncate">
                        #{{ $r->order_number }} · {{ $r->user->name ?? '-' }}
                    </p>
                    <p class="text-[11px] text-forest-400 mt-0.5 truncate">
                        {{ $r->items->first()?->menu->name ?? '-' }}
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-display font-semibold text-xs text-forest-900">
                        Rp {{ number_format($r->total_price / 1000, 0) }}rb
                    </p>
                    <span class="inline-flex items-center
                        {{ $statusMap[$r->status] ?? 'bg-gray-100 text-gray-700' }}
                        text-[10px] font-bold px-2 py-0.5 rounded-full mt-1">
                        {{ $statusLabel[$r->status] ?? $r->status }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-forest-400 text-sm">
                Belum ada pesanan hari ini
            </div>
            @endforelse
        </div>
    </div>

    {{-- Top Menu + Mini Chart (xl: 2/5) --}}
    <div class="xl:col-span-2 bg-cream-50 rounded-2xl shadow-sm border border-cream-200">
        <div class="px-6 py-4 border-b border-cream-200">
            <h2 class="font-display font-semibold text-forest-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-fire text-amber-600 text-sm"></i>
                Menu Terlaris Hari Ini
            </h2>
        </div>
        <div class="p-5 space-y-4">

            @forelse($topMenus as $m)
            <div>
                <div class="flex items-center gap-2.5 mb-1.5">
                    <span class="font-display font-bold text-lg
                        {{ $m['rank']===1 ? 'text-amber-600' : ($m['rank']===2 ? 'text-forest-500' : 'text-forest-400') }}
                        w-5 text-center flex-shrink-0">{{ $m['rank'] }}</span>
                    <span class="text-xl">{{ $m['emoji'] }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-forest-900 truncate">{{ $m['name'] }}</p>
                        <p class="text-[10px] text-forest-400">{{ $m['sold'] }} porsi</p>
                    </div>
                    <span class="font-display font-bold text-sm text-forest-700 flex-shrink-0">
                        {{ $m['pct'] }}%
                    </span>
                </div>
                <div class="ml-8 h-1.5 bg-cream-200 rounded-full overflow-hidden">
                    <div class="{{ $m['barColor'] }} h-full rounded-full bar-anim"
                         style="width:{{ $m['pct'] }}%"></div>
                </div>
            </div>
            @empty
            <div class="py-6 text-center text-forest-400 text-sm">
                Belum ada penjualan hari ini
            </div>
            @endforelse

            {{-- Hourly bar chart --}}
            <div class="mt-5 pt-4 border-t border-cream-200">
                <p class="text-[10px] font-semibold tracking-widest text-forest-400 uppercase mb-3">
                    Pesanan per Jam
                </p>
                @php $maxBar = max(array_merge($hourlyBars, [1])); @endphp
                <div class="flex items-end gap-1 h-16">
                    @foreach($hourlyBars as $b)
                    <div class="flex-1 {{ $b === $maxBar && $b > 0 ? 'bg-forest-500' : 'bg-forest-200' }}
                                rounded-t-sm hover:bg-forest-400 transition-colors cursor-default"
                         style="height:{{ $maxBar > 0 ? round(($b / $maxBar) * 100) : 0 }}%">
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-[9px] text-forest-400">07:00</span>
                    <span class="text-[9px] text-forest-400">12:00</span>
                    <span class="text-[9px] text-forest-400">18:00</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── QUICK ACTIONS ────────────────────────────────────── --}}
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
    @php
    $actions = [
        ['url'=>'/pengelola/orders',   'icon'=>'fa-bell',       'label'=>'Pesanan Baru',  'badge'=> $badgePesanan,    'color'=>'bg-forest-700'],
        ['url'=>'/pengelola/delivery', 'icon'=>'fa-truck-fast', 'label'=>'Pengiriman',     'badge'=> $badgePengiriman, 'color'=>'bg-amber-600'],
        ['url'=>'/pengelola/menu',     'icon'=>'fa-utensils',   'label'=>'Kelola Menu',    'badge'=> null,             'color'=>'bg-teal-700'],
        ['url'=>'/pengelola/report',   'icon'=>'fa-chart-bar',  'label'=>'Laporan',        'badge'=> null,             'color'=>'bg-forest-500'],
    ];
    @endphp
    @foreach($actions as $act)
    <a href="{{ url($act['url']) }}"
       class="bg-cream-50 border border-cream-200 rounded-2xl p-4 flex items-center gap-3
              hover:shadow-md hover:border-forest-200 transition-all group shadow-sm">
        <div class="w-10 h-10 {{ $act['color'] }} rounded-xl flex items-center justify-center shadow flex-shrink-0
                    group-hover:scale-105 transition-transform">
            <i class="fa-solid {{ $act['icon'] }} text-white text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-forest-800 truncate">{{ $act['label'] }}</p>
            @if($act['badge'])
            <p class="text-[10px] text-forest-500">{{ $act['badge'] }} item aktif</p>
            @else
            <p class="text-[10px] text-forest-500 group-hover:text-forest-700 transition-colors">Buka →</p>
            @endif
        </div>
    </a>
    @endforeach
</div>

@endsection