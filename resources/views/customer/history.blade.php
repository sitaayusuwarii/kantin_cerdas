@extends('layouts.app')
@section('title', 'Riwayat Pesanan — SmartCanteen')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="font-heading font-bold text-2xl md:text-3xl text-canteen-dark">Riwayat Pesanan 📋</h1>
            <p class="text-gray-400 text-sm mt-0.5">Semua histori transaksi kamu ada di sini</p>
        </div>
        <div class="flex gap-2">
            <select class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl focus:outline-none focus:border-primary-300 transition-all">
                <option>Semua Status</option>
                <option>Selesai</option>
                <option>Diproses</option>
                <option>Dibatalkan</option>
            </select>
            <select class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl focus:outline-none focus:border-primary-300 transition-all">
                <option>April 2025</option>
                <option>Maret 2025</option>
                <option>Februari 2025</option>
            </select>
        </div>
    </div>

    {{-- Summary Chips --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="flex items-center gap-2 bg-white border border-gray-100 px-4 py-2.5 rounded-xl shadow-sm">
            <div class="w-2.5 h-2.5 rounded-full bg-gray-300"></div>
            <span class="text-sm font-semibold text-gray-700">12 Pesanan</span>
        </div>
        <div class="flex items-center gap-2 bg-green-50 border border-green-100 px-4 py-2.5 rounded-xl">
            <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
            <span class="text-sm font-semibold text-green-700">10 Selesai</span>
        </div>
        <div class="flex items-center gap-2 bg-yellow-50 border border-yellow-100 px-4 py-2.5 rounded-xl">
            <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
            <span class="text-sm font-semibold text-yellow-700">1 Diproses</span>
        </div>
        <div class="flex items-center gap-2 bg-red-50 border border-red-100 px-4 py-2.5 rounded-xl">
            <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
            <span class="text-sm font-semibold text-red-700">1 Dibatalkan</span>
        </div>
    </div>

    @php
    $orders = [
        ['id' => 'SC-001', 'date' => '19 Apr 2025', 'items' => 'Nasi Gudeg + Es Teh + Pisang Goreng', 'total' => 'Rp 25.000', 'status' => 'Diproses', 'status_key' => 'process', 'payment' => 'Belum Bayar'],
        ['id' => 'SC-002', 'date' => '18 Apr 2025', 'items' => 'Mie Goreng + Jus Alpukat', 'total' => 'Rp 18.000', 'status' => 'Selesai', 'status_key' => 'done', 'payment' => 'Lunas'],
        ['id' => 'SC-003', 'date' => '17 Apr 2025', 'items' => 'Nasi Ayam Geprek', 'total' => 'Rp 13.000', 'status' => 'Selesai', 'status_key' => 'done', 'payment' => 'Lunas'],
        ['id' => 'SC-004', 'date' => '16 Apr 2025', 'items' => 'Bakso Urat Jumbo + Es Teh', 'total' => 'Rp 15.000', 'status' => 'Selesai', 'status_key' => 'done', 'payment' => 'Lunas'],
        ['id' => 'SC-005', 'date' => '15 Apr 2025', 'items' => 'Indomie Rebus + Pisang Goreng', 'total' => 'Rp 12.000', 'status' => 'Dibatalkan', 'status_key' => 'cancel', 'payment' => 'Refund'],
        ['id' => 'SC-006', 'date' => '14 Apr 2025', 'items' => 'Nasi Gudeg Komplit', 'total' => 'Rp 12.000', 'status' => 'Selesai', 'status_key' => 'done', 'payment' => 'Lunas'],
    ];
    $statusStyles = [
        'done'    => ['bg' => 'bg-green-100 text-green-700', 'dot' => 'bg-green-400', 'icon' => 'fa-check-circle'],
        'process' => ['bg' => 'bg-yellow-100 text-yellow-700', 'dot' => 'bg-yellow-400', 'icon' => 'fa-fire-burner'],
        'cancel'  => ['bg' => 'bg-red-100 text-red-600', 'dot' => 'bg-red-400', 'icon' => 'fa-circle-xmark'],
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
                @foreach($orders as $order)
                @php $style = $statusStyles[$order['status_key']]; @endphp
                <tr class="hover:bg-orange-50/40 transition-colors group">
                    <td class="px-6 py-4">
                        <span class="font-heading font-bold text-sm text-canteen-dark">#{{ $order['id'] }}</span>
                    </td>
                    <td class="px-4 py-4 text-gray-500 text-xs">{{ $order['date'] }}</td>
                    <td class="px-4 py-4">
                        <span class="text-gray-700 text-xs">{{ $order['items'] }}</span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <span class="font-heading font-bold text-sm text-canteen-dark">{{ $order['total'] }}</span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 {{ $style['bg'] }} text-xs font-semibold px-3 py-1.5 rounded-full">
                            <i class="fa-solid {{ $style['icon'] }} text-[10px]"></i>{{ $order['status'] }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($order['payment'] === 'Lunas')
                            <span class="inline-flex items-center gap-1 bg-green-50 text-green-600 text-xs font-semibold px-2.5 py-1 rounded-full"><i class="fa-solid fa-check text-[9px]"></i>Lunas</span>
                        @elseif($order['payment'] === 'Belum Bayar')
                            <span class="inline-flex items-center gap-1 bg-red-50 text-red-500 text-xs font-semibold px-2.5 py-1 rounded-full"><i class="fa-solid fa-xmark text-[9px]"></i>Belum</span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 text-xs font-semibold px-2.5 py-1 rounded-full">Refund</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2 opacity-70 group-hover:opacity-100 transition-opacity">
                            <a href="{{ url('/invoice') }}" class="text-xs text-primary-500 hover:text-primary-700 font-semibold px-3 py-1.5 bg-orange-50 hover:bg-orange-100 rounded-lg transition-all">
                                <i class="fa-solid fa-eye mr-1"></i>Detail
                            </a>
                            @if($order['payment'] === 'Belum Bayar')
                            <a href="{{ url('/payment') }}" class="text-xs text-white btn-primary font-semibold px-3 py-1.5 rounded-lg shadow transition-all">
                                <i class="fa-solid fa-upload mr-1"></i>Bayar
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MOBILE CARD LIST --}}
    <div class="md:hidden space-y-4">
        @foreach($orders as $order)
        @php $style = $statusStyles[$order['status_key']]; @endphp
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <p class="font-heading font-bold text-base text-canteen-dark">#{{ $order['id'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar text-gray-300"></i>{{ $order['date'] }}
                    </p>
                </div>
                <span class="inline-flex items-center gap-1.5 {{ $style['bg'] }} text-xs font-semibold px-3 py-1.5 rounded-full flex-shrink-0">
                    <i class="fa-solid {{ $style['icon'] }} text-[10px]"></i>{{ $order['status'] }}
                </span>
            </div>
            <p class="text-sm text-gray-600 mb-4 leading-relaxed bg-gray-50 rounded-xl p-3">
                <i class="fa-solid fa-utensils text-gray-300 mr-2"></i>{{ $order['items'] }}
            </p>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400">Total Pembayaran</p>
                    <p class="font-heading font-bold text-xl text-primary-500">{{ $order['total'] }}</p>
                    @if($order['payment'] === 'Lunas')
                        <span class="inline-flex items-center gap-1 bg-green-50 text-green-600 text-[10px] font-semibold px-2 py-0.5 rounded-full mt-1"><i class="fa-solid fa-check text-[8px]"></i>Lunas</span>
                    @elseif($order['payment'] === 'Belum Bayar')
                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-500 text-[10px] font-semibold px-2 py-0.5 rounded-full mt-1"><i class="fa-solid fa-xmark text-[8px]"></i>Belum Bayar</span>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ url('/invoice') }}" class="text-xs text-primary-500 font-semibold px-3.5 py-2 bg-orange-50 hover:bg-orange-100 rounded-xl transition-all">
                        <i class="fa-solid fa-eye mr-1"></i>Detail
                    </a>
                    @if($order['payment'] === 'Belum Bayar')
                    <a href="{{ url('/payment') }}" class="text-xs text-white btn-primary font-semibold px-3.5 py-2 rounded-xl shadow-md">
                        <i class="fa-solid fa-upload mr-1"></i>Bayar
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
        <p class="text-sm text-gray-400">Menampilkan 1–6 dari 12 pesanan</p>
        <div class="flex gap-1.5">
            <button class="w-9 h-9 rounded-xl border border-gray-200 bg-white flex items-center justify-center text-gray-400 hover:border-primary-300 hover:text-primary-500 transition-all text-xs">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="w-9 h-9 rounded-xl btn-primary text-white flex items-center justify-center text-xs font-bold shadow-md">1</button>
            <button class="w-9 h-9 rounded-xl border border-gray-200 bg-white flex items-center justify-center text-gray-600 hover:border-primary-300 hover:text-primary-500 transition-all text-xs font-medium">2</button>
            <button class="w-9 h-9 rounded-xl border border-gray-200 bg-white flex items-center justify-center text-gray-400 hover:border-primary-300 hover:text-primary-500 transition-all text-xs">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>
@endsection
