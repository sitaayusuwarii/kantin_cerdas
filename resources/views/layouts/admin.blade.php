<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Canteen Admin')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                    primary: {
                        50:'#fff7ed', 100:'#ffedd5', 200:'#fed7aa',
                        300:'#fdba74', 400:'#fb923c', 500:'#f97316',
                        600:'#ea580c', 700:'#c2410c', 800:'#9a3412', 900:'#7c2d12',
                    },
                    surface: '#fdf6f0',
                    panel:   '#ffffff',
                    border:  '#e5d5c8',
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fdf6f0; color: #1c1917; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #fde8d8; }
        ::-webkit-scrollbar-thumb { background: #ea580c; border-radius: 4px; }
        #sidebar { transition: transform 0.3s ease; }
        #sidebar-overlay { transition: opacity 0.3s ease; }
        .nav-item.active { background: linear-gradient(135deg, #ea580c, #f97316); box-shadow: 0 4px 15px rgba(234,88,12,0.4); }
        .nav-item:not(.active):hover { background: rgba(234,88,12,0.08); }
        .nav-item { transition: all 0.2s ease; }
        .glass-card { background: rgba(255,255,255,0.95); border: 1px solid rgba(229,213,200,0.8); backdrop-filter: blur(12px); overflow: visible; }
        .stat-card::before {
            content: ''; position: absolute; inset: 0; border-radius: inherit; padding: 1px;
            background: linear-gradient(135deg, rgba(99,102,241,0.5), rgba(124,58,237,0.2), transparent);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude; pointer-events: none;
        }
        .pulse-dot::after {
            content: ''; position: absolute; top: 0; right: 0;
            width: 8px; height: 8px; background: #10b981;
            border-radius: 50%; border: 2px solid #fdf6f0;
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.3);opacity:0.7} }
        .table-row:hover td { background: rgba(234,88,12,0.05); }
        .badge { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em; }
        .notif-badge {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            font-size: 0.6rem; min-width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 9999px; border: 2px solid #0f172a;
        }
        main { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
    </style>
    @stack('styles')
</head>
<body class="h-full">

<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/60 z-30 opacity-0 pointer-events-none lg:hidden"
     onclick="toggleSidebar()"></div>

<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar"
           class="fixed lg:static inset-y-0 left-0 z-40 w-64 flex flex-col bg-panel border-r border-border -translate-x-full lg:translate-x-0">

        {{-- Logo --}}
        <div class="relative px-5 py-4 border-b border-borderSoft flex justify-center items-center">
            
            <img src="{{ asset('images/canteen.png') }}"
                alt="Smart Canteen Logo"
                class="w-24 h-24 object-contain">

            <button onclick="closeSidebar()"
                    class="absolute right-5 lg:hidden text-gray-400 hover:text-primary transition-colors">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <p class="text-xs text-black font-semibold uppercase tracking-widest px-3 mb-3">Menu Utama</p>
            <a href="{{ route('admin.dashboard') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                      {{ request()->routeIs('admin.dashboard') ? 'active text-white' : 'text-black' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center
                             {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-orange-100/50' }}">
                    <i class="fa-solid fa-gauge-high text-xs"></i>
                </span>
                Dashboard
            </a>

            <a href="{{ route('admin.verification') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                      {{ request()->routeIs('admin.verification') ? 'active text-white' : 'text-black' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center
                             {{ request()->routeIs('admin.verification') ? 'bg-white/20' : 'bg-orange-100/50' }}">
                    <i class="fa-solid fa-circle-check text-xs"></i>
                </span>
                Verifikasi Pembayaran
                {{-- ↓ Dari database via View Composer, hilang otomatis kalau 0 --}}
                @if($pendingPaymentCount > 0)
                <span class="ml-auto notif-badge text-stone-800 font-bold px-1.5">
                    {{ $pendingPaymentCount > 99 ? '99+' : $pendingPaymentCount }}
                </span>
                @endif
            </a>

            <a href="{{ route('admin.unpaid-orders') }}"
                class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                        {{ request()->routeIs('admin.unpaid-orders') ? 'active text-white' : 'text-black' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center
                                {{ request()->routeIs('admin.unpaid-orders') ? 'bg-white/20' : 'bg-orange-100/50' }}">
                        <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                    </span>
                    Pesanan Belum Bayar
                    @php
                        $unpaidCount = \App\Models\Order::where('status', 'baru')
                            ->where('payment_status', 'pending')
                            ->whereNull('payment_proof')
                            ->whereDate('created_at', today())
                            ->count();
                    @endphp
                    @if($unpaidCount > 0)
                    <span class="ml-auto notif-badge text-stone-800 font-bold px-1.5">
                        {{ $unpaidCount > 99 ? '99+' : $unpaidCount }}
                    </span>
                    @endif
                </a>

            <a href="{{ route('admin.transactions') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                      {{ request()->routeIs('admin.transactions') ? 'active text-white' : 'text-black' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center
                             {{ request()->routeIs('admin.transactions') ? 'bg-white/20' : 'bg-orange-100/50' }}">
                    <i class="fa-solid fa-arrow-right-arrow-left text-xs"></i>
                </span>
                Monitoring Transaksi
            </a>

            <a href="{{ route('admin.payment-methods') }}"
            class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.payment-methods') ? 'active text-white' : 'text-black' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center
                            {{ request()->routeIs('admin.payment-methods') ? 'bg-white/20' : 'bg-orange-100/50' }}">
                    <i class="fa-solid fa-credit-card text-xs"></i>
                </span>
                Metode Pembayaran
            </a>

            <a href="{{ route('admin.laporan-keuangan') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                      {{ request()->routeIs('admin.laporan-keuangan') ? 'active text-white' : 'text-black' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center
                             {{ request()->routeIs('admin.laporan-keuangan') ? 'bg-white/20' : 'bg-orange-100/50' }}">
                    <i class="fa-solid fa-chart-line text-xs"></i>
                </span>
                Laporan Keuangan
            </a>

            <a href="{{ route('admin.kelola-user') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                      {{ request()->routeIs('admin.kelola-user') ? 'active text-white' : 'text-black' }}">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center
                             {{ request()->routeIs('admin.kelola-user') ? 'bg-white/20' : 'bg-orange-100/50' }}">
                    <i class="fa-solid fa-users text-xs"></i>
                </span>
                Kelola User
            </a>
        </nav>

        {{-- ↓ User profile — hanya satu, tidak duplikat --}}
        <div class="px-4 py-4 border-t border-border flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative pulse-dot flex-shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-600
                                flex items-center justify-center text-stone-800 font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->full_name ?? 'A', 0, 1)) }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-black text-sm font-semibold truncate">
                        {{ Auth::user()->name ?? Auth::user()->full_name ?? 'Admin' }}
                    </p>
                    <p class="text-stone-400 text-xs truncate">{{ Auth::user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Yakin mau logout?')"
                            class="text-stone-400 hover:text-red-400 transition-colors"
                            title="Logout">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- NAVBAR --}}
        <header class="flex-shrink-0 h-16 bg-panel border-b border-border flex items-center px-4 lg:px-6 gap-4 z-20">
            <button onclick="toggleSidebar()"
                    class="lg:hidden w-9 h-9 rounded-lg bg-orange-100/50 hover:bg-orange-100 flex items-center justify-center text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>

            <div class="flex-1 min-w-0">
                <h1 class="text-stone-800 font-bold text-base lg:text-lg truncate">@yield('page-title', 'Dashboard')</h1>
                <p class="text-stone-400 text-xs hidden sm:block">@yield('page-subtitle', 'Selamat datang di panel kontrol')</p>
            </div>

            <div class="flex items-center gap-2 lg:gap-3">
                <div class="hidden md:flex items-center gap-2 bg-orange-50 border border-border rounded-xl px-3 py-2 w-40 lg:w-52">
                    <i class="fa-solid fa-search text-stone-400 text-xs"></i>
                    <input type="text" placeholder="Cari..."
                           class="bg-transparent text-sm text-stone-600 placeholder-slate-600 outline-none w-full">
                </div>

                {{-- ↓ Bell notif juga dari database --}}
                <a href="{{ route('admin.notifications') }}"
                class="relative w-9 h-9 rounded-xl bg-orange-50 border border-border flex items-center justify-center text-slate-400 hover:text-white hover:border-primary-500 transition-all"
                id="notif-bell">
                    <i class="fa-solid fa-bell text-sm"></i>
                    @if($adminUnreadCount > 0)
                    <span class="notif-badge absolute -top-1 -right-1 text-white text-xs font-bold px-1" id="notif-badge">
                        {{ $adminUnreadCount > 99 ? '99+' : $adminUnreadCount }}
                    </span>
                    @endif
                </a>

                <div class="relative" id="admin-profile-wrap" style="z-index: 9999; position: relative;">
                <button onclick="toggleAdminDropdown()"
                        class="flex items-center gap-2 bg-orange-50 border border-border rounded-xl px-3 py-1.5 hover:border-primary-500 transition-all">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-600
                                flex items-center justify-center text-stone-800 font-bold text-xs">
                        {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->full_name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="text-stone-700 text-sm font-medium hidden sm:inline">
                        {{ Auth::user()->name ?? Auth::user()->full_name ?? 'Admin' }}
                    </span>
                    <i class="fa-solid fa-chevron-down text-stone-400 text-xs hidden sm:inline"></i>
                </button>

                {{-- Dropdown --}}
            <div id="admin-profile-dropdown"
            class="hidden absolute right-0 top-full mt-2 w-56 bg-white border border-orange-100 rounded-2xl shadow-2xl z-[99999]">
                    {{-- User Info --}}
                    <div class="px-4 py-3.5 bg-orange-50 border-b border-orange-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-600
                                        flex items-center justify-center text-stone-800 font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->full_name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-black text-sm font-semibold truncate">
                                    {{ Auth::user()->name ?? Auth::user()->full_name ?? 'Admin' }}
                                </p>
                                <p class="text-stone-400 text-xs truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-[10px] bg-primary-500/20 text-black font-semibold px-2.5 py-0.5 rounded-full border border-primary-500/20">
                                Administrator
                            </span>
                        </div>
                    </div>

                    {{-- Menu Items --}}
                    <div class="py-1.5">
                        <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-400
                                hover:bg-orange-50 hover:text-white transition-colors group">
                            <div class="w-7 h-7 rounded-lg bg-orange-100 group-hover:bg-slate-600
                                        flex items-center justify-center transition-colors flex-shrink-0">
                                <i class="fa-solid fa-gauge-high text-stone-400 group-hover:text-stone-600 text-xs"></i>
                            </div>
                            <span class="font-medium">Dashboard</span>
                        </a>

                        <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-400
                                hover:bg-orange-50 hover:text-white transition-colors group">
                            <div class="w-7 h-7 rounded-lg bg-orange-100 group-hover:bg-slate-600
                                        flex items-center justify-center transition-colors flex-shrink-0">
                                <i class="fa-solid fa-user-pen text-stone-400 group-hover:text-stone-600 text-xs"></i>
                            </div>
                            <span class="font-medium">Edit Profil</span>
                        </a>
                    </div>

                    {{-- Logout --}}
                    <div class="border-t border-border py-1.5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm
                                        text-red-400 hover:bg-red-500/10 transition-colors group text-left">
                                <div class="w-7 h-7 rounded-lg bg-orange-100 group-hover:bg-red-500/20
                                            flex items-center justify-center transition-colors flex-shrink-0">
                                    <i class="fa-solid fa-right-from-bracket text-stone-400 group-hover:text-red-400 text-xs"></i>
                                </div>
                                <span class="font-semibold">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <footer class="flex-shrink-0 border-t border-border px-4 lg:px-6 py-3">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-1 text-xs text-slate-600">
                <span>&copy; {{ date('Y') }} <span class="text-primary-400 font-semibold">SmartCanteen</span> — Admin Panel</span>
               
            </div>
        </footer>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const isOpen  = !sidebar.classList.contains('-translate-x-full');
    if (isOpen) {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.replace('opacity-100', 'opacity-0');
        overlay.classList.replace('pointer-events-auto', 'pointer-events-none');
    } else {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.replace('opacity-0', 'opacity-100');
        overlay.classList.replace('pointer-events-none', 'pointer-events-auto');
    }
}
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        const overlay = document.getElementById('sidebar-overlay');
        overlay.classList.add('opacity-0', 'pointer-events-none');
        overlay.classList.remove('opacity-100', 'pointer-events-auto');
    }
});



{{-- Auto-refresh unread count setiap 30 detik --}}

function refreshNotifCount() {
    fetch('{{ route('admin.notifications.count') }}')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            const bell  = document.getElementById('notif-bell');
            if (data.count > 0) {
                if (!badge) {
                    const span = document.createElement('span');
                    span.id = 'notif-badge';
                    span.className = 'notif-badge absolute -top-1 -right-1 text-white text-xs font-bold px-1';
                    span.textContent = data.count > 99 ? '99+' : data.count;
                    bell.appendChild(span);
                } else {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                }
            } else if (badge) {
                badge.remove();
            }
        });
}
setInterval(refreshNotifCount, 30000);


function toggleAdminDropdown() {
    document.getElementById('admin-profile-dropdown').classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const wrap = document.getElementById('admin-profile-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('admin-profile-dropdown').classList.add('hidden');
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('admin-profile-dropdown').classList.add('hidden');
    }
});

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.add('-translate-x-full');
    overlay.classList.replace('opacity-100', 'opacity-0');
    overlay.classList.replace('pointer-events-auto', 'pointer-events-none');
}

</script>
@stack('scripts')
</body>
</html>