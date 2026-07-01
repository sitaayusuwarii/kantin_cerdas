@extends('layouts.admin')

@section('title', 'Kelola User — SmartCanteen Admin')
@section('page-title', 'Kelola User')
@section('page-subtitle', 'Manajemen akun siswa & staff SmartCanteen')

@push('styles')
<style>
    .kelola-user-page {
        --ku-orange: #f4510b;
        --ku-orange-dark: #ea580c;
        --ku-orange-soft: #fff7ed;
        --ku-orange-line: #fed7aa;
        --ku-text: #292524;
    }

    .kelola-user-page .glass-card {
        background: rgba(255, 255, 255, 0.96) !important;
        border: 1px solid rgba(253, 186, 116, 0.72) !important;
        box-shadow: 0 18px 45px rgba(124, 45, 18, 0.08) !important;
        backdrop-filter: blur(14px);
    }

    .kelola-user-page .table-row:hover {
        background: #fff7ed !important;
    }

    .kelola-user-page thead tr {
        background: linear-gradient(135deg, rgba(255, 247, 237, 0.95), rgba(255, 255, 255, 0.98));
    }

    .kelola-user-page input,
    .kelola-user-page select,
    .kelola-user-page textarea {
        border-color: var(--ku-orange-line) !important;
        background: #fffefd !important;
        color: var(--ku-text) !important;
    }

    .kelola-user-page input::placeholder,
    .kelola-user-page textarea::placeholder {
        color: #a8a29e !important;
    }

    .kelola-user-page input:focus,
    .kelola-user-page select:focus,
    .kelola-user-page textarea:focus {
        border-color: #fb923c !important;
        box-shadow: 0 0 0 4px rgba(251, 146, 60, 0.14);
    }

    .ku-modal-panel {
        max-height: min(88vh, 860px);
        overflow-y: auto;
        border-right: 5px solid var(--ku-orange);
    }

    .ku-modal-panel::-webkit-scrollbar {
        width: 6px;
    }

    .ku-modal-panel::-webkit-scrollbar-thumb {
        background: var(--ku-orange);
        border-radius: 999px;
    }

    .ku-modal-panel label {
        color: #44403c !important;
        font-size: 0.875rem !important;
        font-weight: 700 !important;
        margin-bottom: 0.55rem !important;
    }

    .ku-modal-panel input,
    .ku-modal-panel select,
    .ku-modal-panel textarea {
        border-radius: 1rem !important;
        padding: 0.85rem 1rem !important;
        font-size: 0.95rem !important;
    }

    .ku-soft-btn {
        background: #ffedd5 !important;
        border: 1px solid #fed7aa !important;
        color: #292524 !important;
    }

    .ku-soft-btn:hover {
        background: #fed7aa !important;
        color: #1c1917 !important;
    }

    .ku-primary-btn {
        background: var(--ku-orange) !important;
        color: #fff !important;
        box-shadow: 0 14px 28px rgba(244, 81, 11, 0.22);
    }

    .ku-primary-btn:hover {
        background: var(--ku-orange-dark) !important;
        transform: translateY(-1px);
    }

    .kelola-user-page .filter-chip {
        background: #fffefd !important;
        border-color: var(--ku-orange-line) !important;
        color: #57534e !important;
    }
</style>
@endpush

@section('content')

<div class="kelola-user-page">

{{-- ===== TOAST (server-side) ===== --}}
@if(session('toast'))
<div id="server-toast-data"
    data-msg="{{ session('toast.msg') }}"
    data-color="{{ session('toast.color') }}">
</div>
@endif

{{-- ===== STATS ROW ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6" style="position: relative; z-index: 1;">
        @php
        $stats = [
            ['label' => 'Total User',    'val' => $totalUsers,     'icon' => 'fa-users',         'c' => 'primary'],
            ['label' => 'Siswa',         'val' => $totalSiswa,     'icon' => 'fa-graduation-cap', 'c' => 'emerald'],
            ['label' => 'Pengelola',         'val' => $totalStaff,     'icon' => 'fa-id-badge',       'c' => 'amber'],
            ['label' => 'Admin','val' => $totalAdmin,'icon' => 'fa-user-shield','c' => 'blue'],
        ];
    @endphp
    @foreach($stats as $s)
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-{{ $s['c'] }}-500/10 border border-{{ $s['c'] }}-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid {{ $s['icon'] }} text-{{ $s['c'] }}-400 text-sm"></i>
        </div>
        <div>
            <p class="text-stone-800 font-bold text-xl leading-none">{{ number_format($s['val']) }}</p>
            <p class="text-stone-400 text-xs mt-0.5">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ===== FILTER / SEARCH BAR ===== --}}
<form method="GET" action="{{ route('admin.kelola-user') }}" id="filter-form">
<div class="glass-card rounded-2xl p-4 mb-4" style="overflow:visible; position:relative; z-index:10;">    <div class="flex flex-col sm:flex-row gap-3 overflow-visible">
        {{-- Status Filter Dropdown --}}
        <div class="relative" id="status-dropdown-wrap">
            <button type="button" onclick="toggleDropdown('status-dropdown')"
                class="filter-chip flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold bg-white border border-orange-200 text-stone-600 hover:border-orange-400 transition-all">
                <i class="fa-solid fa-circle-half-stroke text-orange-400 text-xs"></i>
                Status: {{ request('status') ? ucfirst(request('status')) : 'Semua' }}
                <i class="fa-solid fa-chevron-down text-stone-400 text-xs"></i>
            </button>
            <div id="status-dropdown"
                class="hidden absolute left-0 mt-1 w-44 bg-white border border-orange-100 rounded-xl shadow-lg z-[999]">
                @foreach(['' => 'Semua', 'active' => 'Aktif', 'suspended' => 'Tersuspend', 'inactive' => 'Non-aktif'] as $val => $label)
                <button type="submit" name="status" value="{{ $val }}"
                    class="w-full text-left px-4 py-2.5 text-xs font-semibold transition-all flex items-center gap-2
                    {{ request('status', '') === $val ? 'bg-orange-50 text-orange-600' : 'text-stone-600 hover:bg-orange-50' }}">
                    @if($val === '') <i class="fa-solid fa-list text-stone-400 text-xs"></i>
                    @elseif($val === 'active') <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
                    @elseif($val === 'suspended') <i class="fa-solid fa-user-slash text-red-400 text-xs"></i>
                    @else <i class="fa-solid fa-circle-minus text-stone-400 text-xs"></i>
                    @endif
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Role Filter Dropdown --}}
        <div class="relative" id="role-dropdown-wrap">
            <button type="button" onclick="toggleDropdown('role-dropdown')"
                class="filter-chip flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold bg-white border border-orange-200 text-stone-600 hover:border-orange-400 transition-all">
                <i class="fa-solid fa-users text-orange-400 text-xs"></i>
                Role: {{ request('role') ? ucfirst(request('role')) : 'Semua' }}
                <i class="fa-solid fa-chevron-down text-stone-400 text-xs"></i>
            </button>
            <div id="role-dropdown"
                class="hidden absolute left-0 mt-1 w-44 bg-white border border-orange-100 rounded-xl shadow-lg z-[999]">
                @foreach(['' => 'Semua Role', 'customer' => 'Customer', 'kasir' => 'Kasir', 'pengelola' => 'Pengelola', 'admin' => 'Admin'] as $val => $label)
                <button type="submit" name="role" value="{{ $val }}"
                    class="w-full text-left px-4 py-2.5 text-xs font-semibold transition-all flex items-center gap-2
                    {{ request('role', '') === $val ? 'bg-orange-50 text-orange-600' : 'text-stone-600 hover:bg-orange-50' }}">
                    @if($val === '') <i class="fa-solid fa-layer-group text-stone-400 text-xs"></i>
                    @elseif($val === 'customer') <i class="fa-solid fa-graduation-cap text-emerald-400 text-xs"></i>
                    @elseif($val === 'kasir') <i class="fa-solid fa-utensils text-amber-400 text-xs"></i>
                    @elseif($val === 'pengelola') <i class="fa-solid fa-id-badge text-amber-400 text-xs"></i>
                    @else <i class="fa-solid fa-user-shield text-blue-400 text-xs"></i>
                    @endif
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        <div class="flex gap-2 sm:ml-auto flex-wrap">
            {{-- Search --}}
            <div class="flex items-center gap-2 bg-orange-50 border border-border rounded-xl px-3 py-2 {{ request('search') ? 'border-primary-500/40' : '' }}">
                <i class="fa-solid fa-search text-stone-400 text-xs"></i>
                <input
                    type="text"
                    id="auto-search-user"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama / username..."
                    class="bg-transparent text-sm text-stone-600 placeholder-slate-600 outline-none w-44">
            </div>
            {{-- Search submit --}}
           
            {{-- Reset --}}
            @if(request('search') || request('status') || request('role'))
            <a href="{{ route('admin.kelola-user') }}"
               class="ku-soft-btn flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition-all">
                <i class="fa-solid fa-xmark text-xs"></i>
            </a>
            @endif
            {{-- Tambah User --}}
            <button type="button"
                onclick="openModal('modal-add-user')"
                class="ku-primary-btn flex items-center gap-2 px-3 py-2 rounded-xl text-white transition-all text-xs font-semibold">
                <i class="fa-solid fa-plus"></i>
                <span class="hidden sm:inline">Tambah User</span>
            </button>
        </div>
    </div>
</div>
</form>

{{-- ===== DESKTOP TABLE ===== --}}
<div class="hidden lg:block glass-card rounded-2xl overflow-hidden" style="position:relative; z-index:1;">
    <div class="px-5 py-4 border-b border-border flex items-center justify-between">
        <h2 class="text-stone-800 font-bold text-base">Daftar User</h2>
        <span class="text-stone-400 text-xs">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} data
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Nama User</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Username</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Kelas</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Transaksi</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Terdaftar</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @forelse($users as $user)
                @php
                    $initials  = collect(explode(' ', $user->full_name))->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
                    $statusCfg = match($user->status) {
                        'suspended' => ['bg-red-500/10',    'border-red-500/20',    'text-red-400',    'Tersuspend'],
                        'inactive'  => ['bg-slate-500/10',  'border-slate-500/20',  'text-stone-500',  'Non-aktif'],
                        default     => ['bg-emerald-500/10','border-emerald-500/20','text-emerald-400','Aktif'],
                    };
                    $isSuspended   = $user->status === 'suspended';
                    $suspendLabel  = $isSuspended ? 'Aktifkan' : 'Suspend';
                    $suspendIcon   = $isSuspended ? 'fa-user-check' : 'fa-user-slash';
                    $suspendConfirm = $isSuspended ? 'Aktifkan user ini?' : 'Suspend user ini?';

                    $detailData = json_encode([
                        'id'           => $user->id,
                        'full_name'    => $user->full_name,
                        'username'     => $user->username,
                        'phone'        => $user->phone,
                        'kelas'        => $user->kelas ?? '—',
                        'status'       => $user->status,
                        'orders_count' => $user->orders_count,
                        'created_at'   => $user->created_at->translatedFormat('d M Y'),
                        'role'         => $user->role,
                        'photo'        => $user->photo
                    ], JSON_HEX_QUOT | JSON_HEX_APOS);

                    $editData = json_encode([
                        'id'        => $user->id,
                        'full_name' => $user->full_name,
                        'username'  => $user->username,
                        'phone'     => $user->phone,
                        'kelas'     => $user->kelas ?? '',
                        'status'    => $user->status,
                        'role'      => $user->role,
                    ], JSON_HEX_QUOT | JSON_HEX_APOS);
                @endphp
                <tr class="table-row hover:bg-white/[0.02] transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                           <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0">
                                @if($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}"
                                        class="w-full h-full object-cover" alt="foto">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-500/20 to-primary-700/20
                                                border border-primary-500/20 flex items-center justify-center
                                                text-primary-300 text-xs font-bold">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-stone-800 text-sm font-semibold">{{ $user->full_name }}</p>
                                <p class="text-stone-400 text-xs">{{ $user->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-stone-600 text-sm font-mono">{{ $user->username }}</span>
                    </td>
                    <td class="px-5 py-4 text-stone-600 text-sm">{{ $user->role === 'customer' ? ($user->kelas ?? '—') : '—' }}</td>
                    <td class="px-5 py-4">
                        <span class="text-stone-800 font-bold font-mono text-sm">{{ $user->orders_count }}</span>
                        <span class="text-stone-400 text-xs ml-1">transaksi</span>
                    </td>
                    <td class="px-5 py-4 text-stone-500 text-xs">
                        {{ $user->created_at->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusCfg[0] }} border {{ $statusCfg[1] }} {{ $statusCfg[2] }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ $statusCfg[3] }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            {{-- Detail --}}
                            <button
                                onclick="showDetail({{ $detailData }})"
                                title="Detail"
                                class="w-8 h-8 rounded-lg bg-orange-100/50 hover:bg-primary-500/20 border border-border hover:border-primary-500/30 text-stone-500 hover:text-primary-400 flex items-center justify-center transition-all">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                            {{-- Edit --}}
                            <button
                                onclick="openEditUser({{ $editData }})"
                                title="Edit"
                                class="w-8 h-8 rounded-lg bg-orange-100/50 hover:bg-amber-500/20 border border-border hover:border-amber-500/30 text-stone-500 hover:text-amber-400 flex items-center justify-center transition-all">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                            {{-- Suspend / Aktifkan --}}
                            <form method="POST"
                                action="{{ route('admin.users.toggle-suspend', $user) }}"
                                class="inline form-toggle-suspend"
                                data-user="{{ $user->full_name }}"
                                data-action="{{ $suspendLabel }}"
                                data-confirm="{{ $suspendConfirm }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    title="{{ $suspendLabel }}"
                                    class="w-8 h-8 rounded-lg bg-orange-100/50 hover:bg-amber-500/20 border border-border hover:border-amber-500/30 text-stone-500 hover:text-amber-400 flex items-center justify-center transition-all">
                                    <i class="fa-solid {{ $suspendIcon }} text-xs"></i>
                                </button>
                            </form>
                            {{-- Delete --}}
                            <button
                                onclick="openDelete({{ $user->id }}, '{{ addslashes($user->full_name) }}')"
                                title="Hapus"
                                class="w-8 h-8 rounded-lg bg-orange-100/50 hover:bg-red-500/20 border border-border hover:border-red-500/30 text-stone-500 hover:text-red-400 flex items-center justify-center transition-all">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-14 text-center">
                        <div class="flex flex-col items-center gap-3 text-stone-400">
                            <i class="fa-solid fa-users-slash text-3xl"></i>
                            <p class="text-sm">Tidak ada user ditemukan</p>
                            @if(request('search') || request('status') || request('role'))
                            <a href="{{ route('admin.kelola-user') }}" class="text-primary-400 text-xs hover:underline">Reset filter</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-stone-400 text-xs">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} user
        </p>
        <div class="flex items-center gap-1">
            {{-- Prev --}}
            @if($users->onFirstPage())
            <span class="w-8 h-8 rounded-lg bg-orange-50 border border-border text-stone-400 flex items-center justify-center cursor-not-allowed">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </span>
            @else
            <a href="{{ $users->previousPageUrl() }}"
               class="w-8 h-8 rounded-lg bg-orange-100/50 border border-border text-stone-500 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
            @endif

            {{-- Page numbers --}}
            @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
            <a href="{{ $url }}"
               class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center transition-all
               {{ $page == $users->currentPage()
                    ? 'bg-primary-500 text-white border border-primary-500'
                    : 'bg-orange-100/50 border border-border text-stone-500 hover:border-primary-500 hover:text-primary-400' }}">
                {{ $page }}
            </a>
            @endforeach

            {{-- Next --}}
            @if($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}"
               class="w-8 h-8 rounded-lg bg-orange-100/50 border border-border text-stone-500 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
            @else
            <span class="w-8 h-8 rounded-lg bg-orange-50 border border-border text-stone-400 flex items-center justify-center cursor-not-allowed">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ===== MOBILE CARD LIST ===== --}}
<div class="lg:hidden space-y-3">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-stone-800 font-bold">Daftar User</h2>
        <span class="text-stone-400 text-xs">{{ $users->total() }} total</span>
    </div>
    <div class="space-y-3">
        @forelse($users as $user)
        @php
            $initials  = collect(explode(' ', $user->full_name))->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
            $statusCfg = match($user->status) {
                'suspended' => ['bg-red-500/10',    'border-red-500/20',    'text-red-400',    'Tersuspend'],
                'inactive'  => ['bg-slate-500/10',  'border-slate-500/20',  'text-stone-500',  'Non-aktif'],
                default     => ['bg-emerald-500/10','border-emerald-500/20','text-emerald-400','Aktif'],
            };
            $isSuspended    = $user->status === 'suspended';
            $suspendIcon    = $isSuspended ? 'fa-user-check' : 'fa-user-slash';
            $suspendConfirm = $isSuspended ? 'Aktifkan user ini?' : 'Suspend user ini?';

            $detailData = json_encode([
                'id'           => $user->id,
                'full_name'    => $user->full_name,
                'username'     => $user->username,
                'phone'        => $user->phone,
                'kelas'        => $user->kelas ?? '—',
                'status'       => $user->status,
                'orders_count' => $user->orders_count,
                'created_at'   => $user->created_at->translatedFormat('d M Y'),
                'role'         => $user->role,
            ], JSON_HEX_QUOT | JSON_HEX_APOS);

            $editData = json_encode([
                'id'        => $user->id,
                'full_name' => $user->full_name,
                'username'  => $user->username,
                'phone'     => $user->phone,
                'kelas'     => $user->kelas ?? '',
                'status'    => $user->status,
                'role'      => $user->role,
            ], JSON_HEX_QUOT | JSON_HEX_APOS);
        @endphp
        <div class="glass-card rounded-2xl p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}"
                                class="w-full h-full object-cover" alt="foto">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary-500/20 to-primary-700/20
                                        border border-primary-500/20 flex items-center justify-center
                                        text-primary-300 text-sm font-bold">
                                {{ $initials }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-stone-800 font-semibold text-sm">{{ $user->full_name }}</p>
                        <p class="text-stone-400 text-xs font-mono">{{ $user->username }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusCfg[0] }} border {{ $statusCfg[1] }} {{ $statusCfg[2] }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ $statusCfg[3] }}
                </span>
            </div>
            <div class="grid grid-cols-3 gap-2 mb-3">
                <div class="bg-orange-50/60 rounded-lg p-2.5">
                    <p class="text-stone-400 text-xs mb-0.5">Kelas</p>
                    <p class="text-stone-600 text-xs truncate">{{ $user->role === 'customer' ? ($user->kelas ?? '—') : '—' }}</p>
                </div>
                <div class="bg-orange-50/60 rounded-lg p-2.5">
                    <p class="text-stone-400 text-xs mb-0.5">Transaksi</p>
                    <p class="text-stone-800 font-bold font-mono text-xs">{{ $user->orders_count }}x</p>
                </div>
                <div class="bg-orange-50/60 rounded-lg p-2.5">
                    <p class="text-stone-400 text-xs mb-0.5">Daftar</p>
                    <p class="text-stone-600 text-xs">{{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button
                    onclick="showDetail({{ $detailData }})"
                    class="flex-1 py-2 rounded-xl bg-orange-50 border border-border text-stone-500 text-xs font-medium hover:border-primary-500 hover:text-primary-400 transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-eye text-xs"></i> Detail
                </button>
                <button
                    onclick="openEditUser({{ $editData }})"
                    class="flex-1 py-2 rounded-xl bg-orange-50 border border-border text-stone-500 text-xs font-medium hover:border-amber-500 hover:text-amber-400 transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-pen text-xs"></i> Edit
                </button>
                <form method="POST"
                    action="{{ route('admin.users.toggle-suspend', $user) }}"
                    class="inline form-toggle-suspend"
                    data-user="{{ $user->full_name }}"
                    data-action="{{ $isSuspended ? 'Aktifkan' : 'Suspend' }}"
                    data-confirm="{{ $suspendConfirm }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="py-2 px-3 rounded-xl bg-orange-50 border border-border text-stone-500 text-xs hover:border-amber-500 hover:text-amber-400 transition-all flex items-center justify-center">
                        <i class="fa-solid {{ $suspendIcon }} text-xs"></i>
                    </button>
                </form>
                <button
                    onclick="openDelete({{ $user->id }}, '{{ addslashes($user->full_name) }}')"
                    class="py-2 px-3 rounded-xl bg-orange-50 border border-border text-stone-500 text-xs hover:border-red-500 hover:text-red-400 transition-all flex items-center justify-center">
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-stone-400 text-sm">
            <i class="fa-solid fa-users-slash text-2xl block mb-3"></i>
            Tidak ada user ditemukan
        </div>
        @endforelse
    </div>

    {{-- Mobile Pagination --}}
    @if($users->hasPages())
    <div class="flex justify-between items-center pt-2">
        @if($users->onFirstPage())
        <span class="px-4 py-2 rounded-xl bg-orange-50 border border-border text-stone-400 text-xs cursor-not-allowed">← Sebelumnya</span>
        @else
        <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-orange-50 border border-border text-stone-600 text-xs hover:border-primary-500 hover:text-primary-400 transition-all">← Sebelumnya</a>
        @endif

        <span class="text-stone-400 text-xs">{{ $users->currentPage() }} / {{ $users->lastPage() }}</span>

        @if($users->hasMorePages())
        <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-orange-50 border border-border text-stone-600 text-xs hover:border-primary-500 hover:text-primary-400 transition-all">Berikutnya →</a>
        @else
        <span class="px-4 py-2 rounded-xl bg-orange-50 border border-border text-stone-400 text-xs cursor-not-allowed">Berikutnya →</span>
        @endif
    </div>
    @endif
</div>

{{-- ===== MODAL: TAMBAH USER ===== --}}
<div id="modal-add-user" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-950/65 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="ku-modal-panel bg-white rounded-3xl p-6 sm:p-8 max-w-3xl w-full mx-4 border border-orange-200 shadow-2xl">
        <div class="flex items-start justify-between mb-7">
            <div>
                <h3 class="text-stone-900 font-bold text-2xl flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-orange-500"></i> Tambah User Baru
                </h3>
                <p class="text-stone-400 text-sm mt-1">Lengkapi data akun baru untuk SmartCanteen</p>
            </div>
            <button onclick="closeModal('modal-add-user')" class="w-12 h-12 rounded-xl bg-orange-100 hover:bg-orange-200 hover:text-orange-700 flex items-center justify-center text-stone-500 transition-all">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" id="form-add-user" enctype="multipart/form-data">            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-stone-400 text-xs font-semibold block mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input name="full_name" type="text" placeholder="cth. Mawar" required
                        class="w-full bg-orange-50 border border-border rounded-xl px-3 py-2.5 text-stone-700 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>
                <div>
                    <label class="text-stone-400 text-xs font-semibold block mb-1.5">Username <span class="text-red-400">*</span></label>
                    <input name="username" type="text" placeholder="cth. mawar" required
                        class="w-full bg-orange-50 border border-border rounded-xl px-3 py-2.5 text-stone-700 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>
                <div>
                    <label class="text-stone-400 text-xs font-semibold block mb-1.5">Role <span class="text-red-400">*</span></label>
                    <select name="role" id="add-role-select" onchange="toggleTenantFields()"
                        class="w-full bg-orange-50 border border-border rounded-xl px-3 py-2.5 text-stone-700 text-sm outline-none focus:border-primary-500 transition-colors cursor-pointer">
                        <option value="customer">Customer (Siswa)</option>
                        <option value="kasir">Kasir</option>
                        <option value="pengelola">Pengelola</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="text-stone-400 text-xs font-semibold block mb-1.5">No. HP <span class="text-red-400">*</span></label>
                    <input name="phone" type="text" placeholder="08xx-xxxx-xxxx" required
                        class="w-full bg-orange-50 border border-border rounded-xl px-3 py-2.5 text-stone-700 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>

                {{-- FIELD KELAS - hanya untuk customer --}}
                <div id="add-kelas-wrap">
                    <label class="text-stone-400 text-xs font-semibold block mb-1.5">Kelas</label>
                    <input name="kelas" id="add-kelas-input" type="text" placeholder="cth. TRPL A"
                        class="w-full bg-orange-50 border border-border rounded-xl px-3 py-2.5 text-stone-700 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>

                {{-- FIELD TENANT - muncul hanya kalau role = pengelola --}}
                <div id="tenant-fields" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl border border-orange-200 bg-orange-50/70">
                    <p class="md:col-span-2 text-orange-600 text-sm font-bold flex items-center gap-2">
                        <i class="fa-solid fa-store text-xs"></i> Informasi Tenant / Stan
                    </p>
                    <div>
                        <label class="text-stone-400 text-xs font-semibold block mb-1.5">Nama Stan <span class="text-red-400">*</span></label>
                        <input name="tenant_name" id="tenant-name-input" type="text" placeholder="cth. Stan Bakso Pak Budi"
                            class="w-full bg-orange-50 border border-border rounded-xl px-3 py-2.5 text-stone-700 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-stone-400 text-xs font-semibold block mb-1.5">Deskripsi</label>
                        <input name="tenant_description" type="text" placeholder="cth. Menyediakan bakso dan mie ayam"
                            class="w-full bg-orange-50 border border-border rounded-xl px-3 py-2.5 text-stone-700 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-stone-400 text-xs font-semibold block mb-1.5">Logo Stan</label>
                        <input name="tenant_logo" type="file" accept="image/*"
                            class="w-full bg-orange-50 border border-border rounded-xl px-3 py-2.5 text-stone-700 text-sm outline-none focus:border-primary-500 transition-colors">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="text-stone-400 text-xs font-semibold block mb-1.5">Password Awal <span class="text-red-400">*</span></label>
                    <input name="password" type="password" placeholder="Min. 6 karakter" required minlength="6"
                        class="w-full bg-orange-50 border border-border rounded-xl px-3 py-2.5 text-stone-700 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>
            </div>
            <div class="mt-7 grid grid-cols-2 gap-4">
                <button type="button" onclick="closeModal('modal-add-user')"
                    class="ku-soft-btn px-4 py-3.5 rounded-2xl text-sm font-bold transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="ku-primary-btn px-4 py-3.5 rounded-2xl text-white text-sm font-bold transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL: EDIT USER ===== --}}
<div id="modal-edit-user" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-950/65 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="ku-modal-panel bg-white rounded-3xl w-full max-w-3xl mx-4 overflow-hidden shadow-2xl border border-orange-200">

        {{-- Header --}}
        <div class="px-6 pt-6 pb-5 border-b border-orange-100"
             style="background: linear-gradient(135deg, rgba(249,115,22,0.06), rgba(234,88,12,0.02))">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20
                                flex items-center justify-center">
                        <i class="fa-solid fa-user-pen text-amber-500 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-stone-900 font-bold text-2xl">Edit User</h3>
                        <p class="text-stone-400 text-sm">Perbarui informasi akun</p>
                    </div>
                </div>
                <button onclick="closeModal('modal-edit-user')"
                        class="w-8 h-8 rounded-xl bg-orange-50 hover:bg-red-50 hover:text-red-400
                               flex items-center justify-center text-stone-400 transition-all border border-orange-100">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <form method="POST" id="form-edit-user">
            @csrf @method('PUT')

            <div class="px-6 sm:px-8 py-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Nama Lengkap --}}
                <div>
                    <label class="text-xs font-semibold text-stone-500 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-user text-orange-300 text-[10px]"></i>Nama Lengkap
                    </label>
                    <input id="edit-full_name" name="full_name" type="text" required
                           class="w-full bg-orange-50 border border-orange-100 rounded-xl px-3 py-2.5
                                  text-stone-700 text-sm outline-none focus:border-orange-400
                                  focus:ring-2 focus:ring-orange-100 transition-all">
                </div>

                {{-- Username & Status --}}
                <div>
                    <label class="text-xs font-semibold text-stone-500 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-at text-orange-300 text-[10px]"></i>Username
                    </label>
                    <input id="edit-username" name="username" type="text" required
                           class="w-full bg-orange-50 border border-orange-100 rounded-xl px-3 py-2.5
                                  text-stone-700 text-sm outline-none focus:border-orange-400
                                  focus:ring-2 focus:ring-orange-100 transition-all">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-500 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-half-stroke text-orange-300 text-[10px]"></i>Status
                    </label>
                    <select id="edit-status" name="status"
                            class="w-full bg-orange-50 border border-orange-100 rounded-xl px-3 py-2.5
                                   text-stone-700 text-sm outline-none focus:border-orange-400
                                   focus:ring-2 focus:ring-orange-100 transition-all cursor-pointer">
                        <option value="active">✅ Aktif</option>
                        <option value="suspended">🚫 Tersuspend</option>
                        <option value="inactive">⭕ Non-aktif</option>
                    </select>
                </div>

                {{-- No HP --}}
                <div>
                    <label class="text-xs font-semibold text-stone-500 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-phone text-orange-300 text-[10px]"></i>No. HP
                    </label>
                    <input id="edit-phone" name="phone" type="text" required
                           class="w-full bg-orange-50 border border-orange-100 rounded-xl px-3 py-2.5
                                  text-stone-700 text-sm outline-none focus:border-orange-400
                                  focus:ring-2 focus:ring-orange-100 transition-all">
                </div>

                {{-- Kelas — hanya untuk customer --}}
                <div id="edit-kelas-wrap">
                    <label class="text-xs font-semibold text-stone-500 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-graduation-cap text-orange-300 text-[10px]"></i>Kelas
                    </label>
                    <input id="edit-kelas" name="kelas" type="text"
                           placeholder="cth. XII IPA 1"
                           class="w-full bg-orange-50 border border-orange-100 rounded-xl px-3 py-2.5
                                  text-stone-700 text-sm placeholder-stone-300 outline-none focus:border-orange-400
                                  focus:ring-2 focus:ring-orange-100 transition-all">
                </div>

                {{-- Password --}}
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-stone-500 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-lock text-orange-300 text-[10px]"></i>
                        Password Baru
                        <span class="text-stone-300 font-normal">(kosongkan jika tidak diganti)</span>
                    </label>
                    <input id="edit-password" name="password" type="password"
                           placeholder="Min. 6 karakter"
                           class="w-full bg-orange-50 border border-orange-100 rounded-xl px-3 py-2.5
                                  text-stone-700 text-sm placeholder-stone-300 outline-none focus:border-orange-400
                                  focus:ring-2 focus:ring-orange-100 transition-all">
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 sm:px-8 pb-7 grid grid-cols-2 gap-4">
                <button type="button" onclick="closeModal('modal-edit-user')"
                        class="ku-soft-btn flex-1 py-3.5 rounded-2xl text-sm font-bold transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="ku-primary-btn flex-1 py-3.5 rounded-2xl
                               text-white text-sm font-bold transition-colors shadow-sm
                               flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL: DETAIL USER ===== --}}
<div id="modal-detail-user" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-950/65 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="ku-modal-panel bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full mx-4 border border-orange-200 shadow-2xl">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-stone-900 font-bold text-2xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-orange-500"></i> Detail User
                </h3>
                <p class="text-stone-400 text-sm mt-1">Ringkasan informasi akun pengguna</p>
            </div>
            <button onclick="closeModal('modal-detail-user')" class="w-12 h-12 rounded-xl bg-orange-100 hover:bg-orange-200 hover:text-orange-700 flex items-center justify-center text-stone-500 transition-all">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div id="detail-user-content" class="space-y-3 text-sm">
            {{-- Filled by JS --}}
        </div>
    </div>
</div>

{{-- ===== MODAL: KONFIRMASI SUSPEND / AKTIFKAN ===== --}}
<div id="modal-suspend-confirm" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-950/65 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="ku-modal-panel bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full mx-4 border border-orange-200 shadow-2xl">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="text-stone-900 font-bold text-xl flex items-center gap-2">
                    <i class="fa-solid fa-user-slash text-orange-500"></i> Konfirmasi Status User
                </h3>
                <p class="text-stone-400 text-sm mt-1">Pastikan akun yang dipilih sudah benar</p>
            </div>
            <button onclick="closeModal('modal-suspend-confirm')" class="w-10 h-10 rounded-xl bg-orange-100 hover:bg-orange-200 hover:text-orange-700 flex items-center justify-center text-stone-500 transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="p-4 rounded-2xl bg-orange-50 border border-orange-100 mb-5">
            <p id="suspend-confirm-message" class="text-stone-700 text-sm leading-relaxed">
                Apakah Anda yakin ingin mengubah status user ini?
            </p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <button type="button" onclick="closeModal('modal-suspend-confirm')"
                class="ku-soft-btn px-4 py-3 rounded-2xl text-sm font-bold transition-all">
                Batal
            </button>
            <button type="button" id="btn-confirm-suspend"
                class="ku-primary-btn px-4 py-3 rounded-2xl text-white text-sm font-bold transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-check text-xs"></i> Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

{{-- ===== MODAL: HAPUS USER ===== --}}
<div id="modal-delete" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-950/65 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="ku-modal-panel bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full mx-4 border border-orange-200 shadow-2xl">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="text-stone-900 font-bold text-xl flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-500"></i> Hapus User
                </h3>
                <p class="text-stone-400 text-sm mt-1">Konfirmasi sebelum data dihapus</p>
            </div>
            <button onclick="closeModal('modal-delete')" class="w-10 h-10 rounded-xl bg-orange-100 hover:bg-orange-200 hover:text-orange-700 flex items-center justify-center text-stone-500 transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="p-4 rounded-2xl bg-red-50 border border-red-100 mb-5">
            <p class="text-stone-600 text-sm">
                Anda yakin ingin menghapus akun <strong id="delete-name" class="text-red-700"></strong>?
                Semua data transaksi terkait akan ikut terhapus dan tindakan ini
                <span class="text-red-400 font-semibold">tidak dapat dibatalkan</span>.
            </p>
        </div>
        <form method="POST" id="form-delete-user">
            @csrf @method('DELETE')
            <div class="grid grid-cols-2 gap-4">
                <button type="button" onclick="closeModal('modal-delete')"
                    class="ku-soft-btn px-4 py-3 rounded-2xl text-sm font-bold transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-3 rounded-2xl bg-red-500 hover:bg-red-600 text-white text-sm font-bold transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-trash text-xs"></i> Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== TOAST ===== --}}
<div id="toast"
    class="fixed bottom-6 right-6 z-[100] flex items-center gap-3 px-4 py-3 rounded-2xl bg-white border border-orange-200 shadow-2xl text-sm font-semibold text-stone-700 opacity-0 pointer-events-none transition-all duration-300 translate-y-3">
    <i id="toast-icon" class="fa-solid fa-circle-check text-emerald-400"></i>
    <span id="toast-msg">Berhasil</span>
</div>

</div>

@endsection

@push('scripts')
<script>
// ── Route helpers ────────────────────────────────────────────────────────────
const ROUTES = {
    update : (id) => `/admin/users/${id}`,
    delete : (id) => `/admin/users/${id}`,
};

// ── Status / role config ─────────────────────────────────────────────────────
const statusConfig = {
    active    : { label: 'Aktif',      bg: 'bg-emerald-500/10', border: 'border-emerald-500/20', text: 'text-emerald-400' },
    suspended : { label: 'Tersuspend', bg: 'bg-red-500/10',     border: 'border-red-500/20',     text: 'text-red-400' },
    inactive  : { label: 'Non-aktif',  bg: 'bg-slate-500/10',   border: 'border-slate-500/20',   text: 'text-stone-500' },
};

const roleLabel = { customer: 'Customer', pengelola: 'Pengelola', kasir: 'Kasir', admin: 'Admin' };

// ── Modal utils ───────────────────────────────────────────────────────────────
function openModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('opacity-0','pointer-events-none');
    m.classList.add('opacity-100','pointer-events-auto');
}
function closeModal(id) {
    const m = document.getElementById(id);
    m.classList.add('opacity-0','pointer-events-none');
    m.classList.remove('opacity-100','pointer-events-auto');
}

// Close on overlay click
['modal-add-user','modal-edit-user','modal-detail-user','modal-suspend-confirm','modal-delete'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

// ── Suspend / activate confirmation modal ────────────────────────────────────
let pendingSuspendForm = null;

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.querySelectorAll('.form-toggle-suspend').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        pendingSuspendForm = this;

        const action = this.dataset.action || 'Ubah Status';
        const user = this.dataset.user || 'user ini';
        const confirmText = this.dataset.confirm || 'Ubah status user ini?';

        document.getElementById('suspend-confirm-message').innerHTML = `
            ${escapeHtml(confirmText)}<br>
            <span class="block mt-2 text-stone-500">
                Akun: <strong class="text-stone-900">${escapeHtml(user)}</strong>
            </span>
        `;
        document.getElementById('btn-confirm-suspend').innerHTML = `
            <i class="fa-solid fa-check text-xs"></i> Ya, ${action}
        `;

        openModal('modal-suspend-confirm');
    });
});

document.getElementById('btn-confirm-suspend')?.addEventListener('click', function() {
    if (!pendingSuspendForm) return;
    const form = pendingSuspendForm;
    pendingSuspendForm = null;
    closeModal('modal-suspend-confirm');
    form.submit();
});

// ── Detail modal ──────────────────────────────────────────────────────────────
function showDetail(u) {
    const initials = u.full_name.split(' ').map(w => w[0].toUpperCase()).join('').slice(0, 2);
    const sc = statusConfig[u.status] || statusConfig.inactive;
    const roleColors = {
        customer  : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
        pengelola : 'bg-amber-500/10 border-amber-500/20 text-amber-400',
        kasir     : 'bg-blue-500/10 border-blue-500/20 text-blue-400',
        admin     : 'bg-primary-500/10 border-primary-500/20 text-primary-400',
    };
    const roleColor = roleColors[u.role] || roleColors.admin;

    const avatarHtml = u.photo
        ? `<img src="/storage/${u.photo}" class="w-full h-full object-cover" alt="foto">`
        : `<span class="text-primary-300 text-xl font-bold">${initials}</span>`;

    // Hanya tampilkan baris Kelas jika role = customer
    const kelasRow = u.role === 'customer'
        ? `['fa-graduation-cap','Kelas', u.kelas || '—'],`
        : '';

    const infoRows = [
        ['fa-phone',       'No. HP',         u.phone || '—'],
        ...(u.role === 'customer' ? [['fa-graduation-cap', 'Kelas', u.kelas || '—']] : []),
        ['fa-receipt',     'Total Transaksi', `<span class="font-mono font-bold text-primary-400">${u.orders_count}x</span>`],
        ['fa-calendar',    'Terdaftar Sejak', u.created_at],
        ['fa-fingerprint', 'ID User',         `<span class="font-mono text-primary-400">#USR-${String(u.id).padStart(4,'0')}</span>`],
    ];

    document.getElementById('detail-user-content').innerHTML = `
        <div class="flex items-center gap-4 p-4 rounded-2xl mb-2"
             style="background: linear-gradient(135deg, rgba(249,115,22,0.08), rgba(234,88,12,0.04)); border: 1px solid rgba(249,115,22,0.15)">
            <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gradient-to-br from-primary-500/20 to-primary-700/20
                        border-2 border-primary-500/20 flex items-center justify-center flex-shrink-0 shadow-md">
                ${avatarHtml}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-stone-800 font-bold text-lg leading-tight truncate">${u.full_name}</p>
                <p class="text-stone-400 text-xs font-mono mt-0.5">@${u.username}</p>
                <div class="flex gap-1.5 mt-2 flex-wrap">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold border ${roleColor}">
                        <i class="fa-solid fa-id-badge text-[10px]"></i>
                        ${roleLabel[u.role] || u.role}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold ${sc.bg} border ${sc.border} ${sc.text}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        ${sc.label}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
            ${infoRows.map(([icon, k, v]) => `
            <div class="bg-white rounded-xl p-3 border border-stone-100 shadow-sm">
                <p class="text-stone-400 text-[10px] font-semibold uppercase tracking-wide flex items-center gap-1.5 mb-1.5">
                    <i class="fa-solid ${icon} text-[9px] text-emerald-400"></i>${k}
                </p>
                <p class="text-stone-700 text-sm font-medium">${v}</p>
            </div>
        `).join('')}
        </div>
    `;
    openModal('modal-detail-user');
}

// ── Edit modal ────────────────────────────────────────────────────────────────
function openEditUser(u) {
    const form = document.getElementById('form-edit-user');
    form.action = ROUTES.update(u.id);

    document.getElementById('edit-full_name').value = u.full_name;
    document.getElementById('edit-username').value  = u.username;
    document.getElementById('edit-phone').value     = u.phone;
    document.getElementById('edit-kelas').value     = u.kelas || '';
    document.getElementById('edit-status').value    = u.status;
    document.getElementById('edit-password').value  = '';

    // Tampilkan field kelas hanya untuk customer
    const kelasWrap = document.getElementById('edit-kelas-wrap');
    if (u.role === 'customer') {
        kelasWrap.classList.remove('hidden');
    } else {
        kelasWrap.classList.add('hidden');
    }

    openModal('modal-edit-user');
}

// ── Delete modal ──────────────────────────────────────────────────────────────
function openDelete(id, name) {
    const form = document.getElementById('form-delete-user');
    form.action = ROUTES.delete(id);
    document.getElementById('delete-name').textContent = name;
    openModal('modal-delete');
}

// ── Toast (client) ────────────────────────────────────────────────────────────
function showToast(msg, color = 'emerald') {
    const colorMap = { emerald:'text-emerald-400', red:'text-red-400', amber:'text-amber-400', blue:'text-blue-400' };
    const iconMap  = { emerald:'fa-circle-check', red:'fa-circle-xmark', amber:'fa-triangle-exclamation', blue:'fa-circle-info' };
    const toast    = document.getElementById('toast');

    document.getElementById('toast-icon').className = `fa-solid ${iconMap[color]||'fa-circle-check'} ${colorMap[color]||'text-emerald-400'}`;
    document.getElementById('toast-msg').textContent = msg;

    toast.classList.remove('opacity-0','pointer-events-none','translate-y-3');
    toast.classList.add('opacity-100','pointer-events-auto','translate-y-0');

    setTimeout(() => {
        toast.classList.add('opacity-0','pointer-events-none','translate-y-3');
        toast.classList.remove('opacity-100','pointer-events-auto','translate-y-0');
    }, 3200);
}

// ── Auto-show toast from server session ──────────────────────────────────────
const serverToast = document.getElementById('server-toast-data');
if (serverToast) {
    showToast(serverToast.dataset.msg, serverToast.dataset.color || 'emerald');
}
    
// ── Re-open add modal on validation error ────────────────────────────────────
@if($errors->any())
openModal('modal-add-user');
showToast('{{ $errors->first() }}', 'red');
@endif  

// ── Dropdown toggle ───────────────────────────────────────────────────────────
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    const allDropdowns = ['status-dropdown', 'role-dropdown'];
    allDropdowns.forEach(d => {
        if (d !== id) document.getElementById(d).classList.add('hidden');
    });
    dropdown.classList.toggle('hidden');
}

// Tutup dropdown kalau klik di luar
document.addEventListener('click', function(e) {
    const wraps = ['status-dropdown-wrap', 'role-dropdown-wrap'];
    wraps.forEach(wrapId => {
        const wrap = document.getElementById(wrapId);
        if (wrap && !wrap.contains(e.target)) {
            const dropId = wrapId.replace('-wrap', '');
            document.getElementById(dropId)?.classList.add('hidden');
        }
    });
});

// ── Toggle field tenant & kelas (modal tambah) ────────────────────────────────
function toggleTenantFields() {
    const role      = document.getElementById('add-role-select').value;
    const tenantFields = document.getElementById('tenant-fields');
    const kelasWrap    = document.getElementById('add-kelas-wrap');
    const nameInput    = document.getElementById('tenant-name-input');

    // Kelas hanya untuk customer
    if (role === 'customer') {
        kelasWrap.classList.remove('hidden');
    } else {
        kelasWrap.classList.add('hidden');
    }

    // Tenant hanya untuk pengelola
    if (role === 'pengelola') {
        tenantFields.classList.remove('hidden');
        nameInput.required = true;
    } else {
        tenantFields.classList.add('hidden');
        nameInput.required = false;
    }
}

// Inisialisasi saat modal dibuka — default role = customer, kelas terlihat
document.addEventListener('DOMContentLoaded', function() {
    toggleTenantFields();
});

// Auto search ketika user mengetik
const autoSearchInput = document.getElementById('auto-search-user');

if (autoSearchInput) {
    let searchTimer;

    autoSearchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            const url = new URL(window.location.href);
            const keyword = autoSearchInput.value.trim();

            if (keyword) {
                url.searchParams.set('search', keyword);
            } else {
                url.searchParams.delete('search');
            }

            url.searchParams.delete('page');
            window.location.href = url.toString();
        }, 500);
    });
}
</script>

@endpush
