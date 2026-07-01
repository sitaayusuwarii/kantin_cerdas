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

        /* SIDEBAR - gaya pengelola */
        #sidebar {
            transition: transform 0.3s ease;
            background: linear-gradient(180deg, #ffffff 0%, #fffaf3 100%);
            border-right: 1px solid #F4E6D2;
            box-shadow: 8px 0 24px rgba(243,112,33,.05);
        }
        #sidebar-overlay { display: none; }
        #sidebar-overlay.visible { display: block; }

        .nav-item { transition: all 0.25s ease; position: relative; }
        .nav-item:hover { background: #FFF7EF; transform: translateX(4px); }
        .nav-item.active {
            background: #FFF1E6;
            color: #ea580c;
            box-shadow: 0 8px 20px rgba(234,88,12,.08);
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 16%;
            width: 4px; height: 68%;
            background: #ea580c;
            border-radius: 0 8px 8px 0;
        }
        .nav-icon {
            width: 34px; height: 34px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            background: #FFF4EA;
            flex-shrink: 0;
        }
        .nav-item.active .nav-icon { background: #ea580c; }
        .nav-item.active .nav-icon i { color: white !important; }

        /* TOPBAR */
        .topbar {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e5d5c8;
        }

        /* Notif badge */
        .notif-badge {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            font-size: 0.6rem; min-width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 9999px; border: 2px solid #fdf6f0;
            font-weight: 700;
        }

        /* Pulse dot */
        .pulse-dot { position: relative; }
        .pulse-dot::after {
            content: ''; position: absolute; top: 0; right: 0;
            width: 8px; height: 8px; background: #10b981;
            border-radius: 50%; border: 2px solid #fdf6f0;
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.3);opacity:0.7} }

        /* Profile dropdown */
        #admin-profile-dropdown { display: none; transform-origin: top right; }
        #admin-profile-dropdown.open { display: block; }

        /* Content */
        .table-row:hover td { background: rgba(234,88,12,0.05); }
        .badge { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em; }
        .glass-card { background: rgba(255,255,255,0.95); border: 1px solid rgba(229,213,200,0.8); backdrop-filter: blur(12px); overflow: visible; }
        main { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
    </style>
    @stack('styles')
</head>
<body class="h-full">

<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-30 lg:hidden" onclick="closeSidebar()"></div>

<div class="flex h-screen overflow-hidden">

    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 flex flex-col -translate-x-full lg:translate-x-0">

        <div class="relative px-5 py-4 border-b border-[#F4E6D2] flex justify-center items-center">
            <img src="{{ asset('images/canteen.png') }}" alt="Smart Canteen Logo" class="w-24 h-24 object-contain">
            <button onclick="closeSidebar()" class="absolute right-5 lg:hidden text-gray-400 hover:text-orange-500 transition-colors">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

            <a href="{{ route('admin.dashboard') }}"
               class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium
                      {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-stone-700' }}">
                <div class="nav-icon"><i class="fa-solid fa-gauge-high text-sm {{ request()->routeIs('admin.dashboard') ? '' : 'text-orange-500' }}"></i></div>
                <span class="flex-1">Dashboard</span>
            </a>

            <a href="{{ route('admin.verification') }}"
               class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium
                      {{ request()->routeIs('admin.verification') ? 'active' : 'text-stone-700' }}">
                <div class="nav-icon"><i class="fa-solid fa-circle-check text-sm {{ request()->routeIs('admin.verification') ? '' : 'text-orange-500' }}"></i></div>
                <span class="flex-1">Verifikasi Pembayaran</span>
                @if($pendingPaymentCount > 0)
                <span class="notif-badge text-white px-1.5">{{ $pendingPaymentCount > 99 ? '99+' : $pendingPaymentCount }}</span>
                @endif
            </a>

            @php
                $unpaidCount = \App\Models\Order::where('status', 'baru')
                    ->where('payment_status', 'pending')
                    ->whereNull('payment_proof')
                    ->whereDate('created_at', today())
                    ->count();
            @endphp
            <a href="{{ route('admin.unpaid-orders') }}"
               class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium
                      {{ request()->routeIs('admin.unpaid-orders') ? 'active' : 'text-stone-700' }}">
                <div class="nav-icon"><i class="fa-solid fa-clock-rotate-left text-sm {{ request()->routeIs('admin.unpaid-orders') ? '' : 'text-orange-500' }}"></i></div>
                <span class="flex-1">Pesanan Belum Bayar</span>
                @if($unpaidCount > 0)
                <span class="notif-badge text-white px-1.5">{{ $unpaidCount > 99 ? '99+' : $unpaidCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.transactions') }}"
               class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium
                      {{ request()->routeIs('admin.transactions') ? 'active' : 'text-stone-700' }}">
                <div class="nav-icon"><i class="fa-solid fa-arrow-right-arrow-left text-sm {{ request()->routeIs('admin.transactions') ? '' : 'text-orange-500' }}"></i></div>
                <span class="flex-1">Monitoring Transaksi</span>
            </a>

            <a href="{{ route('admin.payment-methods') }}"
               class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium
                      {{ request()->routeIs('admin.payment-methods') ? 'active' : 'text-stone-700' }}">
                <div class="nav-icon"><i class="fa-solid fa-credit-card text-sm {{ request()->routeIs('admin.payment-methods') ? '' : 'text-orange-500' }}"></i></div>
                <span class="flex-1">Metode Pembayaran</span>
            </a>

            <a href="{{ route('admin.laporan-keuangan') }}"
               class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium
                      {{ request()->routeIs('admin.laporan-keuangan') ? 'active' : 'text-stone-700' }}">
                <div class="nav-icon"><i class="fa-solid fa-chart-line text-sm {{ request()->routeIs('admin.laporan-keuangan') ? '' : 'text-orange-500' }}"></i></div>
                <span class="flex-1">Laporan Keuangan</span>
            </a>

            <a href="{{ route('admin.kelola-user') }}"
               class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium
                      {{ request()->routeIs('admin.kelola-user') ? 'active' : 'text-stone-700' }}">
                <div class="nav-icon"><i class="fa-solid fa-users text-sm {{ request()->routeIs('admin.kelola-user') ? '' : 'text-orange-500' }}"></i></div>
                <span class="flex-1">Kelola User</span>
            </a>

            <a href="{{ route('admin.menus') }}"
               class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium
                      {{ request()->routeIs('admin.menus') ? 'active' : 'text-stone-700' }}">
                <div class="nav-icon"><i class="fa-solid fa-utensils text-sm {{ request()->routeIs('admin.menus') ? '' : 'text-orange-500' }}"></i></div>
                <span class="flex-1">Kelola Menu</span>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-[#F4E6D2] flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative pulse-dot flex-shrink-0">
                    @if(Auth::user()->photo)
                    <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                        class="w-9 h-9 rounded-xl object-cover" alt="foto">
                        @else
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600
                                        flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->full_name ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-black text-sm font-semibold truncate">
                        {{ Auth::user()->name ?? Auth::user()->full_name ?? 'Admin' }}
                    </p>
                    <p class="text-stone-400 text-xs truncate">{{ Auth::user()->email }}</p>
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

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <header class="topbar flex-shrink-0 h-16 flex items-center px-4 lg:px-6 gap-4 z-20">
            <button onclick="toggleSidebar()"
                    class="lg:hidden w-9 h-9 rounded-lg bg-orange-100/50 hover:bg-orange-100 flex items-center justify-center text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>

            <div class="flex-1 min-w-0">
                <h1 class="text-stone-800 font-bold text-base lg:text-lg truncate">@yield('page-title', 'Dashboard')</h1>
                <p class="text-stone-400 text-xs hidden sm:block">@yield('page-subtitle', 'Selamat datang di panel kontrol')</p>
            </div>

            <div class="flex items-center gap-2 lg:gap-3">
                
                {{-- Bell notif - gaya pengelola --}}
                <a href="{{ route('admin.notifications') }}"
                   class="relative w-9 h-9 rounded-xl bg-orange-50 hover:bg-orange-100
                          flex items-center justify-center transition-colors"
                   id="notif-bell">
                    <i class="fa-solid fa-bell text-orange-500 text-sm"></i>
                    @if($adminUnreadCount > 0)
                    <span id="notif-badge"
                          class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white
                                 text-[10px] font-bold rounded-full flex items-center justify-center px-1">
                        {{ $adminUnreadCount > 99 ? '99+' : $adminUnreadCount }}
                    </span>
                    @endif
                </a>

                {{-- Profile Dropdown - gaya pengelola --}}
                <div class="relative" id="admin-profile-wrap">
                    <button onclick="toggleAdminDropdown()"
                            class="flex items-center gap-2 cursor-pointer group select-none">
                        <div class="w-10 h-10 rounded-xl overflow-hidden shadow">
                            @if(Auth::user()->photo)
                            <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                                class="w-full h-full object-cover" alt="foto">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-orange-400 to-orange-600
                                        flex items-center justify-center font-bold text-sm text-white">
                                {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->full_name ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-semibold text-stone-800 leading-none">
                                {{ Auth::user()->name ?? Auth::user()->full_name ?? 'Admin' }}
                            </p>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 group-hover:text-orange-500 transition hidden sm:block"></i>
                    </button>

                    <div id="admin-profile-dropdown"
                         class="absolute right-0 top-12 w-56 bg-white rounded-2xl shadow-xl
                                border border-[#F4E6D2] overflow-hidden z-[99999]">

                        {{-- User Info --}}
                        <div class="px-4 py-3.5 bg-[#FFF3E8] border-b border-[#F4E6D2]">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0 shadow">
                                    @if(Auth::user()->photo)
                                        <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                                            class="w-full h-full object-cover" alt="foto">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-orange-400 to-orange-600
                                                    flex items-center justify-center font-bold text-sm text-white">
                                            {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->full_name ?? 'A', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm text-stone-800 truncate">
                                        {{ Auth::user()->name ?? Auth::user()->full_name ?? 'Admin' }}
                                    </p>
                                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>


                        <div class="py-1.5">
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-stone-800 hover:bg-[#FFF3E8] transition-colors group">
                                <div class="w-7 h-7 rounded-lg bg-orange-50 group-hover:bg-orange-100 flex items-center justify-center transition-colors flex-shrink-0">
                                     <i class="fa-solid fa-gauge-high text-orange-500 text-xs"></i>
                                </div>
                                <span class="font-medium">Dashboard</span>
                            </a>
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-stone-800 hover:bg-[#FFF3E8] transition-colors group">
                                 <div class="w-7 h-7 rounded-lg bg-orange-50 group-hover:bg-orange-100 flex items-center justify-center transition-colors flex-shrink-0">
                                    <i class="fa-solid fa-user-pen text-orange-500 text-xs"></i>
                                </div>
                                <span class="font-medium">Edit Profil</span>
                            </a>
                        </div>

                        <div class="border-t border-border py-1.5">
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

        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @yield('content')
        </main>

        <footer class="flex-shrink-0 border-t border-border px-4 lg:px-6 py-3">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-1 text-xs text-slate-600">
                <span>&copy; {{ date('Y') }} <span class="text-primary-400 font-semibold">SmartCanteen</span> — Admin Panel</span>
            </div>
        </footer>
    </div>
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
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const isOpen = !sidebar.classList.contains('-translate-x-full');
    if (isOpen) {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.remove('visible');
    } else {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('visible');
    }
}
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.remove('visible');
}
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        document.getElementById('sidebar-overlay').classList.remove('visible');
    }
});

function refreshNotifCount() {
    fetch('{{ route("admin.notifications.count") }}')
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
    document.getElementById('admin-profile-dropdown').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('admin-profile-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('admin-profile-dropdown').classList.remove('open');
    }
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('admin-profile-dropdown').classList.remove('open');
    }
});

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