```blade
@extends('layouts.app')
@section('title', 'Detail Pesanan — SmartCanteen')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ url('/menu') }}"
           class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left text-sm text-gray-600"></i>
        </a>

        <div>
            <h1 class="font-heading font-bold text-2xl text-canteen-dark">
                Detail Pesanan
            </h1>

            <p class="text-gray-400 text-sm">
                Order #{{ $order->order_number }}
                ·
                {{ $order->created_at->translatedFormat('l, d F Y') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ORDER ITEMS --}}
        <div class="lg:col-span-2 space-y-4">

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">

                <h2 class="font-heading font-bold text-sm text-canteen-dark mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-basket-shopping text-primary-500"></i>
                    Item Pesanan
                </h2>

                <div class="space-y-3">

                    @foreach($order->items as $item)

                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors group">

                        {{-- IMAGE --}}
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">

                            @if($item->menu && $item->menu->image)
                                <img
                                    src="{{ asset('storage/' . $item->menu->image) }}"
                                    alt="{{ $item->menu->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif

                        </div>

                        {{-- INFO --}}
                        <div class="flex-1 min-w-0">

                            <p class="font-semibold text-sm text-gray-800">
                                {{ $item->menu->name ?? 'Menu tidak tersedia' }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Qty : {{ $item->quantity }}
                            </p>

                        </div>

                        {{-- PRICE --}}
                        <div class="text-right flex-shrink-0">

                            <p class="font-heading font-bold text-sm text-canteen-dark">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>

                            <p class="text-xs text-gray-400">
                                @Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </p>

                        </div>

                    </div>

                    @endforeach

                </div>

                {{-- ADD ITEM --}}
                <div class="mt-5 pt-4 border-t border-dashed border-gray-200">
                    <a href="{{ url('/menu') }}"
                       class="text-sm text-primary-500 hover:text-primary-700 font-semibold flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-plus-circle"></i>
                        Tambah Item
                    </a>
                </div>

            </div>

            {{-- NOTES --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">

                <h2 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-note-sticky text-primary-500"></i>
                    Catatan Pesanan
                </h2>

                <textarea
                    rows="3"
                    readonly
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 resize-none">{{ $order->note ?? '-' }}</textarea>

            </div>

            {{-- PICKUP TIME --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">

                <h2 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-primary-500"></i>
                    Waktu Pengambilan
                </h2>

                @php
                    $pickupLabels = [
                        'istirahat_1' => 'Istirahat 1 (09:30)',
                        'istirahat_2' => 'Istirahat 2 (12:00)',
                        'pulang'      => 'Pulang (14:30)',
                    ];
                @endphp

                <div class="p-3 rounded-xl border-2 border-primary-400 bg-orange-50 text-center text-xs font-semibold text-gray-700">
                    {{ $pickupLabels[$order->pickup_schedule] ?? '-' }}
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

                <div class="space-y-3">

                    <div class="flex justify-between text-sm">

                        <span class="text-gray-500">
                            Subtotal ({{ $order->items->sum('quantity') }} item)
                        </span>

                        <span class="font-semibold text-gray-700">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </span>

                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Biaya layanan</span>
                        <span class="font-semibold text-gray-700">Rp 0</span>
                    </div>

                    <div class="flex justify-between text-sm text-green-600">

                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-tag text-xs"></i>
                            Diskon member
                        </span>

                        <span class="font-semibold">- Rp 0</span>

                    </div>

                    <div class="border-t border-dashed border-gray-200 pt-3 flex justify-between">

                        <span class="font-heading font-bold text-canteen-dark">
                            Total
                        </span>

                        <span class="font-heading font-bold text-xl text-primary-500">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </span>

                    </div>

                </div>

                {{-- STATUS --}}
                <div class="mt-5 p-3 rounded-xl border flex items-center justify-between
                    @if($order->status == 'selesai')
                        bg-green-50 border-green-100
                    @elseif($order->status == 'diproses')
                        bg-blue-50 border-blue-100
                    @else
                        bg-yellow-50 border-yellow-100
                    @endif
                ">

                    <div class="flex items-center gap-2">

                        <i class="fa-solid fa-wallet text-sm
                            @if($order->status == 'selesai')
                                text-green-500
                            @elseif($order->status == 'diproses')
                                text-blue-500
                            @else
                                text-yellow-500
                            @endif
                        "></i>

                        <div>
                            <p class="text-xs font-medium text-gray-700">
                                Status Pesanan
                            </p>

                            <p class="text-xs text-gray-500 capitalize">
                                {{ $order->status }}
                            </p>
                        </div>

                    </div>

                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg
                        @if($order->status == 'selesai')
                            text-green-600 bg-green-100
                        @elseif($order->status == 'diproses')
                            text-blue-600 bg-blue-100
                        @else
                            text-yellow-600 bg-yellow-100
                        @endif
                    ">
                        {{ ucfirst($order->status) }}
                    </span>

                </div>

                {{-- BUTTON --}}
                <form action="{{ route('customer.order.confirm') }}" method="POST">
                    @csrf

                    <input type="hidden"
                           name="cart"
                           value='@json($order->items)'>

                    <input type="hidden"
                           name="pickup"
                           value="{{ $order->pickup_schedule }}">

                    <input type="hidden"
                           name="note"
                           value="{{ $order->note }}">

                    <button
                        type="submit"
                        class="btn-primary w-full text-white font-heading font-bold py-4 rounded-xl mt-5 text-sm shadow-lg flex items-center justify-center gap-2">

                        <i class="fa-solid fa-check-circle"></i>
                        Konfirmasi Pesanan

                    </button>
                </form>

                <a href="{{ url('/menu') }}"
                   class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-3 transition-colors">
                    Kembali ke Menu
                </a>

            </div>

        </div>
    </div>
</div>
@endsection
```
