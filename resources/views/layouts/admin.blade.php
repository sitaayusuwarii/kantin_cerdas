<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Canteen Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:  '#f0f4ff',
                            100: '#e0eaff',
                            200: '#c7d7fe',
                            300: '#a5b8fc',
                            400: '#8191f8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        surface: '#0f172a',
                        panel:   '#1e293b',
                        border:  '#334155',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; color: #e2e8f0; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 4px; }

        /* Sidebar */
        #sidebar { transition: transform 0.3s ease, width 0.3s ease; }
        #sidebar-overlay { transition: opacity 0.3s ease; }

        /* Active nav */
        .nav-item.active { background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4); }
        .nav-item:not(.active):hover { background: rgba(99, 102, 241, 0.1); }
        .nav-item { transition: all 0.2s ease; }

        /* Cards */
        .glass-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(51, 65, 85, 0.6);
            backdrop-filter: blur(12px);
        }

        /* Stat card gradient borders */
        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(99,102,241,0.5), rgba(124,58,237,0.2), transparent);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        /* Pulse dot */
        .pulse-dot::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 8px; height: 8px;
            background: #10b981;
            border-radius: 50%;
            border: 2px solid #0f172a;
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.3);opacity:0.7} }

        /* Table row hover */
        .table-row:hover td { background: rgba(99, 102, 241, 0.05); }

        /* Badge */
        .badge { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em; }

        /* Chart bar animation */
        .chart-bar { transition: height 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }

        /* Notification dot */
        .notif-badge {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            font-size: 0.6rem;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            border: 2px solid #0f172a;
        }

        /* Page fade in */
        main { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    @stack('styles')
</head>
<body class="h-full">

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-30 opacity-0 pointer-events-none lg:hidden" onclick="toggleSidebar()"></div>

<div class="flex h-screen overflow-hidden">

    <!-- ===== SIDEBAR ===== -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 flex flex-col bg-panel border-r border-border -translate-x-full lg:translate-x-0">

        <!-- Logo / Brand -->
        <div class="flex items-center gap-3 px-5 py-5 border-b border-border">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-violet-600 flex items-center justify-center shadow-lg shadow-primary-900/40 flex-shrink-0">
                <i class="fa-solid fa-utensils text-white text-sm"></i>
            </div>
            <div class="leading-tight">
                <p class="text-white font-bold text-sm tracking-wide">SmartCanteen</p>
                <p class="text-primary-400 text-xs font-medium">Admin Panel</p>
            </div>
        </div>

        <!-- Nav Menu -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest px-3 mb-3">Menu Utama</p>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'active text-white' : 'text-slate-400' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-slate-700/50' }}">
                    <i class="fa-solid fa-gauge-high text-xs"></i>
                </span>
                Dashboard
            </a>

            <a href="{{ route('admin.verification') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.verification') ? 'active text-white' : 'text-slate-400' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.verification') ? 'bg-white/20' : 'bg-slate-700/50' }}">
                    <i class="fa-solid fa-circle-check text-xs"></i>
                </span>
                Verifikasi Pembayaran
                <span class="ml-auto notif-badge text-white font-bold px-1.5">5</span>
            </a>

            <a href="{{ route('admin.transactions') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.transactions') ? 'active text-white' : 'text-slate-400' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.transactions') ? 'bg-white/20' : 'bg-slate-700/50' }}">
                    <i class="fa-solid fa-arrow-right-arrow-left text-xs"></i>
                </span>
                Monitoring Transaksi
            </a>

            <a href="{{ route('admin.report') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.report') ? 'active text-white' : 'text-slate-400' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.report') ? 'bg-white/20' : 'bg-slate-700/50' }}">
                    <i class="fa-solid fa-chart-line text-xs"></i>
                </span>
                Laporan Keuangan
            </a>

            <a href="{{ route('admin.kelola-user') }}"
                class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.kelola-user') ? 'active text-white' : 'text-slate-400' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.kelola-user') ? 'bg-white/20' : 'bg-slate-700/50' }}">
                    <i class="fa-solid fa-users text-xs"></i>
                </span>
                Kelola User
            </a>
        </nav>

        <!-- User Profile Bottom -->
        <div class="px-4 py-4 border-t border-border">
            <div class="flex items-center gap-3">
                <div class="relative pulse-dot flex-shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center text-white font-bold text-sm">A</div>
                <!-- User Profile Bottom -->
<div class="px-4 py-4 border-t border-border">
    <div class="flex items-center gap-3">

        <div class="relative pulse-dot flex-shrink-0">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center text-white font-bold text-sm">
                A
            </div>
        </div>

        <div class="flex-1 min-w-0">
            <p class="text-white text-sm font-semibold truncate">Admin Sekolah</p>
            <p class="text-slate-500 text-xs truncate">admin@sekolah.sch.id</p>
        </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        onclick="return confirm('Yakin mau logout?')"
                        class="text-slate-500 hover:text-red-400">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    </button>
                </form>

            </div>
        </div>
        </div>
    </aside>

    <!-- ===== MAIN AREA ===== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- ===== NAVBAR ===== -->
        <header class="flex-shrink-0 h-16 bg-panel border-b border-border flex items-center px-4 lg:px-6 gap-4 z-20">

            <!-- Mobile menu toggle -->
            <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 rounded-lg bg-slate-700/50 hover:bg-slate-700 flex items-center justify-center text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>

            <!-- Page Title (dynamic) -->
            <div class="flex-1 min-w-0">
                <h1 class="text-white font-bold text-base lg:text-lg truncate">@yield('page-title', 'Dashboard')</h1>
                <p class="text-slate-500 text-xs hidden sm:block">@yield('page-subtitle', 'Selamat datang di panel kontrol')</p>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-2 lg:gap-3">

                <!-- Search (desktop) -->
                <div class="hidden md:flex items-center gap-2 bg-slate-800 border border-border rounded-xl px-3 py-2 w-40 lg:w-52">
                    <i class="fa-solid fa-search text-slate-500 text-xs"></i>
                    <input type="text" placeholder="Cari..." class="bg-transparent text-sm text-slate-300 placeholder-slate-600 outline-none w-full">
                </div>

                <!-- Notification Bell -->
                <button class="relative w-9 h-9 rounded-xl bg-slate-800 border border-border flex items-center justify-center text-slate-400 hover:text-white hover:border-primary-500 transition-all">
                    <i class="fa-solid fa-bell text-sm"></i>
                    <span class="notif-badge absolute -top-1 -right-1 text-white text-xs font-bold px-1">5</span>
                </button>

                <!-- Profile -->
                <div class="flex items-center gap-2 bg-slate-800 border border-border rounded-xl px-3 py-1.5 cursor-pointer hover:border-primary-500 transition-all">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center text-white font-bold text-xs">A</div>
                    <span class="text-white text-sm font-medium hidden sm:inline">Admin</span>
                    <i class="fa-solid fa-chevron-down text-slate-500 text-xs hidden sm:inline"></i>
                </div>
            </div>
        </header>

        <!-- ===== PAGE CONTENT ===== -->
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @yield('content')
        </main>

        <!-- ===== FOOTER ===== -->
        <footer class="flex-shrink-0 border-t border-border px-4 lg:px-6 py-3">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-1 text-xs text-slate-600">
                <span>&copy; {{ date('Y') }} <span class="text-primary-400 font-semibold">SmartCanteen</span> — Admin Panel</span>
                <span class="font-mono">v2.0.0 · SMA Negeri 1</span>
            </div>
        </footer>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const isOpen = !sidebar.classList.contains('-translate-x-full');

    if (isOpen) {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.remove('opacity-100', 'pointer-events-auto');
        overlay.classList.add('opacity-0', 'pointer-events-none');
    } else {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.classList.add('opacity-100', 'pointer-events-auto');
    }
}

// Close sidebar on resize to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        const overlay = document.getElementById('sidebar-overlay');
        overlay.classList.add('opacity-0', 'pointer-events-none');
        overlay.classList.remove('opacity-100', 'pointer-events-auto');
    }
});
</script>

@stack('scripts')
</body>
</html>