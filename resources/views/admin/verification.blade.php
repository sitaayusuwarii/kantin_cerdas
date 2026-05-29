@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran — SmartCanteen Admin')
@section('page-title', 'Verifikasi Pembayaran')
@section('page-subtitle', 'Tinjau & verifikasi bukti transfer dari pengguna')

@section('content')

{{-- ===== SUMMARY BAR ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-clock text-amber-400 text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-stone-800 font-bold text-lg leading-none">{{ $summary['menunggu'] }}</p>
            <p class="text-stone-400 text-xs mt-0.5 truncate">Menunggu Verifikasi</p>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-stone-800 font-bold text-lg leading-none">{{ $summary['verified_today'] }}</p>
            <p class="text-stone-400 text-xs mt-0.5 truncate">Diverifikasi Hari Ini</p>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-circle-xmark text-red-400 text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-stone-800 font-bold text-lg leading-none">{{ $summary['rejected_today'] }}</p>
            <p class="text-stone-400 text-xs mt-0.5 truncate">Ditolak Hari Ini</p>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary-500/10 border border-primary-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-money-bills text-primary-400 text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-stone-800 font-bold text-lg leading-none">
                Rp {{ number_format($summary['total_verified_amount'], 0, ',', '.') }}
            </p>
            <p class="text-stone-400 text-xs mt-0.5 truncate">Total Terverifikasi</p>
        </div>
    </div>
</div>

{{-- ===== FILTER BAR ===== --}}
<div class="glass-card rounded-2xl p-4 mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-3">
    <div class="flex items-center gap-2 flex-1">
        <i class="fa-solid fa-filter text-stone-400 text-sm"></i>
        <span class="text-stone-500 text-sm font-medium">Filter:</span>
        <div class="flex gap-2 flex-wrap">
            @foreach(['menunggu' => 'Pending', 'semua' => 'Semua', 'terverifikasi' => 'Lunas', 'ditolak' => 'Ditolak'] as $val => $label)
            <a href="{{ request()->fullUrlWithQuery(['status' => $val, 'page' => 1]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
               {{ $statusFilter === $val
                   ? ($val === 'menunggu' ? 'bg-amber-500/20 border border-amber-500/40 text-amber-300'
                     : ($val === 'terverifikasi' ? 'bg-emerald-500/20 border border-emerald-500/40 text-emerald-300'
                     : ($val === 'ditolak' ? 'bg-red-500/20 border border-red-500/40 text-red-300'
                     : 'bg-primary-500/20 border border-primary-500/40 text-primary-300')))
                   : 'bg-orange-50 border border-orange-100 text-stone-500 hover:text-white hover:border-slate-600' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>
    <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-2 bg-orange-50 border border-border rounded-xl px-3 py-2">
        <input type="hidden" name="status" value="{{ $statusFilter }}">
        <i class="fa-solid fa-search text-stone-400 text-xs"></i>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari No. Order / Nama..."
               class="bg-transparent text-sm text-stone-600 placeholder-slate-600 outline-none w-36 sm:w-48">
    </form>
</div>

{{-- ===== DESKTOP TABLE ===== --}}
<div class="hidden lg:block glass-card rounded-2xl overflow-hidden mb-4">
    <div class="px-5 py-4 border-b border-border flex items-center justify-between">
        <h2 class="text-stone-800 font-bold text-base">Daftar Pembayaran</h2>
        @if($statusFilter === 'menunggu')
        <span class="badge px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs">
            {{ $summary['menunggu'] }} Perlu Verifikasi
        </span>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">No. Pesanan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Pengguna</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Total</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Metode</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Waktu</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Bukti</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40" id="payments-table-body">
                @forelse($payments as $payment)
                @php
                    $sc = match($payment->status) {
                        'terverifikasi' => ['label'=>'LUNAS',   'bg'=>'bg-emerald-500/10','border'=>'border-emerald-500/20','text'=>'text-emerald-400'],
                        'ditolak'       => ['label'=>'DITOLAK', 'bg'=>'bg-red-500/10',    'border'=>'border-red-500/20',    'text'=>'text-red-400'],
                        default         => ['label'=>'PENDING', 'bg'=>'bg-amber-500/10',  'border'=>'border-amber-500/20',  'text'=>'text-amber-400'],
                    };
                    $methodLabels = [
                        'transfer_bri'=>'BRI','transfer_bca'=>'BCA','transfer_mandiri'=>'Mandiri',
                        'gopay'=>'GoPay','ovo'=>'OVO','dana'=>'DANA','tunai'=>'Tunai',
                    ];
                @endphp
                <tr class="table-row" id="row-{{ $payment->id }}">
                    <td class="px-5 py-4">
                        <span class="font-mono text-primary-400 text-sm font-semibold">
                            {{ $payment->order->order_number ?? '-' }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($payment->user->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-stone-800 text-sm font-semibold">{{ $payment->user->full_name }}</p>
                                <p class="text-stone-400 text-xs">{{ $payment->user->kelas ?? $payment->user->class ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-stone-800 font-bold font-mono text-sm">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-stone-500 text-sm">
                        {{ $methodLabels[$payment->method] ?? $payment->method }}
                    </td>
                    <td class="px-5 py-4 text-stone-500 text-sm">
                        {{ $payment->created_at->format('H:i') }} 
                    </td>
                    <td class="px-5 py-4">
                        @if($payment->proof_path)
                        <button onclick="openProof('{{ Str::startsWith($payment->proof_path, 'http') ? $payment->proof_path : Storage::url($payment->proof_path) }}', '{{ $payment->order->order_number ?? $payment->id }}')"
                            class="group relative overflow-hidden rounded-lg border border-border hover:border-primary-500 transition-all">
                            <img src="{{ Str::startsWith($payment->proof_path, 'http') ? $payment->proof_path : Storage::url($payment->proof_path) }}"
                                 alt="Bukti Transfer"
                                 class="w-16 h-12 object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                            <div class="absolute inset-0 bg-primary-500/0 group-hover:bg-primary-500/20 transition-all flex items-center justify-center">
                                <i class="fa-solid fa-eye text-white opacity-0 group-hover:opacity-100 transition-opacity text-xs"></i>
                            </div>
                        </button>
                        @else
                        <span class="text-stone-400 text-xs italic">Tidak ada</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="badge-status-{{ $payment->id }} badge px-2.5 py-1 rounded-lg {{ $sc['bg'] }} border {{ $sc['border'] }} {{ $sc['text'] }} text-xs font-semibold">
                            {{ $sc['label'] }}
                        </span>
                        @if($payment->status === 'ditolak' && $payment->rejection_reason)
                        <p class="text-red-400 text-xs mt-1 max-w-[120px] truncate" title="{{ $payment->rejection_reason }}">
                            {{ $payment->rejection_reason }}
                        </p>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($payment->status === 'menunggu')
                        <button onclick="openActionModal({{ $payment->id }}, '{{ $payment->order->order_number ?? $payment->id }}')"
                            class="action-btn-{{ $payment->id }} flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-500/10 border border-primary-500/20 text-primary-400 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all text-xs font-semibold whitespace-nowrap">
                            <i class="fa-solid fa-gavel text-xs"></i> Verifikasi
                        </button>
                        @else
                        <span class="text-stone-400 text-xs italic">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-700 text-3xl"></i>
                            <p class="text-stone-400 text-sm">Tidak ada pembayaran ditemukan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($payments->hasPages())
    <div class="px-5 py-4 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-stone-400 text-xs">
            Menampilkan {{ $payments->firstItem() }}–{{ $payments->lastItem() }} dari {{ $payments->total() }} data
        </p>
        <div class="flex items-center gap-1">
            @if($payments->onFirstPage())
            <span class="w-8 h-8 rounded-lg bg-orange-100/30 border border-border text-stone-400 flex items-center justify-center cursor-not-allowed">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </span>
            @else
            <a href="{{ $payments->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-orange-100/50 border border-border text-stone-500 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
            @endif

            @foreach($payments->getUrlRange(max(1, $payments->currentPage()-2), min($payments->lastPage(), $payments->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="w-8 h-8 rounded-lg text-xs font-semibold transition-all flex items-center justify-center
                {{ $page === $payments->currentPage() ? 'bg-primary-500 text-white border border-primary-500' : 'bg-orange-100/50 border border-border text-stone-500 hover:border-primary-500 hover:text-primary-400' }}">
                {{ $page }}
            </a>
            @endforeach

            @if($payments->hasMorePages())
            <a href="{{ $payments->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-orange-100/50 border border-border text-stone-500 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
            @else
            <span class="w-8 h-8 rounded-lg bg-orange-100/30 border border-border text-stone-400 flex items-center justify-center cursor-not-allowed">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ===== MOBILE CARD LIST ===== --}}
<div class="lg:hidden space-y-3 mb-4">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-stone-800 font-bold">Daftar Pembayaran</h2>
        <span class="text-stone-400 text-xs">{{ $payments->total() }} total</span>
    </div>

    @forelse($payments as $payment)
    @php
        $sc = match($payment->status) {
            'terverifikasi' => ['label'=>'LUNAS',   'bg'=>'bg-emerald-500/10','border'=>'border-emerald-500/20','text'=>'text-emerald-400'],
            'ditolak'       => ['label'=>'DITOLAK', 'bg'=>'bg-red-500/10',    'border'=>'border-red-500/20',    'text'=>'text-red-400'],
            default         => ['label'=>'PENDING', 'bg'=>'bg-amber-500/10',  'border'=>'border-amber-500/20',  'text'=>'text-amber-400'],
        };
    @endphp
    <div class="glass-card rounded-2xl p-4 border border-amber-500/10" id="card-{{ $payment->id }}">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center text-stone-800 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($payment->user->full_name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-stone-800 font-semibold text-sm">{{ $payment->user->full_name }}</p>
                    <p class="text-stone-400 text-xs">{{ $payment->user->kelas ?? $payment->user->class ?? '-' }}</p>
                </div>
            </div>
            <span class="badge-status-{{ $payment->id }} badge px-2.5 py-1 rounded-lg {{ $sc['bg'] }} border {{ $sc['border'] }} {{ $sc['text'] }} text-xs font-semibold">
                {{ $sc['label'] }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-2 mb-3">
            <div class="bg-orange-50/60 rounded-lg p-2.5">
                <p class="text-stone-400 text-xs mb-0.5">No. Pesanan</p>
                <p class="text-primary-400 font-mono font-semibold text-xs">{{ $payment->order->order_number ?? '-' }}</p>
            </div>
            <div class="bg-orange-50/60 rounded-lg p-2.5">
                <p class="text-stone-400 text-xs mb-0.5">Total</p>
                <p class="text-stone-800 font-bold font-mono text-xs">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
            </div>
            <div class="bg-orange-50/60 rounded-lg p-2.5">
                <p class="text-stone-400 text-xs mb-0.5">Waktu</p>
                <p class="text-stone-600 text-xs">{{ $payment->created_at->format('H:i') }} </p>
            </div>
            <div class="bg-orange-50/60 rounded-lg p-2.5">
                <p class="text-stone-400 text-xs mb-0.5">Bukti Transfer</p>
                @if($payment->proof_path)
                <button onclick="openProof('{{ Str::startsWith($payment->proof_path, 'http') ? $payment->proof_path : Storage::url($payment->proof_path) }}', '{{ $payment->order->order_number ?? $payment->id }}')"
                        class="text-primary-400 text-xs font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-image text-xs"></i> Lihat
                </button>
                @else
                <span class="text-stone-400 text-xs italic">Tidak ada</span>
                @endif
            </div>
        </div>

        @if($payment->status === 'ditolak' && $payment->rejection_reason)
        <div class="bg-red-500/5 border border-red-500/20 rounded-lg p-2.5 mb-3">
            <p class="text-red-400 text-xs"><span class="font-semibold">Alasan tolak:</span> {{ $payment->rejection_reason }}</p>
        </div>
        @endif

        @if($payment->status === 'menunggu')
        <button onclick="openActionModal({{ $payment->id }}, '{{ $payment->order->order_number ?? $payment->id }}')"
            class="action-btn-{{ $payment->id }} w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary-500/10 border border-primary-500/20 text-primary-400 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all text-sm font-semibold">
            <i class="fa-solid fa-gavel"></i> Verifikasi
        </button>
        @else
        <div class="w-full py-2.5 rounded-xl bg-orange-50/40 text-stone-400 text-sm text-center">Sudah diproses</div>
        @endif
    </div>
    @empty
    <div class="glass-card rounded-2xl p-8 text-center">
        <i class="fa-solid fa-circle-check text-emerald-700 text-3xl mb-2"></i>
        <p class="text-stone-400 text-sm">Tidak ada pembayaran ditemukan</p>
    </div>
    @endforelse

    @if($payments->hasPages())
    <div class="flex items-center justify-center gap-2 pt-2">
        @if($payments->onFirstPage())
        <span class="px-4 py-2 rounded-xl bg-orange-50/50 border border-border text-stone-400 text-xs cursor-not-allowed">
            <i class="fa-solid fa-chevron-left mr-1"></i> Prev
        </span>
        @else
        <a href="{{ $payments->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-orange-50 border border-border text-stone-500 text-xs hover:border-primary-500 transition-all">
            <i class="fa-solid fa-chevron-left mr-1"></i> Prev
        </a>
        @endif
        <span class="text-stone-400 text-xs px-2">{{ $payments->currentPage() }} / {{ $payments->lastPage() }}</span>
        @if($payments->hasMorePages())
        <a href="{{ $payments->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-orange-50 border border-border text-stone-500 text-xs hover:border-primary-500 transition-all">
            Next <i class="fa-solid fa-chevron-right ml-1"></i>
        </a>
        @else
        <span class="px-4 py-2 rounded-xl bg-orange-50/50 border border-border text-stone-400 text-xs cursor-not-allowed">
            Next <i class="fa-solid fa-chevron-right ml-1"></i>
        </span>
        @endif
    </div>
    @endif
</div>

{{-- ===== PROOF IMAGE MODAL ===== --}}
<div id="proof-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="relative max-w-3xl w-full mx-4">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="text-stone-800 font-bold">Bukti Transfer</h3>
                <p class="text-stone-500 text-xs" id="proof-order-id">-</p>
            </div>
            <div class="flex items-center gap-2">
                <a id="proof-download" href="#" target="_blank"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-500/20 border border-primary-500/30 text-primary-300 text-xs font-semibold hover:bg-primary-500/40 transition-all">
                    <i class="fa-solid fa-download text-xs"></i> Unduh
                </a>
                <button onclick="closeProof()" class="w-8 h-8 rounded-lg bg-orange-100 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-stone-500 transition-all">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>
        {{-- Gambar fullscreen --}}
        <div class="rounded-2xl overflow-hidden border border-border bg-white flex items-center justify-center" style="max-height:80vh">
            <img id="proof-img" src="" alt="Bukti Transfer" class="w-full h-full object-contain" style="max-height:80vh">
        </div>
    </div>
</div>

{{-- ===== ACTION MODAL (Verifikasi / Tolak) ===== --}}
<div id="action-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-6 max-w-md w-full mx-4 border border-border shadow-2xl">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-stone-800 font-bold text-lg">Proses Pembayaran</h3>
                <p class="text-stone-400 text-xs mt-0.5">No. Pesanan: <span id="action-order-id" class="text-primary-400 font-mono font-semibold">-</span></p>
            </div>
            <button onclick="closeActionModal()" class="w-8 h-8 rounded-lg bg-orange-100 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-stone-500 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Step 1: Pilih Aksi --}}
        <div id="step-choose">
            <p class="text-stone-500 text-sm mb-4">Pilih tindakan untuk pembayaran ini:</p>
            <div class="grid grid-cols-2 gap-3">
                <button onclick="chooseVerify()"
                    class="flex flex-col items-center gap-3 p-5 rounded-2xl bg-emerald-500/10 border-2 border-emerald-500/20 hover:border-emerald-500 hover:bg-emerald-500/20 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center group-hover:bg-emerald-500 transition-all">
                        <i class="fa-solid fa-circle-check text-emerald-400 group-hover:text-white text-xl transition-colors"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-emerald-400 font-bold text-sm">Verifikasi</p>
                        <p class="text-stone-400 text-xs mt-0.5">Pembayaran valid</p>
                    </div>
                </button>
                <button onclick="chooseReject()"
                    class="flex flex-col items-center gap-3 p-5 rounded-2xl bg-red-500/10 border-2 border-red-500/20 hover:border-red-500 hover:bg-red-500/20 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-red-500/20 flex items-center justify-center group-hover:bg-red-500 transition-all">
                        <i class="fa-solid fa-circle-xmark text-red-400 group-hover:text-white text-xl transition-colors"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-red-400 font-bold text-sm">Tolak</p>
                        <p class="text-stone-400 text-xs mt-0.5">Bukti tidak valid</p>
                    </div>
                </button>
            </div>
        </div>

        {{-- Step 2a: Konfirmasi Verifikasi --}}
        <div id="step-verify" class="hidden">
            <div class="flex flex-col items-center text-center py-2 mb-5">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-3xl"></i>
                </div>
                <h4 class="text-stone-800 font-bold text-base mb-1">Verifikasi Pembayaran?</h4>
                <p class="text-stone-500 text-sm">Pembayaran akan ditandai <span class="text-emerald-400 font-semibold">LUNAS</span> dan pesanan akan dikonfirmasi. Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="backToChoose()" class="flex-1 py-3 rounded-xl bg-orange-100 text-stone-600 text-sm font-semibold hover:bg-slate-600 transition-all">
                    <i class="fa-solid fa-arrow-left mr-1 text-xs"></i> Kembali
                </button>
                <button onclick="submitVerify()" id="btn-submit-verify"
                    class="flex-1 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-stone-800 text-sm font-semibold transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> Ya, Verifikasi
                </button>
            </div>
        </div>

        {{-- Step 2b: Form Tolak --}}
        <div id="step-reject" class="hidden">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-red-500/10 border border-red-500/20 mb-4">
                <i class="fa-solid fa-triangle-exclamation text-red-400 flex-shrink-0"></i>
                <p class="text-red-300 text-xs">Pembayaran akan ditolak dan pesanan akan dibatalkan. Pengguna perlu melakukan pembayaran ulang.</p>
            </div>
            <div class="mb-4">
                <label class="block text-stone-600 text-sm font-semibold mb-2">
                    Alasan Penolakan <span class="text-red-400">*</span>
                </label>
                <textarea id="rejection-reason"
                    rows="3"
                    placeholder="Contoh: Bukti transfer tidak jelas, nominal tidak sesuai, nama pengirim berbeda..."
                    class="w-full px-4 py-3 bg-orange-50 border border-border rounded-xl text-sm text-stone-600 placeholder-slate-600 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 resize-none transition-all"></textarea>
                <p id="reason-error" class="text-red-400 text-xs mt-1 hidden">Alasan penolakan wajib diisi minimal 5 karakter.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="backToChoose()" class="flex-1 py-3 rounded-xl bg-orange-100 text-stone-600 text-sm font-semibold hover:bg-slate-600 transition-all">
                    <i class="fa-solid fa-arrow-left mr-1 text-xs"></i> Kembali
                </button>
                <button onclick="submitReject()" id="btn-submit-reject"
                    class="flex-1 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-stone-800 text-sm font-semibold transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-xmark"></i> Tolak Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let currentPaymentId = null;

// ── Proof Modal ──────────────────────────────────────────────────────────────
function openProof(src, orderId) {
    document.getElementById('proof-img').src = src;
    document.getElementById('proof-order-id').textContent = 'No. Pesanan: ' + orderId;
    document.getElementById('proof-download').href = src;
    showModal('proof-modal');
}
function closeProof() { hideModal('proof-modal'); }

// ── Action Modal ─────────────────────────────────────────────────────────────
function openActionModal(paymentId, orderNumber) {
    currentPaymentId = paymentId;
    document.getElementById('action-order-id').textContent = orderNumber;

    // Reset ke step 1
    backToChoose();

    // Reset form
    document.getElementById('rejection-reason').value = '';
    document.getElementById('reason-error').classList.add('hidden');

    showModal('action-modal');
}

function closeActionModal() {
    hideModal('action-modal');
    currentPaymentId = null;
}

function chooseVerify() {
    document.getElementById('step-choose').classList.add('hidden');
    document.getElementById('step-reject').classList.add('hidden');
    document.getElementById('step-verify').classList.remove('hidden');
}

function chooseReject() {
    document.getElementById('step-choose').classList.add('hidden');
    document.getElementById('step-verify').classList.add('hidden');
    document.getElementById('step-reject').classList.remove('hidden');
}

function backToChoose() {
    document.getElementById('step-verify').classList.add('hidden');
    document.getElementById('step-reject').classList.add('hidden');
    document.getElementById('step-choose').classList.remove('hidden');
}

// ── Submit Verifikasi ────────────────────────────────────────────────────────
function submitVerify() {
    if (!currentPaymentId) return;

    const btn = document.getElementById('btn-submit-verify');
    setLoading(btn, true, '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...');

    fetch(`/admin/verification/${currentPaymentId}/verify`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeActionModal();
            showToast(res.message, 'emerald');
            setTimeout(() => window.location.reload(), 1500); // ← tambahkan ini
        } else {
            showToast(res.message || 'Gagal memverifikasi.', 'red');
            setLoading(btn, false, '<i class="fa-solid fa-check"></i> Ya, Verifikasi');
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan. Coba lagi.', 'red');
        setLoading(btn, false, '<i class="fa-solid fa-check"></i> Ya, Verifikasi');
    });
}

// ── Submit Tolak ─────────────────────────────────────────────────────────────
function submitReject() {
    if (!currentPaymentId) return;

    const reason = document.getElementById('rejection-reason').value.trim();
    const errEl  = document.getElementById('reason-error');

    if (reason.length < 5) {
        errEl.classList.remove('hidden');
        document.getElementById('rejection-reason').focus();
        return;
    }
    errEl.classList.add('hidden');

    const btn = document.getElementById('btn-submit-reject');
    setLoading(btn, true, '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...');

    fetch(`/admin/verification/${currentPaymentId}/reject`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ rejection_reason: reason }),
    })
    .then(r => r.json())
    .then(res => {
    if (res.success) {
        closeActionModal();
        showToast(res.message, 'red');
        setTimeout(() => window.location.reload(), 1500); // ← tambahkan ini
    } else {
        showToast(res.message || 'Gagal menolak pembayaran.', 'red');
        setLoading(btn, false, '<i class="fa-solid fa-xmark"></i> Tolak Pembayaran');
    }
})
    .catch(() => showToast('Terjadi kesalahan. Coba lagi.', 'red'))
    .finally(() => setLoading(btn, false, '<i class="fa-solid fa-xmark"></i> Tolak Pembayaran'));
}

// ── Update row/card tanpa reload ─────────────────────────────────────────────
function updateRowStatus(paymentId, status, reason = '') {
    const statusConfig = {
        terverifikasi: { label: 'LUNAS',   cls: 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' },
        ditolak:       { label: 'DITOLAK', cls: 'bg-red-500/10 border-red-500/20 text-red-400' },
    };
    const sc = statusConfig[status];
    if (!sc) return;

    // Update badge
    document.querySelectorAll(`.badge-status-${paymentId}`).forEach(el => {
        el.textContent = sc.label;
        el.className = `badge px-2.5 py-1 rounded-lg border text-xs font-semibold ${sc.cls}`;
    });

    // Hapus/disable tombol aksi
    document.querySelectorAll(`.action-btn-${paymentId}`).forEach(el => {
        el.outerHTML = '<span class="text-stone-400 text-xs italic">Selesai</span>';
    });
}

// ── Helpers ──────────────────────────────────────────────────────────────────
function showModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('opacity-0', 'pointer-events-none');
    m.classList.add('opacity-100', 'pointer-events-auto');
}
function hideModal(id) {
    const m = document.getElementById(id);
    m.classList.add('opacity-0', 'pointer-events-none');
    m.classList.remove('opacity-100', 'pointer-events-auto');
}

function setLoading(btn, loading, html) {
    btn.disabled = loading;
    btn.innerHTML = html;
}

function showToast(message, color) {
    const colorMap = { emerald: 'bg-emerald-600', red: 'bg-red-600' };
    const iconMap  = { emerald: 'fa-circle-check', red: 'fa-circle-xmark' };
    const toast = document.createElement('div');
    toast.className = `fixed bottom-6 right-6 z-[100] flex items-center gap-3 px-4 py-3 rounded-xl ${colorMap[color] || 'bg-primary-500'} text-stone-800 text-sm font-semibold shadow-2xl transform translate-y-2 opacity-0 transition-all duration-300`;
    toast.innerHTML = `<i class="fa-solid ${iconMap[color] || 'fa-bell'}"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.replace('translate-y-2', 'translate-y-0') || toast.classList.replace('opacity-0', 'opacity-100'), 10);
    setTimeout(() => {
        toast.classList.replace('translate-y-0', 'translate-y-2');
        toast.classList.replace('opacity-100', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// Close modal on overlay click
['proof-modal', 'action-modal'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) {
            if (id === 'proof-modal') closeProof();
            else closeActionModal();
        }
    });
});
</script>
@endpush