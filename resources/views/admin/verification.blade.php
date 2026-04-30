@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran — SmartCanteen Admin')
@section('page-title', 'Verifikasi Pembayaran')
@section('page-subtitle', 'Tinjau & verifikasi bukti transfer dari pengguna')

@section('content')

{{-- ===== SUMMARY BAR ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @php
        $summaryItems = [
            ['label' => 'Menunggu Verifikasi', 'value' => '5',      'icon' => 'fa-clock',         'color' => 'amber'],
            ['label' => 'Diverifikasi Hari Ini','value' => '18',     'icon' => 'fa-circle-check',  'color' => 'emerald'],
            ['label' => 'Ditolak Hari Ini',     'value' => '2',      'icon' => 'fa-circle-xmark',  'color' => 'red'],
            ['label' => 'Total Terverifikasi',  'value' => 'Rp 4,1jt','icon' => 'fa-money-bills',  'color' => 'primary'],
        ];
    @endphp
    @foreach($summaryItems as $s)
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-{{ $s['color'] }}-500/10 border border-{{ $s['color'] }}-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid {{ $s['icon'] }} text-{{ $s['color'] }}-400 text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-white font-bold text-lg leading-none">{{ $s['value'] }}</p>
            <p class="text-slate-500 text-xs mt-0.5 truncate">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ===== FILTER BAR ===== --}}
<div class="glass-card rounded-2xl p-4 mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-3">
    <div class="flex items-center gap-2 flex-1">
        <i class="fa-solid fa-filter text-slate-500 text-sm"></i>
        <span class="text-slate-400 text-sm font-medium">Filter:</span>
        <div class="flex gap-2 flex-wrap">
            @foreach(['Semua', 'Pending', 'Lunas', 'Ditolak'] as $f)
            <button class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                {{ $f === 'Pending' ? 'bg-amber-500/20 border border-amber-500/40 text-amber-300' : 'bg-slate-800 border border-border text-slate-400 hover:text-white hover:border-slate-600' }}">
                {{ $f }}
            </button>
            @endforeach
        </div>
    </div>
    <div class="flex items-center gap-2 bg-slate-800 border border-border rounded-xl px-3 py-2">
        <i class="fa-solid fa-search text-slate-500 text-xs"></i>
        <input type="text" placeholder="Cari ID / Nama..." class="bg-transparent text-sm text-slate-300 placeholder-slate-600 outline-none w-36 sm:w-48">
    </div>
</div>

{{-- ===== DESKTOP TABLE ===== --}}
<div class="hidden lg:block glass-card rounded-2xl overflow-hidden mb-4">
    <div class="px-5 py-4 border-b border-border flex items-center justify-between">
        <h2 class="text-white font-bold text-base">Daftar Pembayaran Pending</h2>
        <span class="badge px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400">5 Perlu Verifikasi</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">ID Pesanan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengguna</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Bukti Transfer</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @php
                    $payments = [
                        ['id' => 'TRX-0821', 'name' => 'Budi Santoso',  'class' => 'XI IPA 2', 'total' => 45000, 'time' => '08:24 WIB', 'img' => 'https://placehold.co/80x60/1e293b/4f46e5?text=BT'],
                        ['id' => 'TRX-0822', 'name' => 'Sari Dewi',     'class' => 'X IPS 1',  'total' => 32000, 'time' => '08:31 WIB', 'img' => 'https://placehold.co/80x60/1e293b/4f46e5?text=BT'],
                        ['id' => 'TRX-0823', 'name' => 'Agus Pratama',  'class' => 'XII IPA 1','total' => 67000, 'time' => '08:45 WIB', 'img' => 'https://placehold.co/80x60/1e293b/4f46e5?text=BT'],
                        ['id' => 'TRX-0824', 'name' => 'Rina Melati',   'class' => 'X IPA 3',  'total' => 28000, 'time' => '09:02 WIB', 'img' => 'https://placehold.co/80x60/1e293b/4f46e5?text=BT'],
                        ['id' => 'TRX-0825', 'name' => 'Doni Kurniawan','class' => 'XI IPS 2', 'total' => 53000, 'time' => '09:15 WIB', 'img' => 'https://placehold.co/80x60/1e293b/4f46e5?text=BT'],
                    ];
                @endphp
                @foreach($payments as $p)
                <tr class="table-row">
                    <td class="px-5 py-4">
                        <span class="font-mono text-primary-400 text-sm font-semibold">{{ $p['id'] }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ substr($p['name'], 0, 1) }}
                            </div>
                            <div>
                                <p class="text-white text-sm font-semibold">{{ $p['name'] }}</p>
                                <p class="text-slate-500 text-xs">{{ $p['class'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-white font-bold font-mono text-sm">Rp {{ number_format($p['total'], 0, ',', '.') }}</span>
                    </td>
                    <td class="px-5 py-4 text-slate-400 text-sm">{{ $p['time'] }}</td>
                    <td class="px-5 py-4">
                        <button onclick="openModal('{{ $p['img'] }}', '{{ $p['id'] }}')"
                            class="group relative overflow-hidden rounded-lg border border-border hover:border-primary-500 transition-all">
                            <img src="{{ $p['img'] }}" alt="Bukti Transfer" class="w-16 h-12 object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                            <div class="absolute inset-0 bg-primary-500/0 group-hover:bg-primary-500/20 transition-all flex items-center justify-center">
                                <i class="fa-solid fa-eye text-white opacity-0 group-hover:opacity-100 transition-opacity text-xs"></i>
                            </div>
                        </button>
                    </td>
                    <td class="px-5 py-4">
                        <span class="badge px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400">PENDING</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <button onclick="verifyPayment('{{ $p['id'] }}', this)"
                                class="verify-btn flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition-all text-xs font-semibold">
                                <i class="fa-solid fa-check text-xs"></i> Verifikasi
                            </button>
                            <button onclick="rejectPayment('{{ $p['id'] }}', this)"
                                class="reject-btn flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all text-xs font-semibold">
                                <i class="fa-solid fa-xmark text-xs"></i> Tolak
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ===== MOBILE CARD LIST ===== --}}
<div class="lg:hidden space-y-3 mb-4">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-white font-bold">Pembayaran Pending</h2>
        <span class="badge px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400">5 item</span>
    </div>

    @foreach($payments as $p)
    <div class="glass-card rounded-2xl p-4 border border-amber-500/10">
        {{-- Header --}}
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ substr($p['name'], 0, 1) }}
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">{{ $p['name'] }}</p>
                    <p class="text-slate-500 text-xs">{{ $p['class'] }}</p>
                </div>
            </div>
            <span class="badge px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400">PENDING</span>
        </div>

        {{-- Info Grid --}}
        <div class="grid grid-cols-2 gap-2 mb-3">
            <div class="bg-slate-800/60 rounded-lg p-2.5">
                <p class="text-slate-600 text-xs mb-0.5">ID Pesanan</p>
                <p class="text-primary-400 font-mono font-semibold text-sm">{{ $p['id'] }}</p>
            </div>
            <div class="bg-slate-800/60 rounded-lg p-2.5">
                <p class="text-slate-600 text-xs mb-0.5">Total</p>
                <p class="text-white font-bold font-mono text-sm">Rp {{ number_format($p['total'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-slate-800/60 rounded-lg p-2.5">
                <p class="text-slate-600 text-xs mb-0.5">Waktu</p>
                <p class="text-slate-300 text-sm">{{ $p['time'] }}</p>
            </div>
            <div class="bg-slate-800/60 rounded-lg p-2.5">
                <p class="text-slate-600 text-xs mb-0.5">Bukti Transfer</p>
                <button onclick="openModal('{{ $p['img'] }}', '{{ $p['id'] }}')" class="text-primary-400 text-sm font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-image text-xs"></i> Lihat
                </button>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-2">
            <button onclick="verifyPayment('{{ $p['id'] }}', this)"
                class="verify-btn flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition-all text-sm font-semibold">
                <i class="fa-solid fa-check"></i> Verifikasi
            </button>
            <button onclick="rejectPayment('{{ $p['id'] }}', this)"
                class="reject-btn flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all text-sm font-semibold">
                <i class="fa-solid fa-xmark"></i> Tolak
            </button>
        </div>
    </div>
    @endforeach
</div>

{{-- ===== IMAGE PREVIEW MODAL ===== --}}
<div id="img-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-5 max-w-sm w-full mx-4 border border-border shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-white font-bold">Bukti Transfer</h3>
                <p class="text-slate-500 text-xs" id="modal-trx-id">TRX-XXXX</p>
            </div>
            <button onclick="closeModal()" class="w-8 h-8 rounded-lg bg-slate-700 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="rounded-xl overflow-hidden border border-border mb-4">
            <img id="modal-img" src="" alt="Bukti Transfer" class="w-full object-contain max-h-64">
        </div>
        <div class="flex gap-2">
            <button onclick="closeModal()" class="flex-1 py-2.5 rounded-xl bg-slate-700 text-slate-300 text-sm font-semibold hover:bg-slate-600 transition-all">
                Tutup
            </button>
            <a href="#" id="modal-download" download class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary-500/20 border border-primary-500/30 text-primary-300 text-sm font-semibold hover:bg-primary-500/40 transition-all">
                <i class="fa-solid fa-download text-xs"></i> Unduh
            </a>
        </div>
    </div>
</div>

{{-- ===== CONFIRM MODAL ===== --}}
<div id="confirm-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-6 max-w-sm w-full mx-4 border border-border shadow-2xl text-center">
        <div id="confirm-icon" class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center"></div>
        <h3 id="confirm-title" class="text-white font-bold text-lg mb-2"></h3>
        <p id="confirm-msg" class="text-slate-400 text-sm mb-6"></p>
        <div class="flex gap-3">
            <button onclick="closeConfirm()" class="flex-1 py-2.5 rounded-xl bg-slate-700 text-slate-300 text-sm font-semibold hover:bg-slate-600 transition-all">Batal</button>
            <button id="confirm-ok" class="flex-1 py-2.5 rounded-xl text-white text-sm font-semibold transition-all"></button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let pendingAction = null;

function openModal(imgSrc, trxId) {
    document.getElementById('modal-img').src = imgSrc;
    document.getElementById('modal-trx-id').textContent = trxId;
    document.getElementById('modal-download').href = imgSrc;
    const m = document.getElementById('img-modal');
    m.classList.remove('opacity-0', 'pointer-events-none');
    m.classList.add('opacity-100', 'pointer-events-auto');
}

function closeModal() {
    const m = document.getElementById('img-modal');
    m.classList.add('opacity-0', 'pointer-events-none');
    m.classList.remove('opacity-100', 'pointer-events-auto');
}

function verifyPayment(id, btn) {
    showConfirm({
        icon: '✓', iconClass: 'bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-3xl',
        title: 'Verifikasi Pembayaran?',
        msg: `Pesanan ${id} akan diubah status menjadi LUNAS. Tindakan ini tidak dapat dibatalkan.`,
        okLabel: 'Ya, Verifikasi', okClass: 'bg-emerald-600 hover:bg-emerald-500',
        action: () => {
            const row = btn.closest('tr') || btn.closest('.glass-card');
            const badge = row ? row.querySelector('.badge') : null;
            if (badge) {
                badge.textContent = 'LUNAS';
                badge.className = 'badge px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400';
            }
            const verifyBtns = row ? row.querySelectorAll('.verify-btn, .reject-btn') : [];
            verifyBtns.forEach(b => b.remove());
            closeConfirm();
            showToast('Pembayaran berhasil diverifikasi!', 'emerald');
        }
    });
}

function rejectPayment(id, btn) {
    showConfirm({
        icon: '✕', iconClass: 'bg-red-500/20 border border-red-500/30 text-red-400 text-3xl',
        title: 'Tolak Pembayaran?',
        msg: `Pesanan ${id} akan ditolak. Pengguna akan mendapatkan notifikasi penolakan.`,
        okLabel: 'Ya, Tolak', okClass: 'bg-red-600 hover:bg-red-500',
        action: () => {
            const row = btn.closest('tr') || btn.closest('.glass-card');
            const badge = row ? row.querySelector('.badge') : null;
            if (badge) {
                badge.textContent = 'DITOLAK';
                badge.className = 'badge px-2.5 py-1 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400';
            }
            const verifyBtns = row ? row.querySelectorAll('.verify-btn, .reject-btn') : [];
            verifyBtns.forEach(b => b.remove());
            closeConfirm();
            showToast('Pembayaran berhasil ditolak.', 'red');
        }
    });
}

function showConfirm({icon, iconClass, title, msg, okLabel, okClass, action}) {
    document.getElementById('confirm-icon').textContent = icon;
    document.getElementById('confirm-icon').className = `w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center ${iconClass}`;
    document.getElementById('confirm-title').textContent = title;
    document.getElementById('confirm-msg').textContent = msg;
    const okBtn = document.getElementById('confirm-ok');
    okBtn.textContent = okLabel;
    okBtn.className = `flex-1 py-2.5 rounded-xl text-white text-sm font-semibold transition-all ${okClass}`;
    okBtn.onclick = action;
    const m = document.getElementById('confirm-modal');
    m.classList.remove('opacity-0', 'pointer-events-none');
    m.classList.add('opacity-100', 'pointer-events-auto');
}

function closeConfirm() {
    const m = document.getElementById('confirm-modal');
    m.classList.add('opacity-0', 'pointer-events-none');
    m.classList.remove('opacity-100', 'pointer-events-auto');
}

function showToast(message, color) {
    const toast = document.createElement('div');
    const colorMap = { emerald: 'bg-emerald-500', red: 'bg-red-500' };
    toast.className = `fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl ${colorMap[color] || 'bg-primary-500'} text-white text-sm font-semibold shadow-2xl transform translate-y-2 opacity-0 transition-all duration-300`;
    toast.innerHTML = `<i class="fa-solid ${color === 'emerald' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => { toast.classList.remove('translate-y-2', 'opacity-0'); toast.classList.add('translate-y-0', 'opacity-100'); }, 10);
    setTimeout(() => { toast.classList.add('translate-y-2', 'opacity-0'); setTimeout(() => toast.remove(), 300); }, 3000);
}
</script>
@endpush
