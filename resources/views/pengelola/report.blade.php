@extends('layouts.pengelola')
@section('title', 'Laporan Favorit')
@section('page-title', 'Laporan Menu Favorit')
@section('page-subtitle', 'Analisis penjualan untuk perencanaan stok')

@section('content')

{{-- ── CONTROLS ─────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex gap-2 flex-wrap">
        @foreach([
            'hari'   => 'Hari Ini',
            'minggu' => 'Minggu Ini',
            'bulan'  => 'Bulan Ini',
        ] as $key => $label)
        <a href="{{ route('pengelola.report', ['period' => $key]) }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
               {{ $period === $key
                   ? 'bg-forest-800 text-cream-100 shadow-md'
                   : 'bg-cream-50 border border-cream-300 text-forest-600 hover:border-forest-400' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Export Excel --}}
    <a href="{{ route('pengelola.report.export', ['period' => $period]) }}"
       class="btn-primary text-white font-semibold text-sm px-5 py-2.5 rounded-xl
              flex items-center gap-2 shadow-lg flex-shrink-0 no-underline">
        <i class="fa-solid fa-file-excel text-xs"></i>Export Excel
    </a>
</div>

{{-- ── Sub-label periode aktif ───────────────────────────── --}}
<p class="text-xs text-forest-400 -mt-3 mb-5 font-medium">
    <i class="fa-regular fa-calendar text-forest-300 mr-1"></i>
    {{ $periodLabel }}
</p>

{{-- ── SUMMARY CARDS ───────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-7">
    @php
    $summaries = [
        [
            'label' => 'Total Transaksi',
            'val' => $totalTransactions,
            'sub' => 'Semua transaksi',
            'icon' => 'fa-receipt',
            'bg' => 'bg-forest-700'
        ],

        [
            'label' => 'Pendapatan',
            'val' => 'Rp ' . number_format($totalRevenue,0,',','.'),
            'sub' => 'Total pendapatan',
            'icon' => 'fa-coins',
            'bg' => 'bg-amber-600'
        ],

        [
            'label' => 'Menu Aktif',
            'val' => $activeMenus,
            'sub' => 'Menu tersedia',
            'icon' => 'fa-utensils',
            'bg' => 'bg-teal-700'
        ],

        [
            'label' => 'Menu Terlaris',
            'val' => $bestMenu?->name ?? '-',
            'sub' => ($bestMenu?->total_sold ?? 0) . ' porsi terjual',
            'icon' => 'fa-fire',
            'bg' => 'bg-red-600'
        ],
    ];
    @endphp
    @foreach($summaries as $s)
    <div class="bg-cream-50 rounded-2xl p-4 shadow-sm border border-cream-200">
        <div class="w-9 h-9 {{ $s['bg'] }} rounded-xl flex items-center justify-center shadow mb-3">
            <i class="fa-solid {{ $s['icon'] }} text-white text-sm"></i>
        </div>
        <p class="font-display font-bold text-xl text-forest-900 truncate">{{ $s['val'] }}</p>
        <p class="text-xs text-forest-600 font-medium mt-0.5">{{ $s['label'] }}</p>
        <p class="text-[10px] text-forest-400 mt-0.5">{{ $s['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── MAIN GRID ────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

    {{-- Top 5 Bar Chart (xl: 3/5) --}}
    <div class="xl:col-span-3 bg-cream-50 rounded-2xl shadow-sm border border-cream-200 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-display font-semibold text-forest-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-ranking-star text-amber-600 text-sm"></i>
                    Top 5 Menu Favorit
                </h2>
                <p class="text-forest-400 text-xs mt-0.5">{{ $periodLabel }} · semua kategori</p>
            </div>
        </div>

        <div class="space-y-5">
            @foreach($topMenus as $m)
            <div>
                <div class="flex items-center gap-3 mb-1.5">
                    <span class="font-display font-bold text-base w-5 text-center flex-shrink-0
                        {{ $m['r']===1 ? 'text-amber-600' : 'text-forest-400' }}">
                        {{ $m['r'] }}
                    </span>
                    <span class="text-xl">{{ $m['e'] }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-forest-900 truncate">{{ $m->menu->name }}</p>
                        <p class="text-[10px] text-forest-400">{{ $m->menu->category->name ?? '-' }}</p>
                    </div>
                    <p class="font-display font-bold text-sm text-forest-800 flex-shrink-0">
                        {{ $m['sold'] }} porsi
                    </p>
                    <p class="font-display font-semibold text-sm text-emerald-700 flex-shrink-0 hidden sm:block">
                        Rp {{ number_format($m['rev'],0,',','.') }}
                    </p>
                </div>
                <div class="ml-8 flex items-center gap-2">
                    <div class="flex-1 h-2 bg-cream-200 rounded-full overflow-hidden">
                        <div class="{{ $m['bar'] }} h-full rounded-full bar-anim" style="width:{{ $m['pct'] }}%"></div>
                    </div>
                    <span class="text-[10px] font-semibold text-forest-500 w-8 text-right">{{ $m['pct'] }}%</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right Panels (xl: 2/5) --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- Tren Harian --}}
        <div class="bg-cream-50 rounded-2xl shadow-sm border border-cream-200 p-5">
            <h3 class="font-display font-semibold text-sm text-forest-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-forest-500 text-xs"></i>
               Tren Harian ({{ Carbon\Carbon::now()->translatedFormat('M') }})</h3>

            @php $trendBars = [22,28,31,25,29,35,38,30,27,33,40,38,35,42,44,38,41,47,39,36]; @endphp
            <div class="flex items-end gap-1 h-20">
                @foreach($trendBars as $ti => $tv)
                <div class="flex-1 group relative cursor-default">
                    <div class="{{ $tv === max($trendBars) ? 'bg-forest-600' : 'bg-forest-200' }}
                                rounded-t-sm hover:bg-forest-400 transition-colors"
                         style="height:{{ round(($tv/max($trendBars))*100) }}%">
                    </div>
                    <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-forest-900 text-cream-100
                                text-[9px] px-1.5 py-0.5 rounded hidden group-hover:block whitespace-nowrap z-10">
                        {{ $tv }}
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-1.5">
                <span class="text-[9px] text-forest-400">1 Apr</span>
                <span class="text-[9px] text-forest-400">20 Apr</span>
            </div>
        </div>

        {{-- Kategori --}}
        <div class="bg-cream-50 rounded-2xl shadow-sm border border-cream-200 p-5">
            <h3 class="font-display font-semibold text-sm text-forest-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-forest-500 text-xs"></i>
                Distribusi Kategori
            </h3>

            @php
            // Kelompokkan berdasarkan kategori
            $grouped = $topMenus->groupBy(function ($m) {
                return $m->menu->category->name ?? 'Lainnya';
            });

            // total semua terjual
            $total = $topMenus->sum('sold');

            // mapping jadi format kategori
            $cats = $grouped->map(function ($items, $name) use ($total) {
                $sold = $items->sum('sold');
                return [
                    'name' => $name,
                    'pct' => $total ? round(($sold / $total) * 100) : 0,
                ];
            })->values()->toArray();

            // warna (biar tetap cakep)
            $colors = ['bg-forest-600','bg-amber-500','bg-teal-500','bg-red-400'];

            foreach ($cats as $i => $c) {
                $cats[$i]['bar'] = $colors[$i % count($colors)];
            }
            @endphp
            <div class="flex h-3 rounded-full overflow-hidden gap-0.5 mb-3">
                @foreach($cats as $c)
                <div class="{{ $c['bar'] }} rounded-full" style="width:{{ $c['pct'] }}%"></div>
                @endforeach
            </div>
            <div class="space-y-2">
                @foreach($cats as $c)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full {{ $c['bar'] }}"></div>
                        <span class="text-xs text-forest-700 font-medium">{{ $c['name'] }}</span>
                    </div>
                    <span class="font-display font-bold text-sm text-forest-800">{{ $c['pct'] }}%</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Rekomendasi Stok --}}
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
            <h3 class="font-display font-semibold text-sm text-amber-900 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-xs"></i>
                Rekomendasi Stok
            </h3>
            <div class="space-y-2.5">

            @foreach($topMenus as $m)
            @php
                // logic rekomendasi berdasarkan ranking
                if ($loop->iteration === 1) {
                    $action = 'Tambah stok +15%';
                    $lvl = 'high';
                } elseif ($loop->iteration === 2) {
                    $action = 'Pertahankan stok';
                    $lvl = 'ok';
                } else {
                    $action = 'Normal';
                    $lvl = 'low';
                }

            @endphp

            <div class="flex items-center gap-2.5 bg-white rounded-xl p-2.5 border border-amber-100">

                {{-- indikator --}}
                <div class="w-2 h-2 rounded-full flex-shrink-0
                    {{ $lvl==='high'?'bg-amber-500':($lvl==='low'?'bg-red-500':'bg-forest-500') }}">
                </div>

                {{-- nama menu --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-forest-900 truncate">
                        {{ $m->menu->name }}
                    </p>

                    <p class="text-[10px]
                        {{ $lvl==='low'?'text-red-500':'text-forest-500' }}">
                        {{ $action }}
                    </p>
                </div>

                {{-- icon --}}
                <i class="fa-solid text-xs flex-shrink-0
                    {{ $lvl==='high'
                        ? 'fa-arrow-trend-up text-amber-500'
                        : ($lvl==='low'
                            ? 'fa-exclamation text-red-400'
                            : 'fa-check text-forest-500') }}">
                </i>

            </div>

            @endforeach

        </div>
                </div>
            </div>
        </div>

{{-- ── DETAIL TABLE ─────────────────────────────────────── --}}
<div class="mt-6 bg-cream-50 rounded-2xl shadow-sm border border-cream-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-cream-200 flex items-center justify-between">
        <h3 class="font-display font-semibold text-forest-900 text-sm flex items-center gap-2">
            <i class="fa-solid fa-table-list text-forest-500 text-xs"></i>Detail Semua Menu
        </h3>
        <span class="text-xs text-forest-400">{{ $periodLabel }}</span>
    </div>

    {{-- Desktop --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-cream-100/80">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Menu</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Terjual</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Pendapatan</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Tren</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Rekomendasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cream-200">
                @foreach($topMenus as $m)
                <tr class="hover:bg-cream-100/50 transition-colors">
                    <td class="px-6 py-3.5 flex items-center gap-2.5">
                        <span class="text-xl">{{ $m['e'] }}</span>
                        <div>
                            <p class="font-semibold text-sm text-forest-900">{{ $m->menu->name }}</p>
                            <p class="text-[10px] text-forest-400">{{ $m->menu->category->name ?? '-' }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-center font-display font-bold text-forest-900">{{ $m->sold }}</td>
                    <td class="px-4 py-3.5 text-right font-display font-bold text-emerald-700">
                        Rp {{ number_format($m->revenue,0,',','.') }}
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="text-xs text-emerald-700 bg-emerald-100 font-semibold px-2.5 py-1 rounded-xl flex items-center gap-1 justify-center">
                            <i class="fa-solid fa-arrow-trend-up text-[10px]"></i>+{{ 10 + $m['r'] }}%
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @if($m['r']===1)
                        <span class="text-xs text-amber-700 bg-amber-100 font-semibold px-2.5 py-1 rounded-xl">Tambah +15%</span>
                        @elseif($m['r']===2)
                        <span class="text-xs text-forest-700 bg-forest-100 font-semibold px-2.5 py-1 rounded-xl">Pertahankan</span>
                        @else
                        <span class="text-xs text-teal-700 bg-teal-100 font-semibold px-2.5 py-1 rounded-xl">Normal</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile --}}
    <div class="sm:hidden divide-y divide-cream-200">
        @foreach($topMenus as $m)
        <div class="px-4 py-3.5 flex items-center gap-3">
            <span class="font-display font-bold text-base w-5 text-center flex-shrink-0
                {{ $m['r']===1?'text-amber-600':'text-forest-400' }}">{{ $m['r'] }}</span>
            <span class="text-xl">{{ $m['e'] }}</span>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-forest-900 truncate">{{ $m->menu->name }}</p>
                <p class="text-[10px] text-forest-400">{{ $m->sold }} terjual</p>
            </div>
            <p class="font-display font-bold text-sm text-emerald-700 flex-shrink-0">
                Rp {{ number_format($m->revenue/1000,0) }}rb
            </p>
        </div>
        @endforeach
    </div>
</div>

@endsection
