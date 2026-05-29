@extends('layouts.app')
@section('title', 'Riwayat Pesanan — SmartCanteen')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="font-heading font-bold text-2xl md:text-3xl text-canteen-dark">Riwayat Pesanan </h1>
            <p class="text-gray-400 text-sm mt-0.5">Semua histori transaksi kamu ada di sini</p>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('customer.history') }}" class="flex gap-2">
            <select name="status" onchange="this.form.submit()"
                class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl focus:outline-none focus:border-primary-300 transition-all">
                <option value="semua" {{ request('status') === 'semua' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                <option value="selesai"    {{ request('status') === 'selesai'    ? 'selected' : '' }}>Selesai</option>
                <option value="diproses"   {{ request('status') === 'diproses'   ? 'selected' : '' }}>Diproses</option>
                <option value="baru"       {{ request('status') === 'baru'       ? 'selected' : '' }}>Baru</option>
                <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </form>
    </div>

    {{-- Summary Chips --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="flex items-center gap-2 bg-white border border-gray-100 px-4 py-2.5 rounded-xl shadow-sm">
            <div class="w-2.5 h-2.5 rounded-full bg-gray-300"></div>
            <span class="text-sm font-semibold text-gray-700">{{ $total }} Pesanan</span>
        </div>
        <div class="flex items-center gap-2 bg-green-50 border border-green-100 px-4 py-2.5 rounded-xl">
            <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
            <span class="text-sm font-semibold text-green-700">{{ $selesai }} Selesai</span>
        </div>
        <div class="flex items-center gap-2 bg-yellow-50 border border-yellow-100 px-4 py-2.5 rounded-xl">
            <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
            <span class="text-sm font-semibold text-yellow-700">{{ $diproses }} Diproses</span>
        </div>
        <div class="flex items-center gap-2 bg-red-50 border border-red-100 px-4 py-2.5 rounded-xl">
            <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
            <span class="text-sm font-semibold text-red-700">{{ $dibatalkan }} Dibatalkan</span>
        </div>
    </div>

    @php
    $statusStyles = [
        'selesai'    => ['bg' => 'bg-green-100 text-green-700',  'icon' => 'fa-check-circle'],
        'diproses'   => ['bg' => 'bg-yellow-100 text-yellow-700','icon' => 'fa-fire-burner'],
        'baru'       => ['bg' => 'bg-blue-100 text-blue-700',    'icon' => 'fa-clock'],
        'dibatalkan' => ['bg' => 'bg-red-100 text-red-600',      'icon' => 'fa-circle-xmark'],
    ];
    @endphp

    {{-- DESKTOP TABLE --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ID Pesanan</th>
                    <th class="text-left px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="text-left px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Menu</th>
                    <th class="text-right px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="text-center px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Pembayaran</th>
                    <th class="text-center px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                @php
                    $style       = $statusStyles[$order->status] ?? $statusStyles['baru'];
                    $paymentStatus = $order->payment?->status;
                    $menuNames   = $order->items->take(2)->map(fn($i) => $i->menu->name ?? '-')->join(', ');
                    if ($order->items->count() > 2) $menuNames .= ' +' . ($order->items->count() - 2) . ' lainnya';
                @endphp
                <tr class="hover:bg-orange-50/40 transition-colors group">
                    <td class="px-6 py-4">
                        <span class="font-heading font-bold text-sm text-canteen-dark">
                            #{{ $order->order_number }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-gray-500 text-xs">
                        {{ $order->created_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-gray-700 text-xs">{{ $menuNames }}</span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <span class="font-heading font-bold text-sm text-canteen-dark">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 {{ $style['bg'] }} text-xs font-semibold px-3 py-1.5 rounded-full">
                            <i class="fa-solid {{ $style['icon'] }} text-[10px]"></i>
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($paymentStatus === 'terverifikasi')
                            <span class="inline-flex items-center gap-1 bg-green-50 text-green-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <i class="fa-solid fa-check text-[9px]"></i>Lunas
                            </span>
                        @elseif($paymentStatus === 'menunggu')
                            <span class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <i class="fa-solid fa-clock text-[9px]"></i>Verifikasi
                            </span>
                        @elseif($paymentStatus === 'ditolak')
                            <span class="inline-flex items-center gap-1 bg-red-50 text-red-500 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <i class="fa-solid fa-xmark text-[9px]"></i>Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-red-50 text-red-500 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <i class="fa-solid fa-xmark text-[9px]"></i>Belum Bayar
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2 opacity-70 group-hover:opacity-100 transition-opacity">
                            <a href="{{ url('/invoice/' . $order->order_number) }}"
                               class="text-xs text-primary-500 hover:text-primary-700 font-semibold px-3 py-1.5 bg-orange-50 hover:bg-orange-100 rounded-lg transition-all">
                                <i class="fa-solid fa-eye mr-1"></i>Detail
                            </a>
                            @if(!$order->payment || $order->payment->status === 'ditolak')
                            <a href="{{ url('/payment') }}"
                               class="text-xs text-white btn-primary font-semibold px-3 py-1.5 rounded-lg shadow transition-all">
                                <i class="fa-solid fa-upload mr-1"></i>Bayar
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-gray-400">
                            <i class="fa-solid fa-box-open text-4xl text-gray-200"></i>
                            <p class="font-semibold">Belum ada pesanan</p>
                            <a href="{{ route('customer.menu') }}"
                               class="text-sm text-primary-500 font-semibold hover:text-primary-700">
                                Yuk pesan sekarang →
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MOBILE CARD LIST --}}
    <div class="md:hidden space-y-4">
        @forelse($orders as $order)
        @php
            $style       = $statusStyles[$order->status] ?? $statusStyles['baru'];
            $paymentStatus = $order->payment?->status;
            $menuNames   = $order->items->take(2)->map(fn($i) => $i->menu->name ?? '-')->join(', ');
            if ($order->items->count() > 2) $menuNames .= ' +' . ($order->items->count() - 2) . ' lainnya';
        @endphp
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <p class="font-heading font-bold text-base text-canteen-dark">
                        #{{ $order->order_number }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar text-gray-300"></i>
                        {{ $order->created_at->format('d M Y') }}
                    </p>
                </div>
                <span class="inline-flex items-center gap-1.5 {{ $style['bg'] }} text-xs font-semibold px-3 py-1.5 rounded-full flex-shrink-0">
                    <i class="fa-solid {{ $style['icon'] }} text-[10px]"></i>
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <p class="text-sm text-gray-600 mb-4 leading-relaxed bg-gray-50 rounded-xl p-3">
                <i class="fa-solid fa-utensils text-gray-300 mr-2"></i>{{ $menuNames }}
            </p>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400">Total Pembayaran</p>
                    <p class="font-heading font-bold text-xl text-primary-500">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </p>
                    @if($paymentStatus === 'terverifikasi')
                        <span class="inline-flex items-center gap-1 bg-green-50 text-green-600 text-[10px] font-semibold px-2 py-0.5 rounded-full mt-1">
                            <i class="fa-solid fa-check text-[8px]"></i>Lunas
                        </span>
                    @elseif($paymentStatus === 'menunggu')
                        <span class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-600 text-[10px] font-semibold px-2 py-0.5 rounded-full mt-1">
                            <i class="fa-solid fa-clock text-[8px]"></i>Verifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-500 text-[10px] font-semibold px-2 py-0.5 rounded-full mt-1">
                            <i class="fa-solid fa-xmark text-[8px]"></i>Belum Bayar
                        </span>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ url('/invoice/' . $order->order_number) }}"
                       class="text-xs text-primary-500 font-semibold px-3.5 py-2 bg-orange-50 hover:bg-orange-100 rounded-xl transition-all">
                        <i class="fa-solid fa-eye mr-1"></i>Detail
                    </a>
                    @if(!$order->payment || $order->payment->status === 'ditolak')
                    <a href="{{ url('/payment') }}"
                       class="text-xs text-white btn-primary font-semibold px-3.5 py-2 rounded-xl shadow-md">
                        <i class="fa-solid fa-upload mr-1"></i>Bayar
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-box-open text-4xl text-gray-200 block mb-3"></i>
            <p class="font-semibold">Belum ada pesanan</p>
            <a href="{{ route('customer.menu') }}"
               class="text-sm text-primary-500 font-semibold mt-2 inline-block">
                Yuk pesan sekarang →
            </a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
        <p class="text-sm text-gray-400">
            Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }}
            dari {{ $orders->total() }} pesanan
        </p>
        <div class="flex gap-1.5">
            {{-- Prev --}}
            @if($orders->onFirstPage())
            <button disabled class="w-9 h-9 rounded-xl border border-gray-200 bg-white flex items-center justify-center text-gray-300 text-xs">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            @else
            <a href="{{ $orders->previousPageUrl() }}"
               class="w-9 h-9 rounded-xl border border-gray-200 bg-white flex items-center justify-center text-gray-400 hover:border-primary-300 hover:text-primary-500 transition-all text-xs">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            @endif

            {{-- Page numbers --}}
            @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
            <a href="{{ $url }}"
               class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-medium transition-all
                      {{ $page == $orders->currentPage()
                          ? 'btn-primary text-white shadow-md'
                          : 'border border-gray-200 bg-white text-gray-600 hover:border-primary-300 hover:text-primary-500' }}">
                {{ $page }}
            </a>
            @endforeach

            {{-- Next --}}
            @if($orders->hasMorePages())
            <a href="{{ $orders->nextPageUrl() }}"
               class="w-9 h-9 rounded-xl border border-gray-200 bg-white flex items-center justify-center text-gray-400 hover:border-primary-300 hover:text-primary-500 transition-all text-xs">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
            @else
            <button disabled class="w-9 h-9 rounded-xl border border-gray-200 bg-white flex items-center justify-center text-gray-300 text-xs">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection

@push('styles')
<style>
    .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); }
    .btn-primary:hover { filter: brightness(1.05); }
    .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
    .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
</style>
@endpush