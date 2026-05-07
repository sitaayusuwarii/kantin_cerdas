@extends('layouts.app')
@section('title', 'Detail Pesanan — SmartCanteen')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        {{-- Rute diperbaiki ke keranjang --}}
        <a href="{{ route('customer.cart.index') }}" class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
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

                    {{-- Tambah Item (Dari kodemu) --}}
                    <div class="mt-5 pt-4 border-t border-dashed border-gray-200">
                        <a href="{{ route('customer.menu') }}" class="text-sm text-primary-500 hover:text-primary-700 font-semibold flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-plus-circle"></i>Tambah Item
                        </a>
                    </div>
                </div>

                {{-- NOTES (Dari fitur baru temanmu) --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-note-sticky text-primary-500"></i>
                        Catatan Pesanan
                    </h2>
                    <textarea name="note" rows="3"
                              placeholder="Catatan khusus keseluruhan pesanan, misal: titip di pos satpam..."
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
                    <button type="submit"
                            class="btn-primary w-full text-white font-heading font-bold py-4 rounded-xl mt-5 text-sm shadow-lg flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check-circle"></i>
                        Konfirmasi Pesanan
                    </button>

                    {{-- Link diperbaiki ke rute yang benar --}}
                    <a href="{{ route('customer.cart.index') }}"
                       class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-3 transition-colors">
                        ← Kembali ke Keranjang
                    </a>

                </div>
            </div>

        </div>
    </form>
</div>
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
});
</script>
@endpush