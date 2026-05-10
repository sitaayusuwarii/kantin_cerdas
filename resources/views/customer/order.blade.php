@extends('layouts.app')
@section('title', 'Detail Pesanan — SmartCanteen')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ url('/menu') }}" class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left text-sm text-gray-600"></i>
        </a>
        <div>
            <h1 class="font-heading font-bold text-2xl text-canteen-dark">Detail Pesanan</h1>
            <p class="text-gray-400 text-sm">Order #SC-001 · Senin, 19 April 2025</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Order Items --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h2 class="font-heading font-bold text-sm text-canteen-dark mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-basket-shopping text-primary-500"></i>Item Pesanan
                </h2>

                @php
                $items = [
                    ['emoji' => '🍛', 'name' => 'Nasi Gudeg Komplit', 'note' => 'Tanpa sambal', 'price' => 12000, 'qty' => 1, 'color' => 'bg-yellow-50'],
                    ['emoji' => '🧋', 'name' => 'Es Teh Manis', 'note' => '-', 'price' => 4000, 'qty' => 2, 'color' => 'bg-amber-50'],
                    ['emoji' => '🍌', 'name' => 'Pisang Goreng', 'note' => '-', 'price' => 5000, 'qty' => 1, 'color' => 'bg-yellow-50'],
                ];
                @endphp

                <div class="space-y-3">
                    @foreach($items as $item)
                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                        <div class="w-12 h-12 {{ $item['color'] }} rounded-xl flex items-center justify-center text-2xl flex-shrink-0">{{ $item['emoji'] }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-800">{{ $item['name'] }}</p>
                            @if($item['note'] !== '-')
                                <p class="text-xs text-gray-400 flex items-center gap-1 mt-0.5"><i class="fa-solid fa-note-sticky text-gray-300"></i>{{ $item['note'] }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <div class="flex items-center gap-2 bg-gray-100 rounded-lg px-2 py-1">
                                <button class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-primary-500 transition-colors text-sm font-bold">−</button>
                                <span class="text-sm font-semibold text-gray-700 w-4 text-center">{{ $item['qty'] }}</span>
                                <button class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-primary-500 transition-colors text-sm font-bold">+</button>
                            </div>
                            <div class="text-right w-20">
                                <p class="font-heading font-bold text-sm text-canteen-dark">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-400">@Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <button class="w-7 h-7 flex items-center justify-center text-gray-300 hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-4 border-t border-dashed border-gray-200">
                    <a href="{{ url('/menu') }}" class="text-sm text-primary-500 hover:text-primary-700 font-semibold flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-plus-circle"></i>Tambah Item
                    </a>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h2 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-note-sticky text-primary-500"></i>Catatan Pesanan
                </h2>
                <textarea placeholder="Tulis catatan khusus untuk pesananmu... (opsional)" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 resize-none transition-all"></textarea>
            </div>

            {{-- Waktu Pengambilan --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h2 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-primary-500"></i>Waktu Pengambilan
                </h2>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['Istirahat 1 (09:30)', 'Istirahat 2 (12:00)', 'Pulang (14:30)'] as $i => $time)
                    <label class="cursor-pointer">
                        <input type="radio" name="pickup" value="{{ $i }}" class="sr-only" {{ $i === 0 ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border-2 {{ $i === 0 ? 'border-primary-400 bg-orange-50' : 'border-gray-200 bg-white' }} text-center text-xs font-semibold text-gray-700 hover:border-primary-300 transition-all">
                            {{ $time }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 sticky top-24">
                <h2 class="font-heading font-bold text-sm text-canteen-dark mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-primary-500"></i>Ringkasan
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal (4 item)</span>
                        <span class="font-semibold text-gray-700">Rp 25.000</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Biaya layanan</span>
                        <span class="font-semibold text-gray-700">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm text-green-600">
                        <span class="flex items-center gap-1"><i class="fa-solid fa-tag text-xs"></i>Diskon member</span>
                        <span class="font-semibold">- Rp 0</span>
                    </div>
                    <div class="border-t border-dashed border-gray-200 pt-3 flex justify-between">
                        <span class="font-heading font-bold text-canteen-dark">Total</span>
                        <span class="font-heading font-bold text-xl text-primary-500">Rp 25.000</span>
                    </div>
                </div>

                {{-- Saldo --}}
                <div class="mt-5 p-3 bg-green-50 rounded-xl border border-green-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-wallet text-green-500 text-sm"></i>
                        <div>
                            <p class="text-xs font-medium text-gray-700">Saldo Tersedia</p>
                            <p class="text-xs text-gray-500">Rp 85.000</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-green-600 bg-green-100 px-2.5 py-1 rounded-lg">Cukup ✓</span>
                </div>

                <button class="btn-primary w-full text-white font-heading font-bold py-4 rounded-xl mt-5 text-sm shadow-lg flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check-circle"></i>
                    Konfirmasi Pesanan
                </button>

                <a href="{{ url('/menu') }}" class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-3 transition-colors">
                    Kembali ke Menu
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
