@extends('layouts.app')
@section('title', 'Detail Pesanan - SmartCanteen')

@section('content')
@php
    $isKasir = auth()->check() && auth()->user()->role === 'kasir';
    $totalQty = $cart->items->sum('quantity');
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 pb-28">

    {{-- Header --}}
    <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 px-5 py-7 shadow-xl shadow-orange-200/50 sm:px-8 lg:px-10">
        <div class="absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.22),transparent_40%)]"></div>
        <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-6 right-10 hidden text-white/10 lg:block">
            <i class="fa-solid fa-clipboard-check text-[8rem]"></i>
        </div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('cart') }}"
                   class="mb-5 inline-flex items-center gap-2 rounded-2xl border border-white/20 bg-white/15 px-4 py-2 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Kembali ke Keranjang
                </a>

                <h1 class="font-heading text-3xl font-extrabold leading-tight text-white md:text-4xl">
                    Detail Pesanan
                </h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-orange-50">
                    Cek item, pilih tipe layanan, atur jadwal, lalu konfirmasi pesanan.
                </p>
            </div>

            <div class="w-full rounded-3xl border border-white/20 bg-white/15 p-4 text-white backdrop-blur sm:w-auto sm:min-w-[280px]">
                <p class="text-xs font-bold uppercase tracking-wide text-orange-100">Total Pesanan</p>
                <p class="mt-1 font-heading text-3xl font-extrabold">
                    Rp {{ number_format($totalPrice, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-sm text-orange-100">{{ $totalQty }} item dipilih</p>
            </div>
        </div>
    </section>

    <form action="{{ route('customer.order.confirm') }}" method="POST" id="main-order-form">
        @csrf

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-[1fr_390px] lg:items-start">

            {{-- Left Column --}}
            <div class="space-y-6">

                {{-- Items --}}
                <section class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Isi Keranjang</p>
                            <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950">
                                Item Pesanan
                            </h2>
                        </div>
                        <span class="rounded-full bg-orange-50 px-3 py-1.5 text-xs font-extrabold text-orange-600">
                            {{ $totalQty }} item
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($cart->items as $item)
                            <div class="flex gap-4 rounded-3xl border border-gray-100 bg-white p-3 transition hover:border-orange-100 hover:bg-orange-50/40">
                                <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-2xl bg-orange-50">
                                    @if($item->menu->image)
                                        <img src="{{ asset('storage/' . $item->menu->image) }}"
                                             alt="{{ $item->menu->name }}"
                                             class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-orange-400">
                                            <i class="fa-solid fa-bowl-food text-2xl"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate font-heading text-base font-extrabold text-gray-950">
                                                {{ $item->menu->name }}
                                            </p>
                                            @if($item->menu->tenant)
                                                <p class="mt-1 text-xs font-bold text-orange-500">
                                                    {{ $item->menu->tenant->name }}
                                                </p>
                                            @endif
                                        </div>
                                        <p class="whitespace-nowrap text-right font-heading text-base font-extrabold text-gray-950">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        <span class="rounded-full bg-gray-50 px-3 py-1 font-bold">
                                            {{ $item->quantity }} x Rp {{ number_format($item->menu->price, 0, ',', '.') }}
                                        </span>
                                        @if($item->menu->category)
                                            <span class="rounded-full bg-orange-50 px-3 py-1 font-bold text-orange-600">
                                                {{ $item->menu->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ route('customer.menu') }}"
                       class="mt-5 inline-flex items-center gap-2 rounded-2xl bg-orange-50 px-4 py-3 text-sm font-extrabold text-orange-600 transition hover:bg-orange-100">
                        <i class="fa-solid fa-plus-circle"></i>
                        Tambah Item
                    </a>
                </section>

                {{-- Order Type --}}
                <section class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Layanan</p>
                        <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950">Tipe Pesanan</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="dine_in"
                                   class="sr-only order-type-radio" id="type-dinein">
                            <div class="order-type-opt h-full rounded-3xl border-2 border-gray-100 bg-white p-4 transition hover:border-teal-300 hover:bg-teal-50">
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-teal-600">
                                    <i class="fa-solid fa-plate-wheat text-lg"></i>
                                </div>
                                <p class="font-heading text-base font-extrabold text-gray-900">Dine In</p>
                                <p class="mt-1 text-xs leading-5 text-gray-500">Disajikan di piring dan makan di kantin.</p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="takeaway"
                                   class="sr-only order-type-radio" id="type-takeaway" checked>
                            <div class="order-type-opt is-active-takeaway h-full rounded-3xl border-2 border-orange-400 bg-orange-50 p-4 transition">
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                                </div>
                                <p class="font-heading text-base font-extrabold text-gray-900">Take Away</p>
                                <p class="mt-1 text-xs leading-5 text-gray-500">Dibungkus dan diambil sesuai jam.</p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="delivery"
                                   class="sr-only order-type-radio" id="type-delivery">
                            <div class="order-type-opt h-full rounded-3xl border-2 border-gray-100 bg-white p-4 transition hover:border-purple-300 hover:bg-purple-50">
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-purple-600">
                                    <i class="fa-solid fa-person-walking text-lg"></i>
                                </div>
                                <p class="font-heading text-base font-extrabold text-gray-900">Antar ke Kelas</p>
                                <p class="mt-1 text-xs leading-5 text-gray-500">Pesanan diantar ke kelas tujuan.</p>
                            </div>
                        </label>
                    </div>

                    <div id="dinein-info" class="hidden mt-4 rounded-3xl border border-teal-100 bg-teal-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-white text-teal-600">
                                <i class="fa-solid fa-circle-info text-sm"></i>
                            </div>
                            <p class="text-sm leading-6 text-teal-700">
                                Pesanan akan disajikan di piring. Tunjukkan nomor order ke kasir saat mengambil makanan.
                            </p>
                        </div>
                    </div>

                    <div id="classroom-section" class="hidden mt-4 space-y-4 rounded-3xl border border-purple-100 bg-purple-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-white text-purple-600">
                                <i class="fa-solid fa-circle-info text-sm"></i>
                            </div>
                            <p class="text-sm leading-6 text-purple-700">
                                Pastikan nama pemesan dan kelas tujuan sudah benar sebelum konfirmasi.
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wide text-purple-700">
                                    Nama Pemesan
                                </label>
                                <input type="text"
                                       value="{{ auth()->user()->full_name }}"
                                       readonly
                                       class="w-full rounded-2xl border border-purple-100 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-500">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wide text-purple-700">
                                    Kelas Tujuan <span class="text-red-400">*</span>
                                </label>
                                <input type="text"
                                       name="classroom"
                                       id="classroom-input"
                                       placeholder="Contoh: XI RPL A"
                                       value="{{ old('classroom', auth()->user()->class) }}"
                                       class="w-full rounded-2xl border border-purple-100 bg-white px-4 py-3 text-sm font-semibold text-gray-700 focus:border-purple-300 focus:outline-none focus:ring-4 focus:ring-purple-100">
                                @error('classroom')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @if(auth()->user()->class)
                            <p class="text-xs text-purple-500">Kelas otomatis diambil dari profil, tetapi tetap bisa diubah.</p>
                        @else
                            <p class="text-xs text-amber-600">Profil akun belum memiliki kelas, isi kelas tujuan manual.</p>
                        @endif
                    </div>
                </section>

                {{-- Pickup Time --}}
                <section class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6" id="pickup-section">
                    <div class="mb-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Jadwal</p>
                        <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950" id="pickup-section-title">
                            Jam Pengambilan
                        </h2>
                    </div>

                    <div id="pickup-slots" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach([
                            ['val' => 'istirahat_1', 'time' => 'Istirahat 1', 'sub' => '09:30 - 10:00'],
                            ['val' => 'istirahat_2', 'time' => 'Istirahat 2', 'sub' => '12:00 - 12:30'],
                            ['val' => 'pulang',      'time' => 'Pulang',      'sub' => '14:30 - 15:00'],
                            ['val' => 'atur_jam',    'time' => 'Atur Jam',    'sub' => 'Input manual'],
                        ] as $i => $slot)
                            <label class="cursor-pointer">
                                <input type="radio" name="pickup" value="{{ $slot['val'] }}"
                                       class="sr-only pickup-radio" {{ $i === 0 ? 'checked' : '' }}>
                                <div class="pickup-opt rounded-3xl border-2 p-4 text-left transition
                                            {{ $i === 0 ? 'border-orange-400 bg-orange-50' : 'border-gray-100 bg-white hover:border-orange-200 hover:bg-orange-50/50' }}">
                                    <p class="font-heading text-sm font-extrabold text-gray-900">{{ $slot['time'] }}</p>
                                    <p class="mt-1 text-xs font-medium text-gray-500">{{ $slot['sub'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div id="pickup-timepicker" class="hidden mt-4 rounded-3xl border border-orange-100 bg-orange-50 p-4">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wide text-orange-600">
                            Pilih Jam
                        </label>
                        <input type="time"
                               name="pickup_time"
                               id="pickup-time-input"
                               min="07:00"
                               max="15:00"
                               class="w-full rounded-2xl border border-orange-100 bg-white px-4 py-3 text-sm font-bold text-gray-700 focus:border-orange-300 focus:outline-none focus:ring-4 focus:ring-orange-100">
                        <p class="mt-2 text-xs leading-5 text-gray-500">
                            Pilih jam pengambilan, penyajian, atau pengantaran pesanan antara 07:00 sampai 15:00.
                        </p>
                        @error('pickup_time')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                {{-- Notes --}}
                <section class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Opsional</p>
                        <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950">Catatan Pesanan</h2>
                    </div>
                    <textarea name="note"
                              rows="4"
                              placeholder="Contoh: tidak pedas, saus dipisah, minumannya tanpa es..."
                              class="w-full resize-none rounded-3xl border border-gray-100 bg-gray-50 px-4 py-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-orange-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-orange-100">{{ old('note') }}</textarea>
                </section>
            </div>

            {{-- Summary --}}
            <aside class="lg:sticky lg:top-24">
                <section class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Checkout</p>
                            <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950">Ringkasan</h2>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-2">
                        <span id="summary-type-badge"
                              class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1.5 text-xs font-extrabold text-orange-700">
                            <i class="fa-solid fa-bag-shopping text-[10px]"></i>
                            Take Away
                        </span>
                        <span id="summary-pickup-badge"
                              class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-extrabold text-gray-600">
                            <i class="fa-solid fa-clock text-[10px]"></i>
                            <span id="summary-pickup-text">Pilih jam...</span>
                        </span>
                        <span id="summary-class-badge"
                              class="hidden inline-flex items-center gap-1.5 rounded-full bg-purple-100 px-3 py-1.5 text-xs font-extrabold text-purple-700">
                            <i class="fa-solid fa-school text-[10px]"></i>
                            <span id="summary-class-text">-</span>
                        </span>
                    </div>

                    <div class="max-h-56 space-y-3 overflow-y-auto rounded-3xl bg-gray-50 p-4">
                        @foreach($cart->items as $item)
                            <div class="flex justify-between gap-3 text-sm">
                                <div class="min-w-0">
                                    <p class="truncate font-bold text-gray-800">{{ $item->menu->name }} x{{ $item->quantity }}</p>
                                    @if($item->menu->tenant)
                                        <p class="mt-0.5 text-xs font-semibold text-orange-500">{{ $item->menu->tenant->name }}</p>
                                    @endif
                                </div>
                                <span class="whitespace-nowrap font-bold text-gray-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 rounded-3xl bg-orange-50 p-4">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-500">Subtotal</span>
                            <span class="font-bold text-gray-900">
                                Rp {{ number_format($totalPrice, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="mt-4 border-t border-orange-100 pt-4">
                            <div class="flex items-end justify-between gap-3">
                                <span class="font-heading text-base font-extrabold text-gray-950">Total</span>
                                <span class="text-right font-heading text-3xl font-extrabold text-orange-600">
                                    Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($isKasir)
                        <button type="submit"
                                class="mt-5 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 py-4 text-sm font-heading font-extrabold text-white shadow-lg shadow-orange-100 transition hover:-translate-y-0.5 hover:shadow-orange-200 active:scale-95">
                            <i class="fa-solid fa-check-circle"></i>
                            Buat Pesanan
                        </button>
                    @else
                        <button type="button"
                                id="btn-confirm"
                                class="mt-5 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 py-4 text-sm font-heading font-extrabold text-white shadow-lg shadow-orange-100 transition hover:-translate-y-0.5 hover:shadow-orange-200 active:scale-95">
                            <i class="fa-solid fa-check-circle"></i>
                            Konfirmasi Pesanan
                        </button>
                    @endif

                    <a href="{{ route('cart') }}"
                       class="mt-4 block text-center text-sm font-bold text-gray-400 transition hover:text-gray-600">
                        Kembali ke Keranjang
                    </a>
                </section>
            </aside>
        </div>
    </form>
</div>

{{-- Payment Modal --}}
@if(!$isKasir)
<div id="payment-modal"
     class="fixed inset-0 z-50 hidden items-end justify-center p-4 sm:items-center"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-title">

    <div id="modal-backdrop"
         class="absolute inset-0 bg-black/50 opacity-0 backdrop-blur-sm transition-opacity duration-300"></div>

    <div id="modal-sheet"
         class="relative w-full max-w-md translate-y-8 rounded-[2rem] bg-white p-5 opacity-0 shadow-2xl transition-all duration-300 sm:p-6">

        <div class="mx-auto mb-5 h-1 w-10 rounded-full bg-gray-200 sm:hidden"></div>

        <div class="mb-5 rounded-3xl border px-4 py-4 text-center" id="modal-preview-box">
            <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">Ringkasan Pesanan</p>
            <div class="flex items-center justify-center gap-2">
                <span id="modal-type-icon" class="flex h-8 w-8 items-center justify-center rounded-xl bg-white text-sm font-extrabold text-orange-600">TA</span>
                <span class="font-heading text-base font-extrabold text-gray-950" id="modal-type-label">Take Away</span>
            </div>
            <p class="mt-2 text-xs font-medium text-gray-500" id="modal-pickup-info">-</p>
            <p class="mt-1 hidden text-xs font-bold text-purple-600" id="modal-class-info"></p>
            <div class="mt-3 border-t border-dashed border-gray-200 pt-3">
                <p class="font-heading text-xl font-extrabold text-orange-600">
                    Rp {{ number_format($totalPrice, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <h3 id="modal-title" class="text-center font-heading text-xl font-extrabold text-gray-950">
            Pilih Cara Pembayaran
        </h3>
        <p class="mb-5 mt-1 text-center text-sm text-gray-500">
            Konfirmasi pesanan dan lanjutkan pembayaran.
        </p>

        <button id="btn-pay-telegram"
                type="button"
                class="group mb-3 flex w-full items-center gap-4 rounded-3xl border-2 border-gray-100 p-4 text-left transition hover:border-blue-300 hover:bg-blue-50">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 text-white shadow-md transition group-hover:scale-105">
                <i class="fa-brands fa-telegram text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-bold text-gray-900">Bayar via Telegram</p>
                <p class="mt-0.5 text-xs text-gray-500">Konfirmasi dan bayar lewat bot.</p>
            </div>
            <i class="fa-solid fa-chevron-right text-xs text-gray-300 group-hover:text-blue-500"></i>
        </button>

        <button id="btn-pay-upload"
                type="button"
                class="group mb-5 flex w-full items-center gap-4 rounded-3xl border-2 border-gray-100 p-4 text-left transition hover:border-orange-300 hover:bg-orange-50">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-md transition group-hover:scale-105">
                <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-bold text-gray-900">Upload Bukti Transfer</p>
                <p class="mt-0.5 text-xs text-gray-500">Upload langsung di website.</p>
            </div>
            <i class="fa-solid fa-chevron-right text-xs text-gray-300 group-hover:text-orange-500"></i>
        </button>

        <button id="btn-cancel-modal"
                type="button"
                class="w-full rounded-2xl py-3 text-center text-sm font-bold text-gray-400 transition hover:bg-gray-50 hover:text-gray-600">
            Batal
        </button>
    </div>
</div>

<form id="form-confirm-telegram" action="{{ route('customer.order.confirm') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="order_type" id="hid-type-tg">
    <input type="hidden" name="pickup" id="hid-pickup-tg">
    <input type="hidden" name="pickup_time" id="hid-pickuptime-tg">
    <input type="hidden" name="classroom" id="hid-class-tg">
    <input type="hidden" name="note" id="hid-note-tg">
    <input type="hidden" name="redirect_to" value="telegram">
</form>

<form id="form-confirm-upload" action="{{ route('customer.order.confirm') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="order_type" id="hid-type-up">
    <input type="hidden" name="pickup" id="hid-pickup-up">
    <input type="hidden" name="pickup_time" id="hid-pickuptime-up">
    <input type="hidden" name="classroom" id="hid-class-up">
    <input type="hidden" name="note" id="hid-note-up">
    <input type="hidden" name="redirect_to" value="payment">
</form>
@endif
@endsection

@push('styles')
<style>
    .order-type-opt.is-active-dinein {
        border-color: #14b8a6 !important;
        background-color: #f0fdfa !important;
    }

    .order-type-opt.is-active-takeaway {
        border-color: #f97316 !important;
        background-color: #fff7ed !important;
    }

    .order-type-opt.is-active-delivery {
        border-color: #a855f7 !important;
        background-color: #faf5ff !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const state = {
        orderType: 'takeaway',
        pickup: 'istirahat_1',
        pickupTime: '',
        classroom: '',
    };

    const mainForm = document.getElementById('main-order-form');
    const pickupSlots = document.getElementById('pickup-slots');
    const pickupTimepicker = document.getElementById('pickup-timepicker');
    const pickupTitle = document.getElementById('pickup-section-title');
    const dineinInfo = document.getElementById('dinein-info');
    const classroomSection = document.getElementById('classroom-section');
    const classroomInput = document.getElementById('classroom-input');
    const timeInput = document.getElementById('pickup-time-input');

    const summaryTypeBadge = document.getElementById('summary-type-badge');
    const summaryPickupText = document.getElementById('summary-pickup-text');
    const summaryClassBadge = document.getElementById('summary-class-badge');
    const summaryClassText = document.getElementById('summary-class-text');

    const pickupLabels = {
        istirahat_1: 'Istirahat 1 (09:30)',
        istirahat_2: 'Istirahat 2 (12:00)',
        pulang: 'Pulang (14:30)',
        atur_jam: 'Atur jam sendiri',
    };

    const typeConfig = {
        dine_in: {
            short: 'DI',
            label: 'Dine In',
            badgeClass: 'bg-teal-100 text-teal-700',
            badgeIcon: 'fa-plate-wheat',
        },
        takeaway: {
            short: 'TA',
            label: 'Take Away',
            badgeClass: 'bg-orange-100 text-orange-700',
            badgeIcon: 'fa-bag-shopping',
        },
        delivery: {
            short: 'AK',
            label: 'Antar ke Kelas',
            badgeClass: 'bg-purple-100 text-purple-700',
            badgeIcon: 'fa-person-walking',
        },
    };

    function syncTimepicker() {
        const useCustomTime = state.orderType === 'takeaway' || state.pickup === 'atur_jam';

        pickupTimepicker.classList.toggle('hidden', !useCustomTime);
        timeInput.required = useCustomTime;

        if (!useCustomTime) {
            state.pickupTime = '';
            timeInput.value = '';
        }
    }

    function onTypeChange(type) {
        state.orderType = type;

        document.querySelectorAll('.order-type-opt').forEach(option => {
            option.classList.remove('is-active-dinein', 'is-active-takeaway', 'is-active-delivery');
        });

        const activeOpt = document.querySelector('.order-type-radio:checked')?.nextElementSibling;
        if (activeOpt) {
            activeOpt.classList.add(`is-active-${type === 'dine_in' ? 'dinein' : type === 'takeaway' ? 'takeaway' : 'delivery'}`);
        }

        dineinInfo.classList.toggle('hidden', type !== 'dine_in');
        classroomSection.classList.toggle('hidden', type !== 'delivery');

        if (type === 'takeaway') {
            pickupSlots.classList.add('hidden');
            pickupTitle.textContent = 'Jam Pengambilan';
        } else {
            pickupSlots.classList.remove('hidden');
            pickupTitle.textContent = type === 'delivery' ? 'Waktu Pengantaran' : 'Waktu Penyajian';
        }

        syncTimepicker();
        updateSummary();
    }

    function updateSummary() {
        const cfg = typeConfig[state.orderType];

        summaryTypeBadge.className = `inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-extrabold ${cfg.badgeClass}`;
        summaryTypeBadge.innerHTML = `<i class="fa-solid ${cfg.badgeIcon} text-[10px]"></i> ${cfg.label}`;

        if (state.orderType === 'takeaway') {
            summaryPickupText.textContent = state.pickupTime ? `Jam ${state.pickupTime}` : 'Pilih jam...';
        } else if (state.pickup === 'atur_jam') {
            summaryPickupText.textContent = state.pickupTime ? `Jam ${state.pickupTime}` : 'Pilih jam...';
        } else {
            summaryPickupText.textContent = pickupLabels[state.pickup] || state.pickup;
        }

        if (state.orderType === 'delivery' && state.classroom.trim()) {
            summaryClassBadge.classList.remove('hidden');
            summaryClassText.textContent = state.classroom;
        } else {
            summaryClassBadge.classList.add('hidden');
        }
    }

    document.querySelectorAll('.order-type-radio').forEach(radio => {
        radio.addEventListener('change', () => onTypeChange(radio.value));
    });

    document.querySelectorAll('.pickup-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.pickup-opt').forEach(option => {
                option.classList.remove('border-orange-400', 'bg-orange-50');
                option.classList.add('border-gray-100', 'bg-white');
            });

            radio.nextElementSibling.classList.add('border-orange-400', 'bg-orange-50');
            radio.nextElementSibling.classList.remove('border-gray-100', 'bg-white');

            state.pickup = radio.value;
            syncTimepicker();
            updateSummary();
        });
    });

    timeInput.addEventListener('change', () => {
        state.pickupTime = timeInput.value;
        updateSummary();
    });

    if (classroomInput) {
        state.classroom = classroomInput.value;
        classroomInput.addEventListener('input', () => {
            state.classroom = classroomInput.value;
            updateSummary();
        });
    }

    function validate() {
        if (state.orderType === 'takeaway' && !state.pickupTime) {
            timeInput.focus();
            pickupTimepicker.classList.add('ring-4', 'ring-red-100');
            setTimeout(() => pickupTimepicker.classList.remove('ring-4', 'ring-red-100'), 1800);
            alert('Pilih jam pengambilan untuk Take Away.');
            return false;
        }

        if (state.orderType !== 'takeaway' && state.pickup === 'atur_jam' && !state.pickupTime) {
            timeInput.focus();
            pickupTimepicker.classList.add('ring-4', 'ring-red-100');
            setTimeout(() => pickupTimepicker.classList.remove('ring-4', 'ring-red-100'), 1800);
            alert(state.orderType === 'delivery' ? 'Pilih jam pengantaran.' : 'Pilih jam penyajian.');
            return false;
        }

        if (state.orderType === 'delivery' && !state.classroom.trim()) {
            classroomInput.focus();
            classroomInput.classList.add('border-red-400', 'ring-4', 'ring-red-100');
            setTimeout(() => classroomInput.classList.remove('border-red-400', 'ring-4', 'ring-red-100'), 1800);
            alert('Masukkan nama kelas untuk Antar ke Kelas.');
            return false;
        }

        return true;
    }

    function syncFields(suffix) {
        const note = document.querySelector('textarea[name="note"]');
        document.getElementById(`hid-type-${suffix}`).value = state.orderType;
        document.getElementById(`hid-pickup-${suffix}`).value = state.orderType !== 'takeaway' && state.pickup !== 'atur_jam' ? state.pickup : '';
        document.getElementById(`hid-pickuptime-${suffix}`).value = state.orderType === 'takeaway' || state.pickup === 'atur_jam' ? state.pickupTime : '';
        document.getElementById(`hid-class-${suffix}`).value = state.orderType === 'delivery' ? state.classroom : '';
        document.getElementById(`hid-note-${suffix}`).value = note ? note.value : '';
    }

    function updateModalPreview() {
        const cfg = typeConfig[state.orderType];
        document.getElementById('modal-type-icon').textContent = cfg.short;
        document.getElementById('modal-type-label').textContent = cfg.label;

        const pickupInfo = document.getElementById('modal-pickup-info');
        const classInfo = document.getElementById('modal-class-info');

        if (state.orderType === 'takeaway') {
            pickupInfo.textContent = state.pickupTime ? `Ambil jam ${state.pickupTime}` : '-';
        } else if (state.pickup === 'atur_jam') {
            pickupInfo.textContent = state.pickupTime
                ? `${state.orderType === 'delivery' ? 'Antar' : 'Sajikan'} jam ${state.pickupTime}`
                : '-';
        } else {
            pickupInfo.textContent = pickupLabels[state.pickup] || '-';
        }

        if (state.orderType === 'delivery' && state.classroom.trim()) {
            classInfo.textContent = state.classroom;
            classInfo.classList.remove('hidden');
        } else {
            classInfo.classList.add('hidden');
        }

        const box = document.getElementById('modal-preview-box');
        box.className = 'mb-5 rounded-3xl border px-4 py-4 text-center ';
        if (state.orderType === 'dine_in') box.className += 'bg-teal-50 border-teal-200';
        if (state.orderType === 'takeaway') box.className += 'bg-orange-50 border-orange-200';
        if (state.orderType === 'delivery') box.className += 'bg-purple-50 border-purple-200';
    }

    const modal = document.getElementById('payment-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const sheet = document.getElementById('modal-sheet');

    function openModal() {
        if (!validate()) return;
        updateModalPreview();
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        requestAnimationFrame(() => {
            backdrop.classList.replace('opacity-0', 'opacity-100');
            sheet.classList.replace('opacity-0', 'opacity-100');
            sheet.classList.replace('translate-y-8', 'translate-y-0');
        });
    }

    function closeModal() {
        backdrop.classList.replace('opacity-100', 'opacity-0');
        sheet.classList.replace('opacity-100', 'opacity-0');
        sheet.classList.replace('translate-y-0', 'translate-y-8');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    const btnConfirm = document.getElementById('btn-confirm');
    if (btnConfirm) {
        btnConfirm.addEventListener('click', openModal);
        document.getElementById('btn-cancel-modal').addEventListener('click', closeModal);
        backdrop.addEventListener('click', closeModal);

        document.getElementById('btn-pay-telegram').addEventListener('click', () => {
            syncFields('tg');
            document.getElementById('form-confirm-telegram').submit();
        });

        document.getElementById('btn-pay-upload').addEventListener('click', () => {
            syncFields('up');
            document.getElementById('form-confirm-upload').submit();
        });
    }

    if (mainForm) {
        mainForm.addEventListener('submit', event => {
            if (!validate()) event.preventDefault();
        });
    }

    onTypeChange('takeaway');
});
</script>
@endpush
