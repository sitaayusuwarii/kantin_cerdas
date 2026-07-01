@extends('layouts.app')
@section('title', 'Tagihan - SmartCanteen')

@section('content')
@php
    $paymentStatus = $order->payment?->status;
    $isPaid = in_array($paymentStatus, ['accepted', 'terverifikasi', 'paid']);
    $isPending = in_array($paymentStatus, ['pending', 'menunggu']);
    $isRejected = in_array($paymentStatus, ['rejected', 'ditolak']);

    $paymentBadge = $isPaid
        ? ['label' => 'Lunas', 'class' => 'bg-emerald-100 text-emerald-700', 'icon' => 'fa-circle-check']
        : ($isPending
            ? ['label' => 'Menunggu Verifikasi', 'class' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-clock']
            : ($isRejected
                ? ['label' => 'Ditolak', 'class' => 'bg-rose-100 text-rose-700', 'icon' => 'fa-circle-xmark']
                : ['label' => 'Belum Lunas', 'class' => 'bg-rose-100 text-rose-700', 'icon' => 'fa-receipt']));

    $pickupLabels = [
        'istirahat_1' => 'Istirahat 1 (09:30)',
        'istirahat_2' => 'Istirahat 2 (12:00)',
        'pulang' => 'Pulang (14:30)',
    ];

    $pickupValue = $order->pickup_time
        ? 'Jam ' . \Illuminate\Support\Str::of((string) $order->pickup_time)->substr(0, 5)
        : ($pickupLabels[$order->pickup_schedule] ?? '-');

    $infoItems = [
        ['label' => 'Nama', 'value' => $order->user->full_name ?? $order->user->name, 'icon' => 'fa-user'],
        ['label' => 'Tanggal', 'value' => $order->created_at->format('d M Y, H:i'), 'icon' => 'fa-calendar'],
        ['label' => 'Status Order', 'value' => str_replace('_', ' ', ucfirst($order->status)), 'icon' => 'fa-circle-info'],
        ['label' => 'Jadwal', 'value' => $pickupValue, 'icon' => 'fa-clock'],
    ];
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <div class="no-print mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ url()->previous() }}"
           class="inline-flex w-fit items-center gap-2 rounded-2xl border border-orange-100 bg-white px-4 py-3 text-sm font-bold text-gray-600 shadow-sm transition hover:bg-orange-50 hover:text-orange-600">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Kembali
        </a>

        <div class="flex flex-wrap gap-2">
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold text-gray-600 shadow-sm transition hover:bg-gray-50">
                <i class="fa-solid fa-print"></i>
                Cetak
            </button>
            <a href="https://telegram.me/6281234567890?text=Halo, saya ingin konfirmasi pembayaran Order %23{{ $order->order_number }}"
               target="_blank"
               class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-600">
                <i class="fa-brands fa-telegram"></i>
                Telegram
            </a>
        </div>
    </div>

    <section class="overflow-hidden rounded-[2rem] border border-orange-100 bg-white shadow-xl shadow-orange-100/40">
        <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 px-6 py-8 sm:px-10">
            <div class="absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.22),transparent_40%)]"></div>
            <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-white">
                            <i class="fa-solid fa-bowl-food text-xl"></i>
                        </div>
                        <div>
                            <p class="font-heading text-xl font-extrabold text-white">SmartCanteen</p>
                            <p class="text-xs font-medium text-orange-100">Kantin digital sekolah</p>
                        </div>
                    </div>

                    <h1 class="mt-8 font-heading text-3xl font-extrabold text-white md:text-4xl">
                        Invoice Pesanan
                    </h1>
                    <p class="mt-2 text-sm text-orange-50">Simpan invoice ini sebagai bukti detail transaksi.</p>
                </div>

                <div class="rounded-3xl border border-white/20 bg-white/15 p-4 text-white backdrop-blur sm:text-right">
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-100">Nomor Pesanan</p>
                    <p class="mt-1 font-heading text-2xl font-extrabold">#{{ $order->order_number }}</p>
                    <span class="mt-3 inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-extrabold {{ $paymentBadge['class'] }}">
                        <i class="fa-solid {{ $paymentBadge['icon'] }} text-[10px]"></i>
                        {{ $paymentBadge['label'] }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 border-b border-orange-100 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($infoItems as $info)
                <div class="border-b border-orange-100 px-5 py-4 sm:border-r lg:border-b-0">
                    <p class="flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-wide text-orange-500">
                        <i class="fa-solid {{ $info['icon'] }}"></i>
                        {{ $info['label'] }}
                    </p>
                    <p class="mt-1 font-heading text-sm font-extrabold text-gray-950">{{ $info['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="p-5 sm:p-8">
            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Detail Item</p>
                    <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950">Menu yang Dipesan</h2>
                </div>
                <span class="rounded-full bg-orange-50 px-3 py-1.5 text-xs font-extrabold text-orange-600">
                    {{ $order->items->sum('quantity') }} item
                </span>
            </div>

            <div class="hidden overflow-hidden rounded-3xl border border-gray-100 sm:block">
                <table class="w-full text-sm">
                    <thead class="bg-orange-50/70">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-orange-700">Menu</th>
                            <th class="px-4 py-4 text-center text-xs font-extrabold uppercase tracking-wide text-orange-700">Qty</th>
                            <th class="px-4 py-4 text-right text-xs font-extrabold uppercase tracking-wide text-orange-700">Harga</th>
                            <th class="px-5 py-4 text-right text-xs font-extrabold uppercase tracking-wide text-orange-700">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <tr class="transition hover:bg-orange-50/40">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-11 w-11 flex-shrink-0 overflow-hidden rounded-2xl bg-orange-50">
                                            @if($item->menu?->image)
                                                <img src="{{ asset('storage/' . $item->menu->image) }}" class="h-full w-full object-cover" alt="{{ $item->menu->name }}">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-orange-400">
                                                    <i class="fa-solid fa-bowl-food"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-950">{{ $item->menu->name ?? 'Menu tidak tersedia' }}</p>
                                            @if($item->menu?->tenant)
                                                <p class="text-xs font-semibold text-orange-500">{{ $item->menu->tenant->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center font-bold text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-gray-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-heading font-extrabold text-gray-950">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="space-y-3 sm:hidden">
                @foreach($order->items as $item)
                    <div class="rounded-3xl border border-gray-100 bg-gray-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 overflow-hidden rounded-2xl bg-orange-50">
                                    @if($item->menu?->image)
                                        <img src="{{ asset('storage/' . $item->menu->image) }}" class="h-full w-full object-cover" alt="{{ $item->menu->name }}">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-orange-400">
                                            <i class="fa-solid fa-bowl-food"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-gray-950">{{ $item->menu->name ?? 'Menu tidak tersedia' }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <p class="font-heading font-extrabold text-orange-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid gap-5 lg:grid-cols-[1fr_360px]">
                <div class="rounded-3xl border border-orange-100 bg-orange-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-white text-orange-500">
                            <i class="fa-solid fa-circle-info text-sm"></i>
                        </div>
                        <p class="text-sm leading-6 text-gray-600">
                            Silakan upload bukti pembayaran atau konfirmasi via Telegram. Pastikan nominal transfer sesuai total tagihan.
                        </p>
                    </div>
                    @if($order->note)
                        <div class="mt-4 rounded-2xl bg-white p-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Catatan</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $order->note }}</p>
                        </div>
                    @endif
                </div>

                <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Subtotal</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Biaya tambahan</span>
                            <span class="font-bold text-gray-900">Rp 0</span>
                        </div>
                        <div class="border-t border-dashed border-gray-200 pt-4">
                            <div class="flex items-end justify-between gap-3">
                                <span class="font-heading font-extrabold text-gray-950">Total</span>
                                <span class="text-right font-heading text-3xl font-extrabold text-orange-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="no-print mt-6">
                @if(!$paymentStatus || $isRejected)
                    <div class="grid gap-3 sm:grid-cols-2">
                        <a href="{{ url('/payment?order=' . $order->order_number) }}"
                           class="flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 px-5 py-4 text-sm font-heading font-extrabold text-white shadow-lg shadow-orange-100 transition hover:-translate-y-0.5">
                            <i class="fa-solid fa-upload"></i>
                            Upload Bukti Bayar
                        </a>
                        <a href="https://telegram.me/6281234567890?text=Halo, konfirmasi pembayaran Order %23{{ $order->order_number }}"
                           target="_blank"
                           class="flex items-center justify-center gap-2 rounded-2xl bg-emerald-500 px-5 py-4 text-sm font-heading font-extrabold text-white shadow-sm transition hover:bg-emerald-600">
                            <i class="fa-brands fa-telegram"></i>
                            Konfirmasi via Telegram
                        </a>
                    </div>
                @elseif($isPending)
                    <div class="rounded-3xl border border-amber-100 bg-amber-50 p-5 text-center">
                        <i class="fa-solid fa-clock mb-2 text-2xl text-amber-500"></i>
                        <p class="font-heading font-extrabold text-amber-700">Menunggu Verifikasi Admin</p>
                        <p class="mt-1 text-sm text-amber-600">Bukti pembayaran sudah diterima dan sedang diperiksa.</p>
                    </div>
                @elseif($isPaid)
                    <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5 text-center">
                        <i class="fa-solid fa-circle-check mb-2 text-2xl text-emerald-500"></i>
                        <p class="font-heading font-extrabold text-emerald-700">Pembayaran Dikonfirmasi</p>
                        <p class="mt-1 text-sm text-emerald-600">Pembayaran sudah lunas.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    @media print {
        header, footer, .no-print { display: none !important; }
        body { background: #fff !important; }
        section { box-shadow: none !important; }
    }
</style>
@endpush
