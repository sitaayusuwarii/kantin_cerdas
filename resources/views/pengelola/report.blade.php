@extends('layouts.pengelola')
@section('title', 'Laporan Favorit')
@section('page-title', 'Laporan Menu Favorit')
@section('page-subtitle', 'Analisis penjualan untuk perencanaan stok')

@section('content')

{{-- ── CONTROLS ─────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-7">
    <div class="flex gap-2 flex-wrap">
        @foreach(['Hari Ini','Minggu Ini','Bulan Ini'] as $i => $p)
        <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
            {{ $i===2
                ? 'bg-primary text-white shadow-md'
                : 'bg-white border border-borderSoft text-darkText hover:border-primary hover:text-primary'
            }}">
            {{ $p }}
        </button>
        @endforeach
    </div>

    <button class="btn-primary text-white font-semibold text-sm px-5 py-3 rounded-2xl flex items-center gap-2 shadow-md hover:shadow-lg transition-all flex-shrink-0">
        <i class="fa-solid fa-file-pdf text-xs"></i>
        Export PDF
    </button>
</div>

{{-- ── SUMMARY CARDS ───────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-7">
    @php
    $summaries = [
        ['label'=>'Total Transaksi','val'=>'847','sub'=>'Bulan April','icon'=>'fa-bag-shopping','bg'=>'bg-forest-700'],
        ['label'=>'Pendapatan','val'=>'Rp 9,8jt','sub'=>'+12% vs bulan lalu','icon'=>'fa-coins','bg'=>'bg-amber-600'],
        ['label'=>'Menu Aktif','val'=>'10','sub'=>'dari 12 total','icon'=>'fa-utensils','bg'=>'bg-teal-700'],
        ['label'=>'Menu Terlaris','val'=>'Gudeg','sub'=>'203 porsi terjual','icon'=>'fa-fire','bg'=>'bg-red-600'],
    ];
    @endphp

    @foreach($summaries as $s)
    <div class="bg-white/85 backdrop-blur-sm rounded-3xl p-5 shadow-md border border-cream-200 hover:shadow-lg transition-all duration-300">
        <div class="w-10 h-10 {{ $s['bg'] }} rounded-2xl flex items-center justify-center shadow mb-4">
            <i class="fa-solid {{ $s['icon'] }} text-white text-sm"></i>
        </div>

        <p class="font-display font-bold text-2xl text-gray-900 truncate">{{ $s['val'] }}</p>
        <p class="text-xs text-gray-600 font-medium mt-1">{{ $s['label'] }}</p>
        <p class="text-[10px] text-gray-500 mt-0.5">{{ $s['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── MAIN GRID ───────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-7">

    {{-- Top 5 Menu --}}
    <div class="xl:col-span-3 bg-white/85 backdrop-blur-sm rounded-3xl shadow-md border border-cream-200 p-6 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-display font-semibold text-gray-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-ranking-star text-amber-600 text-sm"></i>
                    Top 5 Menu Favorit
                </h2>
                <p class="text-gray-500 text-xs mt-1">April 2025 · semua kategori</p>
            </div>
        </div>

        @php
        $topMenus = [
            ['r'=>1,'e'=>'🍛','n'=>'Nasi Gudeg Komplit','cat'=>'Makanan','sold'=>203,'rev'=>2436000,'pct'=>100,'bar'=>'bg-forest-600'],
            ['r'=>2,'e'=>'🧋','n'=>'Es Teh Manis','cat'=>'Minuman','sold'=>187,'rev'=>748000,'pct'=>92,'bar'=>'bg-forest-500'],
            ['r'=>3,'e'=>'🍜','n'=>'Mie Goreng Spesial','cat'=>'Makanan','sold'=>164,'rev'=>1640000,'pct'=>81,'bar'=>'bg-forest-400'],
            ['r'=>4,'e'=>'🍗','n'=>'Nasi Ayam Geprek','cat'=>'Makanan','sold'=>142,'rev'=>1846000,'pct'=>70,'bar'=>'bg-forest-300'],
            ['r'=>5,'e'=>'🍲','n'=>'Bakso Urat Jumbo','cat'=>'Makanan','sold'=>118,'rev'=>1298000,'pct'=>58,'bar'=>'bg-forest-200'],
        ];
        @endphp

        <div class="space-y-5">
            @foreach($topMenus as $m)
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-display font-bold text-base w-5 text-center flex-shrink-0
                        {{ $m['r']===1 ? 'text-amber-600' : 'text-gray-400' }}">
                        {{ $m['r'] }}
                    </span>

                    <span class="text-xl">{{ $m['e'] }}</span>

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-gray-900 truncate">{{ $m['n'] }}</p>
                        <p class="text-[10px] text-gray-500">{{ $m['cat'] }}</p>
                    </div>

                    <p class="font-display font-bold text-sm text-gray-800 flex-shrink-0">
                        {{ $m['sold'] }} porsi
                    </p>

                    <p class="font-display font-semibold text-sm text-emerald-700 hidden sm:block">
                        Rp {{ number_format($m['rev'],0,',','.') }}
                    </p>
                </div>

                <div class="ml-8 flex items-center gap-2">
                    <div class="flex-1 h-2.5 bg-cream-200 rounded-full overflow-hidden shadow-inner">
                        <div class="{{ $m['bar'] }} h-full rounded-full bar-anim"
                             style="width:{{ $m['pct'] }}%"></div>
                    </div>
                    <span class="text-[10px] font-semibold text-forest-500 w-8 text-right">
                        {{ $m['pct'] }}%
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- RIGHT PANELS --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- Trend --}}
        <div class="bg-white/85 backdrop-blur-sm rounded-3xl shadow-md border border-cream-200 p-5">
            <h3 class="font-display font-semibold text-sm text-forest-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-forest-500 text-xs"></i>
                Tren Harian (Apr)
            </h3>

            @php $trendBars = [22,28,31,25,29,35,38,30,27,33,40,38,35,42,44,38,41,47,39,36]; @endphp

            <div class="flex items-end gap-1 h-20">
                @foreach($trendBars as $tv)
                <div class="flex-1 {{ $tv === max($trendBars) ? 'bg-forest-600' : 'bg-forest-200' }}
                            rounded-t-sm hover:bg-forest-400 transition-colors"
                     style="height:{{ round(($tv/max($trendBars))*100) }}%">
                </div>
                @endforeach
            </div>

            <div class="flex justify-between mt-2">
                <span class="text-[9px] text-forest-400">1 Apr</span>
                <span class="text-[9px] text-forest-400">20 Apr</span>
            </div>
        </div>

        {{-- Category --}}
        <div class="bg-white/85 backdrop-blur-sm rounded-3xl shadow-md border border-cream-200 p-5">
            <h3 class="font-display font-semibold text-sm text-forest-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-forest-500 text-xs"></i>
                Distribusi Kategori
            </h3>

            @php
            $cats = [
                ['name'=>'Makanan','pct'=>62,'bar'=>'bg-forest-600'],
                ['name'=>'Minuman','pct'=>28,'bar'=>'bg-amber-500'],
                ['name'=>'Snack','pct'=>10,'bar'=>'bg-teal-500'],
            ];
            @endphp

            <div class="flex h-3 rounded-full overflow-hidden gap-0.5 mb-4">
                @foreach($cats as $c)
                <div class="{{ $c['bar'] }}" style="width:{{ $c['pct'] }}%"></div>
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

        {{-- Recommendation --}}
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-5 shadow-md">
            <h3 class="font-display font-semibold text-sm text-amber-900 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-xs"></i>
                Rekomendasi Stok
            </h3>

            @php
            $recs = [
                ['n'=>'Nasi Gudeg Komplit','a'=>'Tambah stok +15%'],
                ['n'=>'Es Teh Manis','a'=>'Pertahankan stok'],
                ['n'=>'Jus Alpukat','a'=>'Restok — habis!'],
            ];
            @endphp

            <div class="space-y-3">
                @foreach($recs as $rec)
                <div class="bg-white rounded-2xl p-3 border border-amber-100 shadow-sm">
                    <p class="text-xs font-semibold text-forest-900">{{ $rec['n'] }}</p>
                    <p class="text-[10px] text-forest-500 mt-0.5">{{ $rec['a'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- TABLE --}}
<div class="mt-7 bg-white/85 backdrop-blur-sm rounded-3xl shadow-md border border-cream-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-cream-200 flex items-center justify-between">
        <h3 class="font-display font-semibold text-gray-900 text-sm flex items-center gap-2">
            <i class="fa-solid fa-table-list text-gray-900 text-xs"></i>
            Detail Semua Menu
        </h3>
        <span class="text-xs text-gray-900">April 2025</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-cream-100 border-b border-cream-200">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-900 uppercase">Menu</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-900 uppercase">Terjual</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-900 uppercase">Pendapatan</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-cream-200">
                @foreach($topMenus as $m)
                <tr class="hover:bg-forest-50/40 transition-colors">
                    <td class="px-6 py-4 flex items-center gap-3">
                        <span class="text-xl">{{ $m['e'] }}</span>
                        <div>
                            <p class="font-semibold text-sm text-gray-900">{{ $m['n'] }}</p>
                            <p class="text-[10px] text-gray-500">{{ $m['cat'] }}</p>
                        </div>
                    </td>

                    <td class="px-4 py-4 text-center font-display font-bold text-gray-900">
                        {{ $m['sold'] }}
                    </td>

                    <td class="px-4 py-4 text-right font-display font-bold text-emerald-700">
                        Rp {{ number_format($m['rev'],0,',','.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection