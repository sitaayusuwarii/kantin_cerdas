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

            {{-- LOGO ONLY --}}
            <a href="{{ url('/') }}" class="group">

                <img src="{{ asset('images/canteen.png') }}"
                alt="Logo"
                class="w-14 h-14 object-contain group-hover:scale-105 transition-transform"
            </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ url('/home') }}" class="nav-link font-medium text-sm text-gray-700 hover:text-primary-600 transition-colors {{ request()->is('home') ? 'active' : '' }}">
                        <i class="fa-solid fa-house mr-1.5 text-xs"></i>Home
                    </a>
                    <a href="{{ url('/menu') }}" class="nav-link font-medium text-sm text-gray-700 hover:text-primary-600 transition-colors {{ request()->is('menu*') ? 'active' : '' }}">
                        <i class="fa-solid fa-utensils mr-1.5 text-xs"></i>Menu
                    </a>
                    <a href="{{ url('/history') }}" class="nav-link font-medium text-sm text-gray-700 hover:text-primary-600 transition-colors {{ request()->is('history*') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left mr-1.5 text-xs"></i>Riwayat
                    </a>
                  
                </div>

                {{-- Right Actions --}}
                <div class="hidden md:flex items-center gap-3">
                    <!-- <a href="{{ url('/payment') }}" class="btn-primary text-white text-sm font-semibold px-4 py-2 rounded-xl flex items-center gap-2 shadow-md">
                        <i class="fa-solid fa-upload text-xs"></i>
                        Upload Bukti Bayar
                    </a> -->

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
                                    {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
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
                                                {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                    <div class="min-w-0">
                                        <p class="font-heading font-bold text-sm text-canteen-dark truncate">
                                            @auth {{ auth()->user()->name }} @else Guest @endauth
                                        </p>
                                        <p class="text-xs text-gray-400 truncate">
                                            @auth {{ auth()->user()->email }} @endauth
                                        </p>
                                    </div>
                                </div>
                                @auth
                                    @if(auth()->user()->student_id || auth()->user()->class)
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        @if(auth()->user()->student_id)
                                        <span class="text-[10px] bg-primary-100 text-primary-600 font-semibold px-2 py-0.5 rounded-full">
                                            {{ auth()->user()->student_id }}
                                        </span>
                                        @endif
                                        @if(auth()->user()->class)
                                        <span class="text-[10px] bg-gray-100 text-gray-500 font-medium px-2 py-0.5 rounded-full">
                                            {{ auth()->user()->class }}
                                        </span>
                                        @endif
                                    </div>
                                    @endif
                                @endauth
                            </div>

                            {{-- Menu Items --}}
                            <div class="py-1.5">
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700
                                          hover:bg-orange-50 hover:text-primary-600 transition-colors group">
                                    <div class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-primary-100
                                                flex items-center justify-center transition-colors flex-shrink-0">
                                        <i class="fa-solid fa-user-pen text-gray-500 group-hover:text-primary-500 text-xs"></i>
                                    </div>
                                    <span class="font-medium">Edit Profil</span>
                                </a>

                                <!-- <a href="{{ url('/history') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700
                                          hover:bg-orange-50 hover:text-primary-600 transition-colors group">
                                    <div class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-primary-100
                                                flex items-center justify-center transition-colors flex-shrink-0">
                                        <i class="fa-solid fa-clock-rotate-left text-gray-500 group-hover:text-primary-500 text-xs"></i>
                                    </div>
                                    <span class="font-medium">Riwayat Pesanan</span>
                                </a> -->
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
                <a href="{{ url('/home') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary-600 transition-all {{ request()->is('home') ? 'bg-orange-50 text-primary-600' : '' }}">
                    <i class="fa-solid fa-house w-4 text-center text-primary-400"></i>Home
                </a>
                <a href="{{ url('/menu') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary-600 transition-all {{ request()->is('menu*') ? 'bg-orange-50 text-primary-600' : '' }}">
                    <i class="fa-solid fa-utensils w-4 text-center text-primary-400"></i>Menu
                </a>
                <a href="{{ url('/history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary-600 transition-all {{ request()->is('history*') ? 'bg-orange-50 text-primary-600' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left w-4 text-center text-primary-400"></i>Riwayat
                </a>

                <div class="pt-3 pb-1 border-t border-orange-100 space-y-2">
                    <a href="{{ url('/payment') }}" class="btn-primary text-white text-sm font-semibold px-4 py-3 rounded-xl flex items-center justify-center gap-2 w-full shadow-md">
                        <i class="fa-solid fa-upload text-xs"></i>Upload Bukti Bayar
                    </a>

                    {{-- Mobile Profile Row --}}
                    <div class="flex items-center gap-3 px-4 py-3 bg-orange-50 rounded-xl">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600
                                    flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            @auth {{ strtoupper(substr(auth()->user()->name, 0, 1)) }} @else A @endauth
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-heading font-bold text-sm text-canteen-dark truncate">
                                @auth {{ auth()->user()->name }} @else Guest @endauth
                            </p>
                            <p class="text-xs text-gray-400 truncate">
                                @auth {{ auth()->user()->class ?? auth()->user()->email }} @endauth
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
<footer class="relative mt-16 overflow-hidden border-t border-orange-200">

    {{-- Background --}}
    <div class="absolute inset-0 bg-gradient-to-br from-[#c2410c] via-[#ea580c] to-[#f97316]"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">

        <div class="py-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            {{-- Brand --}}
            <div class="lg:col-span-2">

                <div class="flex items-center mb-5">

                    <div class="w-14 h-14 rounded-2xl overflow-hidden bg-white/90 p-2 shadow-lg">
                        <img src="{{ asset('images/canteen.png') }}"
                             alt="Logo"
                             class="w-full h-full object-contain">
                    </div>

                </div>

                <p class="text-orange-50 text-sm leading-relaxed max-w-md">
                    Platform kantin digital modern untuk membantu siswa memesan makanan,
                    melihat riwayat transaksi, dan melakukan pembayaran dengan cepat dan praktis.
                </p>

                {{-- Social --}}
                <div class="flex items-center gap-3 mt-6">

                    <a href="https://instagram.com/username"
                       target="_blank"
                       class="w-10 h-10 rounded-xl bg-white/80 shadow-sm flex items-center justify-center text-pink-500 hover:bg-pink-500 hover:text-white transition-all">

                        <i class="fa-brands fa-instagram"></i>

                    </a>

                    <a href="https://facebook.com/username"
                       target="_blank"
                       class="w-10 h-10 rounded-xl bg-white/80 shadow-sm flex items-center justify-center text-blue-500 hover:bg-blue-500 hover:text-white transition-all">

                        <i class="fa-brands fa-facebook-f"></i>

                    </a>

                    <a href="https://wa.me/6281234567890"
                       target="_blank"
                       class="w-10 h-10 rounded-xl bg-white/80 shadow-sm flex items-center justify-center text-green-500 hover:bg-green-500 hover:text-white transition-all">

                        <i class="fa-brands fa-whatsapp"></i>

                    </a>

                </div>

            </div>

            {{-- Navigation --}}
            <div>

                <h3 class="font-heading font-bold text-white mb-5">
                    Navigasi
                </h3>

                <ul class="space-y-3">

                    <li>
                        <a href="{{ url('/home') }}"
                           class="text-sm text-orange-100 hover:text-white transition">
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/menu') }}"
                           class="text-sm text-orange-100 hover:text-white transition">
                            Menu Kantin
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/history') }}"
                           class="text-sm text-orange-100 hover:text-white transition">
                            Riwayat
                        </a>
                    </li>

                </ul>

            </div>

            {{-- Contact --}}
            <div>

                <h3 class="font-heading font-bold text-white mb-5">
                    Bantuan
                </h3>

                <div class="space-y-4">

                    {{-- WhatsApp --}}
                    <a href="https://wa.me/6281234567890"
                    target="_blank"
                    class="flex items-start gap-3 group transition-all">

                        <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0 group-hover:bg-green-500 transition-all">
                            <i class="fa-brands fa-whatsapp text-green-300 group-hover:text-white"></i>
                        </div>

                        <div>
                            <p class="text-xs text-orange-100/80 mb-1">
                                WhatsApp
                            </p>

                            <p class="text-sm text-white group-hover:text-green-100 transition">
                                +62 812-3456-7890
                            </p>
                        </div>

                    </a>

                    {{-- Email --}}
                    <a href="mailto:kantin@sekolah.sch.id"
                    class="flex items-start gap-3 group transition-all">

                        <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-500 transition-all">
                            <i class="fa-solid fa-envelope text-blue-200 group-hover:text-white"></i>
                        </div>

                        <div>
                            <p class="text-xs text-orange-100/80 mb-1">
                                Email
                            </p>

                            <p class="text-sm text-white group-hover:text-blue-100 transition">
                                kantin@sekolah.sch.id
                            </p>
                        </div>

                    </a>

                    {{-- Lokasi --}}
                    <a href="https://share.google/qr7crJrj6nqOjRS3q"
                    target="_blank"
                    class="flex items-start gap-3 group transition-all">

                        <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0 group-hover:bg-red-500 transition-all">
                            <i class="fa-solid fa-location-dot text-red-200 group-hover:text-white"></i>
                        </div>

                        <div>
                            <p class="text-xs text-orange-100/80 mb-1">
                                Lokasi Sekolah
                            </p>

                            <p class="text-sm text-white group-hover:text-red-100 transition">
                                Buka Google Maps
                            </p>
                        </div>

                    </a>

                </div>

            </div>

        </div>

            {{-- Bottom --}}
            <div class="mt-8 pt-5 pb-24 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-3">

                <p class="text-sm text-white/95 font-medium tracking-wide">
                    © {{ date('Y') }} SmartCanteen. All rights reserved.
                </p>

                <div class="flex items-center gap-2 text-sm text-white/95 font-medium">

                    <span>Dibuat dengan</span>

                    <i class="fa-solid fa-heart text-red-400 animate-pulse"></i>

                    <span>untuk digitalisasi kantin sekolah</span>

                </div>

            </div>
    </div>

</footer>

<script>
    // Hamburger
    const btn = document.getElementById('hamburger-btn');
    const menu = document.getElementById('mobile-menu');
    const icon = document.getElementById('hamburger-icon');

    btn.addEventListener('click', () => {
        menu.classList.toggle('open');

        icon.className = menu.classList.contains('open')
            ? 'fa-solid fa-xmark text-primary-500'
            : 'fa-solid fa-bars text-primary-500';
    });

    // Profile dropdown
    function toggleProfileDropdown() {
        document.getElementById('profile-dropdown').classList.toggle('open');
    }

    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('profile-dropdown-wrap');

        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('profile-dropdown').classList.remove('open');
        }
    });
</script>

@stack('scripts')

</body>
</html>