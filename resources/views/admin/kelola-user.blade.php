@extends('layouts.admin')

@section('title', 'Kelola User — SmartCanteen Admin')
@section('page-title', 'Kelola User')
@section('page-subtitle', 'Manajemen akun siswa & staff SmartCanteen')

@section('content')

{{-- ===== TOAST (server-side) ===== --}}
@if(session('toast'))
<div id="server-toast-data"
    data-msg="{{ session('toast.msg') }}"
    data-color="{{ session('toast.color') }}">
</div>
@endif

{{-- ===== STATS ROW ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
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
            <p class="text-white font-bold text-xl leading-none">{{ number_format($s['val']) }}</p>
            <p class="text-slate-500 text-xs mt-0.5">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ===== FILTER / SEARCH BAR ===== --}}
<form method="GET" action="{{ route('admin.kelola-user') }}" id="filter-form">
<div class="glass-card rounded-2xl p-4 mb-4">
    <div class="flex flex-col sm:flex-row gap-3">
        {{-- Status Filter --}}
        <div class="flex items-center gap-2 flex-wrap">
            @foreach(['' => 'Semua', 'active' => 'Aktif', 'suspended' => 'Tersuspend', 'inactive' => 'Non-aktif'] as $val => $label)
            <button type="submit" name="status" value="{{ $val }}"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                {{ request('status', '') === $val
                    ? 'bg-primary-500/20 border border-primary-500/40 text-primary-300'
                    : 'bg-slate-800 border border-border text-slate-400 hover:text-white hover:border-slate-600' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Role Filter --}}
        <div class="flex items-center gap-2 flex-wrap">
            @foreach([
                '' => 'Semua Role',
                'customer' => 'Customer',
                'pengelola' => 'Pengelola',
                'admin' => 'Admin'
            ] as $val => $label)

            <button type="submit" name="role" value="{{ $val }}"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                {{ request('role', '') === $val
                    ? 'bg-blue-500/20 border border-blue-500/40 text-blue-300'
                    : 'bg-slate-800 border border-border text-slate-400 hover:text-white hover:border-slate-600' }}">
                {{ $label }}
            </button>

            @endforeach
        </div>

        <div class="flex gap-2 sm:ml-auto flex-wrap">
            {{-- Search --}}
            <div class="flex items-center gap-2 bg-slate-800 border border-border rounded-xl px-3 py-2 {{ request('search') ? 'border-primary-500/40' : '' }}">
                <i class="fa-solid fa-search text-slate-500 text-xs"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama / username..."
                    class="bg-transparent text-sm text-slate-300 placeholder-slate-600 outline-none w-44">
            </div>
            {{-- Search submit --}}
            <button type="submit" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-700 border border-border text-slate-300 hover:text-white text-xs font-semibold transition-all">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span class="hidden sm:inline">Cari</span>
            </button>
            {{-- Reset --}}
            @if(request('search') || request('status'))
            <a href="{{ route('admin.kelola-user') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-700 border border-border text-slate-400 hover:text-white text-xs font-semibold transition-all">
                <i class="fa-solid fa-xmark text-xs"></i>
            </a>
            @endif
            {{-- Tambah User --}}
            <button type="button"
                onclick="openModal('modal-add-user')"
                class="flex items-center gap-2 px-3 py-2 rounded-xl bg-primary-500 hover:bg-primary-600 text-white transition-all text-xs font-semibold">
                <i class="fa-solid fa-plus"></i>
                <span class="hidden sm:inline">Tambah User</span>
            </button>
        </div>
    </div>
</div>
</form>

{{-- ===== DESKTOP TABLE ===== --}}
<div class="hidden lg:block glass-card rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-border flex items-center justify-between">
        <h2 class="text-white font-bold text-base">Daftar User</h2>
        <span class="text-slate-500 text-xs">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} data
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama User</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Username</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kelas</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Transaksi</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Terdaftar</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @forelse($users as $user)
                @php
                    $initials  = collect(explode(' ', $user->full_name))->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
                    $statusCfg = match($user->status) {
                        'suspended' => ['bg-red-500/10',    'border-red-500/20',    'text-red-400',    'Tersuspend'],
                        'inactive'  => ['bg-slate-500/10',  'border-slate-500/20',  'text-slate-400',  'Non-aktif'],
                        default     => ['bg-emerald-500/10','border-emerald-500/20','text-emerald-400','Aktif'],
                    };
                    $isSuspended   = $user->status === 'suspended';
                    $suspendLabel  = $isSuspended ? 'Aktifkan' : 'Suspend';
                    $suspendIcon   = $isSuspended ? 'fa-user-check' : 'fa-user-slash';
                    $suspendConfirm = $isSuspended ? 'Aktifkan user ini?' : 'Suspend user ini?';

                    // Prepare JS payload as JSON string — built in PHP, no multiline inside onclick
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
                <tr class="table-row hover:bg-white/[0.02] transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500/20 to-primary-700/20 border border-primary-500/20 flex items-center justify-center text-primary-300 text-xs font-bold flex-shrink-0">
                                {{ $initials }}
                            </div>
                            <div>
                                <p class="text-white text-sm font-semibold">{{ $user->full_name }}</p>
                                <p class="text-slate-500 text-xs">{{ $user->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-slate-300 text-sm font-mono">{{ $user->username }}</span>
                    </td>
                    <td class="px-5 py-4 text-slate-300 text-sm">{{ $user->kelas ?? '—' }}</td>
                    <td class="px-5 py-4">
                        <span class="text-white font-bold font-mono text-sm">{{ $user->orders_count }}</span>
                        <span class="text-slate-500 text-xs ml-1">transaksi</span>
                    </td>
                    <td class="px-5 py-4 text-slate-400 text-xs">
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
                                class="w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-primary-500/20 border border-border hover:border-primary-500/30 text-slate-400 hover:text-primary-400 flex items-center justify-center transition-all">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                            {{-- Edit --}}
                            <button
                                onclick="openEditUser({{ $editData }})"
                                title="Edit"
                                class="w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-amber-500/20 border border-border hover:border-amber-500/30 text-slate-400 hover:text-amber-400 flex items-center justify-center transition-all">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                            {{-- Suspend / Aktifkan --}}
                            <form method="POST" action="{{ route('admin.users.toggle-suspend', $user) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    title="{{ $suspendLabel }}"
                                    onclick="return confirm('{{ $suspendConfirm }}')"
                                    class="w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-amber-500/20 border border-border hover:border-amber-500/30 text-slate-400 hover:text-amber-400 flex items-center justify-center transition-all">
                                    <i class="fa-solid {{ $suspendIcon }} text-xs"></i>
                                </button>
                            </form>
                            {{-- Delete --}}
                            <button
                                onclick="openDelete({{ $user->id }}, '{{ addslashes($user->full_name) }}')"
                                title="Hapus"
                                class="w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-red-500/20 border border-border hover:border-red-500/30 text-slate-400 hover:text-red-400 flex items-center justify-center transition-all">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-14 text-center">
                        <div class="flex flex-col items-center gap-3 text-slate-600">
                            <i class="fa-solid fa-users-slash text-3xl"></i>
                            <p class="text-sm">Tidak ada user ditemukan</p>
                            @if(request('search') || request('status'))
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
        <p class="text-slate-500 text-xs">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} user
        </p>
        <div class="flex items-center gap-1">
            {{-- Prev --}}
            @if($users->onFirstPage())
            <span class="w-8 h-8 rounded-lg bg-slate-800 border border-border text-slate-600 flex items-center justify-center cursor-not-allowed">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </span>
            @else
            <a href="{{ $users->previousPageUrl() }}"
               class="w-8 h-8 rounded-lg bg-slate-700/50 border border-border text-slate-400 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
            @endif

            {{-- Page numbers --}}
            @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
            <a href="{{ $url }}"
               class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center transition-all
               {{ $page == $users->currentPage()
                    ? 'bg-primary-500 text-white border border-primary-500'
                    : 'bg-slate-700/50 border border-border text-slate-400 hover:border-primary-500 hover:text-primary-400' }}">
                {{ $page }}
            </a>
            @endforeach

            {{-- Next --}}
            @if($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}"
               class="w-8 h-8 rounded-lg bg-slate-700/50 border border-border text-slate-400 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
            @else
            <span class="w-8 h-8 rounded-lg bg-slate-800 border border-border text-slate-600 flex items-center justify-center cursor-not-allowed">
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
        <h2 class="text-white font-bold">Daftar User</h2>
        <span class="text-slate-500 text-xs">{{ $users->total() }} total</span>
    </div>
    <div class="space-y-3">
        @forelse($users as $user)
        @php
            // These variables are already set in the desktop loop above if both loops
            // share the same @foreach — but mobile is a separate loop, so we set them again.
            $initials  = collect(explode(' ', $user->full_name))->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
            $statusCfg = match($user->status) {
                'suspended' => ['bg-red-500/10',    'border-red-500/20',    'text-red-400',    'Tersuspend'],
                'inactive'  => ['bg-slate-500/10',  'border-slate-500/20',  'text-slate-400',  'Non-aktif'],
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
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500/20 to-primary-700/20 border border-primary-500/20 flex items-center justify-center text-primary-300 text-sm font-bold flex-shrink-0">
                        {{ $initials }}
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">{{ $user->full_name }}</p>
                        <p class="text-slate-500 text-xs font-mono">{{ $user->username }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusCfg[0] }} border {{ $statusCfg[1] }} {{ $statusCfg[2] }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ $statusCfg[3] }}
                </span>
            </div>
            <div class="grid grid-cols-3 gap-2 mb-3">
                <div class="bg-slate-800/60 rounded-lg p-2.5">
                    <p class="text-slate-600 text-xs mb-0.5">Kelas</p>
                    <p class="text-slate-300 text-xs truncate">{{ $user->kelas ?? '—' }}</p>
                </div>
                <div class="bg-slate-800/60 rounded-lg p-2.5">
                    <p class="text-slate-600 text-xs mb-0.5">Transaksi</p>
                    <p class="text-white font-bold font-mono text-xs">{{ $user->orders_count }}x</p>
                </div>
                <div class="bg-slate-800/60 rounded-lg p-2.5">
                    <p class="text-slate-600 text-xs mb-0.5">Daftar</p>
                    <p class="text-slate-300 text-xs">{{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button
                    onclick="showDetail({{ $detailData }})"
                    class="flex-1 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs font-medium hover:border-primary-500 hover:text-primary-400 transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-eye text-xs"></i> Detail
                </button>
                <button
                    onclick="openEditUser({{ $editData }})"
                    class="flex-1 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs font-medium hover:border-amber-500 hover:text-amber-400 transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-pen text-xs"></i> Edit
                </button>
                <form method="POST" action="{{ route('admin.users.toggle-suspend', $user) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit"
                        onclick="return confirm('{{ $suspendConfirm }}')"
                        class="py-2 px-3 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs hover:border-amber-500 hover:text-amber-400 transition-all flex items-center justify-center">
                        <i class="fa-solid {{ $suspendIcon }} text-xs"></i>
                    </button>
                </form>
                <button
                    onclick="openDelete({{ $user->id }}, '{{ addslashes($user->full_name) }}')"
                    class="py-2 px-3 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs hover:border-red-500 hover:text-red-400 transition-all flex items-center justify-center">
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-slate-600 text-sm">
            <i class="fa-solid fa-users-slash text-2xl block mb-3"></i>
            Tidak ada user ditemukan
        </div>
        @endforelse
    </div>

    {{-- Mobile Pagination --}}
    @if($users->hasPages())
    <div class="flex justify-between items-center pt-2">
        @if($users->onFirstPage())
        <span class="px-4 py-2 rounded-xl bg-slate-800 border border-border text-slate-600 text-xs cursor-not-allowed">← Sebelumnya</span>
        @else
        <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-slate-800 border border-border text-slate-300 text-xs hover:border-primary-500 hover:text-primary-400 transition-all">← Sebelumnya</a>
        @endif

        <span class="text-slate-500 text-xs">{{ $users->currentPage() }} / {{ $users->lastPage() }}</span>

        @if($users->hasMorePages())
        <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-slate-800 border border-border text-slate-300 text-xs hover:border-primary-500 hover:text-primary-400 transition-all">Berikutnya →</a>
        @else
        <span class="px-4 py-2 rounded-xl bg-slate-800 border border-border text-slate-600 text-xs cursor-not-allowed">Berikutnya →</span>
        @endif
    </div>
    @endif
</div>

{{-- ===== MODAL: TAMBAH USER ===== --}}
<div id="modal-add-user" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-5 max-w-md w-full mx-4 border border-border shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-white font-bold text-base flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-primary-400"></i> Tambah User Baru
            </h3>
            <button onclick="closeModal('modal-add-user')" class="w-8 h-8 rounded-lg bg-slate-700 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" id="form-add-user">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input name="full_name" type="text" placeholder="cth. Andi Pratama" required
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-slate-500 text-xs font-semibold block mb-1.5">Username <span class="text-red-400">*</span></label>
                        <input name="username" type="text" placeholder="cth. andi2025" required
                            class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-slate-500 text-xs font-semibold block mb-1.5">Role <span class="text-red-400">*</span></label>
                        <select name="role"
                            class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors cursor-pointer">
                            <option value="customer">Customer (Siswa)</option>
                            <option value="pengelola">Pengelola</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">No. HP <span class="text-red-400">*</span></label>
                    <input name="phone" type="text" placeholder="08xx-xxxx-xxxx" required
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">Kelas</label>
                    <input name="kelas" type="text" placeholder="cth. XII IPA 1"
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">Password Awal <span class="text-red-400">*</span></label>
                    <input name="password" type="password" placeholder="Min. 6 karakter" required minlength="6"
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <button type="button" onclick="closeModal('modal-add-user')"
                    class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm font-semibold transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL: EDIT USER ===== --}}
<div id="modal-edit-user" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-5 max-w-md w-full mx-4 border border-border shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-white font-bold text-base flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-amber-400"></i> Edit User
            </h3>
            <button onclick="closeModal('modal-edit-user')" class="w-8 h-8 rounded-lg bg-slate-700 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <form method="POST" id="form-edit-user">
            @csrf @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">Nama Lengkap</label>
                    <input id="edit-full_name" name="full_name" type="text" required
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-slate-500 text-xs font-semibold block mb-1.5">Username</label>
                        <input id="edit-username" name="username" type="text" required
                            class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-slate-500 text-xs font-semibold block mb-1.5">Status</label>
                        <select id="edit-status" name="status"
                            class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors cursor-pointer">
                            <option value="active">Aktif</option>
                            <option value="suspended">Tersuspend</option>
                            <option value="inactive">Non-aktif</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">No. HP</label>
                    <input id="edit-phone" name="phone" type="text" required
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors">
                </div>
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">Kelas</label>
                    <input id="edit-kelas" name="kelas" type="text"
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors">
                </div>
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">
                        Password Baru <span class="text-slate-600 font-normal">(kosongkan jika tidak diganti)</span>
                    </label>
                    <input id="edit-password" name="password" type="password" placeholder="Min. 6 karakter"
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <button type="button" onclick="closeModal('modal-edit-user')"
                    class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm font-semibold transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL: DETAIL USER ===== --}}
<div id="modal-detail-user" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-5 max-w-md w-full mx-4 border border-border shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-primary-400"></i> Detail User
            </h3>
            <button onclick="closeModal('modal-detail-user')" class="w-8 h-8 rounded-lg bg-slate-700 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div id="detail-user-content" class="space-y-3 text-sm">
            {{-- Filled by JS --}}
        </div>
    </div>
</div>

{{-- ===== MODAL: HAPUS USER ===== --}}
<div id="modal-delete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-5 max-w-sm w-full mx-4 border border-border shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-red-400"></i> Hapus User
            </h3>
            <button onclick="closeModal('modal-delete')" class="w-8 h-8 rounded-lg bg-slate-700 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="p-3 rounded-xl bg-red-500/5 border border-red-500/15 mb-4">
            <p class="text-slate-300 text-sm">
                Anda yakin ingin menghapus akun <strong id="delete-name" class="text-white"></strong>?
                Semua data transaksi terkait akan ikut terhapus dan tindakan ini
                <span class="text-red-400 font-semibold">tidak dapat dibatalkan</span>.
            </p>
        </div>
        <form method="POST" id="form-delete-user">
            @csrf @method('DELETE')
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModal('modal-delete')"
                    class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm font-semibold transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition-all flex items-center gap-2">
                    <i class="fa-solid fa-trash text-xs"></i> Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== TOAST ===== --}}
<div id="toast"
    class="fixed bottom-6 right-6 z-[100] flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-800 border border-border shadow-2xl text-sm font-semibold text-white opacity-0 pointer-events-none transition-all duration-300 translate-y-3">
    <i id="toast-icon" class="fa-solid fa-circle-check text-emerald-400"></i>
    <span id="toast-msg">Berhasil</span>
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
    inactive  : { label: 'Non-aktif',  bg: 'bg-slate-500/10',   border: 'border-slate-500/20',   text: 'text-slate-400' },
};

const roleLabel = { customer: 'Customer', pengelola: 'Pengelola', admin: 'Admin' };

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
['modal-add-user','modal-edit-user','modal-detail-user','modal-delete'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

// ── Detail modal ──────────────────────────────────────────────────────────────
function showDetail(u) {
    const initials = u.full_name.split(' ').map(w => w[0].toUpperCase()).join('').slice(0, 2);
    const sc = statusConfig[u.status] || statusConfig.inactive;

    document.getElementById('detail-user-content').innerHTML = `
        <div class="flex items-center gap-4 p-4 bg-slate-800/50 rounded-xl mb-1">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500/30 to-primary-700/30 border border-primary-500/20 flex items-center justify-center text-primary-300 text-xl font-bold flex-shrink-0">
                ${initials}
            </div>
            <div>
                <p class="text-white font-bold text-base">${u.full_name}</p>
                <p class="text-slate-400 text-xs mt-0.5 font-mono">${u.username}</p>
                <div class="flex gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-primary-500/10 border border-primary-500/20 text-primary-400">
                        ${roleLabel[u.role] || u.role}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold ${sc.bg} border ${sc.border} ${sc.text}">
                        ${sc.label}
                    </span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
            ${[
                ['No. HP',         u.phone],
                ['Kelas',          u.kelas || '—'],
                ['Total Transaksi', `<span class="font-mono font-bold text-white">${u.orders_count}x</span>`],
                ['Terdaftar Sejak', u.created_at],
                ['ID User',        `<span class="font-mono text-primary-400">#USR-${String(u.id).padStart(4,'0')}</span>`],
            ].map(([k, v]) => `
                <div class="bg-slate-800/60 rounded-xl p-3">
                    <p class="text-slate-500 text-xs mb-1">${k}</p>
                    <p class="text-slate-200 text-sm">${v}</p>
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
</script>
@endpush