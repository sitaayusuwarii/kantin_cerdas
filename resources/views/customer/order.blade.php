@extends('layouts.app')
@section('title', 'Detail Pesanan — SmartCanteen')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('cart') }}"
           class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left text-sm text-gray-600"></i>
        </a>
        <div>
            <h1 class="font-heading font-bold text-2xl text-canteen-dark">Detail Pesanan</h1>
            <p class="text-gray-400 text-sm">Review pesananmu sebelum dikonfirmasi</p>
        </div>
    </div>

    <form action="{{ route('customer.order.confirm') }}" method="POST" id="main-order-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ═══ KOLOM KIRI ═══ --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- ORDER ITEMS --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-basket-shopping text-primary-500"></i>
                        Item Pesanan ({{ $cart->items->sum('quantity') }} item)
                    </h2>
                    <div class="space-y-3">
                        @foreach($cart->items as $item)
                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                @if($item->menu->image)
                                    <img src="{{ asset('storage/' . $item->menu->image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl">🍽️</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-800">{{ $item->menu->name }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $item->quantity }} × Rp {{ number_format($item->menu->price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-heading font-bold text-sm text-canteen-dark">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-5 pt-4 border-t border-dashed border-gray-200">
                        <a href="{{ route('customer.menu') }}"
                           class="text-sm text-primary-500 hover:text-primary-700 font-semibold flex items-center gap-2">
                            <i class="fa-solid fa-plus-circle"></i> Tambah Item
                        </a>
                    </div>
                </div>

                {{-- ═══ TIPE PESANAN ═══ --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-utensils text-primary-500"></i>
                        Tipe Pesanan
                    </h2>

                    <div class="grid grid-cols-3 gap-3">

                        {{-- Dine In --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="dine_in"
                                   class="sr-only order-type-radio" id="type-dinein">
                            <div class="order-type-opt border-2 border-gray-200 rounded-xl p-4 text-center
                                        hover:border-teal-400 hover:bg-teal-50 transition-all duration-200 h-full">
                                <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-teal-100 flex items-center justify-center">
                                    <i class="fa-solid fa-plate-wheat text-teal-600 text-lg"></i>
                                </div>
                                <p class="font-bold text-sm text-gray-700">Dine In</p>
                                <p class="text-[10px] text-gray-400 mt-0.5 leading-tight">Disajikan di piring, makan di kantin</p>
                            </div>
                        </label>

                        {{-- Take Away --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="takeaway"
                                   class="sr-only order-type-radio" id="type-takeaway" checked>
                            <div class="order-type-opt border-2 border-primary-400 bg-orange-50 rounded-xl p-4 text-center
                                        transition-all duration-200 h-full">
                                <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-orange-100 flex items-center justify-center">
                                    <i class="fa-solid fa-bag-shopping text-primary-500 text-lg"></i>
                                </div>
                                <p class="font-bold text-sm text-gray-700">Take Away</p>
                                <p class="text-[10px] text-gray-400 mt-0.5 leading-tight">Dibungkus, ambil di kasir</p>
                            </div>
                        </label>

                        {{-- Antar ke Kelas --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="delivery"
                                   class="sr-only order-type-radio" id="type-delivery">
                            <div class="order-type-opt border-2 border-gray-200 rounded-xl p-4 text-center
                                        hover:border-purple-400 hover:bg-purple-50 transition-all duration-200 h-full">
                                <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-purple-100 flex items-center justify-center">
                                    <i class="fa-solid fa-person-walking text-purple-600 text-lg"></i>
                                </div>
                                <p class="font-bold text-sm text-gray-700">Antar ke Kelas</p>
                                <p class="text-[10px] text-gray-400 mt-0.5 leading-tight">Diantar ke ruang kelas</p>
                            </div>
                        </label>

                    </div>

                    {{-- Info Dine In --}}
                    <div id="dinein-info" class="hidden mt-4">
                        <div class="flex items-start gap-3 p-3 bg-teal-50 rounded-xl border border-teal-100">
                            <i class="fa-solid fa-circle-info text-teal-500 mt-0.5 flex-shrink-0"></i>
                            <p class="text-xs text-teal-700 leading-relaxed">
                                Pesananmu akan disajikan di piring. Tunjukkan <strong>nomor order</strong> ke kasir saat mengambil makanan.
                            </p>
                        </div>
                    </div>

                    {{-- Input Kelas — muncul hanya kalau delivery --}}
                    <div id="classroom-section" class="hidden mt-4 space-y-3">

                        {{-- Info box --}}
                        <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-xl border border-purple-100">
                            <i class="fa-solid fa-circle-info text-purple-400 mt-0.5 flex-shrink-0"></i>
                            <p class="text-xs text-purple-700 leading-relaxed">
                                Pesanan akan diantar ke kelas. Pastikan nama kelas sudah benar.
                            </p>
                        </div>

                        {{-- Nama pemesan (readonly dari akun) --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                <i class="fa-solid fa-user text-purple-500 mr-1"></i>
                                Nama Pemesan
                            </label>
                            <input type="text"
                                   value="{{ auth()->user()->full_name }}"
                                   readonly
                                   class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm text-gray-400 cursor-not-allowed select-none">
                        </div>

                        {{-- Kelas — default dari users.class, bisa diedit --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                <i class="fa-solid fa-school text-purple-500 mr-1"></i>
                                Kelas Tujuan <span class="text-red-400">*</span>
                                @if(auth()->user()->class)
                                    <span class="ml-1 text-[10px] font-normal text-gray-400">(dari profil akunmu)</span>
                                @endif
                            </label>
                            <input type="text"
                                   name="classroom"
                                   id="classroom-input"
                                   placeholder="cth: TK A, Kelas B2, Ruang Mawar..."
                                   value="{{ old('classroom', auth()->user()->class) }}"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700
                                          focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all">
                            @error('classroom')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @if(auth()->user()->class)
                                <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    Bisa diubah jika berbeda dengan kelas di profil
                                </p>
                            @else
                                <p class="text-[10px] text-amber-500 mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Profil akunmu belum ada kelas — isi manual
                                </p>
                            @endif
                        </div>

                    </div>

                </div>

                {{-- ═══ WAKTU PENGAMBILAN / PENGIRIMAN ═══ --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100" id="pickup-section">
                    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-primary-500"></i>
                        <span id="pickup-section-title">Waktu Pengambilan</span>
                    </h2>

                    {{-- Slot tetap: Dine In & Delivery --}}
                    <div id="pickup-slots" class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        @foreach([
                            ['val' => 'istirahat_1', 'time' => 'Istirahat 1', 'sub' => '09:30 — 10:00'],
                            ['val' => 'istirahat_2', 'time' => 'Istirahat 2', 'sub' => '12:00 — 12:30'],
                            ['val' => 'pulang',      'time' => 'Pulang',      'sub' => '14:30 — 15:00'],
                        ] as $i => $slot)
                        <label class="cursor-pointer">
                            <input type="radio" name="pickup" value="{{ $slot['val'] }}"
                                   class="sr-only pickup-radio" {{ $i === 0 ? 'checked' : '' }}>
                            <div class="pickup-opt p-3 rounded-xl border-2
                                        {{ $i === 0 ? 'border-primary-400 bg-orange-50' : 'border-gray-200' }}
                                        hover:border-primary-300 transition-all text-center">
                                <p class="text-xs font-bold text-gray-700">{{ $slot['time'] }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $slot['sub'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Time picker bebas: hanya untuk Take Away --}}
                    <div id="pickup-timepicker" class="hidden">
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <input type="time"
                                       name="pickup_time"
                                       id="pickup-time-input"
                                       min="07:00" max="15:00"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700
                                              focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2 flex items-center gap-1">
                            <i class="fa-solid fa-circle-info"></i>
                            Pilih jam kamu akan mengambil pesanan di kasir (07:00 — 15:00)
                        </p>
                        @error('pickup_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- NOTES --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-note-sticky text-primary-500"></i>
                        Catatan Pesanan
                    </h2>
                    <textarea name="note" rows="3"
                              placeholder="Catatan khusus, misal: tidak pedas..."
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 resize-none focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">{{ old('note') }}</textarea>
                </div>

            </div>

            {{-- ═══ KOLOM KANAN — SUMMARY ═══ --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 sticky top-24">

                    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-primary-500"></i>
                        Ringkasan
                    </h2>

                    {{-- Badge tipe pesanan --}}
                    <div class="mb-4 flex flex-wrap gap-1.5">
                        <span id="summary-type-badge"
                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                            <i class="fa-solid fa-bag-shopping text-[10px]"></i>
                            Take Away
                        </span>
                        <span id="summary-pickup-badge"
                              class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                            <i class="fa-solid fa-clock text-[10px]"></i>
                            <span id="summary-pickup-text">Istirahat 1</span>
                        </span>
                        <span id="summary-class-badge" class="hidden inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                            <i class="fa-solid fa-school text-[10px]"></i>
                            <span id="summary-class-text">—</span>
                        </span>
                    </div>

                    <div class="space-y-2 mb-4 max-h-44 overflow-y-auto">
                        @foreach($cart->items as $item)
                        <div class="flex justify-between text-xs text-gray-500">
                            <span class="truncate pr-2">{{ $item->menu->name }} ×{{ $item->quantity }}</span>
                            <span class="font-medium flex-shrink-0">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-dashed border-gray-200 pt-3">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-700">
                                Rp {{ number_format($totalPrice, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-3 pt-3 flex justify-between items-center">
                        <span class="font-heading font-bold text-canteen-dark">Total</span>
                        <span class="font-heading font-bold text-xl text-primary-500">
                            Rp {{ number_format($totalPrice, 0, ',', '.') }}
                        </span>
                    </div>

                    <button type="button" id="btn-confirm"
                            class="btn-primary w-full text-white font-heading font-bold py-4 rounded-xl mt-5 text-sm shadow-lg flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check-circle"></i>
                        Konfirmasi Pesanan
                    </button>

                    <a href="{{ route('cart') }}"
                       class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-3 transition-colors">
                        ← Kembali ke Keranjang
                    </a>

                </div>
            </div>

        </div>
    </form>

</div>

{{-- ═══ MODAL PEMBAYARAN ═══ --}}
<div id="payment-modal"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 hidden"
     role="dialog" aria-modal="true" aria-labelledby="modal-title">

    <div id="modal-backdrop"
         class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <div id="modal-sheet"
         class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6 translate-y-8 opacity-0 transition-all duration-300">

        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5 sm:hidden"></div>

        {{-- Preview pesanan di modal --}}
        <div class="rounded-2xl px-4 py-3 mb-5 text-center border" id="modal-preview-box">
            <p class="text-[10px] text-gray-400 uppercase tracking-widest mb-1">Ringkasan Pesanan</p>
            <div class="flex items-center justify-center gap-2 mb-1">
                <span id="modal-type-icon" class="text-xl">🛍️</span>
                <span class="font-heading font-bold text-base text-canteen-dark" id="modal-type-label">Take Away</span>
            </div>
            <p class="text-xs text-gray-500" id="modal-pickup-info">—</p>
            <p class="text-xs text-purple-600 font-medium hidden" id="modal-class-info"></p>
            <div class="mt-2 pt-2 border-t border-dashed border-gray-200">
                <p class="font-heading font-bold text-primary-500">
                    Rp {{ number_format($totalPrice, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <h3 id="modal-title" class="font-heading font-bold text-lg text-canteen-dark text-center mb-1">
            Pilih Cara Pembayaran
        </h3>
        <p class="text-xs text-gray-400 text-center mb-5">Konfirmasi pesanan dan lanjutkan pembayaran</p>

        <button id="btn-pay-telegram" type="button"
                class="group w-full flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-100 hover:border-blue-400 hover:bg-blue-50 transition-all duration-200 mb-3 text-left">
            <div class="w-11 h-11 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-105 transition-transform">
                <i class="fa-brands fa-telegram text-white text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-gray-800">Bayar via Telegram</p>
                <p class="text-xs text-gray-400">Konfirmasi & bayar lewat bot</p>
            </div>
            <i class="fa-solid fa-chevron-right text-xs text-gray-300 group-hover:text-blue-400 transition-colors"></i>
        </button>

        <button id="btn-pay-upload" type="button"
                class="group w-full flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-100 hover:border-primary-400 hover:bg-orange-50 transition-all duration-200 mb-5 text-left">
            <div class="w-11 h-11 btn-primary rounded-xl flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-cloud-arrow-up text-white text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-gray-800">Upload Bukti Transfer</p>
                <p class="text-xs text-gray-400">Upload langsung di website</p>
            </div>
            <i class="fa-solid fa-chevron-right text-xs text-gray-300 group-hover:text-primary-400 transition-colors"></i>
        </button>

        <button id="btn-cancel-modal" type="button"
                class="w-full text-center text-sm text-gray-400 hover:text-gray-600 transition-colors py-1">
            Batal
        </button>
    </div>
</div>

{{-- Hidden forms --}}
<form id="form-confirm-telegram" action="{{ route('customer.order.confirm') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="order_type"   id="hid-type-tg">
    <input type="hidden" name="pickup"        id="hid-pickup-tg">
    <input type="hidden" name="pickup_time"   id="hid-pickuptime-tg">
    <input type="hidden" name="classroom"     id="hid-class-tg">
    <input type="hidden" name="note"          id="hid-note-tg">
    <input type="hidden" name="redirect_to"   value="telegram">
</form>

<form id="form-confirm-upload" action="{{ route('customer.order.confirm') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="order_type"   id="hid-type-up">
    <input type="hidden" name="pickup"        id="hid-pickup-up">
    <input type="hidden" name="pickup_time"   id="hid-pickuptime-up">
    <input type="hidden" name="classroom"     id="hid-class-up">
    <input type="hidden" name="note"          id="hid-note-up">
    <input type="hidden" name="redirect_to"   value="payment">
</form>

@endsection

@push('styles')
<style>
    .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); }
    .btn-primary:hover { filter: brightness(1.05); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(234,88,12,0.3); }

    .order-type-opt.is-active-dinein   { border-color: #14b8a6 !important; background-color: #f0fdfa !important; }
    .order-type-opt.is-active-takeaway { border-color: #f97316 !important; background-color: #fff7ed !important; }
    .order-type-opt.is-active-delivery { border-color: #a855f7 !important; background-color: #faf5ff !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─── State ───────────────────────────────────────────
    const state = {
        orderType : 'takeaway',
        pickup    : 'istirahat_1',
        pickupTime: '',
        classroom : '',
    };

    // ─── Elemen ──────────────────────────────────────────
    const pickupSlots      = document.getElementById('pickup-slots');
    const pickupTimepicker = document.getElementById('pickup-timepicker');
    const pickupTitle      = document.getElementById('pickup-section-title');
    const dineinInfo       = document.getElementById('dinein-info');
    const classroomSection = document.getElementById('classroom-section');
    const classroomInput   = document.getElementById('classroom-input');
    const timeInput        = document.getElementById('pickup-time-input');

    // Summary badges
    const summaryTypeBadge   = document.getElementById('summary-type-badge');
    const summaryPickupBadge = document.getElementById('summary-pickup-badge');
    const summaryPickupText  = document.getElementById('summary-pickup-text');
    const summaryClassBadge  = document.getElementById('summary-class-badge');
    const summaryClassText   = document.getElementById('summary-class-text');

    const pickupLabels = {
        'istirahat_1': 'Istirahat 1 (09:30)',
        'istirahat_2': 'Istirahat 2 (12:00)',
        'pulang'     : 'Pulang (14:30)',
    };

    const typeConfig = {
        dine_in  : { icon: '🍽️', label: 'Dine In',        badgeClass: 'bg-teal-100 text-teal-700',     badgeIcon: 'fa-plate-wheat' },
        takeaway : { icon: '🛍️', label: 'Take Away',       badgeClass: 'bg-orange-100 text-orange-700', badgeIcon: 'fa-bag-shopping' },
        delivery : { icon: '🚶', label: 'Antar ke Kelas',  badgeClass: 'bg-purple-100 text-purple-700', badgeIcon: 'fa-person-walking' },
    };

    // ─── Update UI saat tipe berubah ─────────────────────
    function onTypeChange(type) {
        state.orderType = type;

        // Reset active class semua opt
        document.querySelectorAll('.order-type-opt').forEach(el => {
            el.classList.remove('is-active-dinein','is-active-takeaway','is-active-delivery');
            el.classList.add('border-gray-200');
            el.classList.remove('border-teal-400','bg-teal-50','border-primary-400','bg-orange-50','border-purple-400','bg-purple-50');
        });
        const activeOpt = document.querySelector(`.order-type-radio:checked`).nextElementSibling;
        activeOpt.classList.add(`is-active-${type === 'dine_in' ? 'dinein' : type === 'takeaway' ? 'takeaway' : 'delivery'}`);
        activeOpt.classList.remove('border-gray-200');

        // Tampilkan/sembunyikan section
        dineinInfo.classList.toggle('hidden', type !== 'dine_in');
        classroomSection.classList.toggle('hidden', type !== 'delivery');

        // Waktu pengambilan
        if (type === 'takeaway') {
            pickupSlots.classList.add('hidden');
            pickupTimepicker.classList.remove('hidden');
            pickupTitle.textContent = 'Jam Pengambilan';
        } else {
            pickupSlots.classList.remove('hidden');
            pickupTimepicker.classList.add('hidden');
            pickupTitle.textContent = type === 'delivery' ? 'Slot Pengiriman' : 'Waktu Pengambilan';
        }

        updateSummary();
    }

    // ─── Update summary sidebar ───────────────────────────
    function updateSummary() {
        const cfg = typeConfig[state.orderType];

        // Badge tipe
        summaryTypeBadge.className = `inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ${cfg.badgeClass}`;
        summaryTypeBadge.innerHTML = `<i class="fa-solid ${cfg.badgeIcon} text-[10px]"></i> ${cfg.label}`;

        // Badge pickup
        if (state.orderType === 'takeaway') {
            summaryPickupText.textContent = state.pickupTime ? `Jam ${state.pickupTime}` : 'Pilih jam...';
        } else {
            summaryPickupText.textContent = pickupLabels[state.pickup] || state.pickup;
        }

        // Badge kelas
        if (state.orderType === 'delivery' && state.classroom) {
            summaryClassBadge.classList.remove('hidden');
            summaryClassText.textContent = state.classroom;
        } else {
            summaryClassBadge.classList.add('hidden');
        }
    }

    // ─── Event listeners ─────────────────────────────────

    // Tipe pesanan
    document.querySelectorAll('.order-type-radio').forEach(r => {
        r.addEventListener('change', () => onTypeChange(r.value));
    });

    // Pickup slot
    document.querySelectorAll('.pickup-radio').forEach(r => {
        r.addEventListener('change', () => {
            document.querySelectorAll('.pickup-opt').forEach(el => {
                el.classList.remove('border-primary-400', 'bg-orange-50');
                el.classList.add('border-gray-200');
            });
            r.nextElementSibling.classList.add('border-primary-400', 'bg-orange-50');
            r.nextElementSibling.classList.remove('border-gray-200');
            state.pickup = r.value;
            updateSummary();
        });
    });

    // Time picker
    timeInput.addEventListener('change', () => {
        state.pickupTime = timeInput.value;
        updateSummary();
    });

    // ─── Validasi ────────────────────────────────────────
    function validate() {
        if (state.orderType === 'takeaway' && !state.pickupTime) {
            pickupTimepicker.querySelector('input').focus();
            pickupTimepicker.classList.add('ring-2','ring-red-400','rounded-xl','p-2');
            setTimeout(() => pickupTimepicker.classList.remove('ring-2','ring-red-400','rounded-xl','p-2'), 2000);
            alert('Pilih jam pengambilan untuk Take Away.');
            return false;
        }
        if (state.orderType === 'delivery' && !state.classroom.trim()) {
            classroomInput.focus();
            classroomInput.classList.add('border-red-400','ring-2','ring-red-100');
            setTimeout(() => classroomInput.classList.remove('border-red-400','ring-2','ring-red-100'), 2000);
            alert('Masukkan nama kelas untuk Antar ke Kelas.');
            return false;
        }
        return true;
    }

    // ─── Sync hidden fields ───────────────────────────────
    function syncFields(suffix) {
        const note = document.querySelector('textarea[name="note"]');
        document.getElementById(`hid-type-${suffix}`).value        = state.orderType;
        document.getElementById(`hid-pickup-${suffix}`).value      = state.orderType !== 'takeaway' ? state.pickup : '';
        document.getElementById(`hid-pickuptime-${suffix}`).value  = state.orderType === 'takeaway' ? state.pickupTime : '';
        document.getElementById(`hid-class-${suffix}`).value       = state.orderType === 'delivery' ? state.classroom : '';
        document.getElementById(`hid-note-${suffix}`).value        = note ? note.value : '';
    }

    // ─── Update modal preview ────────────────────────────
    function updateModalPreview() {
        const cfg = typeConfig[state.orderType];
        document.getElementById('modal-type-icon').textContent  = cfg.icon;
        document.getElementById('modal-type-label').textContent = cfg.label;

        const pickupInfo = document.getElementById('modal-pickup-info');
        const classInfo  = document.getElementById('modal-class-info');

        if (state.orderType === 'takeaway') {
            pickupInfo.textContent = state.pickupTime ? `Ambil jam ${state.pickupTime}` : '—';
        } else {
            pickupInfo.textContent = pickupLabels[state.pickup] || '—';
        }

        if (state.orderType === 'delivery' && state.classroom) {
            classInfo.textContent = `📍 ${state.classroom}`;
            classInfo.classList.remove('hidden');
        } else {
            classInfo.classList.add('hidden');
        }

        // Warna box preview sesuai tipe
        const box = document.getElementById('modal-preview-box');
        box.className = 'rounded-2xl px-4 py-3 mb-5 text-center border ';
        if (state.orderType === 'dine_in')  box.className += 'bg-teal-50 border-teal-200';
        if (state.orderType === 'takeaway') box.className += 'bg-orange-50 border-orange-200';
        if (state.orderType === 'delivery') box.className += 'bg-purple-50 border-purple-200';
    }

    // ─── Modal ───────────────────────────────────────────
    const modal    = document.getElementById('payment-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const sheet    = document.getElementById('modal-sheet');

    function openModal() {
        if (!validate()) return;
        updateModalPreview();
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.replace('opacity-0','opacity-100');
            sheet.classList.replace('opacity-0','opacity-100');
            sheet.classList.replace('translate-y-8','translate-y-0');
        });
    }

    function closeModal() {
        backdrop.classList.replace('opacity-100','opacity-0');
        sheet.classList.replace('opacity-100','opacity-0');
        sheet.classList.replace('translate-y-0','translate-y-8');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    document.getElementById('btn-confirm').addEventListener('click', openModal);
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

    // Init — classroom default dari value input (sudah diisi users.class di blade)
    if (classroomInput) {
        state.classroom = classroomInput.value;
        classroomInput.addEventListener('input', () => {
            state.classroom = classroomInput.value;
            updateSummary();
        });
    }

    onTypeChange('takeaway');
});
</script>
@endpush