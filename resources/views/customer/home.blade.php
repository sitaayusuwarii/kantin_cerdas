@extends('layouts.app')
@section('title', 'Dashboard - SmartCanteen')

@section('content')
@php
    $hour = (int) now()->format('H');
    if ($hour < 12) {
        $greeting = 'Selamat Pagi';
        $greetingIcon = 'fa-sun';
    } elseif ($hour < 15) {
        $greeting = 'Selamat Siang';
        $greetingIcon = 'fa-cloud-sun';
    } elseif ($hour < 18) {
        $greeting = 'Selamat Sore';
        $greetingIcon = 'fa-cloud';
    } else {
        $greeting = 'Selamat Malam';
        $greetingIcon = 'fa-moon';
    }

    $user = auth()->user();

    $stats = [
        [
            'label' => 'Total Pesanan',
            'value' => $totalPesanan,
            'icon' => 'fa-bag-shopping',
            'color' => 'bg-orange-50 text-orange-600',
        ],
        [
            'label' => 'Selesai',
            'value' => $selesai,
            'icon' => 'fa-circle-check',
            'color' => 'bg-emerald-50 text-emerald-600',
        ],
        [
            'label' => 'Diproses',
            'value' => $proses,
            'icon' => 'fa-clock',
            'color' => 'bg-amber-50 text-amber-600',
        ],
        [
            'label' => 'Tagihan',
            'value' => $tagihan,
            'icon' => 'fa-receipt',
            'color' => 'bg-rose-50 text-rose-600',
        ],
    ];

    $quickActions = [
        [
            'label' => 'Pesan Menu',
            'caption' => 'Pilih makanan kantin',
            'url' => route('customer.menu'),
            'icon' => 'fa-utensils',
            'color' => 'from-orange-500 to-orange-600',
        ],
        [
            'label' => 'Tagihan',
            'caption' => 'Cek pembayaran',
            'url' => url('/invoice'),
            'icon' => 'fa-file-invoice-dollar',
            'color' => 'from-sky-500 to-blue-600',
        ],
        [
            'label' => 'Upload Bukti',
            'caption' => 'Kirim bukti bayar',
            'url' => url('/payment'),
            'icon' => 'fa-upload',
            'color' => 'from-emerald-500 to-green-600',
        ],
        [
            'label' => 'Riwayat',
            'caption' => 'Lihat pesananmu',
            'url' => url('/history'),
            'icon' => 'fa-clock-rotate-left',
            'color' => 'from-violet-500 to-purple-600',
        ],
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 shadow-xl shadow-orange-200/60">
        <div class="absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.24),transparent_38%)]"></div>
        <div class="absolute -left-20 -bottom-24 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute right-12 bottom-8 hidden lg:block text-white/10">
            <i class="fa-solid fa-bowl-food text-[9rem]"></i>
        </div>

        <div class="relative grid lg:grid-cols-[1fr_360px] gap-8 px-5 py-7 sm:px-8 lg:px-10 lg:py-9">
            <div class="flex flex-col justify-center">
                <div class="inline-flex w-fit items-center gap-2 rounded-full border border-white/20 bg-white/15 px-4 py-2 text-sm font-semibold text-white backdrop-blur">
                    <i class="fa-solid {{ $greetingIcon }} text-orange-100"></i>
                    {{ $greeting }}
                </div>

                <h1 class="mt-5 font-heading text-3xl font-extrabold leading-tight text-white md:text-4xl">
                    Hai, {{ $user->full_name ?? $user->name }}
                </h1>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-orange-50 md:text-base">
                    Pilih menu kantin, pantau pesanan, dan selesaikan pembayaran dari satu dashboard.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('customer.menu') }}"
                       class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-bold text-orange-600 shadow-lg shadow-orange-900/10 transition hover:-translate-y-0.5 hover:shadow-xl">
                        <i class="fa-solid fa-utensils"></i>
                        Pesan Sekarang
                    </a>
                    <a href="{{ url('/history') }}"
                       class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        Lihat Riwayat
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-white/20 bg-white/15 p-5 text-white backdrop-blur-md">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-orange-100">Hari ini</p>
                        <p class="mt-1 font-heading text-2xl font-bold">{{ now()->translatedFormat('l') }}</p>
                        <p class="mt-1 text-sm text-orange-100">{{ now()->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15">
                        <i class="fa-regular fa-calendar text-xl"></i>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-3 text-sm">
                    @if($user->student_id)
                        <div class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3">
                            <i class="fa-solid fa-id-card text-orange-100"></i>
                            <span>{{ $user->student_id }}</span>
                        </div>
                    @endif
                    @if($user->class)
                        <div class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3">
                            <i class="fa-solid fa-graduation-cap text-orange-100"></i>
                            <span>{{ $user->class }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
        @foreach($stats as $stat)
            <div class="rounded-3xl border border-orange-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="font-heading text-2xl font-extrabold text-gray-900">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-xs font-semibold text-gray-500">{{ $stat['label'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $stat['color'] }}">
                        <i class="fa-solid {{ $stat['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        {{-- Payment Status --}}
        <div class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Status Pembayaran</p>
                    <h2 class="mt-1 font-heading text-xl font-bold text-gray-950">Pembayaran Terkini</h2>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>

            @if($tagihanTerbaru)
                <div class="mt-5 rounded-3xl border border-rose-100 bg-rose-50 p-4 sm:p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-white text-rose-500 shadow-sm">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">Order #{{ $tagihanTerbaru->order_number }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">{{ $tagihanTerbaru->created_at->translatedFormat('d F Y, H:i') }}</p>
                            </div>
                        </div>

                        <div class="sm:text-right">
                            <p class="font-heading text-xl font-extrabold text-rose-600">
                                Rp {{ number_format($tagihanTerbaru->total_price, 0, ',', '.') }}
                            </p>
                            <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-bold text-rose-600">
                                <i class="fa-solid fa-circle text-[7px]"></i>
                                Belum Lunas
                            </span>
                        </div>
                    </div>

                    <a href="{{ url('/payment') }}"
                       class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-rose-200 transition hover:bg-rose-600 sm:w-auto">
                        <i class="fa-solid fa-upload"></i>
                        Upload Bukti Pembayaran
                    </a>
                </div>
            @else
                <div class="mt-5 rounded-3xl border border-emerald-100 bg-emerald-50 p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-500 shadow-sm">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Semua Lunas</p>
                            <p class="mt-1 text-sm text-gray-500">Tidak ada tagihan yang perlu dibayar saat ini.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Active Order --}}
        <div class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Pantauan Pesanan</p>
                    <h2 class="mt-1 font-heading text-xl font-bold text-gray-950">Pesanan Aktif</h2>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-bell-concierge"></i>
                </div>
            </div>

            @if($pesananAktif)
                <div class="mt-5 rounded-3xl border border-amber-100 bg-amber-50 p-5">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-white text-amber-500 shadow-sm">
                            <i class="fa-solid fa-bowl-food"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-gray-900">
                                Order #{{ $pesananAktif->order_number }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $pesananAktif->items->take(2)->map(fn($item) => $item->menu->name)->join(', ') }}
                                @if($pesananAktif->items->count() > 2)
                                    <span class="text-gray-400">+{{ $pesananAktif->items->count() - 2 }} lainnya</span>
                                @endif
                            </p>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-bold text-amber-700">
                                    <i class="fa-solid {{ $pesananAktif->status === 'diproses' ? 'fa-fire-burner' : 'fa-clock' }}"></i>
                                    {{ $pesananAktif->status === 'diproses' ? 'Sedang Dimasak' : 'Menunggu Konfirmasi' }}
                                </span>
                                <span class="text-xs font-medium text-gray-500">{{ $pesananAktif->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('/history') }}"
                       class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-4 py-3 text-sm font-bold text-amber-700 shadow-sm transition hover:bg-amber-100">
                        Lihat Detail
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            @else
                <div class="mt-5 rounded-3xl border border-gray-100 bg-gray-50 p-5 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-gray-400 shadow-sm">
                        <i class="fa-solid fa-clipboard-check text-xl"></i>
                    </div>
                    <p class="mt-3 font-bold text-gray-800">Belum ada pesanan aktif</p>
                    <p class="mt-1 text-sm text-gray-500">Pesanan yang sedang berjalan akan tampil di sini.</p>
                    <a href="{{ route('customer.menu') }}"
                       class="mt-4 inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-orange-200 transition hover:bg-orange-600">
                        Pesan Menu
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
        {{-- Quick Actions --}}
        <div class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Shortcut</p>
                    <h2 class="mt-1 font-heading text-xl font-bold text-gray-950">Aksi Cepat</h2>
                </div>
                <i class="fa-solid fa-bolt text-orange-500"></i>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach($quickActions as $action)
                    <a href="{{ $action['url'] }}"
                       class="group flex items-center gap-3 rounded-3xl border border-gray-100 bg-gray-50 p-4 transition hover:-translate-y-0.5 hover:border-orange-100 hover:bg-orange-50 hover:shadow-md">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $action['color'] }} text-white shadow-md transition group-hover:scale-105">
                            <i class="fa-solid {{ $action['icon'] }}"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900">{{ $action['label'] }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $action['caption'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Popular Menu --}}
        <div class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Rekomendasi</p>
                    <h2 class="mt-1 font-heading text-xl font-bold text-gray-950">Menu Terpopuler</h2>
                </div>
                <a href="{{ route('customer.menu') }}" class="text-sm font-bold text-orange-500 hover:text-orange-600">
                    Lihat Semua
                </a>
            </div>

            <div class="space-y-3">
                @forelse($menuFavorit as $i => $menu)
                    <a href="{{ route('customer.menu') }}"
                       class="group flex items-center gap-4 rounded-3xl border border-gray-100 bg-white p-3 transition hover:border-orange-100 hover:bg-orange-50/70">
                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-2xl bg-gray-50 font-heading text-sm font-extrabold text-orange-500">
                            #{{ $i + 1 }}
                        </div>

                        <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-2xl bg-orange-50">
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}"
                                     alt="{{ $menu->name }}"
                                     class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-orange-500">
                                    <i class="fa-solid fa-utensils"></i>
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-bold text-gray-900">{{ $menu->name }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $menu->total_sold }}x dipesan</p>
                        </div>

                        <div class="text-right">
                            <p class="whitespace-nowrap font-heading text-base font-extrabold text-orange-600">
                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </p>
                            <p class="mt-0.5 text-xs font-bold text-orange-400 opacity-0 transition group-hover:opacity-100">
                                Pesan
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-gray-400 shadow-sm">
                            <i class="fa-solid fa-bowl-food text-xl"></i>
                        </div>
                        <p class="mt-3 font-bold text-gray-700">Belum ada menu populer</p>
                        <p class="mt-1 text-sm text-gray-400">Data menu favorit akan muncul setelah ada transaksi.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
