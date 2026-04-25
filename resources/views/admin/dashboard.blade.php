@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional kantin hari ini')

@section('content')

{{-- ===== STAT CARDS ===== --}}
@php
$stats = [
    ['label' => 'Total Pesanan', 'value' => '47', 'sub' => '+8 dari kemarin', 'icon' => 'fa-bag-shopping', 'color' => 'from-blue-500 to-blue-600', 'bg' => 'bg-blue-50', 'text' => 'text-blue-500', 'trend' => 'up'],
    ['label' => 'Sedang Diproses', 'value' => '12', 'sub' => '3 butuh perhatian', 'icon' => 'fa-fire-burner', 'color' => 'from-brand-400 to-brand-600', 'bg' => 'bg-amber-50', 'text' => 'text-brand-500', 'trend' => 'warn'],
    ['label' => 'Selesai', 'value' => '35', 'sub' => '74% dari total', 'icon' => 'fa-circle-check', 'color' => 'from-emerald-500 to-emerald-600', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-500', 'trend' => 'up'],
    ['label' => 'Pendapatan', 'value' => 'Rp 587rb', 'sub' => 'Hari ini', 'icon' => 'fa-coins', 'color' => 'from-violet-500 to-violet-600', 'bg' => 'bg-violet-50', 'text' => 'text-violet-500', 'trend' => 'up'],
];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($stats as $i => $s)
    <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100" style="animation-delay: {{ $i * 60 }}ms">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 bg-gradient-to-br {{ $s['color'] }} rounded-xl flex items-center justify-center shadow-md">
                <i class="fa-solid {{ $s['icon'] }} text-white text-sm"></i>
            </div>
            @if($s['trend'] === 'up')
            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full flex items-center gap-0.5">
                <i class="fa-solid fa-arrow-trend-up text-[9px]"></i>+8%
            </span>
            @elseif($s['trend'] === 'warn')
            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
            </span>
            @endif
        </div>
        <p class="font-heading font-bold text-xl sm:text-2xl text-gray-800">{{ $s['value'] }}</p>
        <p class="text-gray-500 text-xs mt-0.5">{{ $s['label'] }}</p>
        <p class="text-gray-400 text-[10px] mt-1">{{ $s['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- ===== MAIN CONTENT GRID ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Pesanan Terbaru --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-heading font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-bell text-brand-500"></i>Pesanan Terbaru
            </h2>
            <a href="{{ url('/admin/orders') }}" class="text-xs text-brand-500 hover:text-brand-700 font-semibold">Lihat Semua →</a>
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500">ID</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Siswa</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Menu</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500">Total</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php
                    $recentOrders = [
                        ['id'=>'SC-047','name'=>'Ahmad Rizky','menu'=>'Nasi Gudeg + Es Teh','total'=>'Rp 16.000','status'=>'Baru','sc'=>'bg-blue-100 text-blue-700'],
                        ['id'=>'SC-046','name'=>'Siti Rahma','menu'=>'Mie Goreng Spesial','total'=>'Rp 10.000','status'=>'Diproses','sc'=>'bg-amber-100 text-amber-700'],
                        ['id'=>'SC-045','name'=>'Budi Santoso','menu'=>'Nasi Ayam Geprek','total'=>'Rp 13.000','status'=>'Selesai','sc'=>'bg-emerald-100 text-emerald-700'],
                        ['id'=>'SC-044','name'=>'Dewi Lestari','menu'=>'Bakso Urat + Teh','total'=>'Rp 15.000','status'=>'Selesai','sc'=>'bg-emerald-100 text-emerald-700'],
                    ];
                    @endphp
                    @foreach($recentOrders as $o)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-3.5 font-heading font-bold text-xs text-gray-700">#{{ $o['id'] }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-700 font-medium">{{ $o['name'] }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">{{ $o['menu'] }}</td>
                        <td class="px-4 py-3.5 text-right text-xs font-heading font-bold text-gray-800">{{ $o['total'] }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 {{ $o['sc'] }} text-[10px] font-bold px-2.5 py-1 rounded-full">{{ $o['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="sm:hidden divide-y divide-gray-50">
            @foreach($recentOrders as $o)
            <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                <div>
                    <p class="font-heading font-bold text-xs text-gray-700">#{{ $o['id'] }} · {{ $o['name'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $o['menu'] }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-heading font-bold text-xs text-gray-800">{{ $o['total'] }}</p>
                    <span class="inline-flex items-center {{ $o['sc'] }} text-[10px] font-bold px-2 py-0.5 rounded-full mt-1">{{ $o['status'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Top 3 Menu Terlaris --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-heading font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-fire text-brand-500"></i>Menu Terlaris Hari Ini
            </h2>
        </div>
        <div class="p-5 space-y-4">
            @php
            $topMenus = [
                ['rank'=>1,'emoji'=>'🍛','name'=>'Nasi Gudeg Komplit','sold'=>18,'pct'=>90,'color'=>'bg-gradient-to-r from-brand-400 to-brand-600'],
                ['rank'=>2,'emoji'=>'🍜','name'=>'Mie Goreng Spesial','sold'=>14,'pct'=>70,'color'=>'bg-gradient-to-r from-blue-400 to-blue-600'],
                ['rank'=>3,'emoji'=>'🍗','name'=>'Nasi Ayam Geprek','sold'=>11,'pct'=>55,'color'=>'bg-gradient-to-r from-emerald-400 to-emerald-600'],
            ];
            @endphp
            @foreach($topMenus as $m)
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-heading font-bold text-lg {{ $m['rank']===1?'text-brand-500':($m['rank']===2?'text-blue-500':'text-emerald-500') }} w-5 text-center flex-shrink-0">{{ $m['rank'] }}</span>
                    <span class="text-xl">{{ $m['emoji'] }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-gray-800 truncate">{{ $m['name'] }}</p>
                        <p class="text-[10px] text-gray-400">{{ $m['sold'] }} porsi terjual</p>
                    </div>
                </div>
                <div class="ml-8 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="{{ $m['color'] }} h-full rounded-full" style="width: {{ $m['pct'] }}%"></div>
                </div>
            </div>
            @endforeach

            {{-- Mini Chart --}}
            <div class="mt-6 pt-5 border-t border-dashed border-gray-200">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-3">Pesanan per Jam</p>
                <div class="flex items-end gap-1.5 h-16">
                    @php $bars = [3,5,8,12,9,15,18,14,11,7,4,6]; @endphp
                    @foreach($bars as $bi => $b)
                    <div class="flex-1 {{ $b === max($bars) ? 'bg-gradient-to-t from-brand-500 to-brand-400' : 'bg-gray-200' }} rounded-t-sm transition-all" style="height:{{ round(($b / max($bars)) * 100) }}%"></div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-[9px] text-gray-400">07:00</span>
                    <span class="text-[9px] text-gray-400">12:00</span>
                    <span class="text-[9px] text-gray-400">15:00</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== QUICK ACTION ROW ===== --}}
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
    @php
    $actions = [
        ['url'=>'/admin/orders','icon'=>'fa-bell','label'=>'Pesanan Baru','count'=>3,'color'=>'from-blue-500 to-blue-600'],
        ['url'=>'/admin/delivery','icon'=>'fa-motorcycle','label'=>'Dalam Pengiriman','count'=>5,'color'=>'from-brand-400 to-brand-600'],
        ['url'=>'/admin/menu','icon'=>'fa-utensils','label'=>'Kelola Menu','count'=>12,'color'=>'from-violet-500 to-violet-600'],
        ['url'=>'/admin/report','icon'=>'fa-chart-column','label'=>'Lihat Laporan','count'=>null,'color'=>'from-emerald-500 to-emerald-600'],
    ];
    @endphp
    @foreach($actions as $a)
    <a href="{{ url($a['url']) }}" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3 hover:shadow-md transition-all group">
        <div class="w-10 h-10 bg-gradient-to-br {{ $a['color'] }} rounded-xl flex items-center justify-center flex-shrink-0 shadow group-hover:scale-105 transition-transform">
            <i class="fa-solid {{ $a['icon'] }} text-white text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-gray-700 truncate">{{ $a['label'] }}</p>
            @if($a['count'])
            <p class="text-[10px] text-gray-400">{{ $a['count'] }} item</p>
            @else
            <p class="text-[10px] text-brand-500 font-medium">Buka →</p>
            @endif
        </div>
    </a>
    @endforeach
</div>

@endsection
