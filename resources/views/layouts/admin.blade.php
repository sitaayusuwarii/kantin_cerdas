<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pengelola') — SmartCanteen Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        heading: ['Syne', 'sans-serif'],
                        body: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        sidebar: {
                            bg: '#0f1623',
                            hover: '#1a2235',
                            active: '#1e2d47',
                            border: '#1e2a3a',
                            text: '#8fa3be',
                            textActive: '#e2eaf5',
                        },
                        brand: {
                            50:  '#fef3e2',
                            100: '#fde6c0',
                            400: '#f9a73e',
                            500: '#f79009',
                            600: '#d97706',
                            700: '#b45309',
                        },
                        surface: '#f4f6fb',
                    },
                    animation: {
                        'fade-up': 'fadeUp 0.4s ease-out both',
                        'slide-right': 'slideRight 0.3s ease-out',
                    },
                    keyframes: {
                        fadeUp: { '0%': { opacity: '0', transform: 'translateY(12px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        slideRight: { '0%': { opacity: '0', transform: 'translateX(-16px)' }, '100%': { opacity: '1', transform: 'translateX(0)' } },
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Outfit', sans-serif; }
        h1,h2,h3,h4,h5,.font-heading { font-family: 'Syne', sans-serif; }
        body { background-color: #f4f6fb; }

        /* Sidebar */
        #sidebar { transition: transform 0.3s ease, width 0.3s ease; }
        #sidebar-overlay { display:none; }
        #sidebar-overlay.active { display:block; }

        /* Nav items */
        .nav-item { position: relative; transition: all 0.15s ease; }
        .nav-item.active { background: linear-gradient(135deg, #1e2d47, #162034); }
        .nav-item.active::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 3px; height: 60%; background: linear-gradient(180deg, #f9a73e, #f79009); border-radius: 0 3px 3px 0; }
        .nav-item:not(.active):hover { background: #1a2235; }

        /* Stat cards */
        .stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .stat-card:hover { transform: translateY(-3px); }

        /* Buttons */
        .btn-brand { background: linear-gradient(135deg, #f9a73e, #f79009); transition: all 0.2s ease; }
        .btn-brand:hover { filter: brightness(1.08); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(247,144,9,0.35); }

        /* Badge pulse */
        .badge-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .6; } }

        /* Chart bars */
        .bar-fill { transition: height 0.8s ease; }

        /* Modal */
        #add-menu-modal { display: none; }
        #add-menu-modal.open { display: flex; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex" x-data>

    {{-- ===== SIDEBAR OVERLAY (mobile) ===== --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-30 lg:hidden" onclick="toggleSidebar()"></div>

    {{-- ===== SIDEBAR ===== --}}
  <aside id="sidebar" 
class="sticky top-0 self-start h-screen w-64 bg-sidebar-bg z-40 flex flex-col overflow-hidden -translate-x-full lg:translate-x-0 lg:relative lg:flex-shrink-0">

        {{-- Logo --}}
        <div class="h-16 flex items-center gap-3 px-5 border-b border-sidebar-border flex-shrink-0">
            <div class="w-8 h-8 btn-brand rounded-lg flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-bowl-food text-white text-sm"></i>
            </div>
            <div>
                <p class="font-heading font-bold text-white text-sm leading-none">Smart<span class="text-brand-400">Canteen</span></p>
                <p class="text-[10px] text-sidebar-text mt-0.5">Pengelola Kantin</p>
            </div>
            <button onclick="toggleSidebar()" class="ml-auto lg:hidden w-7 h-7 flex items-center justify-center text-sidebar-text hover:text-white">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <p class="text-[10px] font-semibold tracking-widest text-sidebar-text/50 uppercase px-3 mb-3 mt-1">Menu Utama</p>

            @php
            $navItems = [
                ['url' => '/admin/dashboard',  'icon' => 'fa-gauge-high',        'label' => 'Dashboard',         'route' => 'admin/dashboard'],
                ['url' => '/admin/menu',        'icon' => 'fa-utensils',          'label' => 'Kelola Menu',        'route' => 'admin/menu'],
                ['url' => '/admin/orders',      'icon' => 'fa-bell',              'label' => 'Pesanan Masuk',      'route' => 'admin/orders',  'badge' => 3],
                ['url' => '/admin/delivery',    'icon' => 'fa-motorcycle',        'label' => 'Proses Pengiriman',  'route' => 'admin/delivery'],
                ['url' => '/admin/report',      'icon' => 'fa-chart-column',      'label' => 'Laporan Favorit',    'route' => 'admin/report'],
            ];
            @endphp

            @foreach($navItems as $item)
            @php $isActive = request()->is($item['route']); @endphp
            <a href="{{ url($item['url']) }}"
               class="nav-item {{ $isActive ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl group"
               onclick="if(window.innerWidth < 1024) toggleSidebar()">
                <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ $isActive ? 'bg-brand-500/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors flex-shrink-0">
                    <i class="fa-solid {{ $item['icon'] }} text-xs {{ $isActive ? 'text-brand-400' : 'text-sidebar-text group-hover:text-sidebar-textActive' }}"></i>
                </div>
                <span class="text-sm font-medium {{ $isActive ? 'text-sidebar-textActive' : 'text-sidebar-text group-hover:text-sidebar-textActive' }} flex-1 transition-colors">
                    {{ $item['label'] }}
                </span>
                @if(isset($item['badge']))
                <span class="badge-pulse bg-brand-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0">{{ $item['badge'] }}</span>
                @endif
            </a>
            @endforeach
        </nav>

        {{-- User Profile --}}
        <div class="p-3 border-t border-sidebar-border flex-shrink-0">
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-sidebar-hover transition-colors cursor-pointer">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">B</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sidebar-textActive text-xs font-semibold truncate">Bu Ratna</p>
                    <p class="text-sidebar-text text-[10px] truncate">Pengelola Kantin</p>
                </div>
                <i class="fa-solid fa-ellipsis-vertical text-sidebar-text text-xs"></i>
            </div>
        </div>
    </aside>

    {{-- ===== MAIN WRAPPER ===== --}}
    <div class="flex-1 flex flex-col min-w-0 lg:ml-0">

        {{-- ===== NAVBAR (Top) ===== --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center px-4 sm:px-6 gap-4 sticky top-0 z-20 shadow-sm">

            {{-- Hamburger --}}
            <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-bars text-gray-600 text-sm"></i>
            </button>

            {{-- Page Title --}}
            <div class="flex-1">
                <h1 class="font-heading font-bold text-gray-800 text-base sm:text-lg leading-none">@yield('page-title', 'Dashboard')</h1>
                <p class="text-gray-400 text-xs mt-0.5 hidden sm:block">@yield('page-subtitle', 'Selamat datang, Bu Ratna!')</p>
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Date --}}
                <div class="hidden md:flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
                    <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                    <span class="text-gray-600 text-xs font-medium">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>

                {{-- Notif --}}
                <button class="relative w-9 h-9 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center hover:bg-gray-100 transition-colors">
                    <i class="fa-solid fa-bell text-gray-500 text-sm"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-400 rounded-full badge-pulse"></span>
                </button>

                {{-- Profile --}}
                <div class="flex items-center gap-2.5 cursor-pointer group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-sm shadow-md">B</div>
                    <div class="hidden sm:block">
                        <p class="text-gray-800 text-xs font-semibold leading-none">Bu Ratna</p>
                        <p class="text-gray-400 text-[10px]">Pengelola</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs hidden sm:block group-hover:text-gray-600 transition-colors"></i>
                </div>
            </div>
        </header>

        {{-- ===== CONTENT ===== --}}
        <main class="flex-1 p-4 sm:p-6 animate-fade-up">
            @yield('content')
        </main>

        {{-- ===== FOOTER ===== --}}
        <footer class="bg-white border-t border-gray-100 px-6 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-gray-400 text-xs">© {{ date('Y') }} SmartCanteen · Panel Pengelola Kantin</p>
                <p class="text-gray-400 text-xs flex items-center gap-1.5">
                    v1.0.0 <span class="w-1 h-1 rounded-full bg-gray-300 inline-block"></span>
                    <span class="text-green-500 font-medium flex items-center gap-1"><i class="fa-solid fa-circle text-[8px]"></i>Sistem Online</span>
                </p>
            </div>
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('active');
            } else {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('active');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>

