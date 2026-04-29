@extends('layouts.pengelola')
@section('title', 'Proses Pengiriman')
@section('page-title', 'Proses Pengiriman')
@section('page-subtitle', 'Kelola status pengiriman pesanan aktif')

@section('content')

{{-- ── SUMMARY STATS ────────────────────────────────────── --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @php
    $dStats = [
        ['label'=>'Diproses',        'val'=>4,  'icon'=>'fa-fire-burner','bg'=>'bg-amber-600'],
        ['label'=>'Dikirim',         'val'=>3,  'icon'=>'fa-truck-fast', 'bg'=>'bg-forest-600'],
        ['label'=>'Selesai Hari Ini','val'=>35, 'icon'=>'fa-circle-check','bg'=>'bg-emerald-600'],
    ];
    @endphp
    @foreach($dStats as $ds)
    <div class="bg-cream-50 rounded-2xl p-4 sm:p-5 shadow-sm border border-cream-200 flex items-center gap-3">
        <div class="w-10 h-10 {{ $ds['bg'] }} rounded-xl flex items-center justify-center shadow flex-shrink-0">
            <i class="fa-solid {{ $ds['icon'] }} text-white text-sm"></i>
        </div>
        <div>
            <p class="font-display font-bold text-2xl text-forest-900">{{ $ds['val'] }}</p>
            <p class="text-xs text-forest-500">{{ $ds['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

@php
$deliveries = [
    ['id'=>'SC-046','name'=>'Siti Rahmawati','cls'=>'XI IPS 1','items'=>'Mie Goreng Spesial', 'total'=>'Rp 10.000','status'=>'Diproses','pickup'=>'Istirahat 1','dur'=>5],
    ['id'=>'SC-044','name'=>'Dewi Lestari',  'cls'=>'XII IPA 1','items'=>'Bakso Urat + Es Teh','total'=>'Rp 15.000','status'=>'Diproses','pickup'=>'Istirahat 2','dur'=>15],
    ['id'=>'SC-043','name'=>'Budi Santoso',  'cls'=>'XI IPA 2', 'items'=>'2× Mie Goreng',     'total'=>'Rp 20.000','status'=>'Diproses','pickup'=>'Istirahat 2','dur'=>25],
    ['id'=>'SC-040','name'=>'Ahmad Rizky',   'cls'=>'XII IPA 2','items'=>'Nasi Gudeg + Es Teh','total'=>'Rp 16.000','status'=>'Dikirim','pickup'=>'Istirahat 1','dur'=>8],
    ['id'=>'SC-039','name'=>'Maya Sari',     'cls'=>'X IPA 1',  'items'=>'Nasi Ayam Geprek',  'total'=>'Rp 13.000','status'=>'Dikirim','pickup'=>'Istirahat 1','dur'=>12],
    ['id'=>'SC-038','name'=>'Rizal Maulana', 'cls'=>'XII IPS 1','items'=>'Bakso Urat Jumbo',  'total'=>'Rp 11.000','status'=>'Dikirim','pickup'=>'Istirahat 1','dur'=>15],
    ['id'=>'SC-035','name'=>'Putri Indah',   'cls'=>'XI IPA 1', 'items'=>'Nasi Gudeg Komplit','total'=>'Rp 12.000','status'=>'Selesai','pickup'=>'Istirahat 1','dur'=>30],
    ['id'=>'SC-034','name'=>'Fahrul Aziz',   'cls'=>'X IPS 2',  'items'=>'Mie Goreng + Jus', 'total'=>'Rp 18.000','status'=>'Selesai','pickup'=>'Istirahat 1','dur'=>35],
];
$cols = [
    'Diproses' => ['dot'=>'bg-amber-500',   'icon'=>'fa-fire-burner', 'head'=>'bg-amber-600',  'next'=>'Dikirim', 'btnLabel'=>'Kirim Sekarang', 'btnClass'=>'btn-primary'],
    'Dikirim'  => ['dot'=>'bg-forest-500',  'icon'=>'fa-truck-fast',  'head'=>'bg-forest-700', 'next'=>'Selesai', 'btnLabel'=>'Tandai Selesai', 'btnClass'=>'bg-emerald-600 hover:bg-emerald-700'],
    'Selesai'  => ['dot'=>'bg-emerald-500', 'icon'=>'fa-circle-check','head'=>'bg-emerald-700','next'=>null,      'btnLabel'=>null,             'btnClass'=>''],
];
@endphp

{{-- ── DESKTOP KANBAN ───────────────────────────────────── --}}
<div class="hidden md:grid md:grid-cols-3 gap-5">
    @foreach($cols as $colName => $col)
    @php $colItems = collect($deliveries)->where('status', $colName); @endphp
    <div class="bg-cream-100/70 rounded-2xl p-4">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-7 h-7 {{ $col['head'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fa-solid {{ $col['icon'] }} text-white text-xs"></i>
            </div>
            <h3 class="font-display font-semibold text-sm text-forest-900">{{ $colName }}</h3>
            <span class="ml-auto w-6 h-6 bg-cream-200 text-forest-600 text-xs font-bold rounded-full flex items-center justify-center">
                {{ $colItems->count() }}
            </span>
        </div>

        <div class="space-y-3">
            @foreach($colItems as $d)
            <div class="bg-cream-50 rounded-xl p-4 shadow-sm border border-cream-200 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div>
                        <p class="font-display font-bold text-xs text-forest-900">#{{ $d['id'] }}</p>
                        <p class="text-xs font-medium text-forest-800 mt-0.5">{{ $d['name'] }}</p>
                        <p class="text-[10px] text-forest-400">{{ $d['cls'] }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-display font-bold text-sm text-forest-700">{{ $d['total'] }}</p>
                        <p class="text-[10px] text-forest-400 flex items-center gap-0.5 justify-end mt-0.5">
                            <i class="fa-solid fa-clock text-forest-300 text-[9px]"></i>{{ $d['dur'] }}m
                        </p>
                    </div>
                </div>
                <div class="bg-cream-100 rounded-lg px-3 py-2 mb-3">
                    <p class="text-[11px] text-forest-700 font-medium">{{ $d['items'] }}</p>
                    <p class="text-[10px] text-forest-400 mt-0.5">
                        <i class="fa-solid fa-clock text-forest-300"></i> {{ $d['pickup'] }}
                    </p>
                </div>
                @if($col['next'])
                <button onclick="updateStatus('{{ $d['id'] }}','{{ $col['next'] }}')"
                        class="{{ $col['btnClass'] }} w-full py-2 rounded-xl text-xs font-bold text-white shadow
                               flex items-center justify-center gap-1.5 transition-all">
                    <i class="fa-solid {{ $colName==='Diproses'?'fa-truck-fast':'fa-check' }} text-[10px]"></i>
                    {{ $col['btnLabel'] }}
                </button>
                @else
                <div class="flex items-center justify-center gap-1.5 py-1">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                    <span class="text-xs text-emerald-700 font-semibold">Selesai</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- ── MOBILE TIMELINE ─────────────────────────────────── --}}
<div class="md:hidden space-y-3">
    @foreach($deliveries as $d)
    @php
    $col = $cols[$d['status']];
    $borderLeft = $d['status']==='Diproses' ? 'border-l-amber-500' : ($d['status']==='Dikirim' ? 'border-l-forest-500' : 'border-l-emerald-500');
    $stBg = $d['status']==='Diproses' ? 'bg-amber-100 text-amber-700' : ($d['status']==='Dikirim' ? 'bg-forest-100 text-forest-700' : 'bg-emerald-100 text-emerald-700');
    @endphp
    <div class="bg-cream-50 rounded-2xl shadow-sm border border-cream-200 border-l-4 {{ $borderLeft }} overflow-hidden">
        <div class="px-4 py-3.5">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $col['dot'] }} flex-shrink-0 {{ $d['status']!=='Selesai'?'badge-new':'' }}"></div>
                    <div>
                        <p class="font-display font-bold text-sm text-forest-900">#{{ $d['id'] }}</p>
                        <p class="text-[10px] text-forest-400">{{ $d['name'] }} · {{ $d['cls'] }}</p>
                    </div>
                </div>
                <span class="{{ $stBg }} text-[10px] font-bold px-2.5 py-1 rounded-full flex-shrink-0">{{ $d['status'] }}</span>
            </div>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs text-forest-700 font-medium">{{ $d['items'] }}</p>
                    <p class="text-[10px] text-forest-400 mt-0.5">{{ $d['pickup'] }} · {{ $d['total'] }}</p>
                </div>
                @if($col['next'])
                <button onclick="updateStatus('{{ $d['id'] }}','{{ $col['next'] }}')"
                        class="{{ $col['btnClass'] }} text-xs font-bold px-3.5 py-2 rounded-xl text-white shadow
                               flex items-center gap-1.5 flex-shrink-0 transition-all">
                    <i class="fa-solid {{ $d['status']==='Diproses'?'fa-truck-fast':'fa-check' }} text-[10px]"></i>
                    {{ $col['btnLabel'] }}
                </button>
                @else
                <span class="text-xs text-emerald-700 font-semibold bg-emerald-50 px-3 py-1.5 rounded-xl">✓ Selesai</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection
@push('scripts')
<script>
function updateStatus(id, status) {
    alert('Pesanan #' + id + ' → ' + status + ' (dummy)');
}
</script>
@endpush
