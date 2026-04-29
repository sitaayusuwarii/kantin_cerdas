<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Pengelola') — SmartCanteen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Fraunces', 'serif'],
                        sans:    ['DM Sans', 'sans-serif'],
                    },
                    colors: {
                        forest: {
                            50:  '#f2f7f2',
                            100: '#e0ece0',
                            200: '#c2d9c3',
                            300: '#96bc98',
                            400: '#619863',
                            500: '#3d7a40',
                            600: '#2d6130',
                            700: '#254e28',
                            800: '#1e3f21',
                            900: '#17311a',
                            950: '#0d1f10',
                        },
                        cream: {
                            50:  '#fdfcf8',
                            100: '#faf7ef',
                            200: '#f4edd8',
                            300: '#ebdfc0',
                        },
                        amber: {
                            warm: '#d4813a',
                        }
                    },
                    keyframes: {
                        fadeSlideUp: {
                            '0%':   { opacity: '0', transform: 'translateY(14px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideInLeft: {
                            '0%':   { opacity: '0', transform: 'translateX(-20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                    },
                    animation: {
                        'fade-up':    'fadeSlideUp 0.45s ease both',
                        'slide-left': 'slideInLeft 0.3s ease both',
                    },
                }
            }
        }
    </script>

    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display, h1, h2, h3 { font-family: 'Fraunces', serif; }

        /* ── Sidebar ─────────────────────────────────────── */
        #sidebar {
            transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
            background: linear-gradient(175deg, #17311a 0%, #0d1f10 100%);
        }
        #sidebar-overlay { display: none; }
        #sidebar-overlay.visible { display: block; }

        /* Nav item */
        .nav-item {
            position: relative;
            transition: background 0.15s, color 0.15s;
        }
        .nav-item::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 0;
            background: #96bc98;
            border-radius: 0 3px 3px 0;
            transition: height 0.2s ease;
        }
        .nav-item.active::before { height: 65%; }
        .nav-item.active {
            background: rgba(150,188,152,0.12);
            color: #c2d9c3;
        }
        .nav-item:not(.active):hover {
            background: rgba(255,255,255,0.06);
        }
        .nav-icon-wrap {
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px;
            transition: background 0.15s;
        }
        .nav-item.active .nav-icon-wrap {
            background: rgba(97,152,99,0.25);
        }

        /* ── Cards & UI ───────────────────────────────────── */
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.09); }

        .btn-primary {
            background: linear-gradient(135deg, #3d7a40, #2d6130);
            transition: filter 0.18s, transform 0.18s, box-shadow 0.18s;
        }
        .btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(45,97,48,0.35);
        }
        .btn-danger {
            background: linear-gradient(135deg, #e05252, #c0392b);
            transition: filter 0.18s, transform 0.18s;
        }
        .btn-danger:hover { filter: brightness(1.08); transform: translateY(-1px); }

        /* ── Modal ────────────────────────────────────────── */
        #menu-modal { display: none; }
        #menu-modal.open { display: flex; }

        /* ── Misc ─────────────────────────────────────────── */
        .badge-new  { animation: pulse-badge 2s infinite; }
        @keyframes pulse-badge {
            0%, 100% { box-shadow: 0 0 0 0 rgba(61,122,64,0.5); }
            50%       { box-shadow: 0 0 0 6px rgba(61,122,64,0); }
        }

        .bar-anim { transition: width 1s cubic-bezier(0.4,0,0.2,1); }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
    </style>
    @stack('styles')
</head>

<body class="h-full bg-cream-100 text-forest-950 antialiased">

{{-- ── Overlay (mobile) ───────────────────────────────── --}}
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/55 backdrop-blur-[2px] z-30 lg:hidden"
     onclick="closeSidebar()"></div>

{{-- ═══════════════════════════════════════════════════════
     SIDEBAR
═══════════════════════════════════════════════════════ --}}
<aside id="sidebar"
       class="fixed inset-y-0 left-0 w-64 z-40 flex flex-col
              -translate-x-full lg:translate-x-0">

    {{-- Logo --}}
    <div class="h-16 flex items-center gap-3 px-5 border-b border-white/8 flex-shrink-0">
        <div class="w-8 h-8 rounded-lg btn-primary flex items-center justify-center shadow-lg flex-shrink-0">
            <i class="fa-solid fa-bowl-food text-white text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="font-display font-semibold text-white text-sm leading-none truncate">
                Smart<span class="text-forest-300">Canteen</span>
            </p>
            <p class="text-forest-400 text-[10px] mt-0.5 truncate">Panel Pengelola</p>
        </div>
        <button onclick="closeSidebar()" class="ml-auto lg:hidden text-forest-400 hover:text-white transition-colors flex-shrink-0">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto scrollbar-thin py-5 px-3 space-y-0.5">
        <p class="text-[9px] font-semibold tracking-[0.15em] text-forest-600 uppercase px-3 mb-3">
            Navigasi
        </p>

        @php
        $navLinks = [
            ['url' => '/pengelola/dashboard',  'icon' => 'fa-gauge',           'label' => 'Dashboard',          'match' => 'pengelola/dashboard'],
            ['url' => '/pengelola/menu',        'icon' => 'fa-utensils',        'label' => 'Kelola Menu',         'match' => 'pengelola/menu'],
            ['url' => '/pengelola/orders',      'icon' => 'fa-bell',            'label' => 'Pesanan Masuk',       'match' => 'pengelola/orders',   'badge' => 3],
            ['url' => '/pengelola/delivery',    'icon' => 'fa-truck-fast',      'label' => 'Proses Pengiriman',   'match' => 'pengelola/delivery'],
            ['url' => '/pengelola/report',      'icon' => 'fa-chart-bar',       'label' => 'Laporan Favorit',     'match' => 'pengelola/report'],
        ];
        @endphp

        @foreach($navLinks as $link)
            @php $active = request()->is($link['match']); @endphp
            <a href="{{ url($link['url']) }}"
               class="nav-item {{ $active ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl group"
               onclick="if(window.innerWidth < 1024) closeSidebar()">
                <div class="nav-icon-wrap flex-shrink-0">
                    <i class="fa-solid {{ $link['icon'] }} text-xs
                       {{ $active ? 'text-forest-300' : 'text-forest-500 group-hover:text-forest-300' }}
                       transition-colors"></i>
                </div>
                <span class="text-sm font-medium flex-1 truncate
                    {{ $active ? 'text-cream-200' : 'text-forest-400 group-hover:text-cream-200' }}
                    transition-colors">
                    {{ $link['label'] }}
                </span>
                @if(!empty($link['badge']))
                    <span class="badge-new bg-forest-500 text-white text-[10px] font-bold
                                 w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0">
                        {{ $link['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>

    {{-- User --}}
    <div class="px-3 pb-4 flex-shrink-0 border-t border-white/8 pt-3">
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/6 transition-colors cursor-pointer">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-forest-400 to-forest-600
                        flex items-center justify-center text-white font-display font-bold text-sm flex-shrink-0">
                R
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-cream-200 text-xs font-semibold truncate">Bu Ratna</p>
                <p class="text-forest-500 text-[10px] truncate">Pengelola Kantin</p>
            </div>
            <i class="fa-solid fa-ellipsis-vertical text-forest-600 text-xs flex-shrink-0"></i>
        </div>
    </div>
</aside>

{{-- ═══════════════════════════════════════════════════════
     MAIN WRAPPER
═══════════════════════════════════════════════════════ --}}
<div class="lg:pl-64 min-h-screen flex flex-col">

    {{-- ── TOPBAR ──────────────────────────────────────── --}}
    <header class="sticky top-0 z-20 h-16 bg-cream-50/95 backdrop-blur-md
                   border-b border-cream-300 flex items-center px-4 sm:px-6 gap-4 shadow-sm">

        {{-- Hamburger --}}
        <button onclick="openSidebar()"
                class="lg:hidden w-9 h-9 rounded-xl bg-cream-200 hover:bg-cream-300
                       flex items-center justify-center transition-colors flex-shrink-0">
            <i class="fa-solid fa-bars text-forest-700 text-sm"></i>
        </button>

        {{-- Title --}}
        <div class="flex-1 min-w-0">
            <h1 class="font-display font-semibold text-forest-900 text-base sm:text-lg leading-none truncate">
                @yield('page-title', 'Dashboard')
            </h1>
            <p class="text-forest-500 text-xs mt-0.5 hidden sm:block truncate">
                @yield('page-subtitle', 'Selamat datang kembali, Bu Ratna!')
            </p>
        </div>

        {{-- Right --}}
        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
            {{-- Date chip --}}
            <div class="hidden md:flex items-center gap-2 bg-cream-200 rounded-xl px-3 py-1.5">
                <i class="fa-regular fa-calendar text-forest-500 text-xs"></i>
                <span class="text-forest-700 text-xs font-medium">
                    {{ now()->locale('id')->isoFormat('dddd, D MMM Y') }}
                </span>
            </div>

            {{-- Notif --}}
            <button class="relative w-9 h-9 rounded-xl bg-cream-200 hover:bg-cream-300
                           flex items-center justify-center transition-colors">
                <i class="fa-solid fa-bell text-forest-600 text-sm"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-400 rounded-full"></span>
            </button>

            {{-- Profile --}}
            <div class="flex items-center gap-2 cursor-pointer group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-forest-400 to-forest-700
                            flex items-center justify-center text-white font-display font-bold text-sm shadow">
                    R
                </div>
                <div class="hidden sm:block">
                    <p class="text-forest-900 text-xs font-semibold leading-none">Bu Ratna</p>
                    <p class="text-forest-500 text-[10px] mt-0.5">Pengelola</p>
                </div>
                <i class="fa-solid fa-chevron-down text-forest-400 text-xs hidden sm:block group-hover:text-forest-600 transition-colors"></i>
            </div>
        </div>
    </header>

    {{-- ── CONTENT ─────────────────────────────────────── --}}
    <main class="flex-1 p-4 sm:p-6 animate-fade-up">
        @yield('content')
    </main>

    {{-- ── FOOTER ──────────────────────────────────────── --}}
    <footer class="bg-cream-50 border-t border-cream-300 px-6 py-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-forest-500 text-xs font-display italic">
                © {{ date('Y') }} SmartCanteen · Panel Pengelola Kantin
            </p>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-forest-400 inline-block"></span>
                <p class="text-forest-500 text-xs font-medium">Sistem Aktif</p>
            </div>
        </div>
    </footer>
</div>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.remove('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.add('visible');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.add('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.remove('visible');
        document.body.style.overflow = '';
    }
</script>
@stack('scripts')
</body>
</html>
