@extends('layouts.admin')
@section('title', 'Pesanan Masuk')
@section('page-title', 'Pesanan Masuk')
@section('page-subtitle', '3 pesanan baru menunggu konfirmasi')

@section('content')

{{-- Alert Banner --}}
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
    <div class="w-9 h-9 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow">
        <i class="fa-solid fa-bell text-white text-sm"></i>
    </div>
    <div class="flex-1">
        <p class="font-semibold text-blue-800 text-sm">3 pesanan baru masuk!</p>
        <p class="text-blue-600 text-xs">Segera konfirmasi untuk mulai memproses pesanan.</p>
    </div>
    <span class="badge-pulse bg-blue-500 text-white text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0">3 Baru</span>
</div>

{{-- Filter Tabs --}}
<div class="flex flex-wrap gap-2 mb-5">
    @php
    $tabs = [
        ['label'=>'Semua', 'count'=>12, 'active'=>true],
        ['label'=>'Baru', 'count'=>3, 'active'=>false],
        ['label'=>'Dikonfirmasi', 'count'=>5, 'active'=>false],
        ['label'=>'Selesai', 'count'=>4, 'active'=>false],
    ];
    @endphp
    @foreach($tabs as $tab)
    <button class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $tab['active'] ? 'bg-sidebar-bg text-white shadow-md' : 'bg-white border border-gray-200 text-gray-600 hover:border-gray-300' }}">
        {{ $tab['label'] }}
        <span class="text-[10px] {{ $tab['active'] ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }} px-1.5 py-0.5 rounded-full font-bold">{{ $tab['count'] }}</span>
    </button>
    @endforeach
</div>

@php
$orders = [
    ['id'=>'SC-047','name'=>'Ahmad Rizky','class'=>'XII IPA 2','time'=>'09:42','items'=>[['emoji'=>'🍛','name'=>'Nasi Gudeg Komplit','qty'=>1,'price'=>12000],['emoji'=>'🧋','name'=>'Es Teh Manis','qty'=>2,'price'=>4000]],'total'=>20000,'status'=>'Baru','pickup'=>'Istirahat 1'],
    ['id'=>'SC-046','name'=>'Siti Rahmawati','class'=>'XI IPS 1','time'=>'09:38','items'=>[['emoji'=>'🍜','name'=>'Mie Goreng Spesial','qty'=>1,'price'=>10000]],'total'=>10000,'status'=>'Baru','pickup'=>'Istirahat 1'],
    ['id'=>'SC-045','name'=>'Ricky Pratama','class'=>'X MIPA 3','time'=>'09:30','items'=>[['emoji'=>'🍗','name'=>'Nasi Ayam Geprek','qty'=>1,'price'=>13000],['emoji'=>'🧋','name'=>'Es Teh Manis','qty'=>1,'price'=>4000]],'total'=>17000,'status'=>'Baru','pickup'=>'Istirahat 1'],
    ['id'=>'SC-044','name'=>'Dewi Lestari','class'=>'XII IPA 1','time'=>'09:15','items'=>[['emoji'=>'🍲','name'=>'Bakso Urat Jumbo','qty'=>1,'price'=>11000],['emoji'=>'🧋','name'=>'Es Teh Manis','qty'=>1,'price'=>4000]],'total'=>15000,'status'=>'Dikonfirmasi','pickup'=>'Istirahat 2'],
    ['id'=>'SC-043','name'=>'Budi Santoso','class'=>'XI IPA 2','time'=>'08:55','items'=>[['emoji'=>'🍜','name'=>'Mie Goreng Spesial','qty'=>2,'price'=>10000]],'total'=>20000,'status'=>'Dikonfirmasi','pickup'=>'Istirahat 2'],
];
$statusConfig = [
    'Baru'         => ['bg'=>'bg-blue-100 text-blue-700', 'border'=>'border-l-4 border-l-blue-400'],
    'Dikonfirmasi' => ['bg'=>'bg-amber-100 text-amber-700', 'border'=>'border-l-4 border-l-amber-400'],
    'Selesai'      => ['bg'=>'bg-emerald-100 text-emerald-700', 'border'=>''],
];
@endphp

{{-- DESKTOP TABLE --}}
<div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Pesanan</th>
                <th class="text-left px-4 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Siswa</th>
                <th class="text-left px-4 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Detail Menu</th>
                <th class="text-right px-4 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                <th class="text-center px-4 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Pickup</th>
                <th class="text-center px-4 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="text-center px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($orders as $o)
            @php $sc = $statusConfig[$o['status']]; @endphp
            <tr class="hover:bg-gray-50/50 transition-colors {{ $o['status']==='Baru' ? 'bg-blue-50/30' : '' }}">
                <td class="px-6 py-4">
                    <p class="font-heading font-bold text-sm text-gray-800">#{{ $o['id'] }}</p>
                    <p class="text-[10px] text-gray-400 flex items-center gap-1 mt-0.5"><i class="fa-solid fa-clock text-gray-300"></i>{{ $o['time'] }}</p>
                </td>
                <td class="px-4 py-4">
                    <p class="font-semibold text-sm text-gray-800">{{ $o['name'] }}</p>
                    <p class="text-[10px] text-gray-400">{{ $o['class'] }}</p>
                </td>
                <td class="px-4 py-4">
                    <div class="space-y-0.5">
                        @foreach($o['items'] as $item)
                        <p class="text-xs text-gray-600">{{ $item['emoji'] }} {{ $item['name'] }} ×{{ $item['qty'] }}</p>
                        @endforeach
                    </div>
                </td>
                <td class="px-4 py-4 text-right">
                    <p class="font-heading font-bold text-sm text-gray-800">Rp {{ number_format($o['total'], 0, ',', '.') }}</p>
                </td>
                <td class="px-4 py-4 text-center">
                    <span class="text-[10px] bg-gray-100 text-gray-600 font-medium px-2 py-1 rounded-lg">{{ $o['pickup'] }}</span>
                </td>
                <td class="px-4 py-4 text-center">
                    <span class="inline-flex items-center gap-1 {{ $sc['bg'] }} text-[10px] font-bold px-2.5 py-1.5 rounded-full">
                        @if($o['status']==='Baru')<i class="fa-solid fa-circle badge-pulse text-[8px]"></i>@endif
                        {{ $o['status'] }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($o['status']==='Baru')
                    <button onclick="acceptOrder('{{ $o['id'] }}')" class="btn-brand text-white text-xs font-bold px-4 py-2 rounded-xl shadow flex items-center gap-1.5 mx-auto">
                        <i class="fa-solid fa-check"></i>Terima
                    </button>
                    @elseif($o['status']==='Dikonfirmasi')
                    <a href="{{ url('/admin/delivery') }}" class="text-xs text-violet-600 bg-violet-50 hover:bg-violet-100 font-semibold px-3 py-2 rounded-xl transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-motorcycle"></i>Proses
                    </a>
                    @else
                    <span class="text-xs text-emerald-600 font-semibold">✓ Selesai</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- MOBILE CARD LIST --}}
<div class="md:hidden space-y-4">
    @foreach($orders as $o)
    @php $sc = $statusConfig[$o['status']]; @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden {{ $sc['border'] }}">
        {{-- Card Header --}}
        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <p class="font-heading font-bold text-sm text-gray-800">#{{ $o['id'] }}</p>
                <span class="text-[10px] text-gray-400">· {{ $o['time'] }}</span>
            </div>
            <span class="inline-flex items-center gap-1 {{ $sc['bg'] }} text-[10px] font-bold px-2.5 py-1 rounded-full">
                @if($o['status']==='Baru')<i class="fa-solid fa-circle badge-pulse text-[8px]"></i>@endif
                {{ $o['status'] }}
            </span>
        </div>
        {{-- Card Body --}}
        <div class="px-4 py-3.5">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <p class="font-semibold text-sm text-gray-800">{{ $o['name'] }}</p>
                    <p class="text-xs text-gray-400">{{ $o['class'] }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-heading font-bold text-base text-gray-800">Rp {{ number_format($o['total'], 0, ',', '.') }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $o['pickup'] }}</p>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 mb-3 space-y-1">
                @foreach($o['items'] as $item)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-700">{{ $item['emoji'] }} {{ $item['name'] }} ×{{ $item['qty'] }}</span>
                    <span class="font-medium text-gray-600">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @if($o['status']==='Baru')
            <button onclick="acceptOrder('{{ $o['id'] }}')" class="btn-brand w-full text-white text-sm font-bold py-2.5 rounded-xl shadow flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i>Terima Pesanan
            </button>
            @elseif($o['status']==='Dikonfirmasi')
            <a href="{{ url('/admin/delivery') }}" class="block w-full text-center text-sm text-violet-600 bg-violet-50 font-semibold py-2.5 rounded-xl transition-colors">
                <i class="fa-solid fa-motorcycle mr-2"></i>Proses Pengiriman
            </a>
            @endif
        </div>
    </div>
    @endforeach
</div>

@endsection

@push('scripts')
<script>
    function acceptOrder(id) {
        if (confirm('Terima pesanan #' + id + '? Menu akan mulai diproses.')) {
            alert('Pesanan #' + id + ' diterima! (dummy)');
        }
    }
</script>
@endpush
