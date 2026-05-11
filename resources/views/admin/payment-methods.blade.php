@extends('layouts.admin')
@section('title', 'Kelola Metode Pembayaran')
@section('page-title', 'Metode Pembayaran')
@section('page-subtitle', 'Kelola rekening & metode pembayaran kantin')

@section('content')

{{-- Tombol Tambah --}}
<div class="flex justify-end mb-5">
    <button onclick="openAddModal()"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-500 text-white text-sm font-semibold transition-all shadow">
        <i class="fa-solid fa-plus"></i> Tambah Metode
    </button>
</div>

{{-- List --}}
@if($methods->isEmpty())
<div class="glass-card rounded-2xl p-16 text-center">
    <div class="text-5xl mb-3">💳</div>
    <p class="text-stone-800 font-semibold">Belum Ada Metode Pembayaran</p>
    <p class="text-stone-400 text-sm mt-1">Klik "Tambah Metode" untuk menambahkan.</p>
</div>
@else
<div class="space-y-4">
    @foreach($methods as $method)
    <div class="glass-card rounded-2xl p-5 border {{ $method->is_active ? 'border-orange-100' : 'border-slate-700/30 opacity-60' }}">
        <div class="flex items-start gap-4">

            {{-- Icon --}}
            <div class="w-12 h-12 rounded-xl bg-primary-500/10 border border-primary-500/20
                        flex items-center justify-center flex-shrink-0">
                <i class="{{ $method->logo_icon }} text-primary-400 text-lg"></i>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-1">
                    <h3 class="text-stone-800 font-bold">{{ $method->name }}</h3>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold border
                        {{ $method->is_active
                            ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                            : 'bg-orange-100 text-stone-400 border-slate-600' }}">
                        {{ $method->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold bg-orange-100 text-slate-400 border border-slate-600">
                        {{ $method->code }}
                    </span>
                </div>

                @if($method->account_number)
                <p class="text-stone-600 text-sm font-mono">{{ $method->account_number }}</p>
                <p class="text-stone-400 text-xs">a/n {{ $method->account_name }}</p>
                @endif

                @if($method->instructions)
                <p class="text-stone-400 text-xs mt-1 italic">{{ $method->instructions }}</p>
                @endif

                @if($method->isQris() && $method->qris_image)
                <img src="{{ Storage::url($method->qris_image) }}"
                     alt="QRIS" class="w-24 h-24 object-contain mt-2 rounded-lg border border-orange-100">
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <button onclick="toggleMethod({{ $method->id }})"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                               {{ $method->is_active
                                   ? 'bg-orange-100 border border-slate-600 text-slate-400 hover:bg-red-500/10 hover:border-red-500/20 hover:text-red-400'
                                   : 'bg-orange-100 border border-slate-600 text-slate-400 hover:bg-emerald-500/10 hover:border-emerald-500/20 hover:text-emerald-400' }}">
                    {{ $method->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
                <button onclick="openEditModal({{ $method->id }}, @js($method))"
                        class="px-3 py-1.5 rounded-lg bg-primary-500/10 border border-primary-500/20
                               text-primary-400 text-xs font-semibold hover:bg-primary-500/20 transition-all">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button onclick="deleteMethod({{ $method->id }}, '{{ $method->name }}')"
                        class="px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20
                               text-red-400 text-xs font-semibold hover:bg-red-500/20 transition-all">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ===== MODAL TAMBAH ===== --}}
<div id="add-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-6 max-w-lg w-full mx-4 border border-orange-100 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-stone-800 font-bold text-lg">Tambah Metode Pembayaran</h3>
            <button onclick="closeModal('add-modal')" class="w-8 h-8 rounded-lg bg-orange-100 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="add-form" onsubmit="submitAdd(event)" enctype="multipart/form-data" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Kode Unik <span class="text-red-400">*</span></label>
                    <input type="text" name="code" placeholder="transfer_bca"
                           class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500 font-mono"
                           required>
                    <p class="text-stone-400 text-xs mt-1">Huruf, angka, underscore</p>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Nama Tampilan <span class="text-red-400">*</span></label>
                    <input type="text" name="name" placeholder="Transfer BCA"
                           class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500"
                           required>
                </div>
            </div>

            <div>
                <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Tipe <span class="text-red-400">*</span></label>
                <select name="type" id="add-type" onchange="toggleFields('add')"
                        class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500">
                    <option value="bank_transfer">Transfer Bank</option>
                    <option value="ewallet">E-Wallet</option>
                    <option value="qris">QRIS</option>
                    <option value="cash">Tunai</option>
                </select>
            </div>

            {{-- Fields untuk bank/ewallet --}}
            <div id="add-bank-fields" class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Nomor Rekening</label>
                    <input type="text" name="account_number" placeholder="1234567890"
                           class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500 font-mono">
                </div>
                <div>
                    <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Atas Nama</label>
                    <input type="text" name="account_name" placeholder="Kantin PAUD"
                           class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500">
                </div>
            </div>

            {{-- Field QRIS --}}
            <div id="add-qris-field" class="hidden">
                <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Upload Gambar QRIS</label>
                <input type="file" name="qris_image" accept="image/*"
                       class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-slate-400">
            </div>

            <div>
                <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Instruksi Pembayaran</label>
                <textarea name="instructions" rows="2" placeholder="Transfer ke rekening di atas, lalu upload bukti..."
                          class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500 resize-none"></textarea>
            </div>

            <div>
                <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Urutan Tampil</label>
                <input type="number" name="sort_order" value="0" min="0"
                       class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('add-modal')"
                        class="flex-1 py-3 rounded-xl bg-orange-100 text-stone-600 text-sm font-semibold hover:bg-slate-600 transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-primary-600 hover:bg-primary-500 text-white text-sm font-semibold transition-all">
                    <i class="fa-solid fa-plus mr-1"></i> Tambahkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL EDIT ===== --}}
<div id="edit-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
    <div class="glass-card rounded-2xl p-6 max-w-lg w-full mx-4 border border-orange-100 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-stone-800 font-bold text-lg">Edit Metode Pembayaran</h3>
            <button onclick="closeModal('edit-modal')" class="w-8 h-8 rounded-lg bg-orange-100 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="edit-form" onsubmit="submitEdit(event)" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" id="edit-id">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Kode</label>
                    <input type="text" id="edit-code"
                           class="w-full px-4 py-2.5 bg-orange-100/50 border border-orange-100 rounded-xl text-sm text-stone-400 font-mono cursor-not-allowed"
                           disabled>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Nama Tampilan</label>
                    <input type="text" name="name" id="edit-name"
                           class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500">
                </div>
            </div>

            <div id="edit-bank-fields" class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Nomor Rekening</label>
                    <input type="text" name="account_number" id="edit-account-number"
                           class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500 font-mono">
                </div>
                <div>
                    <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Atas Nama</label>
                    <input type="text" name="account_name" id="edit-account-name"
                           class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500">
                </div>
            </div>

            <div id="edit-qris-field" class="hidden">
                <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Upload QRIS Baru</label>
                <input type="file" name="qris_image" accept="image/*"
                       class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-slate-400">
            </div>

            <div>
                <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Instruksi</label>
                <textarea name="instructions" id="edit-instructions" rows="2"
                          class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500 resize-none"></textarea>
            </div>

            <div>
                <label class="block text-slate-400 text-xs mb-1.5 font-semibold">Urutan Tampil</label>
                <input type="number" name="sort_order" id="edit-sort-order" min="0"
                       class="w-full px-4 py-2.5 bg-orange-50 border border-orange-100 rounded-xl text-sm text-white outline-none focus:border-primary-500">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('edit-modal')"
                        class="flex-1 py-3 rounded-xl bg-orange-100 text-stone-600 text-sm font-semibold hover:bg-slate-600 transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-primary-600 hover:bg-primary-500 text-white text-sm font-semibold transition-all">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Modal helpers ─────────────────────────────────────────
function openModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('opacity-0', 'pointer-events-none');
}
function closeModal(id) {
    const m = document.getElementById(id);
    m.classList.add('opacity-0', 'pointer-events-none');
}

// ── Toggle fields berdasarkan tipe ───────────────────────
function toggleFields(prefix) {
    const type = document.getElementById(prefix + '-type')?.value;
    const bankFields = document.getElementById(prefix + '-bank-fields');
    const qrisField  = document.getElementById(prefix + '-qris-field');

    if (bankFields) bankFields.classList.toggle('hidden', type === 'qris' || type === 'cash');
    if (qrisField)  qrisField.classList.toggle('hidden', type !== 'qris');
}

// ── Tambah ───────────────────────────────────────────────
function openAddModal() {
    document.getElementById('add-form').reset();
    toggleFields('add');
    openModal('add-modal');
}

function submitAdd(e) {
    e.preventDefault();
    const data = new FormData(e.target);

    fetch('/admin/payment-methods', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF },
        body: data,
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeModal('add-modal');
            showToast(res.message, 'emerald');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(res.message ?? 'Gagal menyimpan.', 'red');
        }
    });
}

// ── Edit ─────────────────────────────────────────────────
function openEditModal(id, method) {
    document.getElementById('edit-id').value           = id;
    document.getElementById('edit-code').value         = method.code;
    document.getElementById('edit-name').value         = method.name;
    document.getElementById('edit-account-number').value = method.account_number ?? '';
    document.getElementById('edit-account-name').value = method.account_name ?? '';
    document.getElementById('edit-instructions').value = method.instructions ?? '';
    document.getElementById('edit-sort-order').value   = method.sort_order ?? 0;

    // Toggle fields
    const bankFields = document.getElementById('edit-bank-fields');
    const qrisField  = document.getElementById('edit-qris-field');
    bankFields.classList.toggle('hidden', method.type === 'qris' || method.type === 'cash');
    qrisField.classList.toggle('hidden', method.type !== 'qris');

    openModal('edit-modal');
}

function submitEdit(e) {
    e.preventDefault();
    const id   = document.getElementById('edit-id').value;
    const data = new FormData(e.target);
    data.append('_method', 'PUT');

    fetch(`/admin/payment-methods/${id}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF },
        body: data,
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeModal('edit-modal');
            showToast(res.message, 'emerald');
            setTimeout(() => window.location.reload(), 1000);
        }
    });
}

// ── Toggle aktif ─────────────────────────────────────────
function toggleMethod(id) {
    fetch(`/admin/payment-methods/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(() => window.location.reload());
}

// ── Hapus ────────────────────────────────────────────────
function deleteMethod(id, name) {
    if (!confirm(`Hapus metode "${name}"? Tindakan ini tidak dapat dibatalkan.`)) return;

    fetch(`/admin/payment-methods/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showToast(res.message, 'emerald');
            setTimeout(() => window.location.reload(), 800);
        }
    });
}

// ── Toast ────────────────────────────────────────────────
function showToast(message, color) {
    const colors = { emerald: 'bg-emerald-600', red: 'bg-red-600' };
    const toast = document.createElement('div');
    toast.className = `fixed bottom-6 right-6 z-[100] px-4 py-3 rounded-xl ${colors[color] ?? 'bg-primary-600'} text-white text-sm font-semibold shadow-2xl`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Close modal on overlay click
['add-modal', 'edit-modal'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});
</script>
@endpush

@endsection