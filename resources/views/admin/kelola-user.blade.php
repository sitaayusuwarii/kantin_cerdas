@extends('layouts.admin')

@section('title', 'Kelola User — SmartCanteen Admin')
@section('page-title', 'Kelola User')
@section('page-subtitle', 'Manajemen akun siswa, guru, dan staff SmartCanteen')

@section('content')

{{-- ===== STATS ROW ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @php
        $stats = [
            ['label' => 'Total User',    'val' => '312',  'icon' => 'fa-users',         'c' => 'primary'],
            ['label' => 'Siswa',         'val' => '298',  'icon' => 'fa-graduation-cap', 'c' => 'emerald'],
            ['label' => 'Guru / Staff',  'val' => '14',   'icon' => 'fa-chalkboard-user','c' => 'amber'],
            ['label' => 'Tersuspend',    'val' => '3',    'icon' => 'fa-user-slash',     'c' => 'red'],
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
        {{-- Tipe Filter --}}
        <div class="flex items-center gap-2 flex-wrap" id="filter-tabs">
            @foreach(['Semua', 'Siswa', 'Guru', 'Staff'] as $f)
            <button
                onclick="setFilter('{{ $f }}')"
                data-filter="{{ $f }}"
                class="filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                    {{ $f === 'Semua' ? 'bg-primary-500/20 border border-primary-500/40 text-primary-300 active' : 'bg-slate-800 border border-border text-slate-400 hover:text-white hover:border-slate-600' }}">
                {{ $f }}
            </button>
            @endforeach
        </div>
        <div class="flex gap-2 sm:ml-auto flex-wrap">
            {{-- Search --}}
            <div class="flex items-center gap-2 bg-slate-800 border border-border rounded-xl px-3 py-2">
                <i class="fa-solid fa-search text-slate-500 text-xs"></i>
                <input
                    type="text"
                    id="search-input"
                    placeholder="Cari nama / email..."
                    oninput="applySearch()"
                    class="bg-transparent text-sm text-slate-300 placeholder-slate-600 outline-none w-40">
            </div>
            {{-- Tambah User --}}
            <button
                onclick="openModal('modal-add-user')"
                class="flex items-center gap-2 px-3 py-2 rounded-xl bg-primary-500 hover:bg-primary-600 text-white transition-all text-xs font-semibold">
                <i class="fa-solid fa-plus"></i>
                <span class="hidden sm:inline">Tambah User</span>
            </button>
            {{-- Export --}}
            <button class="flex items-center gap-2 px-3 py-2 rounded-xl bg-primary-500/10 border border-primary-500/20 text-primary-400 hover:bg-primary-500/20 transition-all text-xs font-semibold">
                <i class="fa-solid fa-download"></i>
                <span class="hidden sm:inline">Export CSV</span>
            </button>
        </div>
    </div>
</div>

{{-- ===== DESKTOP TABLE ===== --}}
<div class="hidden lg:block glass-card rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-border flex items-center justify-between">
        <h2 class="text-white font-bold text-base">Daftar User</h2>
        <span class="text-slate-500 text-xs" id="count-label">Menampilkan 7 dari 312 data</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama User</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kelas / Jabatan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Transaksi</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Terdaftar</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody id="user-tbody" class="divide-y divide-border/40">
                {{-- Rendered by JS --}}
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-5 py-4 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-slate-500 text-xs" id="pagination-info">Menampilkan 1–7 dari 7 user</p>
        <div class="flex items-center gap-1">
            <button class="w-8 h-8 rounded-lg bg-slate-700/50 border border-border text-slate-400 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <button class="w-8 h-8 rounded-lg text-xs font-semibold bg-primary-500 text-white border border-primary-500">1</button>
            <button class="w-8 h-8 rounded-lg bg-slate-700/50 border border-border text-slate-400 flex items-center justify-center hover:border-primary-500 hover:text-primary-400 transition-all">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>
</div>

{{-- ===== MOBILE CARD LIST ===== --}}
<div class="lg:hidden space-y-3">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-white font-bold">Daftar User</h2>
        <span class="text-slate-500 text-xs">312 total</span>
    </div>
    <div id="mobile-user-list" class="space-y-3">
        {{-- Rendered by JS --}}
    </div>
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
        <div class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input id="add-nama" type="text" placeholder="cth. Andi Pratama"
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
                </div>
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">Tipe <span class="text-red-400">*</span></label>
                    <select id="add-tipe"
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors cursor-pointer">
                        <option value="Siswa">Siswa</option>
                        <option value="Guru">Guru</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-slate-500 text-xs font-semibold block mb-1.5">Email <span class="text-red-400">*</span></label>
                <input id="add-email" type="email" placeholder="email@sekolah.sch.id"
                    class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
            </div>
            <div>
                <label class="text-slate-500 text-xs font-semibold block mb-1.5">Kelas / Jabatan <span class="text-red-400">*</span></label>
                <input id="add-kelas" type="text" placeholder="cth. XII IPA 1 / Guru Matematika"
                    class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
            </div>
            <div>
                <label class="text-slate-500 text-xs font-semibold block mb-1.5">No. HP</label>
                <input id="add-hp" type="text" placeholder="08xx-xxxx-xxxx"
                    class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
            </div>
            <div>
                <label class="text-slate-500 text-xs font-semibold block mb-1.5">Password Awal <span class="text-red-400">*</span></label>
                <input id="add-pass" type="password" placeholder="Min. 6 karakter"
                    class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm placeholder-slate-600 outline-none focus:border-primary-500 transition-colors">
            </div>
        </div>
        <div class="mt-5 flex gap-3 justify-end">
            <button onclick="closeModal('modal-add-user')"
                class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm font-semibold transition-all">
                Batal
            </button>
            <button onclick="submitAddUser()"
                class="px-4 py-2 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i> Tambah User
            </button>
        </div>
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
        <input type="hidden" id="edit-id"/>
        <div class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">Nama Lengkap</label>
                    <input id="edit-nama" type="text"
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors">
                </div>
                <div>
                    <label class="text-slate-500 text-xs font-semibold block mb-1.5">Tipe</label>
                    <select id="edit-tipe"
                        class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors cursor-pointer">
                        <option value="Siswa">Siswa</option>
                        <option value="Guru">Guru</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-slate-500 text-xs font-semibold block mb-1.5">Email</label>
                <input id="edit-email" type="email"
                    class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors">
            </div>
            <div>
                <label class="text-slate-500 text-xs font-semibold block mb-1.5">Kelas / Jabatan</label>
                <input id="edit-kelas" type="text"
                    class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors">
            </div>
            <div>
                <label class="text-slate-500 text-xs font-semibold block mb-1.5">Status</label>
                <select id="edit-status"
                    class="w-full bg-slate-800 border border-border rounded-xl px-3 py-2.5 text-slate-200 text-sm outline-none focus:border-primary-500 transition-colors cursor-pointer">
                    <option value="active">Aktif</option>
                    <option value="suspended">Tersuspend</option>
                    <option value="inactive">Non-aktif</option>
                </select>
            </div>
        </div>
        <div class="mt-5 flex gap-3 justify-end">
            <button onclick="closeModal('modal-edit-user')"
                class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm font-semibold transition-all">
                Batal
            </button>
            <button onclick="submitEditUser()"
                class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition-all flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
            </button>
        </div>
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

{{-- ===== MODAL: KONFIRMASI HAPUS ===== --}}
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
            <p class="text-slate-300 text-sm">Anda yakin ingin menghapus akun <strong id="delete-name" class="text-white"></strong>? Semua data transaksi terkait akan ikut terhapus dan tindakan ini <span class="text-red-400 font-semibold">tidak dapat dibatalkan</span>.</p>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="closeModal('modal-delete')"
                class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm font-semibold transition-all">
                Batal
            </button>
            <button onclick="confirmDelete()"
                class="px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition-all flex items-center gap-2">
                <i class="fa-solid fa-trash text-xs"></i> Ya, Hapus
            </button>
        </div>
    </div>
</div>

{{-- ===== MODAL: KONFIRMASI SUSPEND ===== --}}
<div id="modal-suspend" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-5 max-w-sm w-full mx-4 border border-border shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-bold flex items-center gap-2" id="suspend-modal-title">
                <i class="fa-solid fa-user-slash text-amber-400"></i> Suspend User
            </h3>
            <button onclick="closeModal('modal-suspend')" class="w-8 h-8 rounded-lg bg-slate-700 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="p-3 rounded-xl bg-amber-500/5 border border-amber-500/15 mb-4">
            <p class="text-slate-300 text-sm" id="suspend-modal-msg">User <strong id="suspend-name" class="text-white"></strong> akan disuspend dan tidak bisa login ke sistem.</p>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="closeModal('modal-suspend')"
                class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm font-semibold transition-all">
                Batal
            </button>
            <button onclick="confirmSuspend()" id="suspend-confirm-btn"
                class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition-all flex items-center gap-2">
                <i class="fa-solid fa-user-slash text-xs"></i> Suspend
            </button>
        </div>
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
// ===== DATA (ganti dengan data dari controller) =====
let users = [
    { id: 1,  nama: 'Andi Pratama',  email: 'andi@siswa.sch.id',    tipe: 'Siswa',  kelas: 'XII IPA 1',              transaksi: 24, terdaftar: '12 Jan 2025', status: 'active' },
    { id: 2,  nama: 'Rina Melati',   email: 'rina@siswa.sch.id',    tipe: 'Siswa',  kelas: 'XI IPS 2',               transaksi: 18, terdaftar: '14 Jan 2025', status: 'active' },
    { id: 3,  nama: 'Pak Ahmad',     email: 'ahmad@guru.sch.id',    tipe: 'Guru',   kelas: 'Guru Matematika',        transaksi: 31, terdaftar: '5 Jan 2025',  status: 'active' },
    { id: 4,  nama: 'Sari Dewi',     email: 'sari@siswa.sch.id',    tipe: 'Siswa',  kelas: 'X MIPA 3',               transaksi: 7,  terdaftar: '20 Jan 2025', status: 'active' },
    { id: 5,  nama: 'Bu Lestari',    email: 'lestari@guru.sch.id',  tipe: 'Guru',   kelas: 'Guru Bahasa Indonesia',  transaksi: 14, terdaftar: '5 Jan 2025',  status: 'active' },
    { id: 6,  nama: 'Doni Setiawan', email: 'doni@siswa.sch.id',    tipe: 'Siswa',  kelas: 'XII IPS 1',              transaksi: 0,  terdaftar: '3 Feb 2025',  status: 'suspended' },
    { id: 7,  nama: 'Pak Hendra',    email: 'hendra@staff.sch.id',  tipe: 'Staff',  kelas: 'Tata Usaha Keuangan',   transaksi: 8,  terdaftar: '5 Jan 2025',  status: 'active' },
];

let currentFilter  = 'Semua';
let currentSearch  = '';
let pendingDeleteId  = null;
let pendingSuspendId = null;

// ===== TIPE CONFIG =====
const tipeConfig = {
    Siswa : { bg: 'bg-emerald-500/10', border: 'border-emerald-500/20', text: 'text-emerald-400', icon: 'fa-graduation-cap' },
    Guru  : { bg: 'bg-amber-500/10',   border: 'border-amber-500/20',   text: 'text-amber-400',   icon: 'fa-chalkboard-user' },
    Staff : { bg: 'bg-blue-500/10',    border: 'border-blue-500/20',    text: 'text-blue-400',    icon: 'fa-id-badge' },
};

// ===== STATUS CONFIG =====
const statusConfig = {
    active    : { label: 'Aktif',       bg: 'bg-emerald-500/10', border: 'border-emerald-500/20', text: 'text-emerald-400' },
    suspended : { label: 'Tersuspend',  bg: 'bg-red-500/10',     border: 'border-red-500/20',     text: 'text-red-400' },
    inactive  : { label: 'Non-aktif',   bg: 'bg-slate-500/10',   border: 'border-slate-500/20',   text: 'text-slate-400' },
};

// ===== FILTER =====
function setFilter(val) {
    currentFilter = val;
    document.querySelectorAll('.filter-btn').forEach(btn => {
        const isActive = btn.dataset.filter === val;
        btn.className = `filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all ${
            isActive
                ? 'bg-primary-500/20 border border-primary-500/40 text-primary-300 active'
                : 'bg-slate-800 border border-border text-slate-400 hover:text-white hover:border-slate-600'
        }`;
    });
    renderTable();
}

function applySearch() {
    currentSearch = document.getElementById('search-input').value.toLowerCase();
    renderTable();
}

function getFiltered() {
    return users.filter(u => {
        const matchTipe   = currentFilter === 'Semua' || u.tipe === currentFilter;
        const matchSearch = !currentSearch || u.nama.toLowerCase().includes(currentSearch) || u.email.toLowerCase().includes(currentSearch);
        return matchTipe && matchSearch;
    });
}

function initials(name) {
    return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
}

// ===== RENDER TABLE =====
function renderTable() {
    const data = getFiltered();
    const tbody = document.getElementById('user-tbody');
    const mobileList = document.getElementById('mobile-user-list');

    document.getElementById('count-label').textContent = `Menampilkan ${data.length} dari ${users.length} data`;
    document.getElementById('pagination-info').textContent = `Menampilkan 1–${data.length} dari ${data.length} user`;

    if (data.length === 0) {
        const empty = `
            <tr><td colspan="7" class="px-5 py-12 text-center">
                <div class="flex flex-col items-center gap-3 text-slate-600">
                    <i class="fa-solid fa-users-slash text-3xl"></i>
                    <p class="text-sm">Tidak ada user ditemukan</p>
                </div>
            </td></tr>`;
        tbody.innerHTML = empty;
        mobileList.innerHTML = `<div class="text-center py-12 text-slate-600 text-sm"><i class="fa-solid fa-users-slash text-2xl block mb-3"></i>Tidak ada user ditemukan</div>`;
        return;
    }

    // Desktop
    tbody.innerHTML = data.map(u => {
        const tc = tipeConfig[u.tipe] || tipeConfig.Staff;
        const sc = statusConfig[u.status] || statusConfig.inactive;
        return `
        <tr class="table-row">
            <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500/20 to-primary-700/20 border border-primary-500/20 flex items-center justify-center text-primary-300 text-xs font-bold flex-shrink-0">
                        ${initials(u.nama)}
                    </div>
                    <div>
                        <p class="text-white text-sm font-semibold">${u.nama}</p>
                        <p class="text-slate-500 text-xs">${u.email}</p>
                    </div>
                </div>
            </td>
            <td class="px-5 py-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold ${tc.bg} border ${tc.border} ${tc.text}">
                    <i class="fa-solid ${tc.icon} text-[10px]"></i> ${u.tipe}
                </span>
            </td>
            <td class="px-5 py-4 text-slate-300 text-sm">${u.kelas}</td>
            <td class="px-5 py-4">
                <span class="text-white font-bold font-mono text-sm">${u.transaksi}</span>
                <span class="text-slate-500 text-xs ml-1">transaksi</span>
            </td>
            <td class="px-5 py-4 text-slate-400 text-xs">${u.terdaftar}</td>
            <td class="px-5 py-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold ${sc.bg} border ${sc.border} ${sc.text}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span> ${sc.label}
                </span>
            </td>
            <td class="px-5 py-4">
                <div class="flex items-center gap-2">
                    <button onclick="showDetail(${u.id})" title="Detail"
                        class="w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-primary-500/20 border border-border hover:border-primary-500/30 text-slate-400 hover:text-primary-400 flex items-center justify-center transition-all">
                        <i class="fa-solid fa-eye text-xs"></i>
                    </button>
                    <button onclick="openEditUser(${u.id})" title="Edit"
                        class="w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-amber-500/20 border border-border hover:border-amber-500/30 text-slate-400 hover:text-amber-400 flex items-center justify-center transition-all">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <button onclick="openSuspend(${u.id})" title="${u.status === 'suspended' ? 'Aktifkan' : 'Suspend'}"
                        class="w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-amber-500/20 border border-border hover:border-amber-500/30 text-slate-400 hover:text-amber-400 flex items-center justify-center transition-all">
                        <i class="fa-solid ${u.status === 'suspended' ? 'fa-user-check' : 'fa-user-slash'} text-xs"></i>
                    </button>
                    <button onclick="openDelete(${u.id})" title="Hapus"
                        class="w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-red-500/20 border border-border hover:border-red-500/30 text-slate-400 hover:text-red-400 flex items-center justify-center transition-all">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');

    // Mobile
    mobileList.innerHTML = data.map(u => {
        const tc = tipeConfig[u.tipe] || tipeConfig.Staff;
        const sc = statusConfig[u.status] || statusConfig.inactive;
        return `
        <div class="glass-card rounded-2xl p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500/20 to-primary-700/20 border border-primary-500/20 flex items-center justify-center text-primary-300 text-sm font-bold flex-shrink-0">
                        ${initials(u.nama)}
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">${u.nama}</p>
                        <p class="text-slate-500 text-xs">${u.email}</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold ${sc.bg} border ${sc.border} ${sc.text}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span> ${sc.label}
                </span>
            </div>
            <div class="grid grid-cols-3 gap-2 mb-3">
                <div class="bg-slate-800/60 rounded-lg p-2.5">
                    <p class="text-slate-600 text-xs mb-0.5">Tipe</p>
                    <p class="${tc.text} text-xs font-semibold">${u.tipe}</p>
                </div>
                <div class="bg-slate-800/60 rounded-lg p-2.5">
                    <p class="text-slate-600 text-xs mb-0.5">Transaksi</p>
                    <p class="text-white font-bold font-mono text-xs">${u.transaksi}x</p>
                </div>
                <div class="bg-slate-800/60 rounded-lg p-2.5">
                    <p class="text-slate-600 text-xs mb-0.5">Kelas</p>
                    <p class="text-slate-300 text-xs truncate">${u.kelas}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="showDetail(${u.id})" class="flex-1 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs font-medium hover:border-primary-500 hover:text-primary-400 transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-eye text-xs"></i> Detail
                </button>
                <button onclick="openEditUser(${u.id})" class="flex-1 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs font-medium hover:border-amber-500 hover:text-amber-400 transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-pen text-xs"></i> Edit
                </button>
                <button onclick="openSuspend(${u.id})" class="py-2 px-3 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs hover:border-amber-500 hover:text-amber-400 transition-all flex items-center justify-center">
                    <i class="fa-solid ${u.status === 'suspended' ? 'fa-user-check' : 'fa-user-slash'} text-xs"></i>
                </button>
                <button onclick="openDelete(${u.id})" class="py-2 px-3 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs hover:border-red-500 hover:text-red-400 transition-all flex items-center justify-center">
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            </div>
        </div>`;
    }).join('');
}

// ===== MODAL UTILS =====
function openModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('opacity-0', 'pointer-events-none');
    m.classList.add('opacity-100', 'pointer-events-auto');
}
function closeModal(id) {
    const m = document.getElementById(id);
    m.classList.add('opacity-0', 'pointer-events-none');
    m.classList.remove('opacity-100', 'pointer-events-auto');
}

// Close on overlay click
['modal-add-user','modal-edit-user','modal-detail-user','modal-delete','modal-suspend'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

// ===== DETAIL =====
function showDetail(id) {
    const u = users.find(x => x.id === id);
    if (!u) return;
    const tc = tipeConfig[u.tipe] || tipeConfig.Staff;
    const sc = statusConfig[u.status] || statusConfig.inactive;
    document.getElementById('detail-user-content').innerHTML = `
        <div class="flex items-center gap-4 p-4 bg-slate-800/50 rounded-xl mb-1">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500/30 to-primary-700/30 border border-primary-500/20 flex items-center justify-center text-primary-300 text-xl font-bold flex-shrink-0">
                ${initials(u.nama)}
            </div>
            <div>
                <p class="text-white font-bold text-base">${u.nama}</p>
                <p class="text-slate-400 text-xs mt-0.5">${u.email}</p>
                <div class="flex gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold ${tc.bg} border ${tc.border} ${tc.text}">
                        <i class="fa-solid ${tc.icon} text-[10px]"></i> ${u.tipe}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold ${sc.bg} border ${sc.border} ${sc.text}">
                        ${sc.label}
                    </span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
            ${[
                ['Kelas / Jabatan', u.kelas],
                ['Total Transaksi', `<span class="font-mono font-bold text-white">${u.transaksi}x</span>`],
                ['Terdaftar Sejak', u.terdaftar],
                ['ID User', `<span class="font-mono text-primary-400">#USR-${String(u.id).padStart(4,'0')}</span>`],
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

// ===== EDIT =====
function openEditUser(id) {
    const u = users.find(x => x.id === id);
    if (!u) return;
    document.getElementById('edit-id').value    = u.id;
    document.getElementById('edit-nama').value  = u.nama;
    document.getElementById('edit-email').value = u.email;
    document.getElementById('edit-tipe').value  = u.tipe;
    document.getElementById('edit-kelas').value = u.kelas;
    document.getElementById('edit-status').value = u.status;
    openModal('modal-edit-user');
}

function submitEditUser() {
    const id     = parseInt(document.getElementById('edit-id').value);
    const u      = users.find(x => x.id === id);
    if (!u) return;
    u.nama   = document.getElementById('edit-nama').value.trim()  || u.nama;
    u.email  = document.getElementById('edit-email').value.trim() || u.email;
    u.tipe   = document.getElementById('edit-tipe').value;
    u.kelas  = document.getElementById('edit-kelas').value.trim() || u.kelas;
    u.status = document.getElementById('edit-status').value;
    closeModal('modal-edit-user');
    renderTable();
    showToast('Data user berhasil diperbarui', 'emerald');
}

// ===== TAMBAH =====
function submitAddUser() {
    const nama  = document.getElementById('add-nama').value.trim();
    const email = document.getElementById('add-email').value.trim();
    const tipe  = document.getElementById('add-tipe').value;
    const kelas = document.getElementById('add-kelas').value.trim();
    const pass  = document.getElementById('add-pass').value;

    if (!nama || !email || !kelas || !pass) {
        showToast('Lengkapi semua field yang wajib diisi!', 'red');
        return;
    }
    if (pass.length < 6) {
        showToast('Password minimal 6 karakter!', 'red');
        return;
    }

    const newId = Math.max(...users.map(u => u.id)) + 1;
    users.unshift({
        id: newId,
        nama, email, tipe, kelas,
        transaksi : 0,
        terdaftar : new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
        status    : 'active',
    });

    // Reset form
    ['add-nama','add-email','add-kelas','add-pass'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('add-tipe').value = 'Siswa';

    closeModal('modal-add-user');
    renderTable();
    showToast('User baru berhasil ditambahkan!', 'emerald');
}

// ===== SUSPEND =====
function openSuspend(id) {
    const u = users.find(x => x.id === id);
    if (!u) return;
    pendingSuspendId = id;
    const isSuspended = u.status === 'suspended';
    document.getElementById('suspend-modal-title').innerHTML = isSuspended
        ? '<i class="fa-solid fa-user-check text-emerald-400"></i> Aktifkan User'
        : '<i class="fa-solid fa-user-slash text-amber-400"></i> Suspend User';
    document.getElementById('suspend-name').textContent = u.nama;
    document.getElementById('suspend-modal-msg').innerHTML = isSuspended
        ? `User <strong class="text-white">${u.nama}</strong> akan diaktifkan kembali dan bisa login ke sistem.`
        : `User <strong class="text-white">${u.nama}</strong> akan disuspend dan tidak bisa login ke sistem.`;
    document.getElementById('suspend-confirm-btn').innerHTML = isSuspended
        ? '<i class="fa-solid fa-user-check text-xs"></i> Aktifkan'
        : '<i class="fa-solid fa-user-slash text-xs"></i> Suspend';
    document.getElementById('suspend-confirm-btn').className = isSuspended
        ? 'px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold transition-all flex items-center gap-2'
        : 'px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition-all flex items-center gap-2';
    openModal('modal-suspend');
}

function confirmSuspend() {
    const u = users.find(x => x.id === pendingSuspendId);
    if (!u) return;
    const wasActive = u.status === 'active';
    u.status = wasActive ? 'suspended' : 'active';
    closeModal('modal-suspend');
    renderTable();
    showToast(wasActive ? `${u.nama} berhasil disuspend` : `${u.nama} berhasil diaktifkan`, wasActive ? 'amber' : 'emerald');
}

// ===== DELETE =====
function openDelete(id) {
    const u = users.find(x => x.id === id);
    if (!u) return;
    pendingDeleteId = id;
    document.getElementById('delete-name').textContent = u.nama;
    openModal('modal-delete');
}

function confirmDelete() {
    users = users.filter(x => x.id !== pendingDeleteId);
    closeModal('modal-delete');
    renderTable();
    showToast('Akun user berhasil dihapus', 'red');
}

// ===== TOAST =====
function showToast(msg, color = 'emerald') {
    const colorMap = {
        emerald : 'text-emerald-400',
        red     : 'text-red-400',
        amber   : 'text-amber-400',
        blue    : 'text-blue-400',
    };
    const iconMap = {
        emerald : 'fa-circle-check',
        red     : 'fa-circle-xmark',
        amber   : 'fa-triangle-exclamation',
        blue    : 'fa-circle-info',
    };
    const toast     = document.getElementById('toast');
    const toastIcon = document.getElementById('toast-icon');
    const toastMsg  = document.getElementById('toast-msg');

    toastIcon.className = `fa-solid ${iconMap[color] || 'fa-circle-check'} ${colorMap[color] || 'text-emerald-400'}`;
    toastMsg.textContent = msg;

    toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-3');
    toast.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');

    setTimeout(() => {
        toast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-3');
        toast.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
    }, 3000);
}

// ===== INIT =====
renderTable();
</script>
@endpush