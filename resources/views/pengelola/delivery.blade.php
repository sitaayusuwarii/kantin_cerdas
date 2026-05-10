@extends('layouts.pengelola')
@section('title', 'Proses Pengiriman')
@section('page-title', 'Proses Pengiriman')
@section('page-subtitle', 'Kelola status pengiriman pesanan aktif')

@section('content')

{{-- SUMMARY --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @php
    $dStats = [
        ['label'=>'Diproses','val'=>4,'icon'=>'fa-fire-burner','bg'=>'bg-amber-500'],
        ['label'=>'Dikirim','val'=>3,'icon'=>'fa-truck-fast','bg'=>'bg-primary'],
        ['label'=>'Selesai Hari Ini','val'=>35,'icon'=>'fa-circle-check','bg'=>'bg-emerald-500'],
    ];
    @endphp

    @foreach($dStats as $ds)
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-orange-100 flex items-center gap-3">
        <div class="w-11 h-11 {{ $ds['bg'] }} rounded-2xl flex items-center justify-center shadow-sm">
            <i class="fa-solid {{ $ds['icon'] }} text-white text-sm"></i>
        </div>

        <div>
            <p class="text-2xl font-bold text-darkText">{{ $ds['val'] }}</p>
            <p class="text-xs text-gray-500">{{ $ds['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

@php
$deliveries = [
    ['id'=>'SC-046','name'=>'Siti Rahmawati','cls'=>'XI IPS 1','items'=>'Mie Goreng Spesial','total'=>'Rp 10.000','status'=>'Diproses','pickup'=>'Istirahat 1','dur'=>5],
    ['id'=>'SC-044','name'=>'Dewi Lestari','cls'=>'XII IPA 1','items'=>'Bakso Urat + Es Teh','total'=>'Rp 15.000','status'=>'Diproses','pickup'=>'Istirahat 2','dur'=>15],
    ['id'=>'SC-043','name'=>'Budi Santoso','cls'=>'XI IPA 2','items'=>'2× Mie Goreng','total'=>'Rp 20.000','status'=>'Diproses','pickup'=>'Istirahat 2','dur'=>25],
    ['id'=>'SC-040','name'=>'Ahmad Rizky','cls'=>'XII IPA 2','items'=>'Nasi Gudeg + Es Teh','total'=>'Rp 16.000','status'=>'Dikirim','pickup'=>'Istirahat 1','dur'=>8],
    ['id'=>'SC-039','name'=>'Maya Sari','cls'=>'X IPA 1','items'=>'Nasi Ayam Geprek','total'=>'Rp 13.000','status'=>'Dikirim','pickup'=>'Istirahat 1','dur'=>12],
    ['id'=>'SC-038','name'=>'Rizal Maulana','cls'=>'XII IPS 1','items'=>'Bakso Urat Jumbo','total'=>'Rp 11.000','status'=>'Dikirim','pickup'=>'Istirahat 1','dur'=>15],
    ['id'=>'SC-035','name'=>'Putri Indah','cls'=>'XI IPA 1','items'=>'Nasi Gudeg Komplit','total'=>'Rp 12.000','status'=>'Selesai','pickup'=>'Istirahat 1','dur'=>30],
    ['id'=>'SC-034','name'=>'Fahrul Aziz','cls'=>'X IPS 2','items'=>'Mie Goreng + Jus','total'=>'Rp 18.000','status'=>'Selesai','pickup'=>'Istirahat 1','dur'=>35],
];

$cols = [
    'Diproses' => [
        'icon'=>'fa-fire-burner',
        'head'=>'bg-amber-500',
        'next'=>'Dikirim',
        'btnLabel'=>'Kirim Sekarang',
        'btnClass'=>'btn-primary'
    ],
    'Dikirim' => [
        'icon'=>'fa-truck-fast',
        'head'=>'bg-primary',
        'next'=>'Selesai',
        'btnLabel'=>'Tandai Selesai',
        'btnClass'=>'bg-emerald-500 hover:bg-emerald-600'
    ],
    'Selesai' => [
        'icon'=>'fa-circle-check',
        'head'=>'bg-emerald-500',
        'next'=>null,
        'btnLabel'=>null,
        'btnClass'=>''
    ],
];
@endphp

{{-- DESKTOP KANBAN --}}
<div class="hidden md:grid md:grid-cols-3 gap-5">
    @foreach($cols as $colName => $col)
    @php $colItems = collect($deliveries)->where('status', $colName); @endphp

    <div class="bg-white rounded-3xl border border-orange-100 shadow-sm p-4">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 {{ $col['head'] }} rounded-xl flex items-center justify-center">
                <i class="fa-solid {{ $col['icon'] }} text-white text-xs"></i>
            </div>

            <h3 class="font-semibold text-darkText text-sm">{{ $colName }}</h3>

            <span class="ml-auto w-7 h-7 bg-orange-50 text-primary text-xs font-bold rounded-full flex items-center justify-center">
                {{ $colItems->count() }}
            </span>
        </div>

        <div class="space-y-3">
            @foreach($colItems as $d)
            <div class="bg-white border border-orange-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-semibold text-darkText text-sm">#{{ $d['id'] }}</p>
                        <p class="text-sm font-medium text-darkText mt-1">{{ $d['name'] }}</p>
                        <p class="text-xs text-gray-400">{{ $d['cls'] }}</p>
                    </div>

                    <div class="text-right">
                        <p class="font-semibold text-primary">{{ $d['total'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $d['dur'] }}m</p>
                    </div>
                </div>

                <div class="bg-orange-50 rounded-xl p-3 mb-3">
                    <p class="text-sm text-gray-700">{{ $d['items'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $d['pickup'] }}</p>
                </div>

                @if($col['next'])
                <button onclick="updateStatus('{{ $d['id'] }}','{{ $col['next'] }}')"
                        class="{{ $col['btnClass'] }} w-full py-2.5 rounded-xl text-white text-sm font-semibold">
                    {{ $col['btnLabel'] }}
                </button>
                @else
                <div class="text-center py-2 text-emerald-600 font-semibold text-sm">
                    ✓ Selesai
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- MOBILE --}}
<div class="md:hidden space-y-4">
    @foreach($deliveries as $d)
    @php
    $statusColor = match($d['status']) {
        'Diproses' => 'bg-amber-50 text-amber-700',
        'Dikirim' => 'bg-orange-50 text-primary',
        default => 'bg-emerald-50 text-emerald-700'
    };
    @endphp

    <div class="bg-white rounded-2xl border border-orange-100 shadow-sm p-4">
        <div class="flex justify-between items-start mb-3">
            <div>
                <p class="font-semibold text-darkText">#{{ $d['id'] }}</p>
                <p class="text-xs text-gray-400">{{ $d['name'] }} · {{ $d['cls'] }}</p>
            </div>

            <span class="{{ $statusColor }} text-xs font-semibold px-3 py-1 rounded-full">
                {{ $d['status'] }}
            </span>
        </div>

        <div class="bg-orange-50 rounded-xl p-3 mb-3">
            <p class="text-sm text-gray-700">{{ $d['items'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $d['pickup'] }} · {{ $d['total'] }}</p>
        </div>

        @if($d['status'] !== 'Selesai')
        <button onclick="updateStatus('{{ $d['id'] }}','next')"
                class="btn-primary w-full py-2.5 rounded-xl text-white text-sm font-semibold">
            Update Status
        </button>
        @else
        <div class="text-center text-emerald-600 font-semibold text-sm">
            ✓ Selesai
        </div>
        @endif
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