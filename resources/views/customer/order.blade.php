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

    <form action="{{ route('customer.order.confirm') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ORDER ITEMS --}}
            <div class="lg:col-span-2 space-y-4">

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-basket-shopping text-primary-500"></i>
                        Item Pesanan ({{ $cart->items->sum('quantity') }} item)
                    </h2>

                    <div class="space-y-3">
                        @foreach($cart->items as $item)
                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">

                            {{-- Image --}}
                            <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                @if($item->menu->image)
                                    <img src="{{ asset('storage/' . $item->menu->image) }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl">🍽️</div>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-800">{{ $item->menu->name }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $item->quantity }} × Rp {{ number_format($item->menu->price, 0, ',', '.') }}
                                </p>
                            </div>

                            {{-- Price --}}
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

                {{-- NOTES --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-note-sticky text-primary-500"></i>
                        Catatan Pesanan
                    </h2>
                    <textarea name="note" rows="3"
                              placeholder="Catatan khusus, misal: extra sambal..."
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 resize-none focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all"></textarea>
                </div>

                {{-- PICKUP TIME --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-primary-500"></i>
                        Waktu Pengambilan
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
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
                </div>

            </div>

            {{-- SUMMARY --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 sticky top-24">

                    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-primary-500"></i>
                        Ringkasan
                    </h2>

                    <div class="space-y-2 mb-4 max-h-40 overflow-y-auto">
                        @foreach($cart->items as $item)
                        <div class="flex justify-between text-xs text-gray-500">
                            <span class="truncate">{{ $item->menu->name }} ×{{ $item->quantity }}</span>
                            <span class="font-medium flex-shrink-0 ml-2">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-dashed border-gray-200 pt-3 space-y-2">
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

                   

                    {{-- Submit --}}
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
<div id="payment-modal"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 hidden"
     role="dialog" aria-modal="true" aria-labelledby="modal-title">
 
    {{-- Backdrop --}}
    <div id="modal-backdrop"
         class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>
 
    {{-- Sheet --}}
    <div id="modal-sheet"
         class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6 translate-y-8 opacity-0 transition-all duration-300">
 
        {{-- Handle bar (mobile) --}}
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5 sm:hidden"></div>
 
        <h3 id="modal-title" class="font-heading font-bold text-lg text-canteen-dark text-center mb-1">
            Pilih Cara Pembayaran
        </h3>
        <p class="text-xs text-gray-400 text-center mb-6">Konfirmasi pesanan dan lanjutkan pembayaran</p>
 
        {{-- Opsi Telegram --}}
        <button id="btn-pay-telegram" type="button"
                class="group w-full flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-100 hover:border-blue-400 hover:bg-blue-50 transition-all duration-200 mb-3 text-left">
            <div class="w-11 h-11 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-105 transition-transform">
                <i class="fa-brands fa-telegram text-white text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-gray-800">Bayar via Telegram</p>
                <p class="text-xs text-gray-400 mt-0.5">QRIS, BCA, atau BRI lewat bot Telegram</p>
            </div>
            <i class="fa-solid fa-chevron-right text-xs text-gray-300 group-hover:text-blue-400 transition-colors"></i>
        </button>
 
        {{-- Opsi Upload Manual --}}
        <button id="btn-pay-upload" type="button"
                class="group w-full flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-100 hover:border-primary-400 hover:bg-orange-50 transition-all duration-200 mb-5 text-left">
            <div class="w-11 h-11 btn-primary rounded-xl flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-cloud-arrow-up text-white text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-gray-800">Upload Bukti Transfer</p>
                <p class="text-xs text-gray-400 mt-0.5">BRI, BCA, GoPay, OVO, DANA, Tunai</p>
            </div>
            <i class="fa-solid fa-chevron-right text-xs text-gray-300 group-hover:text-primary-400 transition-colors"></i>
        </button>
 
        {{-- Batal --}}
        <button id="btn-cancel-modal" type="button"
                class="w-full text-center text-sm text-gray-400 hover:text-gray-600 transition-colors py-1">
            Batal
        </button>
    </div>
</div>
 
{{-- Form tersembunyi untuk submit order lalu redirect ke Telegram --}}
<form id="form-confirm-telegram" action="{{ route('customer.order.confirm') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="pickup" id="hidden-pickup">
    <input type="hidden" name="note"   id="hidden-note">
    <input type="hidden" name="redirect_to" value="telegram">
</form>
 
<form id="form-confirm-upload" action="{{ route('customer.order.confirm') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="pickup" id="hidden-pickup-upload">
    <input type="hidden" name="note"   id="hidden-note-upload">
    <input type="hidden" name="redirect_to" value="payment">
</form>
@endsection

@push('styles')
<style>
    .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); }
    .btn-primary:hover { filter: brightness(1.05); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(234,88,12,0.3); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // === Pickup radio existing logic ===
    document.querySelectorAll('.pickup-radio').forEach(r => {
        r.addEventListener('change', () => {
            document.querySelectorAll('.pickup-opt').forEach(el => {
                el.classList.remove('border-primary-400', 'bg-orange-50');
                el.classList.add('border-gray-200');
            });
            r.nextElementSibling.classList.add('border-primary-400', 'bg-orange-50');
            r.nextElementSibling.classList.remove('border-gray-200');
        });
    });
 
    // === Modal logic ===
    const modal      = document.getElementById('payment-modal');
    const backdrop   = document.getElementById('modal-backdrop');
    const sheet      = document.getElementById('modal-sheet');
    const btnConfirm = document.getElementById('btn-confirm');
 
    function openModal() {
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.add('opacity-100');
            backdrop.classList.remove('opacity-0');
            sheet.classList.add('opacity-100', 'translate-y-0');
            sheet.classList.remove('opacity-0', 'translate-y-8');
        });
    }
 
    function closeModal() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        sheet.classList.remove('opacity-100', 'translate-y-0');
        sheet.classList.add('opacity-0', 'translate-y-8');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
 
    // Ambil nilai pickup & note dari form utama
    function syncHiddenFields(pickupId, noteId) {
        const selectedPickup = document.querySelector('.pickup-radio:checked');
        const note = document.querySelector('textarea[name="note"]');
        document.getElementById(pickupId).value = selectedPickup ? selectedPickup.value : '';
        document.getElementById(noteId).value   = note ? note.value : '';
    }
 
    btnConfirm.addEventListener('click', openModal);
 
    document.getElementById('btn-cancel-modal').addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
 
    document.getElementById('btn-pay-telegram').addEventListener('click', function () {
        syncHiddenFields('hidden-pickup', 'hidden-note');
        document.getElementById('form-confirm-telegram').submit();
    });
 
    document.getElementById('btn-pay-upload').addEventListener('click', function () {
        syncHiddenFields('hidden-pickup-upload', 'hidden-note-upload');
        document.getElementById('form-confirm-upload').submit();
    });
});
</script>
@endpush