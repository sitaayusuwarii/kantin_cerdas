@extends('layouts.app')
@section('title', 'Riwayat Transaksi Kasir')

@section('content')
@php
    $statusLabels = [
        'pending' => 'Menunggu',
        'menunggu_pembayaran' => 'Menunggu Bayar',
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'pembayaran_terverifikasi' => 'Terverifikasi',
        'diproses' => 'Diproses',
        'selesai_dimasak' => 'Selesai Dimasak',
        'dikirim' => 'Dikirim',
        'selesai' => 'Selesai',
    ];

    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-700',
        'menunggu_pembayaran' => 'bg-red-100 text-red-700',
        'menunggu_konfirmasi' => 'bg-orange-100 text-orange-700',
        'pembayaran_terverifikasi' => 'bg-sky-100 text-sky-700',
        'diproses' => 'bg-indigo-100 text-indigo-700',
        'selesai_dimasak' => 'bg-teal-100 text-teal-700',
        'dikirim' => 'bg-cyan-100 text-cyan-700',
        'selesai' => 'bg-green-100 text-green-700',
    ];

    $kasirCustomerName = function ($order) {
        if (! empty($order->customer_name)) {
            return $order->customer_name;
        }

        if (! empty($order->note) && preg_match('/Nama pelanggan:\s*([^|]+)/i', $order->note, $matches)) {
            return trim($matches[1]);
        }

        return $order->user->full_name
            ?? $order->user->name
            ?? $order->user->username
            ?? 'Pelanggan';
    };
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading font-extrabold text-2xl md:text-3xl text-canteen-dark">
                Riwayat Transaksi Kasir
            </h1>
            <p class="text-sm text-gray-400 mt-1">
                Menampilkan pesanan yang dibuat oleh akun kasir yang sedang login.
            </p>
        </div>

        <a href="{{ route('kasir.dashboard') }}"
           class="btn-primary text-white text-sm font-bold px-5 py-3 rounded-2xl shadow-md inline-flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i>
            Buat Pesanan
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-100">
            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-receipt text-primary-500"></i>
            </div>
            <p class="text-2xl font-heading font-bold text-canteen-dark">{{ $totalPesanan }}</p>
            <p class="text-gray-500 text-xs mt-0.5">Total Pesanan Kasir</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-100">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-wallet text-green-500"></i>
            </div>
            <p class="text-xl md:text-2xl font-heading font-bold text-canteen-dark">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </p>
            <p class="text-gray-500 text-xs mt-0.5">Total Pendapatan Kasir</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-100">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-circle-check text-blue-500"></i>
            </div>
            <p class="text-2xl font-heading font-bold text-canteen-dark">{{ $pesananSelesai }}</p>
            <p class="text-gray-500 text-xs mt-0.5">Pesanan Selesai</p>
        </div>
    </div>

   
    <section class="bg-white rounded-2xl p-5 shadow-sm border border-orange-100">
        <div class="mb-5">
            <div>
                <h2 class="font-heading font-bold text-lg text-canteen-dark flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-primary-500"></i>
                    Semua Transaksi Kasir
                </h2>
                <p class="text-xs text-gray-400 mt-1">Cari berdasarkan nomor order atau nama pelanggan.</p>
            </div>

            <form method="GET" action="{{ route('kasir.history') }}"
                  class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-3">
                <div class="relative xl:col-span-4">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari transaksi..."
                           class="w-full h-12 rounded-2xl border border-orange-100 pl-10 pr-4 text-sm focus:border-primary-400 focus:ring-primary-100">
                </div>

                <div class="xl:col-span-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           title="Dari tanggal"
                           class="w-full h-12 rounded-2xl border border-orange-100 px-4 text-sm focus:border-primary-400 focus:ring-primary-100">
                </div>

                <div class="xl:col-span-2">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           title="Sampai tanggal"
                           class="w-full h-12 rounded-2xl border border-orange-100 px-4 text-sm focus:border-primary-400 focus:ring-primary-100">
                </div>

                <select name="status"
                        class="xl:col-span-2 w-full h-12 rounded-2xl border border-orange-100 px-4 text-sm focus:border-primary-400 focus:ring-primary-100">
                    <option value="all">Semua Status</option>
                    @foreach($statusLabels as $status => $label)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                        class="xl:col-span-1 w-full h-12 bg-orange-50 hover:bg-orange-100 text-primary-600 text-sm font-bold px-5 rounded-2xl">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('kasir.history') }}"
                       class="xl:col-span-1 w-full h-12 bg-gray-50 hover:bg-gray-100 text-gray-500 text-sm font-bold px-5 rounded-2xl flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 border-b border-orange-50">
                        <th class="py-3 pr-4 font-semibold">Order</th>
                        <th class="py-3 pr-4 font-semibold">Pelanggan</th>
                        <th class="py-3 pr-4 font-semibold">Tipe</th>
                        <th class="py-3 pr-4 font-semibold">Item</th>
                        <th class="py-3 pr-4 font-semibold">Total</th>
                        <th class="py-3 pr-4 font-semibold">Status</th>
                        <th class="py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-50">
                    @forelse($orders as $order)
                        @php
                            $customerName = $kasirCustomerName($order);
                        @endphp
                        <tr>
                            <td class="py-4 pr-4 whitespace-nowrap">
                                <p class="font-heading font-bold text-canteen-dark">#{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-400">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </td>
                            <td class="py-4 pr-4">
                                <p class="font-semibold text-gray-700">
                                    {{ $customerName }}
                                </p>
                                @if(! empty($order->table_number))
                                    <p class="text-xs text-gray-400">{{ $order->table_number }}</p>
                                @endif
                            </td>
                            <td class="py-4 pr-4 text-gray-500 whitespace-nowrap">
                                {{ $order->order_type ? str_replace('_', ' ', $order->order_type) : '-' }}
                            </td>
                            <td class="py-4 pr-4 text-gray-500 whitespace-nowrap">
                                {{ $order->items->sum('quantity') }} item
                            </td>
                            <td class="py-4 pr-4 font-heading font-bold text-primary-600 whitespace-nowrap">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            <td class="py-4 pr-4">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    <i class="fa-solid fa-circle text-[6px]"></i>
                                    {{ $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                <a href="{{ route('kasir.payment', ['order' => $order->order_number]) }}"
                                   class="text-xs font-bold text-primary-600 hover:text-primary-700">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-primary-500">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <p class="font-heading font-bold text-gray-700">Belum ada transaksi kasir</p>
                                <p class="text-xs text-gray-400 mt-1">Pesanan yang dibuat kasir akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $orders->links() }}
        </div>
    </section>
</div>
@endsection
