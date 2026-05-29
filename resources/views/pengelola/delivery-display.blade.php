@extends('layouts.pengelola')
@section('title', 'Display Pesanan')
@section('page-title', 'Display Pesanan')
@section('page-subtitle', 'Tampilan live status pengiriman pesanan aktif')

@section('content')


{{-- ── SUMMARY STATS ────────────────────────────────────── --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    @php
    $dStats = [
        [
            'label' => 'Diproses',
            'val'   => $deliveries->where('status', 'diproses')->count(),
            'icon'  => 'fa-fire-burner',
            'bg'    => 'bg-amber-600'
        ],
        [
            'label' => 'Selesai Dimasak',
            'val'   => $deliveries->where('status', 'selesai_dimasak')->count(),
            'icon'  => 'fa-bell',
            'bg'    => 'bg-green-500'
        ],
        [
            'label' => 'Dikirim',
            'val'   => $deliveries->where('status', 'dikirim')->count(),
            'icon'  => 'fa-truck-fast',
            'bg'    => 'bg-blue-500'
        ],
        [
            'label' => 'Selesai',
            'val'   => $deliveries->where('status', 'selesai')->count(),
            'icon'  => 'fa-circle-check',
            'bg'    => 'bg-emerald-500'
        ],
    ];
    @endphp
    @foreach($dStats as $ds)
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-cream-200 flex items-center gap-3">
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
$cols = [
    'diproses' => [
        'label' => 'Sedang Diproses',
        'dot'   => 'bg-amber-500',
        'icon'  => 'fa-fire-burner',
        'head'  => 'bg-amber-600',
    ],
    'selesai_dimasak' => [
        'label' => 'Selesai Dimasak',
        'dot'   => 'bg-green-500',
        'icon'  => 'fa-bell',
        'head'  => 'bg-green-500',
    ],
    'dikirim' => [
        'label' => 'Sedang Dikirim',
        'dot'   => 'bg-blue-500',
        'icon'  => 'fa-truck-fast',
        'head'  => 'bg-blue-500',
    ],
    'selesai' => [
        'label' => 'Selesai',
        'dot'   => 'bg-emerald-500',
        'icon'  => 'fa-circle-check',
        'head'  => 'bg-emerald-500',
    ],
];
@endphp

{{-- ── DESKTOP KANBAN ───────────────────────────────────── --}}
<div class="hidden md:grid md:grid-cols-4 gap-4">
    @foreach($cols as $colName => $col)
    @php $colItems = $deliveries->where('status', $colName); @endphp
    <div class="bg-cream-100/70 rounded-2xl p-4">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-7 h-7 {{ $col['head'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fa-solid {{ $col['icon'] }} text-white text-xs"></i>
            </div>
            <h3 class="font-display font-semibold text-sm text-forest-900">{{ $col['label'] }}</h3>
            <span class="ml-auto w-6 h-6 bg-cream-200 text-forest-600 text-xs font-bold rounded-full flex items-center justify-center">
                {{ $colItems->count() }}
            </span>
        </div>

        <div class="space-y-3">
            @foreach($colItems as $d)
            <div class="bg-white rounded-xl p-4 shadow-sm border border-cream-200">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div>
                        <p class="font-display font-bold text-xs text-forest-900">#{{ $d->order->order_number }}</p>
                        <p class="text-xs font-medium text-forest-800 mt-0.5">{{ $d->order->user->full_name }}</p>
                        <p class="text-[10px] text-forest-400">{{ $d->order->user->class }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-display font-bold text-sm text-forest-700">Rp {{ number_format($d->order->total_price, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-forest-400 flex items-center gap-0.5 justify-end mt-0.5">
                            <i class="fa-solid fa-clock text-forest-300 text-[9px]"></i>{{ $d->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <div class="bg-cream-100 rounded-lg px-3 py-2">
                    <div class="space-y-1 mb-2">
                        @foreach($d->order->items as $item)
                            <p class="text-[11px] text-forest-700 font-medium">
                                {{ $item->menu->name }} ×{{ $item->quantity }}
                            </p>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                        <span class="inline-flex items-center text-[10px] font-semibold px-2 py-1 rounded-full {{ $d->order->order_type_color }}">
                            {{ $d->order->order_type_label }}
                        </span>
                        @if($d->order->isDelivery() && $d->order->classroom)
                            <span class="inline-flex items-center gap-1 text-[10px] font-medium px-2 py-1 rounded-full bg-purple-100 text-purple-700">
                                <i class="fa-solid fa-location-dot"></i> {{ $d->order->classroom }}
                            </span>
                        @endif
                    </div>
                    @if($d->order->note)
                        <p class="text-[10px] text-gray-400 italic">📝 {{ $d->order->note }}</p>
                    @endif
                    <p class="text-[10px] text-forest-400 mt-0.5">
                        <i class="fa-solid fa-clock text-forest-300"></i> {{ $d->order->pickup_display }}
                    </p>
                </div>

                {{-- Status badge pengganti tombol --}}
                <div class="mt-3 flex items-center justify-center gap-1.5 py-1.5 rounded-xl
                    {{ match($d->status) {
                        'diproses'        => 'bg-amber-50',
                        'selesai_dimasak' => 'bg-green-50',
                        'dikirim'         => 'bg-blue-50',
                        default           => 'bg-emerald-50',
                    } }}">
                    <i class="fa-solid {{ $col['icon'] }} text-xs
                        {{ match($d->status) {
                            'diproses'        => 'text-amber-500',
                            'selesai_dimasak' => 'text-green-500',
                            'dikirim'         => 'text-blue-500',
                            default           => 'text-emerald-500',
                        } }}"></i>
                    <span class="text-xs font-semibold
                        {{ match($d->status) {
                            'diproses'        => 'text-amber-700',
                            'selesai_dimasak' => 'text-green-700',
                            'dikirim'         => 'text-blue-700',
                            default           => 'text-emerald-700',
                        } }}">{{ $col['label'] }}</span>
                </div>
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
    $col = $cols[$d->status] ?? $cols['selesai'];
    $borderLeft = match($d->status) {
        'diproses'        => 'border-l-amber-500',
        'selesai_dimasak' => 'border-l-green-500',
        'dikirim'         => 'border-l-blue-500',
        default           => 'border-l-emerald-500',
    };
    $stBg = match($d->status) {
        'diproses'        => 'bg-amber-100 text-amber-700',
        'selesai_dimasak' => 'bg-green-100 text-green-700',
        'dikirim'         => 'bg-blue-100 text-blue-700',
        default           => 'bg-emerald-100 text-emerald-700',
    };
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-cream-200 border-l-4 {{ $borderLeft }} overflow-hidden">
        <div class="px-4 py-3.5">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $col['dot'] }} flex-shrink-0"></div>
                    <div>
                        <p class="font-display font-bold text-sm text-forest-900">#{{ $d->order->order_number }}</p>
                        <p class="text-[10px] text-forest-400">{{ $d->order->user->full_name }} · {{ $d->order->user->class }}</p>
                    </div>
                </div>
                <span class="{{ $stBg }} text-[10px] font-bold px-2.5 py-1 rounded-full flex-shrink-0">{{ $col['label'] }}</span>
            </div>
            <div class="space-y-1 mb-1">
                @foreach($d->order->items as $item)
                    <p class="text-[11px] text-forest-700 font-medium">
                        {{ $item->menu->name }} ×{{ $item->quantity }}
                    </p>
                @endforeach
            </div>
            <div class="flex items-center gap-1.5 mt-1 mb-0.5 flex-wrap">
                <span class="inline-flex items-center text-[10px] font-semibold px-2 py-1 rounded-full {{ $d->order->order_type_color }}">
                    {{ $d->order->order_type_label }}
                </span>
                @if($d->order->isDelivery() && $d->order->classroom)
                    <span class="inline-flex items-center gap-1 text-[10px] font-medium px-2 py-1 rounded-full bg-purple-100 text-purple-700">
                        <i class="fa-solid fa-location-dot"></i> {{ $d->order->classroom }}
                    </span>
                @endif
            </div>
            @if($d->order->note)
                <p class="text-[10px] text-gray-400 italic mb-0.5">📝 {{ $d->order->note }}</p>
            @endif
            <p class="text-[10px] text-forest-400 mt-0.5">{{ $d->order->pickup_display }} · Rp {{ number_format($d->order->total_price, 0, ',', '.') }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Auto-refresh setiap 15 detik --}}
@push('scripts')
<script>
    setInterval(() => location.reload(), 15000);
</script>
@endpush

@endsection