<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartCanteen') — Kantin Sekolah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        heading: ['"Plus Jakarta Sans"', 'sans-serif'],
                        body: ['"DM Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        },
                        canteen: {
                            dark: '#1a1207',
                            mid: '#2d1f0e',
                            cream: '#fdf8f0',
                            warm: '#f5efe6',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'slide-down': 'slideDown 0.3s ease-out',
                        'fade-scale': 'fadeScale 0.15s ease-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0', transform: 'translateY(8px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        slideDown: { '0%': { opacity: '0', transform: 'translateY(-10px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        fadeScale: { '0%': { opacity: '0', transform: 'scale(0.95) translateY(-4px)' }, '100%': { opacity: '1', transform: 'scale(1) translateY(0)' } },
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4, h5, .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: #fdf8f0; }
        .nav-link { position: relative; }
        .nav-link::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 0; height: 2px; background: #f97316; transition: width 0.3s ease; border-radius: 2px; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .nav-link.active { color: #ea580c; }
        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); transition: all 0.2s ease; }
        .btn-primary:hover { background: linear-gradient(135deg, #ea580c, #c2410c); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(234,88,12,0.35); }
        .glass { background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        #mobile-menu { display: none; }
        #mobile-menu.open { display: block; }
        .badge-favorite { background: linear-gradient(135deg, #fbbf24, #f59e0b); }

        /* Dropdown */
        #profile-dropdown {
            display: none;
            animation: fadeScale 0.15s ease-out;
            transform-origin: top right;
        }
        #profile-dropdown.open { display: block; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col text-canteen-dark">

    {{-- ===== NAVBAR ===== --}}
    <nav class="glass sticky top-0 z-50 border-b border-orange-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl btn-primary flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-bowl-food text-white text-sm"></i>
                    </div>
                    <div>
                        <span class="font-heading font-800 text-lg text-canteen-dark leading-none block">Smart<span class="text-primary-500">Canteen</span></span>
                        <span class="text-[10px] text-gray-400 leading-none font-medium tracking-wide">Kantin Digital</span>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('customer.home') }}" class="nav-link font-medium text-sm text-gray-700 hover:text-primary-600 transition-colors {{ request()->routeIs('customer.home') ? 'active' : '' }}">
                        <i class="fa-solid fa-house mr-1.5 text-xs"></i>Home
                    </a>
                    <a href="{{ route('customer.menu') }}" class="nav-link font-medium text-sm text-gray-700 hover:text-primary-600 transition-colors {{ request()->routeIs('customer.menu') ? 'active' : '' }}">
                        <i class="fa-solid fa-utensils mr-1.5 text-xs"></i>Menu
                    </a>
                    <a href="{{ url('/customer/history') }}" class="nav-link font-medium text-sm text-gray-700 hover:text-primary-600 transition-colors {{ request()->is('customer/history*') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left mr-1.5 text-xs"></i>Riwayat
                    </a>
                    <a href="{{ url('/customer/invoice') }}" class="nav-link font-medium text-sm text-gray-700 hover:text-primary-600 transition-colors {{ request()->is('customer/invoice*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar mr-1.5 text-xs"></i>Tagihan
                    </a>
                </div>

                {{-- Right Actions --}}
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('customer.payment.index') }}" class="btn-primary text-white text-sm font-semibold px-4 py-2 rounded-xl flex items-center gap-2 shadow-md">
                        <i class="fa-solid fa-upload text-xs"></i>
                        Upload Bukti Bayar
                    </a>

                    {{-- Profile Dropdown --}}
                    <div class="relative" id="profile-dropdown-wrap">

                        {{-- Avatar Button --}}
                        <button onclick="toggleProfileDropdown()"
                            id="profile-btn"
                            class="w-9 h-9 rounded-full overflow-hidden shadow
                                cursor-pointer hover:scale-105 transition-transform select-none">
                        @auth
                            @if(auth()->user()->photo)
                                <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                    alt="Profile"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-400 to-primary-600
                                            flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary-400 to-primary-600
                                        flex items-center justify-center text-white font-bold text-sm">
                                A
                            </div>
                        @endauth
                        </button>

                        {{-- Dropdown Panel --}}
                        <div id="profile-dropdown"
                             class="absolute right-0 top-12 w-56 bg-white rounded-2xl shadow-xl
                                    border border-gray-100 overflow-hidden z-50">

                            {{-- User Info --}}
                            <div class="px-4 py-3.5 bg-orange-50/70 border-b border-orange-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0">
                                    @auth
                                        @if(auth()->user()->photo)
                                            <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                                alt="Profile"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-primary-400 to-primary-600
                                                        flex items-center justify-center text-white font-bold text-sm">
                                                {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                    @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-heading font-bold text-sm text-canteen-dark truncate">
                                            @auth {{ auth()->user()->full_name ?? auth()->user()->username ?? 'User' }} @else Guest @endauth
                                        </p>
                                        <p class="text-xs text-gray-400 truncate">
                                            @auth {{ auth()->user()->username ?? '' }} @endauth
                                        </p>
                                    </div>
                                </div>
                                @auth
                                    @if(auth()->user()->class)
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        <span class="text-[10px] bg-gray-100 text-gray-500 font-medium px-2 py-0.5 rounded-full">
                                            Kelas {{ auth()->user()->class }}
                                        </span>
                                    </div>
                                    @endif
                                @endauth
                            </div>

                            {{-- Menu Items --}}
                            <div class="py-1.5">
                                {{-- (Anggap route profile.edit sudah ada atau akan dibuat nanti) --}}
                                <a href="{{ url('/profile/edit') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700
                                          hover:bg-orange-50 hover:text-primary-600 transition-colors group">
                                    <div class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-primary-100
                                                flex items-center justify-center transition-colors flex-shrink-0">
                                        <i class="fa-solid fa-user-pen text-gray-500 group-hover:text-primary-500 text-xs"></i>
                                    </div>
                                    <span class="font-medium">Edit Profil</span>
                                </a>
                            </div>

                            {{-- Logout --}}
                            <div class="border-t border-gray-100 py-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm
                                                   text-red-500 hover:bg-red-50 transition-colors group text-left">
                                        <div class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100
                                                    flex items-center justify-center transition-colors flex-shrink-0">
                                            <i class="fa-solid fa-right-from-bracket text-gray-400 group-hover:text-red-400 text-xs"></i>
                                        </div>
                                        <span class="font-semibold">Logout</span>
                                    </button>
                                </form>
                            </div>

                        </div>
                        {{-- End Dropdown Panel --}}

                    </div>
                    {{-- End Profile Dropdown --}}
                </div>

                {{-- Hamburger --}}
                <button id="hamburger-btn" class="md:hidden w-10 h-10 rounded-xl bg-orange-50 hover:bg-orange-100 flex items-center justify-center transition-colors" aria-label="Toggle menu">
                    <i class="fa-solid fa-bars text-primary-500 text-base" id="hamburger-icon"></i>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="md:hidden border-t border-orange-100 animate-slide-down">
            <div class="px-4 py-4 space-y-1 bg-white/95">
                <a href="{{ route('customer.home') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary-600 transition-all {{ request()->routeIs('customer.home') ? 'bg-orange-50 text-primary-600' : '' }}">
                    <i class="fa-solid fa-house w-4 text-center text-primary-400"></i>Home
                </a>
                <a href="{{ route('customer.menu') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary-600 transition-all {{ request()->routeIs('customer.menu') ? 'bg-orange-50 text-primary-600' : '' }}">
                    <i class="fa-solid fa-utensils w-4 text-center text-primary-400"></i>Menu
                </a>
                <a href="{{ url('/customer/history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary-600 transition-all {{ request()->is('customer/history*') ? 'bg-orange-50 text-primary-600' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left w-4 text-center text-primary-400"></i>Riwayat
                </a>
                <a href="{{ url('/customer/invoice') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary-600 transition-all {{ request()->is('customer/invoice*') ? 'bg-orange-50 text-primary-600' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-4 text-center text-primary-400"></i>Tagihan
                </a>

                {{-- Gabungan Action Payment dan Mobile Profile temanmu --}}
                <div class="pt-4 pb-1 border-t border-orange-100 space-y-3 mt-2">
                    <a href="{{ route('customer.payment.index') }}" class="btn-primary text-white text-sm font-semibold px-4 py-3 rounded-xl flex items-center justify-center gap-2 w-full shadow-md">
                        <i class="fa-solid fa-upload text-xs"></i>Upload Bukti Bayar
                    </a>

                    {{-- Mobile Profile Row --}}
                    <div class="flex items-center gap-3 px-4 py-3 bg-orange-50 rounded-xl">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600
                                    flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            @auth {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username ?? 'U', 0, 1)) }} @else A @endauth
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-heading font-bold text-sm text-canteen-dark truncate">
                                @auth {{ auth()->user()->full_name ?? auth()->user()->username ?? 'User' }} @else Guest @endauth
                            </p>
                            <p class="text-xs text-gray-400 truncate">
                                @auth {{ auth()->user()->class ?? auth()->user()->username ?? '' }} @endauth
                            </p>
                        </div>
                    </div>

                    <a href="{{ url('/profile/edit') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium
                              text-gray-700 hover:bg-orange-50 hover:text-primary-600 transition-all">
                        <i class="fa-solid fa-user-pen w-4 text-center text-primary-400"></i>Edit Profil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium
                                       text-red-500 hover:bg-red-50 transition-all">
                            <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-1 animate-fade-in">
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="bg-canteen-dark text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-9 h-9 rounded-xl btn-primary flex items-center justify-center">
                            <i class="fa-solid fa-bowl-food text-white text-sm"></i>
                        </div>
                        <div>
                            <span class="font-heading font-bold text-lg">Smart<span class="text-primary-400">Canteen</span></span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-xs">
                        Platform kantin digital modern untuk siswa dan orang tua. Pesan makanan, bayar tagihan, semua dalam satu genggaman.
                    </p>
                </div>

                <div>
                    <h4 class="font-heading font-semibold text-sm uppercase tracking-widest text-gray-400 mb-4">Navigasi</h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('customer.home') }}" class="text-sm text-gray-300 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-primary-500"></i>Home</a></li>
                        <li><a href="{{ route('customer.menu') }}" class="text-sm text-gray-300 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-primary-500"></i>Menu Kantin</a></li>
                        <li><a href="{{ url('/customer/history') }}" class="text-sm text-gray-300 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-primary-500"></i>Riwayat Pesanan</a></li>
                        <li><a href="{{ url('/customer/invoice') }}" class="text-sm text-gray-300 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-primary-500"></i>Tagihan</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-heading font-semibold text-sm uppercase tracking-widest text-gray-400 mb-4">Bantuan</h4>
                    <ul class="space-y-2.5 mb-5">
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <i class="fa-brands fa-whatsapp text-green-400 w-4"></i>
                            +62 812-3456-7890
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <i class="fa-solid fa-envelope text-primary-400 w-4"></i>
                            kantin@sekolah.sch.id
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-gray-500 text-xs">© {{ date('Y') }} SmartCanteen. Hak cipta dilindungi.</p>
                <p class="text-gray-500 text-xs flex items-center gap-1.5">
                    Dibuat dengan <i class="fa-solid fa-heart text-primary-400 text-xs"></i> untuk pendidikan
                </p>
            </div>
        </div>
    </footer>

    <script>
        // ── Hamburger ─────────────────────────────────────
        const btn  = document.getElementById('hamburger-btn');
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('hamburger-icon');

        btn.addEventListener('click', () => {
            menu.classList.toggle('open');
            icon.className = menu.classList.contains('open')
                ? 'fa-solid fa-xmark text-primary-500 text-base'
                : 'fa-solid fa-bars text-primary-500 text-base';
        });

        // ── Profile Dropdown ──────────────────────────────
        function toggleProfileDropdown() {
            document.getElementById('profile-dropdown').classList.toggle('open');
        }

        // Tutup kalau klik di luar
        document.addEventListener('click', function (e) {
            const wrap = document.getElementById('profile-dropdown-wrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('profile-dropdown').classList.remove('open');
            }
        });

        // Tutup kalau tekan Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.getElementById('profile-dropdown').classList.remove('open');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>