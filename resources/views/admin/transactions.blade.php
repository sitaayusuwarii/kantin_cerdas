@extends('layouts.admin')

@section('title', 'Monitoring Transaksi — SmartCanteen Admin')
@section('page-title', 'Monitoring Transaksi')
@section('page-subtitle', 'Pantau seluruh transaksi sistem (read-only)')

@section('content')

{{-- ===== STATS ROW ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @php
        $stats = [
            ['label' => 'Total Transaksi',    'val' => '248',       'icon' => 'fa-receipt',            'c' => 'primary'],
            ['label' => 'Lunas',              'val' => '231',       'icon' => 'fa-circle-check',       'c' => 'emerald'],
            ['label' => 'Pending',            'val' => '5',         'icon' => 'fa-clock',              'c' => 'amber'],
            ['label' => 'Ditolak',            'val' => '12',        'icon' => 'fa-circle-xmark',       'c' => 'red'],
        ];
    @endphp
    @foreach($stats as $s)
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-{{ $s['c'] }}-500/10 border border-{{ $s['c'] }}-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid {{ $s['icon'] }} text-{{ $s['c'] }}-400 text-sm"></i>
        </div>
        <div>
            <p class="text-white font-bold text-xl leading-none">{{ $s['val'] }}</p>
            <p class="text-slate-500 text-xs mt-0.5">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ===== FILTER / SEARCH BAR ===== --}}
<div class="glass-card rounded-2xl p-4 mb-4">
    <div class="flex flex-col sm:flex-row gap-3">
        {{-- Status Filter --}}
        <div class="flex items-center gap-2 flex-wrap">
            @foreach(['Semua', 'Lunas', 'Pending', 'Ditolak'] as $f)
            <button class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                {{ $f === 'Semua' ? 'bg-primary-500/20 border border-primary-500/40 text-primary-300' : 'bg-slate-800 border border-border text-slate-400 hover:text-white hover:border-slate-600' }}">
                {{ $f }}
            </button>
            @endforeach
        </div>
        <div class="flex gap-2 sm:ml-auto flex-wrap">
            {{-- Date Range --}}
            <div class="flex items-center gap-2 bg-slate-800 border border-border rounded-xl px-3 py-2">
                <i class="fa-solid fa-calendar text-slate-500 text-xs"></i>
                <input type="date" value="{{ date('Y-m-d') }}" class="bg-transparent text-slate-300 text-xs outline-none cursor-pointer">
            </div>
            {{-- Search --}}
            <div class="flex items-center gap-2 bg-slate-800 border border-border rounded-xl px-3 py-2">
                <i class="fa-solid fa-search text-slate-500 text-xs"></i>
                <input type="text" placeholder="Cari ID / Nama..." class="bg-transparent text-sm text-slate-300 placeholder-slate-600 outline-none w-36">
            </div>
            {{-- Export --}}
            <button class="flex items-center gap-2 px-3 py-2 rounded-xl bg-primary-500/10 border border-primary-500/20 text-primary-400 hover:bg-primary-500/20 transition-all text-xs font-semibold">
                <i class="fa-solid fa-download"></i>
                <span class="hidden sm:inline">Export CSV</span>
            </button>
        </div>
    </div>
</div>

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
    <div class="px-5 py-4 border-b border-border flex items-center justify-between">
        <h2 class="text-white font-bold text-base">Semua Transaksi</h2>
        <span class="text-slate-500 text-xs">Menampilkan 10 dari 248 data</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">ID Pesanan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengguna</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Metode</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @php
                    $transactions = [
                        ['id' => 'TRX-0825', 'name' => 'Doni Kurniawan', 'class' => 'XI IPS 2', 'total' => 53000, 'method' => 'Transfer',  'status' => 'pending',  'date' => '27 Apr 2025, 09:15'],
                        ['id' => 'TRX-0824', 'name' => 'Rina Melati',   'class' => 'X IPA 3',  'total' => 28000, 'method' => 'Transfer',  'status' => 'pending',  'date' => '27 Apr 2025, 09:02'],
                        ['id' => 'TRX-0823', 'name' => 'Agus Pratama',  'class' => 'XII IPA 1','total' => 67000, 'method' => 'Transfer',  'status' => 'pending',  'date' => '27 Apr 2025, 08:45'],
                        ['id' => 'TRX-0822', 'name' => 'Sari Dewi',     'class' => 'X IPS 1',  'total' => 32000, 'method' => 'Transfer',  'status' => 'pending',  'date' => '27 Apr 2025, 08:31'],
                        ['id' => 'TRX-0821', 'name' => 'Budi Santoso',  'class' => 'XI IPA 2', 'total' => 45000, 'method' => 'Transfer',  'status' => 'pending',  'date' => '27 Apr 2025, 08:24'],
                        ['id' => 'TRX-0820', 'name' => 'Maya Putri',    'class' => 'XII IPS 1','total' => 38000, 'method' => 'E-Wallet', 'status' => 'lunas',    'date' => '27 Apr 2025, 07:58'],
                        ['id' => 'TRX-0819', 'name' => 'Hendra Wijaya', 'class' => 'X IPA 1',  'total' => 55000, 'method' => 'Transfer',  'status' => 'lunas',    'date' => '26 Apr 2025, 13:21'],
                        ['id' => 'TRX-0818', 'name' => 'Fitri Ayu',     'class' => 'XI IPA 3', 'total' => 22000, 'method' => 'E-Wallet', 'status' => 'ditolak',  'date' => '26 Apr 2025, 12:44'],
                        ['id' => 'TRX-0817', 'name' => 'Reza Ananda',   'class' => 'X IPS 2',  'total' => 41000, 'method' => 'Transfer',  'status' => 'lunas',    'date' => '26 Apr 2025, 12:10'],
                        ['id' => 'TRX-0816', 'name' => 'Nita Sari',     'class' => 'XII IPA 2','total' => 60000, 'method' => 'E-Wallet', 'status' => 'lunas',    'date' => '26 Apr 2025, 11:32'],
                    ];

                    $statusConfig = [
                        'lunas'   => ['label' => 'LUNAS',   'bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-400'],
                        'pending' => ['label' => 'PENDING', 'bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-400'],
                        'ditolak' => ['label' => 'DITOLAK', 'bg' => 'bg-red-500/10',     'border' => 'border-red-500/20',     'text' => 'text-red-400'],
                    ];
                @endphp

                @foreach($transactions as $t)
                @php $sc = $statusConfig[$t['status']]; @endphp
                <tr class="table-row">
                    <td class="px-5 py-4">
                        <span class="font-mono text-primary-400 text-sm font-semibold">{{ $t['id'] }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ substr($t['name'], 0, 1) }}
                            </div>
                            <div>
                                <p class="text-white text-sm font-semibold">{{ $t['name'] }}</p>
                                <p class="text-slate-500 text-xs">{{ $t['class'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-white font-bold font-mono text-sm">Rp {{ number_format($t['total'], 0, ',', '.') }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5 text-slate-400 text-sm">
                            <i class="fa-solid {{ $t['method'] === 'E-Wallet' ? 'fa-wallet' : 'fa-building-columns' }} text-xs text-slate-500"></i>
                            {{ $t['method'] }}
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="badge px-2.5 py-1 rounded-lg {{ $sc['bg'] }} border {{ $sc['border'] }} {{ $sc['text'] }}">{{ $sc['label'] }}</span>
                    </td>
                    <td class="px-5 py-4 text-slate-400 text-xs">{{ $t['date'] }}</td>
                    <td class="px-5 py-4">
                        <button onclick="showDetail('{{ $t['id'] }}')"
                            class="w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-primary-500/20 border border-border hover:border-primary-500/30 text-slate-400 hover:text-primary-400 flex items-center justify-center transition-all">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-5 py-4 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-slate-500 text-xs">Menampilkan 1–10 dari 248 transaksi</p>
        <div class="flex items-center gap-1">
            <button class="w-8 h-8 rounded-lg bg-slate-700/50 border border-border text-slate-400 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            @foreach([1,2,3,'...',25] as $p)
            <button class="w-8 h-8 rounded-lg text-xs font-semibold transition-all
                {{ $p === 1 ? 'bg-primary-500 text-white border border-primary-500' : 'bg-slate-700/50 border border-border text-slate-400 hover:border-primary-500 hover:text-primary-400' }}">
                {{ $p }}
            </button>
            @endforeach
            <button class="w-8 h-8 rounded-lg bg-slate-700/50 border border-border text-slate-400 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>
</div>

{{-- ===== MOBILE CARD LIST ===== --}}
<div class="lg:hidden space-y-3">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-white font-bold">Semua Transaksi</h2>
        <span class="text-slate-500 text-xs">248 total</span>
    </div>

    @foreach($transactions as $t)
    @php $sc = $statusConfig[$t['status']]; @endphp
    <div class="glass-card rounded-2xl p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <p class="text-primary-400 font-mono font-semibold text-sm">{{ $t['id'] }}</p>
                <p class="text-white font-semibold mt-0.5">{{ $t['name'] }}</p>
                <p class="text-slate-500 text-xs">{{ $t['class'] }}</p>
            </div>
            <span class="badge px-2.5 py-1 rounded-lg {{ $sc['bg'] }} border {{ $sc['border'] }} {{ $sc['text'] }}">{{ $sc['label'] }}</span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <div class="bg-slate-800/60 rounded-lg p-2.5">
                <p class="text-slate-600 text-xs mb-0.5">Total</p>
                <p class="text-white font-bold font-mono text-xs">Rp {{ number_format($t['total'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-slate-800/60 rounded-lg p-2.5">
                <p class="text-slate-600 text-xs mb-0.5">Metode</p>
                <p class="text-slate-300 text-xs">{{ $t['method'] }}</p>
            </div>
            <div class="bg-slate-800/60 rounded-lg p-2.5">
                <p class="text-slate-600 text-xs mb-0.5">Tanggal</p>
                <p class="text-slate-300 text-xs">{{ substr($t['date'], 0, 11) }}</p>
            </div>
        </div>
        <button onclick="showDetail('{{ $t['id'] }}')" class="mt-3 w-full py-2 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs font-medium hover:border-primary-500 hover:text-primary-400 transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-eye text-xs"></i> Lihat Detail
        </button>
    </div>
    @endforeach

    {{-- Mobile Pagination --}}
    <div class="flex items-center justify-center gap-2 pt-2">
        <button class="px-4 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs hover:border-primary-500 hover:text-primary-400 transition-all">
            <i class="fa-solid fa-chevron-left mr-1"></i> Prev
        </button>
        <span class="text-slate-500 text-xs px-2">1 / 25</span>
        <button class="px-4 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs hover:border-primary-500 hover:text-primary-400 transition-all">
            Next <i class="fa-solid fa-chevron-right ml-1"></i>
        </button>
    </div>
</div>

{{-- ===== DETAIL MODAL ===== --}}
<div id="detail-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-5 max-w-md w-full mx-4 border border-border shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-bold">Detail Transaksi</h3>
            <button onclick="closeDetail()" class="w-8 h-8 rounded-lg bg-slate-700 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div id="detail-content" class="space-y-3 text-sm">
            {{-- Filled by JS --}}
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
@php
    $txJson = $transactions ?? [
        ['id'=>'TRX-0825','name'=>'Doni Kurniawan','class'=>'XI IPS 2','total'=>53000,'method'=>'Transfer','status'=>'pending','date'=>'27 Apr 2025, 09:15'],
    ];
@endphp

<script>
const txData = @json($txJson);
</script>

function showDetail(id) {
    const tx = txData.find(t => t.id === id) || { id, name: '-', class: '-', total: 0, method: '-', status: '-', date: '-' };
    const statusLabels = { lunas: '<span class="text-emerald-400 font-semibold">LUNAS</span>', pending: '<span class="text-amber-400 font-semibold">PENDING</span>', ditolak: '<span class="text-red-400 font-semibold">DITOLAK</span>' };
    document.getElementById('detail-content').innerHTML = `
        <div class="grid grid-cols-2 gap-2">
            ${[
                ['ID Pesanan', `<span class="font-mono text-primary-400">${tx.id}</span>`],
                ['Status', statusLabels[tx.status] || tx.status],
                ['Nama Pengguna', tx.name],
                ['Kelas', tx.class],
                ['Total Pembayaran', `<span class="font-mono text-white font-bold">Rp ${Number(tx.total).toLocaleString('id-ID')}</span>`],
                ['Metode', tx.method],
                ['Tanggal', tx.date],
            ].map(([k,v]) => `
                <div class="bg-slate-800/60 rounded-xl p-3">
                    <p class="text-slate-500 text-xs mb-1">${k}</p>
                    <p class="text-white text-sm">${v}</p>
                </div>
            `).join('')}
        </div>
    `;
    const m = document.getElementById('detail-modal');
    m.classList.remove('opacity-0', 'pointer-events-none');
    m.classList.add('opacity-100', 'pointer-events-auto');
}

function closeDetail() {
    const m = document.getElementById('detail-modal');
    m.classList.add('opacity-0', 'pointer-events-none');
    m.classList.remove('opacity-100', 'pointer-events-auto');
}
</script>
@endpush
