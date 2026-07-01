<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Pengelola') — SmartCanteen</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                        50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0',
                        300: '#86efac', 400: '#4ade80', 500: '#22c55e',
                        600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d'
                    },
                    amber: {
                        50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a',
                        300: '#fcd34d', 400: '#fbbf24', 500: '#f59e0b',
                        600: '#d97706', 700: '#b45309', 800: '#92400e', 900: '#78350f'
                    },
                    teal: {
                        50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4',
                        300: '#5eead4', 400: '#2dd4bf', 500: '#14b8a6',
                        600: '#0d9488', 700: '#0f766e', 800: '#115e59', 900: '#134e4a'
                    },
                    emerald: {
                        50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0',
                        300: '#6ee7b7', 400: '#34d399', 500: '#10b981',
                        600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b'
                    },
                    red: {
                        50: '#fef2f2', 100: '#fee2e2', 200: '#fecaca',
                        300: '#fca5a5', 400: '#f87171', 500: '#ef4444',
                        600: '#dc2626', 700: '#b91c1c', 800: '#991b1b', 900: '#7f1d1d'
                    }
                },
                keyframes: {
                    fadeScale: {
                        '0%':   { opacity: '0', transform: 'scale(0.95) translateY(-4px)' },
                        '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
                    },
                },
                animation: {
                    'fade-scale': 'fadeScale 0.15s ease-out',
                },
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

        /* ── Sidebar ── */
        #sidebar {
            transition: transform .35s ease;
            background: linear-gradient(180deg, #ffffff 0%, #fffaf3 100%);
            border-right: 1px solid #F4E6D2;
            box-shadow: 8px 0 24px rgba(243,112,33,.05);
        }
        #sidebar-overlay { display: none; }
        #sidebar-overlay.visible { display: block; }

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
            left: 0; top: 16%;
            width: 4px; height: 68%;
            background: #F37021;
            border-radius: 0 8px 8px 0;
        }
        .nav-icon {
            width: 34px; height: 34px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            background: #FFF4EA;
        }
        .nav-item.active .nav-icon {
            background: #F37021;
            color: white;
        }

        /* ── Buttons ── */
        .btn-primary {
            background: linear-gradient(135deg, #F37021 0%, #E96315 100%);
            color: white;
            box-shadow: 0 8px 18px rgba(243,112,33,.18);
            transition: .2s ease;
        }
        .btn-primary:hover { transform: translateY(-2px); }

        /* ── Topbar ── */
        .topbar {
            background: rgba(255,255,255,.75);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #F4E6D2;
        }

        /* ── Profile Dropdown ── */
        #profile-dropdown-pengelola {
            display: none;
            animation: fadeScale 0.15s ease-out;
            transform-origin: top right;
        }
        #profile-dropdown-pengelola.open { display: block; }

        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(243,112,33,.25);
            border-radius: 999px;
        }

        /* ── Sidebar toggle button (fullscreen only) ── */
        #sidebar-toggle-btn {
            display: none;
            position: fixed;
            top: 50%;
            left: 256px;
            transform: translateY(-50%);
            z-index: 50;
            transition: left .35s ease;
        }
        .fullscreen-mode #sidebar-toggle-btn {
            display: flex;
        }
        .sidebar-hidden #sidebar-toggle-btn {
            left: 0;
        }
        .sidebar-hidden #sidebar {
            transform: translateX(-100%);
        }
        .sidebar-hidden .lg\:pl-64 {
            padding-left: 0 !important;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full">

{{-- Tombol toggle sidebar — hanya muncul saat fullscreen --}}
<button id="sidebar-toggle-btn"
        onclick="toggleSidebar()"
        class="w-6 h-14 bg-white border border-borderSoft rounded-r-xl
               items-center justify-center shadow-md hover:bg-softOrange transition-colors">
    <i class="fa-solid fa-chevron-left text-primary text-xs" id="icon-sidebar-toggle"></i>
</button>

{{-- Overlay mobile --}}
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/30 z-30 lg:hidden"
     onclick="closeSidebar()"></div>

{{-- ═══════════ SIDEBAR ═══════════ --}}
<aside id="sidebar"
       class="fixed inset-y-0 left-0 w-64 z-40 flex flex-col -translate-x-full lg:translate-x-0">

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
    <nav class="flex-1 overflow-y-auto scrollbar-thin p-3 space-y-2">
       @php
            $tenantId = \App\Models\Tenant::where('user_id', auth()->id())->value('id');

            $incomingOrderCount = 0;

            if ($tenantId) {
                $incomingOrderCount = \App\Models\Order::whereHas('items', function ($query) use ($tenantId) {
                        $query->where('tenant_id', $tenantId);
                    })
                    ->whereIn('status', ['baru', 'pembayaran_terverifikasi'])
                    ->whereDate('created_at', today())
                    ->count();
            }

            $navLinks = [
                ['url' => '/pengelola/dashboard',       'icon' => 'fa-gauge',      'label' => 'Dashboard',        'match' => 'pengelola/dashboard'],
                ['url' => '/pengelola/menu-management', 'icon' => 'fa-utensils',   'label' => 'Kelola Menu',      'match' => 'pengelola/menu-management'],
                ['url' => '/pengelola/categories',      'icon' => 'fa-tags',       'label' => 'Kelola Kategori',  'match' => 'pengelola/categories'],
                ['url' => '/pengelola/orders',          'icon' => 'fa-bell',       'label' => 'Pesanan Masuk',    'match' => 'pengelola/orders', 'badge' => $incomingOrderCount],
                ['url' => '/pengelola/delivery',        'icon' => 'fa-truck-fast', 'label' => 'Proses Pengiriman','match' => 'pengelola/delivery'],
                ['url' => '/pengelola/delivery/display','icon' => 'fa-tv',         'label' => 'Display Layar',    'match' => 'pengelola/delivery/display'],
                ['url' => '/pengelola/report',          'icon' => 'fa-chart-bar',  'label' => 'Laporan Favorit',  'match' => 'pengelola/report'],
            ];
        @endphp
       

       @foreach($navLinks as $link)
        @php $active = request()->is($link['match']); @endphp
        <a href="{{ url($link['url']) }}"
        class="nav-item {{ $active ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-2xl"
        onclick="if(window.innerWidth < 1024) closeSidebar()">
            <div class="nav-icon">
                <i class="fa-solid {{ $link['icon'] }} text-sm"></i>
            </div>
            <span class="text-sm font-medium flex-1">{{ $link['label'] }}</span>

            {{--  Badge dengan id khusus untuk pesanan masuk --}}
            @if($link['match'] === 'pengelola/orders')
                <span id="nav-order-badge"
                    class="bg-primary text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center {{ empty($link['badge']) ? 'hidden' : '' }}">
                    {{ $link['badge'] ?? 0 }}
                </span>
            @elseif(!empty($link['badge']))
                <span class="bg-primary text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center">
                    {{ $link['badge'] }}
                </span>
            @endif
        </a>
    @endforeach
    </nav>

    {{-- Sidebar User --}}
    <div class="p-4 border-t border-borderSoft">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0">
                @auth
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full btn-primary flex items-center justify-center font-display font-bold text-sm text-white">
                            {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                        </div>
                    @endif
                @endauth
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-darkText truncate">
                    @auth {{ auth()->user()->full_name }} @else Pengelola @endauth
                </p>
                <p class="text-xs text-gray-400">Pengelola Kantin</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button" onclick="openLogoutModal()">
                        <i class="fa-solid fa-right-from-bracket text-sm text-stone-400 hover:text-red-400 transition-colors"></i>
                    </button>
            </form>
        </div>
    </div>
</aside>

{{-- ═══════════ MAIN WRAPPER ═══════════ --}}
<div class="lg:pl-64 min-h-screen flex flex-col" id="main-wrapper">

    {{-- ── TOPBAR ── --}}
    <header class="topbar sticky top-0 z-20 h-16 flex items-center px-4 sm:px-6 gap-4">

        {{-- Hamburger --}}
        <button onclick="openSidebar()"
                class="lg:hidden w-9 h-9 rounded-xl bg-softOrange hover:bg-orange-100
                       flex items-center justify-center transition-colors flex-shrink-0">
            <i class="fa-solid fa-bars text-primary text-sm"></i>
        </button>

        {{-- Title --}}
        <div class="flex-1 min-w-0">
            <h1 class="font-display text-xl font-semibold text-darkText truncate">
                @yield('page-title', 'Dashboard')
            </h1>
            <p class="text-sm text-gray-400 hidden sm:block truncate">
                @yield('page-subtitle', 'Ringkasan operasional kantin hari ini')
            </p>
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">

            {{-- Notif --}}
            <a href="{{ route('pengelola.notifications') }}"
               class="relative w-9 h-9 rounded-xl bg-softOrange hover:bg-orange-100
                      flex items-center justify-center transition-colors">
                <i class="fa-solid fa-bell text-primary text-sm"></i>
                @php
                    $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                                    ->whereNull('read_at')->count();
                @endphp
                @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white
                             text-[10px] font-bold rounded-full flex items-center justify-center px-1">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
                @endif
            </a>

            {{-- Profile Dropdown --}}
            <div class="relative" id="profile-dropdown-pengelola-wrap">
                <button onclick="togglePengelolaDropdown()"
                        class="flex items-center gap-2 cursor-pointer group select-none">
                    <div class="w-10 h-10 rounded-xl overflow-hidden shadow">
                        @auth
                            @if(auth()->user()->photo)
                                <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full btn-primary flex items-center justify-center font-display font-bold text-sm text-white">
                                    {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                                </div>
                            @endif
                        @endauth
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-semibold text-darkText leading-none">
                            @auth {{ auth()->user()->full_name }} @else Pengelola @endauth
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Pengelola</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 group-hover:text-primary transition hidden sm:block"></i>
                </button>

                {{-- Dropdown Panel --}}
                <div id="profile-dropdown-pengelola"
                     class="absolute right-0 top-12 w-56 bg-white rounded-2xl shadow-xl
                            border border-borderSoft overflow-hidden z-50">

                    {{-- User Info --}}
                    <div class="px-4 py-3.5 bg-softOrange border-b border-borderSoft">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0 shadow">
                                @auth
                                    @if(auth()->user()->photo)
                                        <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full btn-primary flex items-center justify-center font-display font-bold text-sm text-white">
                                            {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                                        </div>
                                    @endif
                                @endauth
                            </div>
                            <div class="min-w-0">
                                <p class="font-display font-semibold text-sm text-darkText truncate">
                                    @auth {{ auth()->user()->full_name }} @else Pengelola @endauth
                                </p>
                                <p class="text-xs text-gray-400 truncate">
                                    @auth {{ auth()->user()->email }} @endauth
                                </p>
                            </div>
                        </div>
                        
                    </div>

                    {{-- Menu Items --}}
                    <div class="py-1.5">
                        <a href="{{ url('/pengelola/dashboard') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-darkText
                                  hover:bg-softOrange transition-colors group">
                            <div class="w-7 h-7 rounded-lg bg-orange-50 group-hover:bg-orange-100
                                        flex items-center justify-center transition-colors flex-shrink-0">
                                <i class="fa-solid fa-gauge text-primary text-xs"></i>
                            </div>
                            <span class="font-medium">Dashboard</span>
                        </a>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-darkText
                                  hover:bg-softOrange transition-colors group">
                            <div class="w-7 h-7 rounded-lg bg-orange-50 group-hover:bg-orange-100
                                        flex items-center justify-center transition-colors flex-shrink-0">
                                <i class="fa-solid fa-user-pen text-primary text-xs"></i>
                            </div>
                            <span class="font-medium">Edit Profil</span>
                        </a>

                        {{-- Tombol fullscreen — hanya di halaman Display Layar --}}
                        @if(request()->is('pengelola/delivery/display'))
                        <button onclick="toggleFullscreen(); togglePengelolaDropdown();"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-darkText
                                       hover:bg-softOrange transition-colors group text-left">
                            <div class="w-7 h-7 rounded-lg bg-orange-50 group-hover:bg-orange-100
                                        flex items-center justify-center transition-colors flex-shrink-0">
                                <i class="fa-solid fa-expand text-primary text-xs" id="icon-fullscreen"></i>
                            </div>
                            <span class="font-medium" id="label-fullscreen">Layar Penuh</span>
                        </button>
                        @endif
                    </div>

                    {{-- Logout --}}
                    <div class="border-t border-borderSoft py-1.5">
                        {{-- Logout button - trigger modal --}}
                    <button type="button" onclick="openLogoutModal()"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors group text-left">
                        <div class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100
                                    flex items-center justify-center transition-colors flex-shrink-0">
                            <i class="fa-solid fa-right-from-bracket text-gray-400 group-hover:text-red-400 text-xs"></i>
                        </div>
                        <span class="font-semibold">Logout</span>
                    </button>
                    </div>
                </div>
            </div>

        </div>
    </header>

    {{-- ── CONTENT ── --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>

{{-- Logout Modal --}}
<div id="logout-modal" class="fixed inset-0 bg-black/40 z-[99999] hidden items-center justify-center">
  <div class="bg-white rounded-[20px] border border-[#F4E6D2] w-full max-w-sm mx-4 overflow-hidden shadow-xl">
    
    {{-- Header --}}
    <div class="bg-[#FFF3E8] px-7 pt-7 pb-5 text-center border-b border-[#F4E6D2]">
      <div class="w-15 h-15 bg-white rounded-2xl border border-[#F4E6D2] flex items-center justify-center mx-auto mb-3.5" style="width:60px;height:60px">
        <i class="fa-solid fa-right-from-bracket text-orange-600 text-2xl"></i>
      </div>
      <p class="font-semibold text-stone-800 text-base mb-1">Keluar dari akun?</p>
      <p class="text-sm text-gray-400 leading-relaxed">Sesi kamu akan diakhiri dan kamu perlu login kembali untuk mengakses panel.</p>
    </div>

    {{-- Buttons --}}
    <div class="px-7 py-5 flex flex-col gap-2.5">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium transition-colors">
          Ya, keluar sekarang
        </button>
      </form>
      <button onclick="closeLogoutModal()"
              class="w-full py-2.5 rounded-xl bg-[#FFF3E8] hover:bg-orange-100 text-orange-600 text-sm font-medium border border-[#F4E6D2] transition-colors">
        Batal
      </button>
    </div>
  </div>
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

    function togglePengelolaDropdown() {
        document.getElementById('profile-dropdown-pengelola').classList.toggle('open');
    }

    document.addEventListener('click', function (e) {
        const wrap = document.getElementById('profile-dropdown-pengelola-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('profile-dropdown-pengelola').classList.remove('open');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.getElementById('profile-dropdown-pengelola').classList.remove('open');
        }
    });

    // ── Fullscreen & Sidebar Toggle ──
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            document.body.classList.add('fullscreen-mode');
        } else {
            document.exitFullscreen();
        }
    }

    function toggleSidebar() {
        const hidden = document.body.classList.toggle('sidebar-hidden');
        const btn = document.getElementById('sidebar-toggle-btn');
        const icon = document.getElementById('icon-sidebar-toggle');
        icon.className = hidden
            ? 'fa-solid fa-chevron-right text-primary text-xs'
            : 'fa-solid fa-chevron-left text-primary text-xs';
        btn.style.left = hidden ? '0px' : '256px';
    }

    document.addEventListener('fullscreenchange', function () {
        const isFullscreen = !!document.fullscreenElement;

        if (!isFullscreen) {
            // Keluar fullscreen — reset semua
            document.body.classList.remove('fullscreen-mode', 'sidebar-hidden');
            document.getElementById('sidebar-toggle-btn').style.left = '256px';
            document.getElementById('icon-sidebar-toggle').className =
                'fa-solid fa-chevron-left text-primary text-xs';
        }

        const iconFs = document.getElementById('icon-fullscreen');
        const labelFs = document.getElementById('label-fullscreen');
        if (iconFs) iconFs.className = isFullscreen
            ? 'fa-solid fa-compress text-primary text-xs'
            : 'fa-solid fa-expand text-primary text-xs';
        if (labelFs) labelFs.textContent = isFullscreen ? 'Keluar Layar Penuh' : 'Layar Penuh';
    });

    function refreshOrderBadge() {
    fetch('/pengelola/orders/badge-count')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('nav-order-badge');
            if (!badge) return;
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        })
        .catch(() => {}); // silent fail
}

setInterval(refreshOrderBadge, 15000);

function openLogoutModal() {
  const m = document.getElementById('logout-modal');
  m.classList.remove('hidden');
  m.classList.add('flex');
}
function closeLogoutModal() {
  const m = document.getElementById('logout-modal');
  m.classList.add('hidden');
  m.classList.remove('flex');
}
document.getElementById('logout-modal').addEventListener('click', function(e) {
  if (e.target === this) closeLogoutModal();
});
</script>
@stack('scripts')
</body>
</html>