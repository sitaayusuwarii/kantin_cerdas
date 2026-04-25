@extends('layouts.admin')
@section('title', 'Proses Pengiriman')
@section('page-title', 'Proses Pengiriman')
@section('page-subtitle', 'Kelola status pengiriman pesanan aktif')

@section('content')

{{-- Progress Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @php
    $delivStats = [
        ['label'=>'Diproses', 'value'=>4, 'icon'=>'fa-fire-burner', 'color'=>'from-amber-400 to-orange-500', 'bg'=>'bg-amber-50', 'text'=>'text-amber-600'],
        ['label'=>'Dikirim', 'value'=>3, 'icon'=>'fa-motorcycle', 'color'=>'from-blue-400 to-blue-600', 'bg'=>'bg-blue-50', 'text'=>'text-blue-600'],
        ['label'=>'Selesai Hari Ini', 'value'=>35, 'icon'=>'fa-circle-check', 'color'=>'from-emerald-400 to-emerald-600', 'bg'=>'bg-emerald-50', 'text'=>'text-emerald-600'],
    ];
    @endphp
    @foreach($delivStats as $s)
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br {{ $s['color'] }} rounded-xl flex items-center justify-center shadow flex-shrink-0">
            <i class="fa-solid {{ $s['icon'] }} text-white text-sm"></i>
        </div>
        <div>
            <p class="font-heading font-bold text-xl text-gray-800">{{ $s['value'] }}</p>
            <p class="text-xs text-gray-400">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Kanban / Column View --}}
@php
$deliveries = [
    ['id'=>'SC-046','name'=>'Siti Rahmawati','class'=>'XI IPS 1','items'=>'Mie Goreng Spesial','total'=>'Rp 10.000','status'=>'Diproses','pickup'=>'Istirahat 1','time'=>'09:45','duration'=>5],
    ['id'=>'SC-044','name'=>'Dewi Lestari','class'=>'XII IPA 1','items'=>'Bakso Urat + Es Teh','total'=>'Rp 15.000','status'=>'Diproses','pickup'=>'Istirahat 2','time'=>'09:20','duration'=>15],
    ['id'=>'SC-043','name'=>'Budi Santoso','class'=>'XI IPA 2','items'=>'2x Mie Goreng Spesial','total'=>'Rp 20.000','status'=>'Diproses','pickup'=>'Istirahat 2','time'=>'09:00','duration'=>25],
    ['id'=>'SC-040','name'=>'Ahmad Rizky','class'=>'XII IPA 2','items'=>'Nasi Gudeg + Es Teh','total'=>'Rp 16.000','status'=>'Dikirim','pickup'=>'Istirahat 1','time'=>'09:35','duration'=>8],
    ['id'=>'SC-039','name'=>'Maya Sari','class'=>'X IPA 1','items'=>'Nasi Ayam Geprek','total'=>'Rp 13.000','status'=>'Dikirim','pickup'=>'Istirahat 1','time'=>'09:28','duration'=>12],
    ['id'=>'SC-038','name'=>'Rizal Maulana','class'=>'XII IPS 1','items'=>'Bakso Urat Jumbo','total'=>'Rp 11.000','status'=>'Dikirim','pickup'=>'Istirahat 1','time'=>'09:25','duration'=>15],
    ['id'=>'SC-035','name'=>'Putri Indah','class'=>'XI IPA 1','items'=>'Nasi Gudeg Komplit','total'=>'Rp 12.000','status'=>'Selesai','pickup'=>'Istirahat 1','time'=>'09:10','duration'=>30],
    ['id'=>'SC-034','name'=>'Fahrul Aziz','class'=>'X IPS 2','items'=>'Mie Goreng + Jus','total'=>'Rp 18.000','status'=>'Selesai','pickup'=>'Istirahat 1','time'=>'09:05','duration'=>35],
];

$columns = [
    'Diproses' => ['color'=>'amber', 'icon'=>'fa-fire-burner', 'next'=>'Dikirim', 'nextLabel'=>'Kirim Sekarang'],
    'Dikirim'  => ['color'=>'blue',  'icon'=>'fa-motorcycle',  'next'=>'Selesai',  'nextLabel'=>'Tandai Selesai'],
    'Selesai'  => ['color'=>'emerald','icon'=>'fa-circle-check','next'=>null,       'nextLabel'=>null],
];
@endphp

{{-- Desktop Kanban --}}
<div class="hidden md:grid md:grid-cols-3 gap-5">
    @foreach($columns as $colName => $col)
    @php $colItems = collect($deliveries)->where('status', $colName); @endphp
    <div class="bg-gray-100/80 rounded-2xl p-4">
        {{-- Column Header --}}
        <div class="flex items-center gap-2 mb-4">
            <div class="w-7 h-7 bg-{{ $col['color'] }}-500 rounded-lg flex items-center justify-center">
                <i class="fa-solid {{ $col['icon'] }} text-white text-xs"></i>
            </div>
            <h3 class="font-heading font-bold text-sm text-gray-700">{{ $colName }}</h3>
            <span class="ml-auto bg-{{ $col['color'] }}-100 text-{{ $col['color'] }}-700 text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">{{ $colItems->count() }}</span>
        </div>

        {{-- Column Items --}}
        <div class="space-y-3">
            @foreach($colItems as $d)
            <div class="bg-white rounded-xl p-4 shadow-sm border border-white hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between gap-2 mb-2.5">
                    <div>
                        <p class="font-heading font-bold text-xs text-gray-800">#{{ $d['id'] }}</p>
                        <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $d['name'] }}</p>
                        <p class="text-[10px] text-gray-400">{{ $d['class'] }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-heading font-bold text-sm text-brand-500">{{ $d['total'] }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-0.5 justify-end"><i class="fa-solid fa-clock text-gray-300 text-[9px]"></i>{{ $d['duration'] }}m</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg px-3 py-2 mb-3">
                    <p class="text-[11px] text-gray-600 font-medium">{{ $d['items'] }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1">
                        <i class="fa-solid fa-clock text-gray-300"></i>Pickup: {{ $d['pickup'] }}
                    </p>
                </div>
                @if($col['next'])
                <button onclick="updateStatus('{{ $d['id'] }}', '{{ $col['next'] }}')"
                    class="w-full py-2 rounded-lg text-xs font-bold transition-all
                    {{ $colName==='Diproses' ? 'btn-brand text-white shadow' : 'bg-emerald-500 hover:bg-emerald-600 text-white shadow' }}
                    flex items-center justify-center gap-1.5">
                    <i class="fa-solid {{ $colName==='Diproses' ? 'fa-motorcycle' : 'fa-check' }} text-[10px]"></i>
                    {{ $col['nextLabel'] }}
                </button>
                @else
                <div class="flex items-center justify-center gap-1.5 py-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                    <span class="text-xs text-emerald-600 font-semibold">Selesai</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- Mobile Timeline List --}}
<div class="md:hidden space-y-3">
    @foreach($deliveries as $d)
    @php
    $stColors = ['Diproses'=>['bg'=>'bg-amber-100','text'=>'text-amber-700','dot'=>'bg-amber-400'], 'Dikirim'=>['bg'=>'bg-blue-100','text'=>'text-blue-700','dot'=>'bg-blue-400'], 'Selesai'=>['bg'=>'bg-emerald-100','text'=>'text-emerald-700','dot'=>'bg-emerald-400']];
    $sc = $stColors[$d['status']];
    @endphp
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between gap-3 mb-2">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full {{ $sc['dot'] }} flex-shrink-0 mt-1 {{ $d['status']!=='Selesai' ? 'badge-pulse':'' }}"></div>
                <div>
                    <p class="font-heading font-bold text-sm text-gray-800">#{{ $d['id'] }} · {{ $d['name'] }}</p>
                    <p class="text-[10px] text-gray-400">{{ $d['class'] }}</p>
                </div>
            </div>
            <span class="inline-flex items-center {{ $sc['bg'] }} {{ $sc['text'] }} text-[10px] font-bold px-2.5 py-1 rounded-full flex-shrink-0">{{ $d['status'] }}</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600 font-medium">{{ $d['items'] }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $d['pickup'] }} · {{ $d['total'] }}</p>
            </div>
            @php $col = $columns[$d['status']]; @endphp
            @if($col['next'])
            <button onclick="updateStatus('{{ $d['id'] }}', '{{ $col['next'] }}')"
                class="text-xs font-bold px-3.5 py-2 rounded-xl text-white shadow
                {{ $d['status']==='Diproses' ? 'btn-brand' : 'bg-emerald-500 hover:bg-emerald-600' }}
                flex items-center gap-1.5 flex-shrink-0">
                <i class="fa-solid {{ $d['status']==='Diproses' ? 'fa-motorcycle' : 'fa-check' }} text-[10px]"></i>
                {{ $col['nextLabel'] }}
            </button>
            @else
            <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-3 py-1.5 rounded-xl">✓ Selesai</span>
            @endif
        </div>
    </div>
    @endforeach
</div>

@endsection

@push('scripts')
<script>
    function updateStatus(id, newStatus) {
        alert('Pesanan #' + id + ' → ' + newStatus + ' (dummy)');
    }
</script>
@endpush
