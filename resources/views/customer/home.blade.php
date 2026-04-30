@extends('layouts.app')
@section('title', 'Dashboard — SmartCanteen')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Greeting Banner --}}
    <div class="relative overflow-hidden rounded-2xl mb-8 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-500 via-primary-600 to-orange-700"></div>
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,<svg width=\"60\" height=\"60\" xmlns=\"http://www.w3.org/2000/svg\"><circle cx=\"30\" cy=\"30\" r=\"25\" fill=\"none\" stroke=\"white\" stroke-width=\"1\"/></svg>'); background-size: 60px;"></div>
        <div class="relative px-6 py-8 md:px-10 md:py-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <p class="text-orange-100 text-sm font-medium mb-1">
                    <i class="fa-regular fa-sun mr-1"></i>Selamat Pagi 👋
                </p>
                <h1 class="font-heading font-bold text-2xl md:text-3xl text-white mb-2">Mawar</h1>
                <p class="text-orange-100 text-sm">Kelas XII IPA 2 · SMAN 1 Contoh · ID: <span class="font-semibold">SC-2024-0198</span></p>
                <div class="flex flex-wrap items-center gap-2 mt-4">
                    <!-- <span class="bg-white/20 text-white text-xs font-medium px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <i class="fa-solid fa-circle text-green-300 text-[8px]"></i>Saldo Aktif
                    </span> -->
                    <span class="bg-white/20 text-white text-xs font-medium px-3 py-1.5 rounded-full">
                        <i class="fa-solid fa-star text-yellow-300 mr-1"></i>Member Silver
                    </span>
                </div>
            </div>
            <!-- <div class="text-center bg-white/15 rounded-2xl px-8 py-5 backdrop-blur-sm">
                <p class="text-orange-100 text-xs font-medium uppercase tracking-wider mb-1">Saldo</p>
                <p class="font-heading font-bold text-3xl text-white">Rp 85.000</p>
                <p class="text-orange-200 text-xs mt-1">Terakhir diisi: 15 Apr</p> -->
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-50 card-hover">
            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-bag-shopping text-primary-500"></i>
            </div>
            <p class="text-2xl font-heading font-bold text-canteen-dark">12</p>
            <p class="text-gray-500 text-xs mt-0.5">Total Pesanan</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-50 card-hover">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-circle-check text-green-500"></i>
            </div>
            <p class="text-2xl font-heading font-bold text-canteen-dark">10</p>
            <p class="text-gray-500 text-xs mt-0.5">Selesai</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-50 card-hover">
            <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-clock text-yellow-500"></i>
            </div>
            <p class="text-2xl font-heading font-bold text-canteen-dark">1</p>
            <p class="text-gray-500 text-xs mt-0.5">Proses</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-50 card-hover">
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-triangle-exclamation text-red-400"></i>
            </div>
            <p class="text-2xl font-heading font-bold text-canteen-dark">1</p>
            <p class="text-gray-500 text-xs mt-0.5">Tagihan</p>
        </div>
    </div>

    {{-- Status Pembayaran --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-orange-50 mb-8">
        <h2 class="font-heading font-bold text-base text-canteen-dark mb-4 flex items-center gap-2">
            <i class="fa-solid fa-file-invoice-dollar text-primary-500"></i>
            Status Pembayaran Terkini
        </h2>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 bg-red-50 rounded-xl border border-red-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-receipt text-red-500"></i>
                </div>
                <div>
                    <p class="font-semibold text-sm text-gray-800">Tagihan April 2025</p>
                    <p class="text-xs text-gray-500">Jatuh tempo: 20 April 2025</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="font-heading font-bold text-lg text-red-600">Rp 45.000</p>
                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                        <i class="fa-solid fa-circle text-[8px]"></i>Belum Lunas
                    </span>
                </div>
                <a href="{{ url('/payment') }}" class="btn-primary text-white text-xs font-semibold px-4 py-2.5 rounded-xl flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-upload text-xs"></i>Bayar
                </a>
            </div>
        </div>
    </div>

    {{-- Quick Actions + Favorit --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Quick Actions --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-orange-50">
            <h2 class="font-heading font-bold text-base text-canteen-dark mb-4 flex items-center gap-2">
                <i class="fa-solid fa-bolt text-primary-500"></i>Aksi Cepat
            </h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ url('/menu') }}" class="flex flex-col items-center gap-2 p-4 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors group text-center">
                    <div class="w-11 h-11 btn-primary rounded-xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-utensils text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Pesan Menu</span>
                </a>
                <a href="{{ url('/invoice') }}" class="flex flex-col items-center gap-2 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors group text-center">
                    <div class="w-11 h-11 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-file-invoice text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Tagihan</span>
                </a>
                <a href="{{ url('/payment') }}" class="flex flex-col items-center gap-2 p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-colors group text-center">
                    <div class="w-11 h-11 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-upload text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Upload Bukti</span>
                </a>
                <a href="{{ url('/history') }}" class="flex flex-col items-center gap-2 p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition-colors group text-center">
                    <div class="w-11 h-11 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-clock-rotate-left text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Riwayat</span>
                </a>
            </div>
        </div>

        {{-- Menu Favorit --}}
        <div class="lg:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-orange-50">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading font-bold text-base text-canteen-dark flex items-center gap-2">
                    <i class="fa-solid fa-fire text-primary-500"></i>Menu Favorit
                </h2>
                <a href="{{ url('/menu') }}" class="text-xs text-primary-500 hover:text-primary-700 font-semibold transition-colors">Lihat Semua →</a>
            </div>
            <div class="space-y-3">
                @php
                $favorites = [
                    ['name' => 'Nasi Gudeg Komplit', 'price' => 'Rp 12.000', 'orders' => 48, 'emoji' => '🍛', 'color' => 'bg-yellow-50'],
                    ['name' => 'Mie Goreng Spesial', 'price' => 'Rp 10.000', 'orders' => 41, 'emoji' => '🍜', 'color' => 'bg-orange-50'],
                    ['name' => 'Es Teh Manis', 'price' => 'Rp 4.000', 'orders' => 38, 'emoji' => '🧋', 'color' => 'bg-amber-50'],
                ];
                @endphp
                @foreach($favorites as $i => $item)
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-8 h-8 flex-shrink-0 font-heading font-bold text-sm {{ $i === 0 ? 'text-yellow-500' : ($i === 1 ? 'text-gray-400' : 'text-orange-400') }} flex items-center justify-center">
                        #{{ $i + 1 }}
                    </div>
                    <div class="w-10 h-10 {{ $item['color'] }} rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                        {{ $item['emoji'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-gray-800 truncate">{{ $item['name'] }}</p>
                        <p class="text-xs text-gray-400">{{ $item['orders'] }}x dipesan</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-heading font-bold text-sm text-primary-500">{{ $item['price'] }}</p>
                        <a href="{{ url('/order') }}" class="text-xs text-primary-400 hover:text-primary-600 font-medium opacity-0 group-hover:opacity-100 transition-opacity">Pesan →</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Pesanan Aktif --}}
    <div class="mt-8 bg-white rounded-2xl p-6 shadow-sm border border-orange-50">
        <h2 class="font-heading font-bold text-base text-canteen-dark mb-4 flex items-center gap-2">
            <i class="fa-solid fa-spinner text-primary-500"></i>Pesanan Aktif
        </h2>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 bg-yellow-50 rounded-xl border border-yellow-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center text-xl">🍛</div>
                <div>
                    <p class="font-semibold text-sm text-gray-800">Nasi Gudeg + Es Teh</p>
                    <p class="text-xs text-gray-500">Order #SC-001 · Hari ini 09:30</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                    <i class="fa-solid fa-fire-burner text-xs"></i>Sedang Dimasak
                </span>
                <a href="{{ url('/order') }}" class="text-xs text-primary-500 hover:text-primary-700 font-semibold">Detail →</a>
            </div>
        </div>
    </div>
</div>
@endsection
