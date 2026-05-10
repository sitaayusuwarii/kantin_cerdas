@extends('layouts.pengelola')
@section('title', 'Pesanan Masuk')
@section('page-title', 'Pesanan Masuk')
@section('page-subtitle', '3 pesanan baru menunggu konfirmasi')

@section('content')

{{-- Alert --}}
<div class="flex items-center gap-3 bg-white border border-orange-100 rounded-2xl p-4 mb-6 shadow-sm">
    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
        <i class="fa-solid fa-bell text-white text-sm"></i>
    </div>
    <div class="flex-1">
        <p class="font-semibold text-darkText text-sm">3 pesanan baru masuk!</p>
        <p class="text-gray-500 text-xs">Konfirmasi segera agar dapur mulai memproses.</p>
    </div>
    <span class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0 shadow-sm">
        3 Baru
    </span>
</div>

{{-- Filter Tabs --}}
<div class="flex flex-wrap gap-2 mb-5">
    @php
    $tabs = [
        ['label'=>'Semua','count'=>12,'active'=>true],
        ['label'=>'Baru','count'=>3,'active'=>false],
        ['label'=>'Dikonfirmasi','count'=>5,'active'=>false],
        ['label'=>'Selesai','count'=>4,'active'=>false],
    ];
    @endphp

    @foreach($tabs as $tab)
    <button class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all
        {{ $tab['active']
            ? 'bg-primary text-white shadow-md'
            : 'bg-white border border-orange-100 text-gray-600 hover:border-orange-300' }}">
        {{ $tab['label'] }}

        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
            {{ $tab['active'] ? 'bg-white/20 text-white' : 'bg-orange-50 text-primary' }}">
            {{ $tab['count'] }}
        </span>
    </button>
    @endforeach
</div>

@php
$orders = [
    ['id'=>'SC-047','name'=>'Ahmad Rizky','cls'=>'XII IPA 2','time'=>'09:42',
     'items'=>[['e'=>'🍛','n'=>'Nasi Gudeg Komplit','q'=>1,'p'=>12000],['e'=>'🧋','n'=>'Es Teh Manis','q'=>2,'p'=>4000]],
     'total'=>20000,'status'=>'Baru','pickup'=>'Istirahat 1'],

    ['id'=>'SC-046','name'=>'Siti Rahmawati','cls'=>'XI IPS 1','time'=>'09:38',
     'items'=>[['e'=>'🍜','n'=>'Mie Goreng Spesial','q'=>1,'p'=>10000]],
     'total'=>10000,'status'=>'Baru','pickup'=>'Istirahat 1'],

    ['id'=>'SC-045','name'=>'Ricky Pratama','cls'=>'X MIPA 3','time'=>'09:30',
     'items'=>[['e'=>'🍗','n'=>'Nasi Ayam Geprek','q'=>1,'p'=>13000],['e'=>'🧋','n'=>'Es Teh Manis','q'=>1,'p'=>4000]],
     'total'=>17000,'status'=>'Baru','pickup'=>'Istirahat 1'],

    ['id'=>'SC-044','name'=>'Dewi Lestari','cls'=>'XII IPA 1','time'=>'09:15',
     'items'=>[['e'=>'🍲','n'=>'Bakso Urat Jumbo','q'=>1,'p'=>11000],['e'=>'🧋','n'=>'Es Teh Manis','q'=>1,'p'=>4000]],
     'total'=>15000,'status'=>'Dikonfirmasi','pickup'=>'Istirahat 2'],

    ['id'=>'SC-043','name'=>'Budi Santoso','cls'=>'XI IPA 2','time'=>'08:55',
     'items'=>[['e'=>'🍜','n'=>'Mie Goreng Spesial','q'=>2,'p'=>10000]],
     'total'=>20000,'status'=>'Dikonfirmasi','pickup'=>'Istirahat 2'],
];

$stMap = [
    'Baru' => 'bg-orange-50 text-primary',
    'Dikonfirmasi' => 'bg-amber-50 text-amber-700',
    'Selesai' => 'bg-emerald-50 text-emerald-700',
];
@endphp

{{-- Desktop Table --}}
<div class="hidden md:block bg-white rounded-3xl shadow-sm border border-orange-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-orange-50 border-b border-orange-100">
                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Pesanan</th>
                <th class="text-left px-4 py-4 text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                <th class="text-left px-4 py-4 text-xs font-semibold text-gray-500 uppercase">Menu</th>
                <th class="text-right px-4 py-4 text-xs font-semibold text-gray-500 uppercase">Total</th>
                <th class="text-center px-3 py-4 text-xs font-semibold text-gray-500 uppercase">Pickup</th>
                <th class="text-center px-3 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="text-center px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-orange-50">
            @foreach($orders as $o)
            <tr class="hover:bg-orange-50/40 transition-colors">
                <td class="px-6 py-4">
                    <p class="font-semibold text-sm text-darkText">#{{ $o['id'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">
                        <i class="fa-solid fa-clock mr-1"></i>{{ $o['time'] }}
                    </p>
                </td>

                <td class="px-4 py-4">
                    <p class="font-semibold text-sm text-darkText">{{ $o['name'] }}</p>
                    <p class="text-xs text-gray-400">{{ $o['cls'] }}</p>
                </td>

                <td class="px-4 py-4">
                    @foreach($o['items'] as $it)
                    <p class="text-xs text-gray-600">{{ $it['e'] }} {{ $it['n'] }} ×{{ $it['q'] }}</p>
                    @endforeach
                </td>

                <td class="px-4 py-4 text-right font-semibold text-darkText">
                    Rp {{ number_format($o['total'],0,',','.') }}
                </td>

                <td class="px-3 py-4 text-center">
                    <span class="bg-orange-50 text-primary text-[11px] px-2.5 py-1 rounded-lg font-medium">
                        {{ $o['pickup'] }}
                    </span>
                </td>

                <td class="px-3 py-4 text-center">
                    <span class="inline-flex {{ $stMap[$o['status']] }} px-3 py-1 rounded-full text-[11px] font-semibold">
                        {{ $o['status'] }}
                    </span>
                </td>

                <td class="px-6 py-4 text-center">
                    @if($o['status']==='Baru')
                    <button onclick="acceptOrder('{{ $o['id'] }}')"
                        class="btn-primary text-white text-xs font-semibold px-4 py-2 rounded-xl">
                        Terima
                    </button>
                    @elseif($o['status']==='Dikonfirmasi')
                    <a href="{{ url('/pengelola/delivery') }}"
                       class="bg-amber-50 text-amber-700 px-4 py-2 rounded-xl text-xs font-semibold">
                        Proses
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Mobile --}}
<div class="md:hidden space-y-4">
    @foreach($orders as $o)
    <div class="bg-white rounded-2xl shadow-sm border border-orange-100 p-4">
        <div class="flex justify-between items-start mb-3">
            <div>
                <p class="font-semibold text-darkText">#{{ $o['id'] }}</p>
                <p class="text-xs text-gray-400">{{ $o['time'] }}</p>
            </div>

            <span class="{{ $stMap[$o['status']] }} text-[11px] font-semibold px-3 py-1 rounded-full">
                {{ $o['status'] }}
            </span>
        </div>

        <p class="font-semibold text-sm text-darkText">{{ $o['name'] }}</p>
        <p class="text-xs text-gray-400 mb-3">{{ $o['cls'] }} · {{ $o['pickup'] }}</p>

        <div class="bg-orange-50 rounded-xl p-3 mb-3">
            @foreach($o['items'] as $it)
            <p class="text-xs text-gray-600">{{ $it['e'] }} {{ $it['n'] }} ×{{ $it['q'] }}</p>
            @endforeach
        </div>

        <div class="flex justify-between items-center">
            <p class="font-semibold text-darkText">
                Rp {{ number_format($o['total'],0,',','.') }}
            </p>

            @if($o['status']==='Baru')
            <button onclick="acceptOrder('{{ $o['id'] }}')"
                class="btn-primary text-white text-xs px-4 py-2 rounded-xl">
                Terima
            </button>
            @endif
        </div>
    </div>
    @endforeach
</div>

@endsection

@push('scripts')
<script>
function acceptOrder(id) {
    if (confirm('Terima pesanan #' + id + '?')) {
        alert('Pesanan diterima! (dummy)');
    }
}
</script>
@endpush