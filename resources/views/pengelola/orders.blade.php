@extends('layouts.pengelola')
@section('title', 'Pesanan Masuk')
@section('page-title', 'Pesanan Masuk')
@section('page-subtitle', '3 pesanan baru menunggu konfirmasi')

@section('content')

{{-- Alert --}}
<div class="flex items-center gap-3 bg-forest-50 border border-forest-200 rounded-2xl p-4 mb-6">
    <div class="w-9 h-9 bg-forest-700 rounded-xl flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-bell text-white text-sm"></i>
    </div>
    <div class="flex-1">
        <p class="font-semibold text-forest-900 text-sm">3 pesanan baru masuk!</p>
        <p class="text-forest-600 text-xs">Konfirmasi segera agar dapur mulai memproses.</p>
    </div>
    <span class="badge-new bg-forest-600 text-white text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0">3 Baru</span>
</div>

{{-- Filter Tabs --}}
<div class="flex flex-wrap gap-2 mb-5">
    @php
    $tabs = [
        ['label'=>'Semua',       'count'=>12,'active'=>true],
        ['label'=>'Baru',        'count'=>3, 'active'=>false],
        ['label'=>'Dikonfirmasi','count'=>5, 'active'=>false],
        ['label'=>'Selesai',     'count'=>4, 'active'=>false],
    ];
    @endphp
    @foreach($tabs as $tab)
    <button class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all
        {{ $tab['active']
            ? 'bg-forest-800 text-cream-100 shadow-md'
            : 'bg-cream-50 border border-cream-300 text-forest-600 hover:border-forest-400' }}">
        {{ $tab['label'] }}
        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
            {{ $tab['active'] ? 'bg-white/15 text-white' : 'bg-cream-200 text-forest-600' }}">
            {{ $tab['count'] }}
        </span>
    </button>
    @endforeach
</div>

@php
$orders = [
    ['id'=>'SC-047','name'=>'Ahmad Rizky',    'cls'=>'XII IPA 2','time'=>'09:42',
     'items'=>[['e'=>'🍛','n'=>'Nasi Gudeg Komplit','q'=>1,'p'=>12000],['e'=>'🧋','n'=>'Es Teh Manis','q'=>2,'p'=>4000]],
     'total'=>20000,'status'=>'Baru','pickup'=>'Istirahat 1'],
    ['id'=>'SC-046','name'=>'Siti Rahmawati', 'cls'=>'XI IPS 1', 'time'=>'09:38',
     'items'=>[['e'=>'🍜','n'=>'Mie Goreng Spesial','q'=>1,'p'=>10000]],
     'total'=>10000,'status'=>'Baru','pickup'=>'Istirahat 1'],
    ['id'=>'SC-045','name'=>'Ricky Pratama',  'cls'=>'X MIPA 3', 'time'=>'09:30',
     'items'=>[['e'=>'🍗','n'=>'Nasi Ayam Geprek','q'=>1,'p'=>13000],['e'=>'🧋','n'=>'Es Teh Manis','q'=>1,'p'=>4000]],
     'total'=>17000,'status'=>'Baru','pickup'=>'Istirahat 1'],
    ['id'=>'SC-044','name'=>'Dewi Lestari',   'cls'=>'XII IPA 1','time'=>'09:15',
     'items'=>[['e'=>'🍲','n'=>'Bakso Urat Jumbo','q'=>1,'p'=>11000],['e'=>'🧋','n'=>'Es Teh Manis','q'=>1,'p'=>4000]],
     'total'=>15000,'status'=>'Dikonfirmasi','pickup'=>'Istirahat 2'],
    ['id'=>'SC-043','name'=>'Budi Santoso',   'cls'=>'XI IPA 2', 'time'=>'08:55',
     'items'=>[['e'=>'🍜','n'=>'Mie Goreng Spesial','q'=>2,'p'=>10000]],
     'total'=>20000,'status'=>'Dikonfirmasi','pickup'=>'Istirahat 2'],
];
$stMap = [
    'Baru'         => 'bg-forest-100 text-forest-700',
    'Dikonfirmasi' => 'bg-amber-100 text-amber-700',
    'Selesai'      => 'bg-emerald-100 text-emerald-700',
];
@endphp

{{-- ── DESKTOP TABLE ────────────────────────────────────── --}}
<div class="hidden md:block bg-cream-50 rounded-2xl shadow-sm border border-cream-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-cream-100/80 border-b border-cream-200">
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Pesanan</th>
                <th class="text-left px-4 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Siswa</th>
                <th class="text-left px-4 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Menu</th>
                <th class="text-right px-4 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Total</th>
                <th class="text-center px-3 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Pickup</th>
                <th class="text-center px-3 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Status</th>
                <th class="text-center px-6 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-cream-200">
            @foreach($orders as $o)
            <tr class="hover:bg-cream-100/50 transition-colors {{ $o['status']==='Baru' ? 'bg-forest-50/40':'' }}">
                <td class="px-6 py-4">
                    <p class="font-display font-semibold text-sm text-forest-900">#{{ $o['id'] }}</p>
                    <p class="text-[10px] text-forest-400 flex items-center gap-1 mt-0.5">
                        <i class="fa-solid fa-clock text-forest-300"></i>{{ $o['time'] }}
                    </p>
                </td>
                <td class="px-4 py-4">
                    <p class="font-semibold text-xs text-forest-900">{{ $o['name'] }}</p>
                    <p class="text-[10px] text-forest-400">{{ $o['cls'] }}</p>
                </td>
                <td class="px-4 py-4">
                    <div class="space-y-0.5">
                        @foreach($o['items'] as $it)
                        <p class="text-xs text-forest-600">{{ $it['e'] }} {{ $it['n'] }} ×{{ $it['q'] }}</p>
                        @endforeach
                    </div>
                </td>
                <td class="px-4 py-4 text-right font-display font-bold text-sm text-forest-900">
                    Rp {{ number_format($o['total'],0,',','.') }}
                </td>
                <td class="px-3 py-4 text-center">
                    <span class="text-[10px] bg-cream-200 text-forest-600 font-medium px-2 py-1 rounded-lg">
                        {{ $o['pickup'] }}
                    </span>
                </td>
                <td class="px-3 py-4 text-center">
                    <span class="inline-flex items-center gap-1 {{ $stMap[$o['status']] }}
                                 text-[10px] font-bold px-2.5 py-1.5 rounded-full">
                        @if($o['status']==='Baru')
                        <i class="fa-solid fa-circle badge-new text-[7px]"></i>
                        @endif
                        {{ $o['status'] }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($o['status']==='Baru')
                    <button onclick="acceptOrder('{{ $o['id'] }}')"
                            class="btn-primary text-white text-xs font-bold px-4 py-2 rounded-xl
                                   shadow flex items-center gap-1.5 mx-auto">
                        <i class="fa-solid fa-check text-[10px]"></i>Terima
                    </button>
                    @elseif($o['status']==='Dikonfirmasi')
                    <a href="{{ url('/pengelola/delivery') }}"
                       class="text-xs text-amber-700 bg-amber-50 hover:bg-amber-100 font-semibold
                              px-3 py-2 rounded-xl transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-truck-fast text-[10px]"></i>Proses
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

{{-- ── MOBILE CARDS ─────────────────────────────────────── --}}
<div class="md:hidden space-y-4">
    @foreach($orders as $o)
    @php $borderColor = $o['status']==='Baru' ? 'border-l-forest-500' : ($o['status']==='Dikonfirmasi' ? 'border-l-amber-500' : 'border-l-emerald-500'); @endphp
    <div class="bg-cream-50 rounded-2xl shadow-sm border border-cream-200 overflow-hidden border-l-4 {{ $borderColor }}">
        <div class="flex items-center justify-between px-4 py-3 bg-cream-100/60 border-b border-cream-200">
            <div class="flex items-center gap-2">
                <p class="font-display font-bold text-sm text-forest-900">#{{ $o['id'] }}</p>
                <span class="text-[10px] text-forest-400">· {{ $o['time'] }}</span>
            </div>
            <span class="inline-flex items-center gap-1 {{ $stMap[$o['status']] }}
                         text-[10px] font-bold px-2.5 py-1 rounded-full">
                @if($o['status']==='Baru')<i class="fa-solid fa-circle badge-new text-[7px]"></i>@endif
                {{ $o['status'] }}
            </span>
        </div>
        <div class="px-4 py-3.5">
            <div class="flex items-start justify-between gap-3 mb-2.5">
                <div>
                    <p class="font-semibold text-sm text-forest-900">{{ $o['name'] }}</p>
                    <p class="text-xs text-forest-400">{{ $o['cls'] }} · {{ $o['pickup'] }}</p>
                </div>
                <p class="font-display font-bold text-base text-forest-800 flex-shrink-0">
                    Rp {{ number_format($o['total'],0,',','.') }}
                </p>
            </div>
            <div class="bg-cream-100 rounded-xl p-3 mb-3 space-y-1">
                @foreach($o['items'] as $it)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-forest-700">{{ $it['e'] }} {{ $it['n'] }} ×{{ $it['q'] }}</span>
                    <span class="font-medium text-forest-600">Rp {{ number_format($it['p']*$it['q'],0,',','.') }}</span>
                </div>
                @endforeach
            </div>
            @if($o['status']==='Baru')
            <button onclick="acceptOrder('{{ $o['id'] }}')"
                    class="btn-primary w-full text-white text-sm font-bold py-2.5 rounded-xl shadow
                           flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i>Terima Pesanan
            </button>
            @elseif($o['status']==='Dikonfirmasi')
            <a href="{{ url('/pengelola/delivery') }}"
               class="block w-full text-center text-sm text-amber-700 bg-amber-50 font-semibold py-2.5 rounded-xl transition-colors">
                <i class="fa-solid fa-truck-fast mr-1.5"></i>Proses Pengiriman
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
    if (confirm('Terima pesanan #' + id + '?')) alert('Pesanan diterima! (dummy)');
}
</script>
@endpush
