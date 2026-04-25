@extends('layouts.admin')
@section('title', 'Laporan Favorit')
@section('page-title', 'Laporan Menu Favorit')
@section('page-subtitle', 'Analisis menu terlaris untuk perencanaan stok')

@section('content')

{{-- Period Selector --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex gap-2 flex-wrap">
        @foreach(['Hari Ini', 'Minggu Ini', 'Bulan Ini'] as $i => $p)
        <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $i===2 ? 'bg-sidebar-bg text-white shadow-md' : 'bg-white border border-gray-200 text-gray-600 hover:border-gray-300' }}">
            {{ $p }}
        </button>
        @endforeach
        <div class="relative">
            <input type="month" class="bg-white border border-gray-200 text-gray-600 text-sm px-3 py-2 rounded-xl focus:outline-none focus:border-brand-400 transition-all">
        </div>
    </div>
    <button class="btn-brand text-white font-semibold text-sm px-5 py-2.5 rounded-xl flex items-center gap-2 shadow">
        <i class="fa-solid fa-download"></i>Export PDF
    </button>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @php
    $summaryStats = [
        ['label'=>'Total Transaksi', 'value'=>'847', 'sub'=>'Bulan ini', 'icon'=>'fa-receipt', 'color'=>'from-slate-500 to-slate-700'],
        ['label'=>'Total Pendapatan', 'value'=>'Rp 9,8jt', 'sub'=>'+12% vs bulan lalu', 'icon'=>'fa-coins', 'color'=>'from-brand-400 to-brand-600'],
        ['label'=>'Menu Aktif', 'value'=>'10', 'sub'=>'dari 12 total', 'icon'=>'fa-utensils', 'color'=>'from-violet-500 to-violet-600'],
        ['label'=>'Menu Terpopuler', 'value'=>'Nasi Gudeg', 'sub'=>'203 porsi terjual', 'icon'=>'fa-fire', 'color'=>'from-red-500 to-orange-500'],
    ];
    @endphp
    @foreach($summaryStats as $s)
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <div class="w-9 h-9 bg-gradient-to-br {{ $s['color'] }} rounded-xl flex items-center justify-center shadow mb-3">
            <i class="fa-solid {{ $s['icon'] }} text-white text-sm"></i>
        </div>
        <p class="font-heading font-bold text-base sm:text-xl text-gray-800 truncate">{{ $s['value'] }}</p>
        <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $s['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Main Report Grid --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

    {{-- Top 5 Chart --}}
    <div class="xl:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-heading font-bold text-gray-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-brand-500"></i>Top 5 Menu Favorit
                </h2>
                <p class="text-gray-400 text-xs mt-0.5">Berdasarkan jumlah porsi terjual — April 2025</p>
            </div>
        </div>

        @php
        $topMenus = [
            ['rank'=>1,'emoji'=>'🍛','name'=>'Nasi Gudeg Komplit','cat'=>'Makanan','sold'=>203,'revenue'=>2436000,'pct'=>100,'color'=>'from-brand-400 to-brand-500'],
            ['rank'=>2,'emoji'=>'🧋','name'=>'Es Teh Manis','cat'=>'Minuman','sold'=>187,'revenue'=>748000,'pct'=>92,'color'=>'from-blue-400 to-blue-500'],
            ['rank'=>3,'emoji'=>'🍜','name'=>'Mie Goreng Spesial','cat'=>'Makanan','sold'=>164,'revenue'=>1640000,'pct'=>81,'color'=>'from-violet-400 to-violet-500'],
            ['rank'=>4,'emoji'=>'🍗','name'=>'Nasi Ayam Geprek','cat'=>'Makanan','sold'=>142,'revenue'=>1846000,'pct'=>70,'color'=>'from-emerald-400 to-emerald-500'],
            ['rank'=>5,'emoji'=>'🍲','name'=>'Bakso Urat Jumbo','cat'=>'Makanan','sold'=>118,'revenue'=>1298000,'pct'=>58,'color'=>'from-rose-400 to-rose-500'],
        ];
        @endphp

        <div class="space-y-5">
            @foreach($topMenus as $m)
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-7 h-7 bg-gradient-to-br {{ $m['color'] }} rounded-lg flex items-center justify-center text-white font-heading font-bold text-xs flex-shrink-0 shadow">{{ $m['rank'] }}</div>
                    <span class="text-xl">{{ $m['emoji'] }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-gray-800 truncate">{{ $m['name'] }}</p>
                        <p class="text-[10px] text-gray-400">{{ $m['cat'] }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-heading font-bold text-sm text-gray-800">{{ $m['sold'] }}</p>
                        <p class="text-[10px] text-gray-400">porsi</p>
                    </div>
                    <div class="text-right flex-shrink-0 w-20 hidden sm:block">
                        <p class="font-heading font-bold text-sm text-emerald-600">Rp {{ number_format($m['revenue'], 0, ',', '.') }}</p>
                        <p class="text-[10px] text-gray-400">pendapatan</p>
                    </div>
                </div>
                <div class="ml-10 flex items-center gap-2">
                    <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r {{ $m['color'] }} h-full rounded-full transition-all duration-1000" style="width: {{ $m['pct'] }}%"></div>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-500 w-8 text-right">{{ $m['pct'] }}%</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- Tren Penjualan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-heading font-bold text-sm text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-brand-500"></i>Tren Harian (Apr)
            </h3>
            @php
            $trendData = [22,28,31,25,29,35,38,30,27,33,40,38,35,42,44,38,41,47,39,36];
            $maxTrend = max($trendData);
            @endphp
            <div class="flex items-end gap-1 h-20">
                @foreach($trendData as $ti => $tv)
                <div class="flex-1 relative group">
                    <div class="bg-gradient-to-t {{ $tv === $maxTrend ? 'from-brand-500 to-brand-400' : 'from-gray-200 to-gray-150' }} rounded-sm transition-all hover:from-brand-400 hover:to-brand-300 cursor-pointer" style="height: {{ round(($tv / $maxTrend) * 100) }}%"></div>
                    <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[9px] px-1.5 py-0.5 rounded hidden group-hover:block whitespace-nowrap z-10">{{ $tv }}</div>
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between mt-2">
                <span class="text-[9px] text-gray-400">1 Apr</span>
                <span class="text-[9px] text-gray-400">20 Apr</span>
            </div>
        </div>

        {{-- Distribusi Kategori --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-heading font-bold text-sm text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-brand-500"></i>Distribusi Kategori
            </h3>
            @php
            $categories = [
                ['name'=>'Makanan', 'pct'=>62, 'color'=>'bg-brand-500'],
                ['name'=>'Minuman', 'pct'=>28, 'color'=>'bg-blue-500'],
                ['name'=>'Snack',   'pct'=>10, 'color'=>'bg-violet-500'],
            ];
            @endphp
            {{-- Stacked Bar --}}
            <div class="flex h-4 rounded-full overflow-hidden mb-3 gap-0.5">
                @foreach($categories as $c)
                <div class="{{ $c['color'] }} rounded-full transition-all" style="width:{{ $c['pct'] }}%"></div>
                @endforeach
            </div>
            <div class="space-y-2">
                @foreach($categories as $c)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full {{ $c['color'] }}"></div>
                        <span class="text-xs text-gray-600 font-medium">{{ $c['name'] }}</span>
                    </div>
                    <span class="font-heading font-bold text-sm text-gray-700">{{ $c['pct'] }}%</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stok Alert --}}
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
            <h3 class="font-heading font-bold text-sm text-amber-800 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>Rekomendasi Stok
            </h3>
            <div class="space-y-2.5">
                @php
                $stockRec = [
                    ['name'=>'Nasi Gudeg Komplit','action'=>'Tambah stok +15%','level'=>'high'],
                    ['name'=>'Es Teh Manis','action'=>'Pertahankan stok','level'=>'ok'],
                    ['name'=>'Jus Alpukat','action'=>'Stok habis — restok!','level'=>'low'],
                ];
                @endphp
                @foreach($stockRec as $r)
                <div class="flex items-center gap-2.5 p-2.5 bg-white rounded-xl border border-amber-100">
                    <div class="w-2 h-2 rounded-full flex-shrink-0 {{ $r['level']==='high'?'bg-brand-500':($r['level']==='low'?'bg-red-500':'bg-emerald-500') }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $r['name'] }}</p>
                        <p class="text-[10px] {{ $r['level']==='low'?'text-red-500':'text-gray-400' }}">{{ $r['action'] }}</p>
                    </div>
                    <i class="fa-solid {{ $r['level']==='high'?'fa-arrow-trend-up text-brand-400':($r['level']==='low'?'fa-exclamation text-red-400':'fa-check text-emerald-400') }} text-xs flex-shrink-0"></i>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Detail Table --}}
<div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-heading font-bold text-gray-800 text-sm flex items-center gap-2">
            <i class="fa-solid fa-table-list text-brand-500"></i>Detail Semua Menu
        </h3>
        <span class="text-xs text-gray-400">April 2025</span>
    </div>
    {{-- Desktop --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Menu</th>
                    <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Terjual</th>
                    <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Pendapatan</th>
                    <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Trend</th>
                    <th class="text-center px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Rekomendasi Stok</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($topMenus as $m)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-6 py-3.5 flex items-center gap-2">
                        <span class="text-lg">{{ $m['emoji'] }}</span>
                        <div>
                            <p class="font-semibold text-sm text-gray-800">{{ $m['name'] }}</p>
                            <p class="text-[10px] text-gray-400">{{ $m['cat'] }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-center font-heading font-bold text-gray-800">{{ $m['sold'] }}</td>
                    <td class="px-4 py-3.5 text-right font-heading font-bold text-emerald-600">Rp {{ number_format($m['revenue'], 0, ',', '.') }}</td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="text-xs text-emerald-600 bg-emerald-50 font-semibold px-2 py-1 rounded-lg flex items-center gap-1 justify-center">
                            <i class="fa-solid fa-arrow-trend-up text-[10px]"></i>+12%
                        </span>
                    </td>
                    <td class="px-6 py-3.5 text-center">
                        @if($m['rank']===1)
                        <span class="text-xs text-brand-600 bg-brand-50 font-semibold px-2.5 py-1 rounded-lg">Tambah +15%</span>
                        @elseif($m['rank']===2)
                        <span class="text-xs text-emerald-600 bg-emerald-50 font-semibold px-2.5 py-1 rounded-lg">Pertahankan</span>
                        @else
                        <span class="text-xs text-blue-600 bg-blue-50 font-semibold px-2.5 py-1 rounded-lg">Normal</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- Mobile --}}
    <div class="sm:hidden divide-y divide-gray-50">
        @foreach($topMenus as $m)
        <div class="px-4 py-3.5 flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br {{ $m['color'] }} rounded-lg flex items-center justify-center text-white font-bold text-xs flex-shrink-0">{{ $m['rank'] }}</div>
            <span class="text-xl">{{ $m['emoji'] }}</span>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-gray-800 truncate">{{ $m['name'] }}</p>
                <p class="text-[10px] text-gray-400">{{ $m['sold'] }} terjual</p>
            </div>
            <p class="font-heading font-bold text-sm text-emerald-600 flex-shrink-0">Rp {{ number_format($m['revenue']/1000, 0) }}rb</p>
        </div>
        @endforeach
    </div>
</div>

@endsection
