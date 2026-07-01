@extends('layouts.app')
@section('title', 'Konfirmasi Pembayaran Kasir')

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

    $isPaid = in_array($order->status, ['pembayaran_terverifikasi', 'diproses', 'selesai_dimasak', 'dikirim', 'selesai'], true)
        || (($order->payment_status ?? null) === 'paid');

    $paymentMethodLabels = [
        'qris' => 'QRIS',
        'bca' => 'BCA',
        'bri' => 'BRI',
        'transfer_bca' => 'Transfer BCA',
        'transfer_bri' => 'Transfer BRI',
    ];
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-100 text-green-700 px-4 py-3 rounded-2xl text-sm font-medium flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('kasir.dashboard') }}"
           class="w-11 h-11 bg-white rounded-2xl border border-orange-100 flex items-center justify-center hover:bg-orange-50 shadow-sm">
            <i class="fa-solid fa-arrow-left text-sm text-gray-600"></i>
        </a>
        <div>
            <h1 class="font-heading font-extrabold text-2xl text-canteen-dark">Detail Pembayaran Kasir</h1>
            <p class="text-gray-400 text-sm">Order #{{ $order->order_number }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">
        <section class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-orange-100 overflow-hidden">
            <div class="p-5 border-b border-orange-50 flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-heading font-bold text-lg text-canteen-dark">Ringkasan Pesanan</h2>
                    <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WITA</p>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                    <i class="fa-solid fa-circle text-[6px]"></i>
                    {{ $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>

            <div class="p-5 space-y-4">
                @foreach($order->items as $item)
                    @php
                        $itemPrice = $item->unit_price ?? $item->price ?? 0;
                    @endphp
                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-orange-100 p-4">
                        <div class="min-w-0">
                            <p class="font-heading font-bold text-sm text-canteen-dark truncate">
                                {{ $item->menu->name ?? 'Menu' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $item->quantity }} x Rp {{ number_format($itemPrice, 0, ',', '.') }}
                            </p>
                        </div>
                        <p class="font-heading font-bold text-primary-600 whitespace-nowrap">
                            Rp {{ number_format(($itemPrice * $item->quantity), 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="lg:col-span-2 space-y-6">
            <section class="bg-white rounded-2xl shadow-sm border border-orange-100 p-5">
                <h2 class="font-heading font-bold text-lg text-canteen-dark mb-4">Informasi Transaksi</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500">Pelanggan</span>
                        <span class="font-bold text-gray-800 text-right">
                            {{ $order->customer_name ?? $order->user->full_name ?? $order->user->name ?? 'Pelanggan Kasir' }}
                        </span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500">Tipe</span>
                        <span class="font-bold text-gray-800 text-right">
                            {{ $order->order_type ? str_replace('_', ' ', $order->order_type) : '-' }}
                        </span>
                    </div>
                    @if(! empty($order->table_number))
                        <div class="flex justify-between gap-4">
                            <span class="text-gray-500">Meja/Kelas</span>
                            <span class="font-bold text-gray-800 text-right">{{ $order->table_number }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500">Metode Bayar</span>
                        <span class="font-bold text-gray-800 text-right">
                            {{ $paymentMethodLabels[$order->payment_method] ?? ($order->payment_method ? ucfirst(str_replace('_', ' ', $order->payment_method)) : '-') }}
                        </span>
                    </div>
                    @if(! empty($order->note))
                        <div class="pt-3 border-t border-orange-50">
                            <p class="text-gray-500 mb-1">Catatan</p>
                            <p class="font-medium text-gray-700">{{ $order->note }}</p>
                        </div>
                    @endif
                </div>

                <div class="mt-5 pt-5 border-t border-orange-100">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-500">Total Bayar</span>
                        <span class="font-heading font-extrabold text-2xl text-primary-600">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-orange-100 p-5">
                @if($isPaid)
                    <div class="text-center py-4">
                        <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 mx-auto flex items-center justify-center mb-3">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                        </div>
                        <p class="font-heading font-bold text-gray-800">Pembayaran sudah terverifikasi</p>
                        <p class="text-xs text-gray-400 mt-1">Pesanan sudah bisa diproses oleh tenant.</p>
                    </div>
                    <a href="{{ route('kasir.dashboard') }}"
                       class="mt-4 w-full bg-orange-50 hover:bg-orange-100 text-primary-600 font-bold py-3 rounded-2xl flex items-center justify-center gap-2">
                        Kembali ke Dashboard
                    </a>
                @else
                    <form action="{{ route('kasir.payment.confirm', ['order' => $order->order_number]) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="btn-primary w-full text-white font-bold py-3 rounded-2xl shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>
                            Konfirmasi Pembayaran
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 text-center mt-3">
                        Gunakan tombol ini setelah uang tunai atau QRIS sudah diterima.
                    </p>
                @endif
            </section>
        </aside>
    </div>
</div>
@endsection
