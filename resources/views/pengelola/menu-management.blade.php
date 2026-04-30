@extends('layouts.pengelola')
@section('title', 'Kelola Menu')
@section('page-title', 'Kelola Menu')
@section('page-subtitle', 'Tambah, ubah, dan kelola daftar menu kantin')

@section('content')

{{-- ── TOOLBAR ──────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex items-center gap-2 flex-wrap">
        <div class="relative">
            <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-forest-400 text-xs"></i>
            <input type="text" id="search-menu" placeholder="Cari menu..."
                   class="pl-9 pr-4 py-2.5 bg-cream-50 border border-cream-300 rounded-xl
                          text-sm text-forest-800 placeholder-forest-400 focus:outline-none
                          focus:border-forest-500 focus:ring-2 focus:ring-forest-100 transition-all w-44">
        </div>
        <select class="bg-cream-50 border border-cream-300 text-forest-700 text-sm
                       px-3 py-2.5 rounded-xl focus:outline-none focus:border-forest-500 transition-all">
            <option>Semua Kategori</option>
            <option>Makanan</option>
            <option>Minuman</option>
            <option>Snack</option>
        </select>
    </div>
    <button onclick="openMenuModal()"
            class="btn-primary text-white font-semibold text-sm px-5 py-2.5 rounded-xl
                   flex items-center gap-2 shadow-lg flex-shrink-0">
        <i class="fa-solid fa-plus text-xs"></i>
        Tambah Menu Baru
    </button>
</div>

{{-- ── STAT CHIPS ───────────────────────────────────────── --}}
<div class="flex flex-wrap gap-3 mb-6">
    @foreach([['Total Menu','12','bg-forest-100 text-forest-700'],['Tersedia','10','bg-emerald-100 text-emerald-700'],['Habis','2','bg-red-100 text-red-600']] as [$lbl,$val,$cls])
    <div class="{{ $cls }} text-xs font-bold px-4 py-2 rounded-full flex items-center gap-1.5">
        <span class="font-display text-base font-bold">{{ $val }}</span> {{ $lbl }}
    </div>
    @endforeach
</div>

{{-- ── MENU GRID ────────────────────────────────────────── --}}
@php
$menus = [
    ['id'=>1, 'name'=>'Nasi Gudeg Komplit', 'price'=>'Rp 12.000','cat'=>'Makanan','avail'=>true, 'sold'=>48,'emoji'=>'🍛','grad'=>'from-yellow-300 to-orange-400'],
    ['id'=>2, 'name'=>'Mie Goreng Spesial', 'price'=>'Rp 10.000','cat'=>'Makanan','avail'=>true, 'sold'=>41,'emoji'=>'🍜','grad'=>'from-orange-400 to-red-400'],
    ['id'=>3, 'name'=>'Nasi Ayam Geprek',   'price'=>'Rp 13.000','cat'=>'Makanan','avail'=>true, 'sold'=>35,'emoji'=>'🍗','grad'=>'from-red-400 to-rose-500'],
    ['id'=>4, 'name'=>'Bakso Urat Jumbo',   'price'=>'Rp 11.000','cat'=>'Makanan','avail'=>true, 'sold'=>29,'emoji'=>'🍲','grad'=>'from-amber-400 to-yellow-400'],
    ['id'=>5, 'name'=>'Es Teh Manis',       'price'=>'Rp 4.000', 'cat'=>'Minuman','avail'=>true, 'sold'=>62,'emoji'=>'🧋','grad'=>'from-amber-200 to-amber-400'],
    ['id'=>6, 'name'=>'Jus Alpukat',        'price'=>'Rp 8.000', 'cat'=>'Minuman','avail'=>false,'sold'=>18,'emoji'=>'🥑','grad'=>'from-green-400 to-emerald-500'],
    ['id'=>7, 'name'=>'Pisang Goreng',      'price'=>'Rp 5.000', 'cat'=>'Snack',  'avail'=>true, 'sold'=>24,'emoji'=>'🍌','grad'=>'from-yellow-300 to-amber-400'],
    ['id'=>8, 'name'=>'Indomie Rebus',      'price'=>'Rp 7.000', 'cat'=>'Makanan','avail'=>false,'sold'=>20,'emoji'=>'🍝','grad'=>'from-orange-300 to-yellow-400'],
];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($menus as $m)
    <div class="bg-cream-50 rounded-2xl shadow-sm border border-cream-200 overflow-hidden
                group hover:shadow-md transition-all duration-200 hover:-translate-y-1">
        {{-- Image --}}
        <div class="relative h-36 bg-gradient-to-br {{ $m['grad'] }} flex items-center justify-center overflow-hidden">
            <span class="text-5xl group-hover:scale-110 transition-transform duration-300 select-none">{{ $m['emoji'] }}</span>
            <div class="absolute top-2.5 left-2.5">
                <span class="bg-white/85 text-forest-700 text-[10px] font-semibold px-2 py-0.5 rounded-full">{{ $m['cat'] }}</span>
            </div>
            {{-- Toggle status --}}
            <div class="absolute top-2.5 right-2.5">
                <label class="relative inline-flex items-center cursor-pointer" title="{{ $m['avail']?'Tersedia':'Habis' }}">
                    <input type="checkbox" class="sr-only peer" {{ $m['avail']?'checked':'' }}>
                    <div class="w-9 h-5 bg-gray-300 peer-checked:bg-forest-500 rounded-full transition-colors shadow-sm"></div>
                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                </label>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-4">
            <div class="flex items-start justify-between gap-1 mb-1">
                <h3 class="font-display font-semibold text-sm text-forest-900 leading-tight">{{ $m['name'] }}</h3>
            </div>
            <div class="flex items-center justify-between mb-3">
                <p class="font-display font-bold text-base text-forest-700">{{ $m['price'] }}</p>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                    {{ $m['avail'] ? 'bg-forest-100 text-forest-700' : 'bg-red-100 text-red-600' }}">
                    {{ $m['avail'] ? '✓ Tersedia' : '✗ Habis' }}
                </span>
            </div>
            <p class="text-[10px] text-forest-400 mb-3 flex items-center gap-1">
                <i class="fa-solid fa-chart-simple text-forest-300"></i>
                {{ $m['sold'] }}× terjual bulan ini
            </p>
            <div class="flex gap-2">
                <button onclick="openMenuModal({{ $m['id'] }})"
                        class="flex-1 flex items-center justify-center gap-1.5 bg-cream-100 hover:bg-cream-200
                               text-forest-700 text-xs font-semibold py-2 rounded-xl transition-colors border border-cream-200">
                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>Edit
                </button>
                <button onclick="confirmDelete({{ $m['id'] }}, '{{ $m['name'] }}')"
                        class="w-9 h-9 flex items-center justify-center bg-red-50 hover:bg-red-100
                               text-red-400 hover:text-red-500 rounded-xl border border-red-100 transition-colors">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════ MODAL ════════════════════ --}}
<div id="menu-modal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-cream-50 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden animate-fade-up">

        <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200 bg-forest-950">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 btn-primary rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-utensils text-white text-xs"></i>
                </div>
                <h3 class="font-display font-semibold text-cream-100" id="modal-heading">Tambah Menu Baru</h3>
            </div>
            <button onclick="closeMenuModal()"
                    class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-cream-200 text-sm"></i>
            </button>
        </div>

        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            {{-- Nama --}}
            <div>
                <label class="block text-xs font-semibold text-forest-700 mb-1.5">
                    Nama Menu <span class="text-red-400">*</span>
                </label>
                <input type="text" placeholder="Contoh: Nasi Gudeg Komplit"
                       class="w-full px-4 py-2.5 bg-cream-100 border border-cream-300 rounded-xl
                              text-sm text-forest-800 focus:outline-none focus:border-forest-500
                              focus:ring-2 focus:ring-forest-100 transition-all">
            </div>
            {{-- Harga + Kategori --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-forest-700 mb-1.5">
                        Harga <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-forest-500 text-xs font-semibold">Rp</span>
                        <input type="text" placeholder="12.000"
                               class="w-full pl-9 pr-3 py-2.5 bg-cream-100 border border-cream-300 rounded-xl
                                      text-sm text-forest-800 focus:outline-none focus:border-forest-500 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-forest-700 mb-1.5">Kategori</label>
                    <select class="w-full px-3 py-2.5 bg-cream-100 border border-cream-300 rounded-xl
                                   text-sm text-forest-800 focus:outline-none focus:border-forest-500 transition-all">
                        <option>Makanan</option>
                        <option>Minuman</option>
                        <option>Snack</option>
                    </select>
                </div>
            </div>
            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-semibold text-forest-700 mb-1.5">Deskripsi</label>
                <textarea rows="2" placeholder="Deskripsi singkat menu..."
                          class="w-full px-4 py-2.5 bg-cream-100 border border-cream-300 rounded-xl
                                 text-sm text-forest-800 resize-none focus:outline-none
                                 focus:border-forest-500 transition-all"></textarea>
            </div>
            {{-- Upload --}}
            <div>
                <label class="block text-xs font-semibold text-forest-700 mb-1.5">Foto Menu</label>
                <div class="relative border-2 border-dashed border-cream-300 bg-cream-100
                            rounded-xl p-5 text-center hover:border-forest-400 transition-colors cursor-pointer">
                    <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    <i class="fa-solid fa-cloud-arrow-up text-forest-300 text-3xl mb-2 block"></i>
                    <p class="text-xs text-forest-600 font-medium">Klik atau drag gambar di sini</p>
                    <p class="text-[10px] text-forest-400 mt-0.5">JPG, PNG · Maks 2MB</p>
                </div>
            </div>
            {{-- Status --}}
            <div>
                <label class="block text-xs font-semibold text-forest-700 mb-2">Status</label>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="modal-status" value="1" class="sr-only" checked>
                        <div class="status-opt border-2 border-forest-500 bg-forest-50 p-2.5 rounded-xl text-center text-xs font-semibold text-forest-700 transition-all">
                            <i class="fa-solid fa-check mr-1"></i>Tersedia
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="modal-status" value="0" class="sr-only">
                        <div class="status-opt border-2 border-cream-300 p-2.5 rounded-xl text-center text-xs font-semibold text-forest-500 hover:border-red-300 transition-all">
                            <i class="fa-solid fa-xmark mr-1"></i>Habis
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex gap-3 px-6 py-4 bg-cream-100 border-t border-cream-200">
            <button onclick="closeMenuModal()"
                    class="flex-1 py-2.5 rounded-xl border border-cream-300 text-sm font-semibold
                           text-forest-700 hover:bg-cream-200 transition-colors">
                Batal
            </button>
            <button class="flex-1 btn-primary text-white py-2.5 rounded-xl text-sm font-semibold shadow-lg">
                <i class="fa-solid fa-save mr-1.5"></i>Simpan Menu
            </button>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
function openMenuModal(id = null) {
    document.getElementById('menu-modal').classList.add('open');
    document.getElementById('modal-heading').textContent = id ? 'Edit Menu #' + id : 'Tambah Menu Baru';
}
function closeMenuModal() {
    document.getElementById('menu-modal').classList.remove('open');
}
function confirmDelete(id, name) {
    if (confirm('Hapus "' + name + '"?\nTindakan ini tidak dapat dibatalkan.')) {
        alert('Menu dihapus. (dummy)');
    }
}
document.getElementById('menu-modal').addEventListener('click', e => {
    if (e.target === document.getElementById('menu-modal')) closeMenuModal();
});
document.querySelectorAll('input[name="modal-status"]').forEach(r => {
    r.addEventListener('change', () => {
        document.querySelectorAll('.status-opt').forEach(el => {
            el.className = 'status-opt border-2 border-cream-300 p-2.5 rounded-xl text-center text-xs font-semibold text-forest-500 hover:border-red-300 transition-all';
        });
        r.nextElementSibling.className = 'status-opt border-2 border-forest-500 bg-forest-50 p-2.5 rounded-xl text-center text-xs font-semibold text-forest-700 transition-all';
    });
});
</script>
@endpush
