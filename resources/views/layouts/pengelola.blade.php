<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Pengelola') — SmartCanteen</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href=<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script>
tailwind.config = {
    theme: {
        extend: {
            fontFamily: {
                display: ['Outfit', 'serif'],
                sans: ['Plus Jakarta Sans', 'sans-serif'],
            },
            colors: {
                primary: '#F37021',
                cream: '#FEF8ED',
                softOrange: '#FFF3E8',
                borderSoft: '#F4E6D2',
                darkText: '#2D2A26',

                forest: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d'
                },

                amber: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f'
                },

                teal: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a'
                },

                emerald: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b'
                },

                red: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d'
                }
            }
        }
    }
}
</script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #FEF8ED;
            color: #2D2A26;
        }

        .font-display, h1, h2, h3 {
            font-family: 'Outfit', serif;
        }

        #sidebar {
            transition: transform .35s ease;
            background: linear-gradient(180deg, #ffffff 0%, #fffaf3 100%);
            border-right: 1px solid #F4E6D2;
            box-shadow: 8px 0 24px rgba(243,112,33,.05);
        }

        .nav-item {
            transition: all .25s ease;
            position: relative;
        }

        .nav-item:hover {
            background: #FFF7EF;
            transform: translateX(4px);
        }

        .nav-item.active {
            background: #FFF1E6;
            color: #F37021;
            box-shadow: 0 8px 20px rgba(243,112,33,.08);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 16%;
            width: 4px;
            height: 68%;
            background: #F37021;
            border-radius: 0 8px 8px 0;
        }

        .nav-icon {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFF4EA;
        }

        .nav-item.active .nav-icon {
            background: #F37021;
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F37021 0%, #E96315 100%);
            color: white;
            box-shadow: 0 8px 18px rgba(243,112,33,.18);
            transition: .2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .topbar {
            background: rgba(255,255,255,.75);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #F4E6D2;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(243,112,33,.25);
            border-radius: 999px;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full">

<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/30 z-30 hidden lg:hidden"
     onclick="closeSidebar()"></div>

<aside id="sidebar"
       class="fixed inset-y-0 left-0 w-64 z-40 flex flex-col -translate-x-full lg:translate-x-0">

<div class="px-5 py-4 border-b border-borderSoft flex items-center gap-3">
    <img src="{{ asset('images/canteen.png') }}"
         alt="Smart Canteen Logo"
         class="w-20 h-20 object-contain rounded-xl flex-shrink-0">
</div>

    <nav class="flex-1 overflow-y-auto scrollbar-thin p-3 space-y-2">
        @php
        $navLinks = [
            ['url'=>'/pengelola/dashboard','icon'=>'fa-gauge','label'=>'Dashboard','match'=>'pengelola/dashboard'],
            ['url'=>'/pengelola/menu','icon'=>'fa-utensils','label'=>'Kelola Menu','match'=>'pengelola/menu'],
            ['url'=>'/pengelola/orders','icon'=>'fa-bell','label'=>'Pesanan Masuk','match'=>'pengelola/orders','badge'=>3],
            ['url'=>'/pengelola/delivery','icon'=>'fa-truck-fast','label'=>'Pengiriman','match'=>'pengelola/delivery'],
            ['url'=>'/pengelola/report','icon'=>'fa-chart-column','label'=>'Laporan','match'=>'pengelola/report'],
        ];
        @endphp

        @foreach($navLinks as $link)
            @php $active = request()->is($link['match']); @endphp
            <a href="{{ url($link['url']) }}"
               class="nav-item {{ $active ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-2xl">

                <div class="nav-icon">
                    <i class="fa-solid {{ $link['icon'] }} text-sm"></i>
                </div>

                <span class="text-sm font-medium flex-1">
                    {{ $link['label'] }}
                </span>

                @if(!empty($link['badge']))
                    <span class="bg-primary text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center">
                        {{ $link['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-borderSoft">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl btn-primary flex items-center justify-center font-semibold">
                R
            </div>
            <div>
                <p class="text-sm font-semibold text-darkText">Bu Ratna</p>
                <p class="text-xs text-gray-400">Pengelola Kantin</p>
            </div>
        </div>
    </div>
</aside>

<div class="lg:pl-64 min-h-screen flex flex-col">

    <header class="topbar sticky top-0 z-20 h-16 flex items-center px-6">
        <div class="flex-1">
            <h1 class="font-display text-xl font-semibold text-darkText">
                @yield('page-title', 'Dashboard')
            </h1>
            <p class="text-sm text-gray-400">
                @yield('page-subtitle', 'Ringkasan operasional kantin hari ini')
            </p>
        </div>

        <div class="flex items-center gap-3 cursor-pointer group">
            <div class="w-10 h-10 rounded-xl btn-primary flex items-center justify-center font-semibold">
                R
            </div>

            <div class="hidden sm:block">
                <p class="text-sm font-semibold text-darkText">Bu Ratna</p>
                <p class="text-xs text-gray-400">Pengelola</p>
            </div>

            <i class="fa-solid fa-chevron-down text-xs text-gray-400 group-hover:text-primary transition hidden sm:block"></i>
        </div>
    </header>

    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
}
</script>

@stack('scripts')
</body>
</html>