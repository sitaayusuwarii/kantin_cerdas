@extends('layouts.admin')

@section('title', 'Monitoring Transaksi — SmartCanteen Admin')
@section('page-title', 'Monitoring Transaksi')
@section('page-subtitle', 'Pantau seluruh transaksi sistem (read-only)')

@section('content')

{{-- ===== STATS ROW ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary-500/10 border border-primary-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-receipt text-primary-400 text-sm"></i>
        </div>
        <div>
            <p class="text-stone-800 font-bold text-xl leading-none">{{ $stats['total'] }}</p>
            <p class="text-stone-400 text-xs mt-0.5">Total Transaksi</p>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
        </div>
        <div>
            <p class="text-stone-800 font-bold text-xl leading-none">{{ $stats['terverifikasi'] }}</p>
            <p class="text-stone-400 text-xs mt-0.5">Lunas</p>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-clock text-amber-400 text-sm"></i>
        </div>
        <div>
            <p class="text-stone-800 font-bold text-xl leading-none">{{ $stats['menunggu'] }}</p>
            <p class="text-stone-400 text-xs mt-0.5">Pending</p>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-circle-xmark text-red-400 text-sm"></i>
        </div>
        <div>
            <p class="text-stone-800 font-bold text-xl leading-none">{{ $stats['ditolak'] }}</p>
            <p class="text-stone-400 text-xs mt-0.5">Ditolak</p>
        </div>
    </div>
</div>

{{-- ===== FILTER / SEARCH BAR ===== --}}
<form method="GET" action="{{ route('admin.transactions') }}" id="filter-form">
<div class="glass-card rounded-2xl p-4 mb-4">
    <div class="flex flex-col sm:flex-row gap-3">
        {{-- Status Filter --}}
        <div class="flex items-center gap-2 flex-wrap">
            @foreach(['Semua' => '', 'Lunas' => 'terverifikasi', 'Pending' => 'menunggu', 'Ditolak' => 'ditolak'] as $label => $val)
            <a href="{{ route('admin.transactions', array_merge(request()->query(), ['status' => $val, 'page' => 1])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
               {{ request('status', '') === $val
                   ? 'bg-primary-500/20 border border-primary-500/40 text-primary-300'
                   : 'bg-orange-50 border border-orange-100 text-stone-600 hover:text-primary-600 hover:border-primary-200 hover:bg-orange-100' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        <div class="flex gap-2 sm:ml-auto flex-wrap">
            {{-- Date Filter --}}
            <div class="flex items-center gap-2 bg-orange-50 border border-orange-100 rounded-xl px-3 py-2">
                <i class="fa-solid fa-calendar text-stone-400 text-xs"></i>
                <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}"
                       onchange="document.getElementById('filter-form').submit()"
                       class="bg-transparent text-stone-600 text-xs outline-none cursor-pointer">
            </div>
            {{-- Search --}}
            <div class="flex items-center gap-2 bg-orange-50 border border-orange-100 rounded-xl px-3 py-2">
                <i class="fa-solid fa-search text-stone-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari No. Order / Nama..."
                       onchange="document.getElementById('filter-form').submit()"
                       class="bg-transparent text-sm text-stone-600 placeholder-slate-600 outline-none w-36">
            </div>
            {{-- Export --}}
            <a href="{{ route('admin.transactions.export', request()->query()) }}"
               class="flex items-center gap-2 px-3 py-2 rounded-xl bg-primary-500/10 border border-primary-500/20 text-primary-400 hover:bg-primary-500/20 transition-all text-xs font-semibold">
                <i class="fa-solid fa-download"></i>
                <span class="hidden sm:inline">Export CSV</span>
            </a>
        </div>
    </div>
</div>
</form>

{{-- ===== READ-ONLY NOTICE ===== --}}
<div class="flex items-start gap-3 p-3 rounded-xl bg-blue-500/5 border border-blue-500/20 mb-4">
    <i class="fa-solid fa-circle-info text-blue-400 text-sm mt-0.5 flex-shrink-0"></i>
    <p class="text-blue-300 text-xs leading-relaxed">
        <span class="font-semibold">Mode Read-Only:</span> Halaman ini hanya untuk memantau transaksi. Untuk memverifikasi pembayaran, gunakan menu
        <a href="{{ route('admin.verification') }}" class="text-primary-400 underline font-semibold">Verifikasi Pembayaran</a>.
    </p>
</div>

{{-- ===== DESKTOP TABLE ===== --}}
<div class="hidden lg:block glass-card rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-orange-100 flex items-center justify-between">
        <h2 class="text-stone-800 font-bold text-base">Semua Transaksi</h2>
        <span class="text-stone-400 text-xs">
            Menampilkan {{ $payments->firstItem() }}–{{ $payments->lastItem() }} dari {{ $payments->total() }} data
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-orange-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">No. Pesanan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Pengguna</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Total</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Metode</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Tanggal</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-400 uppercase tracking-wider">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @forelse($payments as $payment)
                @php
                    $statusConfig = [
                        'terverifikasi' => ['label' => 'LUNAS',   'bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-400'],
                        'menunggu'      => ['label' => 'PENDING', 'bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-400'],
                        'ditolak'       => ['label' => 'DITOLAK', 'bg' => 'bg-red-500/10',     'border' => 'border-red-500/20',     'text' => 'text-red-400'],
                    ];
                    $sc = $statusConfig[$payment->status] ?? $statusConfig['menunggu'];

                    $methodLabels = [
                        'transfer_bri'     => 'Transfer BRI',
                        'transfer_bca'     => 'Transfer BCA',
                        'transfer_mandiri' => 'Transfer Mandiri',
                        'gopay'            => 'GoPay',
                        'ovo'              => 'OVO',
                        'dana'             => 'DANA',
                        'tunai'            => 'Tunai',
                    ];
                    $methodIcons = [
                        'transfer_bri'     => 'fa-building-columns',
                        'transfer_bca'     => 'fa-building-columns',
                        'transfer_mandiri' => 'fa-building-columns',
                        'gopay'            => 'fa-wallet',
                        'ovo'              => 'fa-wallet',
                        'dana'             => 'fa-wallet',
                        'tunai'            => 'fa-money-bill',
                    ];
                @endphp
                <tr class="table-row">
                    <td class="px-5 py-4">
                        <span class="font-mono text-primary-400 text-sm font-semibold">
                            {{ $payment->order->order_number }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg overflow-hidden flex-shrink-0">
                                @if($payment->user->photo)
                                    <img src="{{ asset('storage/' . $payment->user->photo) }}"
                                        class="w-full h-full object-cover" alt="foto">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-slate-600 to-slate-700
                                                flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($payment->user->full_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-white text-sm font-semibold">{{ $payment->user->full_name }}</p>
                                <p class="text-stone-400 text-xs">{{ $payment->user->kelas ?? $payment->user->class ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-stone-800 font-bold font-mono text-sm">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5 text-stone-500 text-sm">
                            <i class="fa-solid {{ $methodIcons[$payment->method] ?? 'fa-money-bill' }} text-xs text-stone-400"></i>
                            {{ $methodLabels[$payment->method] ?? $payment->method }}
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="badge px-2.5 py-1 rounded-lg {{ $sc['bg'] }} border {{ $sc['border'] }} {{ $sc['text'] }} text-xs font-semibold">
                            {{ $sc['label'] }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-stone-500 text-xs">
                        {{ $payment->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-5 py-4">
                        <button onclick="showDetail({{ $payment->id }})"
                            class="w-8 h-8 rounded-lg bg-orange-100/50 hover:bg-primary-500/20 border border-orange-100 hover:border-primary-500/30 text-stone-500 hover:text-primary-400 flex items-center justify-center transition-all">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fa-solid fa-receipt text-slate-700 text-3xl"></i>
                            <p class="text-stone-400 text-sm">Tidak ada transaksi ditemukan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-5 py-4 border-t border-orange-100 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-stone-400 text-xs">
            Menampilkan {{ $payments->firstItem() }}–{{ $payments->lastItem() }} dari {{ $payments->total() }} transaksi
        </p>
        <div class="flex items-center gap-1">
            {{-- Prev --}}
            @if($payments->onFirstPage())
            <span class="w-8 h-8 rounded-lg bg-orange-100/30 border border-orange-100 text-stone-400 flex items-center justify-center cursor-not-allowed">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </span>
            @else
            <a href="{{ $payments->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-orange-100/50 border border-orange-100 text-stone-500 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
            @endif

            {{-- Page Numbers --}}
            @foreach($payments->getUrlRange(max(1, $payments->currentPage()-2), min($payments->lastPage(), $payments->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="w-8 h-8 rounded-lg text-xs font-semibold transition-all flex items-center justify-center
                {{ $page === $payments->currentPage()
                    ? 'bg-primary-500 text-white border border-primary-500'
                    : 'bg-orange-100/50 border border-orange-100 text-stone-500 hover:border-primary-500 hover:text-primary-400' }}">
                {{ $page }}
            </a>
            @endforeach

            {{-- Next --}}
            @if($payments->hasMorePages())
            <a href="{{ $payments->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-orange-100/50 border border-orange-100 text-stone-500 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
            @else
            <span class="w-8 h-8 rounded-lg bg-orange-100/30 border border-orange-100 text-stone-400 flex items-center justify-center cursor-not-allowed">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </span>
            @endif
        </div>
    </div>
</div>

{{-- ===== MOBILE CARD LIST ===== --}}
<div class="lg:hidden space-y-3">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-stone-800 font-bold">Semua Transaksi</h2>
        <span class="text-stone-400 text-xs">{{ $payments->total() }} total</span>
    </div>

    @forelse($payments as $payment)
    @php
        $sc = $statusConfig[$payment->status] ?? $statusConfig['menunggu'];
    @endphp
    <div class="glass-card rounded-2xl p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <p class="text-primary-400 font-mono font-semibold text-sm">{{ $payment->order->order_number }}</p>
                <p class="text-stone-800 font-semibold mt-0.5">{{ $payment->user->full_name }}</p>
                <p class="text-stone-400 text-xs">{{ $payment->user->kelas ?? $payment->user->class ?? '-' }}</p>
            </div>
            <span class="badge px-2.5 py-1 rounded-lg {{ $sc['bg'] }} border {{ $sc['border'] }} {{ $sc['text'] }} text-xs font-semibold">
                {{ $sc['label'] }}
            </span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <div class="bg-orange-50/60 rounded-lg p-2.5">
                <p class="text-stone-400 text-xs mb-0.5">Total</p>
                <p class="text-stone-800 font-bold font-mono text-xs">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
            </div>
            <div class="bg-orange-50/60 rounded-lg p-2.5">
                <p class="text-stone-400 text-xs mb-0.5">Metode</p>
                <p class="text-stone-600 text-xs">{{ $methodLabels[$payment->method] ?? $payment->method }}</p>
            </div>
            <div class="bg-orange-50/60 rounded-lg p-2.5">
                <p class="text-stone-400 text-xs mb-0.5">Tanggal</p>
                <p class="text-stone-600 text-xs">{{ $payment->created_at->format('d M Y') }}</p>
            </div>
        </div>
        <button onclick="showDetail({{ $payment->id }})"
                class="mt-3 w-full py-2 rounded-xl bg-orange-50 border border-orange-100 text-stone-500 text-xs font-medium hover:border-primary-500 hover:text-primary-400 transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-eye text-xs"></i> Lihat Detail
        </button>
    </div>
    @empty
    <div class="glass-card rounded-2xl p-8 text-center">
        <i class="fa-solid fa-receipt text-slate-700 text-3xl mb-2"></i>
        <p class="text-stone-400 text-sm">Tidak ada transaksi ditemukan</p>
    </div>
    @endforelse

    {{-- Mobile Pagination --}}
    @if($payments->hasPages())
    <div class="flex items-center justify-center gap-2 pt-2">
        @if($payments->onFirstPage())
        <span class="px-4 py-2 rounded-xl bg-orange-50/50 border border-orange-100 text-stone-400 text-xs cursor-not-allowed">
            <i class="fa-solid fa-chevron-left mr-1"></i> Prev
        </span>
        @else
        <a href="{{ $payments->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-orange-50 border border-orange-100 text-stone-500 text-xs hover:border-primary-500 hover:text-primary-400 transition-all">
            <i class="fa-solid fa-chevron-left mr-1"></i> Prev
        </a>
        @endif

        <span class="text-stone-400 text-xs px-2">{{ $payments->currentPage() }} / {{ $payments->lastPage() }}</span>

        @if($payments->hasMorePages())
        <a href="{{ $payments->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-orange-50 border border-orange-100 text-stone-500 text-xs hover:border-primary-500 hover:text-primary-400 transition-all">
            Next <i class="fa-solid fa-chevron-right ml-1"></i>
        </a>
        @else
        <span class="px-4 py-2 rounded-xl bg-orange-50/50 border border-orange-100 text-stone-400 text-xs cursor-not-allowed">
            Next <i class="fa-solid fa-chevron-right ml-1"></i>
        </span>
        @endif
    </div>
    @endif
</div>

{{-- ===== DETAIL MODAL ===== --}}
<div id="detail-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-5 max-w-md w-full mx-4 border border-orange-100 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-stone-800 font-bold">Detail Transaksi</h3>
            <button onclick="closeDetail()" class="w-8 h-8 rounded-lg bg-orange-100 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-stone-500 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div id="detail-content" class="space-y-3 text-sm">
            <div class="flex items-center justify-center py-8">
                <div class="w-8 h-8 border-4 border-primary-500/30 border-t-primary-500 rounded-full animate-spin"></div>
            </div>
        </div>
        <div class="mt-4 p-3 rounded-xl bg-blue-500/5 border border-blue-500/15">
            <p class="text-blue-300 text-xs flex items-center gap-2">
                <i class="fa-solid fa-lock text-xs"></i>
                Halaman ini hanya bisa dibaca. Untuk verifikasi gunakan menu Verifikasi Pembayaran.
            </p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const statusLabels = {
    terverifikasi: '<span class="text-emerald-400 font-semibold">LUNAS</span>',
    menunggu:      '<span class="text-amber-400 font-semibold">PENDING</span>',
    ditolak:       '<span class="text-red-400 font-semibold">DITOLAK</span>',
};

const methodLabels = {
    transfer_bri:     'Transfer BRI',
    transfer_bca:     'Transfer BCA',
    transfer_mandiri: 'Transfer Mandiri',
    gopay:            'GoPay',
    ovo:              'OVO',
    dana:             'DANA',
    tunai:            'Tunai',
};

function showDetail(paymentId) {
    const modal = document.getElementById('detail-modal');
    const content = document.getElementById('detail-content');

    // Show modal with loading
    content.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="w-8 h-8 border-4 border-primary-500/30 border-t-primary-500 rounded-full animate-spin"></div>
        </div>`;
    modal.classList.remove('opacity-0', 'pointer-events-none');
    modal.classList.add('opacity-100', 'pointer-events-auto');

    // Fetch detail dari server
    fetch(`/admin/transactions/${paymentId}/detail`, {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(p => {
        const rows = [
            ['No. Pesanan',   `<span class="font-mono text-primary-400">${p.order_number}</span>`],
            ['Status',         statusLabels[p.status] ?? p.status],
            ['Nama Pengguna',  p.user_name],
            ['Kelas',          p.user_class ?? '-'],
            ['No. HP',         p.user_phone ?? '-'],
            ['Total Bayar',   `<span class="font-mono text-stone-800 font-bold">Rp ${Number(p.amount).toLocaleString('id-ID')}</span>`],
            ['Metode',         methodLabels[p.method] ?? p.method],
            ['Tanggal',        p.created_at],
        ];

        // Tambah rejection reason kalau ditolak
        if (p.status === 'ditolak' && p.rejection_reason) {
            rows.push(['Alasan Tolak', `<span class="text-red-400">${p.rejection_reason}</span>`]);
        }

        // Bukti transfer
        const proofHtml = p.proof_path
            ? `<div class="col-span-2 bg-orange-50/60 rounded-xl p-3">
                <p class="text-stone-400 text-xs mb-2">Bukti Transfer</p>
                <a href="${p.proof_url}" target="_blank">
                    <img src="${p.proof_url}" alt="Bukti" class="w-full max-h-48 object-contain rounded-lg border border-orange-100 hover:opacity-80 transition-opacity">
                </a>
               </div>`
            : '';

        // Items pesanan
        const itemsHtml = p.order_items && p.order_items.length
            ? `<div class="col-span-2 bg-orange-50/60 rounded-xl p-3">
                <p class="text-stone-400 text-xs mb-2">Item Pesanan</p>
                <div class="space-y-1.5">
                    ${p.order_items.map(item => `
                        <div class="flex justify-between text-xs">
                            <span class="text-stone-600">${item.name} <span class="text-stone-400">x${item.quantity}</span></span>
                            <span class="text-white font-mono">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</span>
                        </div>
                    `).join('')}
                </div>
               </div>`
            : '';

        content.innerHTML = `
            <div class="grid grid-cols-2 gap-2">
                ${rows.map(([k,v]) => `
                    <div class="bg-orange-50/60 rounded-xl p-3">
                        <p class="text-stone-400 text-xs mb-1">${k}</p>
                        <p class="text-white text-sm">${v}</p>
                    </div>
                `).join('')}
                ${proofHtml}
                ${itemsHtml}
            </div>
        `;
    })
    .catch(() => {
        content.innerHTML = `<p class="text-red-400 text-sm text-center py-4">Gagal memuat detail transaksi.</p>`;
    });
}

function closeDetail() {
    const modal = document.getElementById('detail-modal');
    modal.classList.add('opacity-0', 'pointer-events-none');
    modal.classList.remove('opacity-100', 'pointer-events-auto');
}

// Close on overlay click
document.getElementById('detail-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDetail();
});
</script>
@endpush