@extends('layouts.pengelola')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional kantin hari ini')

@section('content')

{{-- ── STAT CARDS ───────────────────────────────────────── --}}
@php
$stats = [
    [
        'label'  => 'Total Pesanan',
        'value'  => '47',
        'sub'    => '+8 dari kemarin',
        'icon'   => 'fa-bag-shopping',
        'trend'  => '+17%',
        'up'     => true,
        'accent' => 'forest',
    ],
    [
        'label'  => 'Sedang Diproses',
        'value'  => '12',
        'sub'    => '3 perlu perhatian',
        'icon'   => 'fa-fire-burner',
        'trend'  => null,
        'up'     => null,
        'accent' => 'amber',
    ],
    [
        'label'  => 'Selesai',
        'value'  => '35',
        'sub'    => '74% completion rate',
        'icon'   => 'fa-circle-check',
        'trend'  => '+5%',
        'up'     => true,
        'accent' => 'green',
    ],
    [
        'label'  => 'Pendapatan Hari Ini',
        'value'  => 'Rp 587rb',
        'sub'    => 'Target: Rp 600rb',
        'icon'   => 'fa-coins',
        'trend'  => '98%',
        'up'     => true,
        'accent' => 'teal',
    ],
];
$accentMap = [
    'forest' => ['bg'=>'bg-forest-700','text'=>'text-forest-50',  'ring'=>'bg-forest-100', 'rtxt'=>'text-forest-700'],
    'amber'  => ['bg'=>'bg-amber-600', 'text'=>'text-amber-50',   'ring'=>'bg-amber-100',  'rtxt'=>'text-amber-700'],
    'green'  => ['bg'=>'bg-emerald-600','text'=>'text-emerald-50','ring'=>'bg-emerald-100','rtxt'=>'text-emerald-700'],
    'teal'   => ['bg'=>'bg-teal-600',  'text'=>'text-teal-50',    'ring'=>'bg-teal-100',   'rtxt'=>'text-teal-700'],
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
            <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full badge-new">
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
        $recents = [
            ['id'=>'SC-047','name'=>'Ahmad Rizky',   'cls'=>'XII IPA 2','menu'=>'Nasi Gudeg + Es Teh', 'total'=>'Rp 16.000','st'=>'Baru',      'sc'=>'bg-forest-100 text-forest-700'],
            ['id'=>'SC-046','name'=>'Siti Rahmawati','cls'=>'XI IPS 1', 'menu'=>'Mie Goreng Spesial',  'total'=>'Rp 10.000','st'=>'Diproses',  'sc'=>'bg-amber-100 text-amber-700'],
            ['id'=>'SC-045','name'=>'Budi Santoso',  'cls'=>'XI IPA 2', 'menu'=>'Ayam Geprek',         'total'=>'Rp 13.000','st'=>'Selesai',   'sc'=>'bg-emerald-100 text-emerald-700'],
            ['id'=>'SC-044','name'=>'Dewi Lestari',  'cls'=>'XII IPA 1','menu'=>'Bakso Urat + Teh',    'total'=>'Rp 15.000','st'=>'Selesai',   'sc'=>'bg-emerald-100 text-emerald-700'],
        ];
        @endphp

        {{-- Desktop --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-cream-100/70">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">ID</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Siswa</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Menu</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Total</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-forest-500 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200">
                    @foreach($recents as $r)
                    <tr class="hover:bg-cream-100/60 transition-colors">
                        <td class="px-6 py-3.5 font-display font-semibold text-sm text-forest-800">#{{ $r['id'] }}</td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-xs text-forest-900">{{ $r['name'] }}</p>
                            <p class="text-[10px] text-forest-400">{{ $r['cls'] }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-forest-600">{{ $r['menu'] }}</td>
                        <td class="px-4 py-3.5 text-right font-display font-semibold text-sm text-forest-900">{{ $r['total'] }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 {{ $r['sc'] }} text-[10px] font-bold px-2.5 py-1 rounded-full">
                                {{ $r['st'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="sm:hidden divide-y divide-cream-200">
            @foreach($recents as $r)
            <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-display font-semibold text-xs text-forest-800 truncate">#{{ $r['id'] }} · {{ $r['name'] }}</p>
                    <p class="text-[11px] text-forest-400 mt-0.5 truncate">{{ $r['menu'] }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-display font-semibold text-xs text-forest-900">{{ $r['total'] }}</p>
                    <span class="inline-flex items-center {{ $r['sc'] }} text-[10px] font-bold px-2 py-0.5 rounded-full mt-1">{{ $r['st'] }}</span>
                </div>
            </div>
            @endforeach
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
            @php
            $topMenus = [
                ['rank'=>1,'emoji'=>'🍛','name'=>'Nasi Gudeg Komplit','sold'=>18,'pct'=>90,'barColor'=>'bg-forest-500'],
                ['rank'=>2,'emoji'=>'🍜','name'=>'Mie Goreng Spesial','sold'=>14,'pct'=>70,'barColor'=>'bg-forest-400'],
                ['rank'=>3,'emoji'=>'🧋','name'=>'Es Teh Manis',      'sold'=>11,'pct'=>55,'barColor'=>'bg-forest-300'],
            ];
            @endphp
            @foreach($topMenus as $m)
            <div>
                <div class="flex items-center gap-2.5 mb-1.5">
                    <span class="font-display font-bold text-lg
                        {{ $m['rank']===1?'text-amber-600':($m['rank']===2?'text-forest-500':'text-forest-400') }}
                        w-5 text-center flex-shrink-0">{{ $m['rank'] }}</span>
                    <span class="text-xl">{{ $m['emoji'] }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-forest-900 truncate">{{ $m['name'] }}</p>
                        <p class="text-[10px] text-forest-400">{{ $m['sold'] }} porsi</p>
                    </div>
                    <span class="font-display font-bold text-sm text-forest-700 flex-shrink-0">{{ $m['pct'] }}%</span>
                </div>
                <div class="ml-8 h-1.5 bg-cream-200 rounded-full overflow-hidden">
                    <div class="{{ $m['barColor'] }} h-full rounded-full bar-anim" style="width:{{ $m['pct'] }}%"></div>
                </div>
            </div>
            @endforeach

            {{-- Hourly bar chart --}}
            <div class="mt-5 pt-4 border-t border-cream-200">
                <p class="text-[10px] font-semibold tracking-widest text-forest-400 uppercase mb-3">
                    Pesanan per Jam
                </p>
                @php $bars = [3,6,10,14,9,17,20,15,12,8,5,7]; @endphp
                <div class="flex items-end gap-1 h-16">
                    @foreach($bars as $b)
                    <div class="flex-1 {{ $b === max($bars) ? 'bg-forest-500' : 'bg-forest-200' }}
                                rounded-t-sm hover:bg-forest-400 transition-colors cursor-default"
                         style="height:{{ round(($b/max($bars))*100) }}%"></div>
                    @endforeach
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-[9px] text-forest-400">07:00</span>
                    <span class="text-[9px] text-forest-400">12:00</span>
                    <span class="text-[9px] text-forest-400">15:00</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── QUICK ACTIONS ────────────────────────────────────── --}}
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
    @php
    $actions = [
        ['url'=>'/pengelola/orders',   'icon'=>'fa-bell',      'label'=>'Pesanan Baru',   'badge'=>3, 'color'=>'bg-forest-700'],
        ['url'=>'/pengelola/delivery', 'icon'=>'fa-truck-fast','label'=>'Pengiriman',      'badge'=>5, 'color'=>'bg-amber-600'],
        ['url'=>'/pengelola/menu',     'icon'=>'fa-utensils',  'label'=>'Kelola Menu',     'badge'=>null,'color'=>'bg-teal-700'],
        ['url'=>'/pengelola/report',   'icon'=>'fa-chart-bar', 'label'=>'Laporan',         'badge'=>null,'color'=>'bg-forest-500'],
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
