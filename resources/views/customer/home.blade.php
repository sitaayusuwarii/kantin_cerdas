@extends('layouts.app')
@section('title', 'Dashboard — SmartCanteen')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

{{-- ===== HERO / GREETING ===== --}}
@php
    $hour = now()->format('H');
    if ($hour < 12) { $greeting = 'Selamat Pagi'; $icon = 'fa-sun'; }
    elseif ($hour < 15) { $greeting = 'Selamat Siang'; $icon = 'fa-cloud-sun'; }
    elseif ($hour < 18) { $greeting = 'Selamat Sore'; $icon = 'fa-cloud'; }
    else { $greeting = 'Selamat Malam'; $icon = 'fa-moon'; }
@endphp

<div class="relative overflow-hidden rounded-3xl mb-8 shadow-xl">
    <div class="absolute inset-0 bg-gradient-to-br from-primary-500 via-primary-600 to-orange-700"></div>
    <div class="absolute -top-10 -right-10 w-56 h-56 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-orange-300/10 rounded-full blur-3xl"></div>

    <div class="relative px-6 py-8 md:px-10 md:py-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
        <div>
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full text-orange-100 text-sm font-medium mb-5 border border-white/10">
                <i class="fa-solid {{ $icon }}"></i>
                {{ $greeting }} 👋
            </div>
            <h1 class="font-heading font-extrabold text-3xl md:text-4xl text-white leading-tight">
                {{ auth()->user()->full_name }}
            </h1>
            <p class="text-orange-100 mt-3 text-sm md:text-base max-w-xl">
                Selamat datang kembali di SmartCanteen.
                Pesan makanan favoritmu dengan cepat dan praktis.
            </p>
            <div class="flex flex-wrap gap-3 mt-6">
                @if(auth()->user()->student_id)
                <div class="bg-white/10 backdrop-blur-md border border-white/10 px-4 py-2 rounded-2xl text-white text-sm">
                    <i class="fa-solid fa-id-card mr-2 text-orange-200"></i>
                    {{ auth()->user()->student_id }}
                </div>
                @endif
                @if(auth()->user()->class)
                <div class="bg-white/10 backdrop-blur-md border border-white/10 px-4 py-2 rounded-2xl text-white text-sm">
                    <i class="fa-solid fa-graduation-cap mr-2 text-orange-200"></i>
                    {{ auth()->user()->class }}
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-xl border border-white/10 rounded-3xl p-5 shadow-lg min-w-[260px]">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-regular fa-calendar text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-orange-100 text-sm">Hari Ini</p>
                    <p class="text-white font-heading font-bold text-lg leading-tight">
                        {{ now()->translatedFormat('l') }}
                    </p>
                    <p class="text-orange-100 text-sm mt-1">
                        {{ now()->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-50 card-hover">
            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-bag-shopping text-primary-500"></i>
            </div>
            <p class="text-2xl font-heading font-bold text-canteen-dark">{{ $totalPesanan }}</p>
            <p class="text-gray-500 text-xs mt-0.5">Total Pesanan</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-50 card-hover">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-circle-check text-green-500"></i>
            </div>
            <p class="text-2xl font-heading font-bold text-canteen-dark">{{ $selesai }}</p>
            <p class="text-gray-500 text-xs mt-0.5">Selesai</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-50 card-hover">
            <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-clock text-yellow-500"></i>
            </div>
            <p class="text-2xl font-heading font-bold text-canteen-dark">{{ $proses }}</p>
            <p class="text-gray-500 text-xs mt-0.5">Proses</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-50 card-hover">
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-triangle-exclamation text-red-400"></i>
            </div>
            <p class="text-2xl font-heading font-bold text-canteen-dark">{{ $tagihan }}</p>
            <p class="text-gray-500 text-xs mt-0.5">Tagihan</p>
        </div>
    </div>

    {{-- Status Pembayaran --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-orange-50 mb-8">
        <h2 class="font-heading font-bold text-base text-canteen-dark mb-4 flex items-center gap-2">
            <i class="fa-solid fa-file-invoice-dollar text-primary-500"></i>
            Status Pembayaran Terkini
        </h2>
        @if($tagihanTerbaru)
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 bg-red-50 rounded-xl border border-red-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-receipt text-red-500"></i>
                </div>
                <div>
                    <p class="font-semibold text-sm text-gray-800">Order #{{ $tagihanTerbaru->order_number }}</p>
                    <p class="text-xs text-gray-500">{{ $tagihanTerbaru->created_at->translatedFormat('d F Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="font-heading font-bold text-lg text-red-600">
                        Rp {{ number_format($tagihanTerbaru->total_price, 0, ',', '.') }}
                    </p>
                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                        <i class="fa-solid fa-circle text-[8px]"></i>Belum Lunas
                    </span>
                </div>
                <a href="{{ url('/payment') }}"
                   class="btn-primary text-white text-xs font-semibold px-4 py-2.5 rounded-xl flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-upload text-xs"></i>Bayar
                </a>
            </div>
        </div>
        @else
        <div class="p-4 bg-green-50 rounded-xl border border-green-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-circle-check text-green-500"></i>
            </div>
            <div>
                <p class="font-semibold text-sm text-gray-800">Semua Lunas</p>
                <p class="text-xs text-gray-500">Tidak ada tagihan yang tertunda</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Quick Actions + Favorit --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-orange-50">
            <h2 class="font-heading font-bold text-base text-canteen-dark mb-4 flex items-center gap-2">
                <i class="fa-solid fa-bolt text-primary-500"></i>Aksi Cepat
            </h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('customer.menu') }}" class="flex flex-col items-center gap-2 p-4 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors group text-center">
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

        <div class="lg:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-orange-50">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading font-bold text-base text-canteen-dark flex items-center gap-2">
                    <i class="fa-solid fa-fire text-primary-500"></i>Menu Terpopuler
                </h2>
                <a href="{{ route('customer.menu') }}" class="text-xs text-primary-500 hover:text-primary-700 font-semibold transition-colors">Lihat Semua →</a>
            </div>
            @forelse($menuFavorit as $i => $menu)
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                <div class="w-8 h-8 flex-shrink-0 font-heading font-bold text-sm
                    {{ $i === 0 ? 'text-yellow-500' : ($i === 1 ? 'text-gray-400' : 'text-orange-400') }}
                    flex items-center justify-center">#{{ $i + 1 }}</div>
                <div class="w-10 h-10 bg-orange-50 rounded-xl overflow-hidden flex-shrink-0">
                    @if($menu->image)
                        <img src="{{ asset('storage/' . $menu->image) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-lg">🍽️</div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-gray-800 truncate">{{ $menu->name }}</p>
                    <p class="text-xs text-gray-400">{{ $menu->total_sold }}x dipesan</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-heading font-bold text-sm text-primary-500">
                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                    </p>
                    <a href="{{ route('customer.menu') }}"
                       class="text-xs text-primary-400 hover:text-primary-600 font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                        Pesan →
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400 text-sm">Belum ada menu tersedia</div>
            @endforelse
        </div>
    </div>

    {{-- Pesanan Aktif --}}
    <div class="mt-8 bg-white rounded-2xl p-6 shadow-sm border border-orange-50">
        <h2 class="font-heading font-bold text-base text-canteen-dark mb-4 flex items-center gap-2">
            <i class="fa-solid fa-spinner text-primary-500"></i>Pesanan Aktif
        </h2>
        @if($pesananAktif)
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 bg-yellow-50 rounded-xl border border-yellow-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center text-xl">🍽️</div>
                <div>
                    <p class="font-semibold text-sm text-gray-800">
                        {{ $pesananAktif->items->take(2)->map(fn($i) => $i->menu->name)->join(', ') }}
                        @if($pesananAktif->items->count() > 2)
                            <span class="text-gray-400">+{{ $pesananAktif->items->count() - 2 }} lainnya</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-500">
                        Order #{{ $pesananAktif->order_number }} · {{ $pesananAktif->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full
                    {{ $pesananAktif->status === 'diproses' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                    <i class="fa-solid {{ $pesananAktif->status === 'diproses' ? 'fa-fire-burner' : 'fa-clock' }} text-xs"></i>
                    {{ $pesananAktif->status === 'diproses' ? 'Sedang Dimasak' : 'Menunggu Konfirmasi' }}
                </span>
                <a href="{{ url('/history') }}" class="text-xs text-primary-500 hover:text-primary-700 font-semibold">Detail →</a>
            </div>
        </div>
        @else
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-center">
            <p class="text-gray-400 text-sm">Tidak ada pesanan aktif saat ini</p>
            <a href="{{ route('customer.menu') }}" class="text-xs text-primary-500 font-semibold mt-1 inline-block">
                Yuk pesan sekarang →
            </a>
        </div>
        @endif
    </div>

</div>
@endsection