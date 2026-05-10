@extends('layouts.pengelola')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional kantin hari ini')

@section('content')

@php
$stats = [
    [
        'label'  => 'Total Pesanan',
        'value'  => '47',
        'sub'    => '+8 dari kemarin',
        'icon'   => 'fa-bag-shopping',
        'trend'  => '+17%',
        'color'  => 'bg-primary',
    ],
    [
        'label'  => 'Sedang Diproses',
        'value'  => '12',
        'sub'    => '3 perlu perhatian',
        'icon'   => 'fa-fire-burner',
        'trend'  => '12',
        'color'  => 'bg-amber-500',
    ],
    [
        'label'  => 'Selesai',
        'value'  => '35',
        'sub'    => '74% completion rate',
        'icon'   => 'fa-circle-check',
        'trend'  => '+5%',
        'color'  => 'bg-emerald-500',
    ],
    [
        'label'  => 'Pendapatan Hari Ini',
        'value'  => 'Rp 587rb',
        'sub'    => 'Target: Rp 600rb',
        'icon'   => 'fa-coins',
        'trend'  => '98%',
        'color'  => 'bg-orange-400',
    ],
];
@endphp

{{-- Stat Cards --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-7">
    @foreach($stats as $stat)
    <div class="bg-white rounded-3xl border border-orange-100 p-5 shadow-sm hover:shadow-md transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="w-11 h-11 {{ $stat['color'] }} rounded-2xl flex items-center justify-center shadow-sm">
                <i class="fa-solid {{ $stat['icon'] }} text-white text-sm"></i>
            </div>

            <span class="bg-orange-50 text-primary text-[11px] font-semibold px-2.5 py-1 rounded-full">
                {{ $stat['trend'] }}
            </span>
        </div>

        <h3 class="text-2xl font-bold text-darkText">{{ $stat['value'] }}</h3>
        <p class="text-sm font-medium text-gray-700 mt-1">{{ $stat['label'] }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $stat['sub'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

    {{-- Recent Orders --}}
    <div class="xl:col-span-3 bg-white rounded-3xl border border-orange-100 shadow-sm overflow-hidden">
        <div class="flex justify-between items-center px-6 py-5 border-b border-orange-100">
            <h2 class="font-semibold text-darkText text-base">
                Pesanan Terbaru
            </h2>

            <a href="{{ url('/pengelola/orders') }}"
               class="text-sm text-primary font-semibold hover:opacity-80">
                Lihat semua →
            </a>
        </div>

        @php
        $recents = [
            ['id'=>'SC-047','name'=>'Ahmad Rizky','cls'=>'XII IPA 2','menu'=>'Nasi Gudeg + Es Teh','total'=>'Rp 16.000','st'=>'Baru','sc'=>'bg-orange-50 text-primary'],
            ['id'=>'SC-046','name'=>'Ganise','cls'=>'XI IPS 1','menu'=>'Mie Goreng Spesial','total'=>'Rp 10.000','st'=>'Diproses','sc'=>'bg-amber-50 text-amber-700'],
            ['id'=>'SC-045','name'=>'Budi Santoso','cls'=>'XI IPA 2','menu'=>'Ayam Geprek','total'=>'Rp 13.000','st'=>'Selesai','sc'=>'bg-emerald-50 text-emerald-700'],
            ['id'=>'SC-044','name'=>'Dewi Lestari','cls'=>'XII IPA 1','menu'=>'Bakso Urat + Teh','total'=>'Rp 15.000','st'=>'Selesai','sc'=>'bg-emerald-50 text-emerald-700'],
        ];
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-orange-50 border-b border-orange-100">
                        <th class="text-left px-6 py-4 text-xs text-gray-500 uppercase">ID</th>
                        <th class="text-left px-4 py-4 text-xs text-gray-500 uppercase">Siswa</th>
                        <th class="text-left px-4 py-4 text-xs text-gray-500 uppercase">Menu</th>
                        <th class="text-right px-4 py-4 text-xs text-gray-500 uppercase">Total</th>
                        <th class="text-center px-4 py-4 text-xs text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-orange-50">
                    @foreach($recents as $r)
                    <tr class="hover:bg-orange-50/40">
                        <td class="px-6 py-4 font-semibold text-darkText">#{{ $r['id'] }}</td>
                        <td class="px-4 py-4">
                            <p class="font-medium text-darkText">{{ $r['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $r['cls'] }}</p>
                        </td>
                        <td class="px-4 py-4 text-gray-600">{{ $r['menu'] }}</td>
                        <td class="px-4 py-4 text-right font-semibold text-darkText">{{ $r['total'] }}</td>
                        <td class="px-4 py-4 text-center">
                            <span class="{{ $r['sc'] }} text-xs px-3 py-1 rounded-full font-semibold">
                                {{ $r['st'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Menu --}}
    <div class="xl:col-span-2 bg-white rounded-3xl border border-orange-100 shadow-sm">
        <div class="px-6 py-5 border-b border-orange-100">
            <h2 class="font-semibold text-darkText text-base">
                Menu Terlaris Hari Ini
            </h2>
        </div>

        <div class="p-5 space-y-5">
            @php
            $menus = [
                ['rank'=>1,'emoji'=>'🍛','name'=>'Nasi Gudeg Komplit','sold'=>18,'pct'=>90],
                ['rank'=>2,'emoji'=>'🍜','name'=>'Mie Goreng Spesial','sold'=>14,'pct'=>70],
                ['rank'=>3,'emoji'=>'🧋','name'=>'Es Teh Manis','sold'=>11,'pct'=>55],
            ];
            @endphp

            @foreach($menus as $m)
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
            @endforeach
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
    @php
    $actions = [
        ['url'=>'/pengelola/orders','icon'=>'fa-bell','label'=>'Pesanan Baru','badge'=>3],
        ['url'=>'/pengelola/delivery','icon'=>'fa-truck-fast','label'=>'Pengiriman','badge'=>5],
        ['url'=>'/pengelola/menu','icon'=>'fa-utensils','label'=>'Kelola Menu','badge'=>null],
        ['url'=>'/pengelola/report','icon'=>'fa-chart-bar','label'=>'Laporan','badge'=>null],
    ];
    @endphp

    @foreach($actions as $a)
    <a href="{{ url($a['url']) }}"
       class="bg-white border border-orange-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all flex items-center gap-3">
        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-sm">
            <i class="fa-solid {{ $a['icon'] }} text-white text-sm"></i>
        </div>

        <div>
            <p class="text-sm font-semibold text-darkText">{{ $a['label'] }}</p>
            @if($a['badge'])
            <p class="text-xs text-gray-400">{{ $a['badge'] }} item aktif</p>
            @else
            <p class="text-xs text-gray-400">Buka →</p>
            @endif
        </div>
    </a>
    @endforeach
</div>

@endsection