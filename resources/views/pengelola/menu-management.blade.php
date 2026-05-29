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
        {{-- Dropdown kategori dinamis dari backend --}}
        <select id="filter-category" name="category_id"
                class="bg-cream-50 border border-cream-300 text-forest-700 text-sm
                       px-3 py-2.5 rounded-xl focus:outline-none focus:border-forest-500 transition-all">
            <option value="">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <button onclick="openMenuModal()"
            class="btn-primary text-white font-semibold text-sm px-5 py-2.5 rounded-xl
                   flex items-center gap-2 shadow-lg flex-shrink-0">
        <i class="fa-solid fa-plus text-xs"></i>
        Tambah Menu Baru
    </button>
</div>

{{-- ── STAT CARDS (UI doc 14: card dengan icon) ───────────── --}}
@php
$totalMenu   = $menus->count();
$totalAvail  = $menus->where('is_available', 1)->count();
$totalHabis  = $menus->where('is_available', 0)->count();
$bestSeller  = $menus->sortByDesc('total_sold')->first();
@endphp
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Total Menu',  $totalMenu,                              'bg-forest-100 text-forest-700',   'fa-utensils'],
        ['Tersedia',    $totalAvail,                             'bg-emerald-100 text-emerald-700',  'fa-check'],
        ['Habis',       $totalHabis,                             'bg-red-100 text-red-600',          'fa-xmark'],
        ['Best Seller', $bestSeller ? $bestSeller->name : '-',   'bg-amber-100 text-amber-700',      'fa-fire'],
    ] as [$lbl, $val, $cls, $icon])
    <div class="bg-white border border-cream-200 rounded-2xl p-4 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ explode(' ', $cls)[0] }} flex-shrink-0">
            <i class="fa-solid {{ $icon }} text-sm {{ explode(' ', $cls)[1] }}"></i>
        </div>
        <div class="min-w-0">
            <p class="font-display font-bold text-lg text-forest-900 truncate">{{ $val }}</p>
            <p class="text-xs text-forest-500">{{ $lbl }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ── MENU GRID ────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($menus as $menu)
    <div class="bg-white rounded-2xl shadow-sm border border-cream-200 overflow-hidden
                group hover:shadow-md transition-all duration-200 hover:-translate-y-1 menu-card"
         data-name="{{ strtolower($menu->name) }}"
         data-category="{{ $menu->category_id }}">

        {{-- Image (UI doc 14: gradient + emoji fallback, real image jika ada) --}}
        <div class="relative h-36 flex items-center justify-center overflow-hidden
                    {{ $menu->image ? '' : 'bg-gradient-to-br from-amber-300 to-orange-400' }}">

            @if($menu->image)
                <img src="{{ asset('storage/' . $menu->image) }}"
                     class="w-full h-full object-cover">
            @else
                <span class="text-5xl group-hover:scale-110 transition-transform duration-300 select-none">🍽️</span>
            @endif

            <div class="absolute top-2.5 left-2.5">
                <span class="bg-white/85 text-forest-700 text-[10px] font-semibold px-2 py-0.5 rounded-full">
                    {{ $menu->category->name ?? '-' }}
                </span>
            </div>

            <div class="absolute top-2.5 right-2.5">
                <label class="relative inline-flex items-center cursor-pointer"
                       title="{{ $menu->is_available ? 'Tersedia' : 'Habis' }}">
                    <input type="checkbox"
                           class="sr-only peer toggle-availability"
                           data-id="{{ $menu->id }}"
                           {{ $menu->is_available ? 'checked' : '' }}>
                    <div class="w-9 h-5 bg-gray-300 peer-checked:bg-forest-500 rounded-full transition-colors shadow-sm"></div>
                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                </label>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-4">
            <h3 class="font-display font-semibold text-sm text-forest-900 leading-tight mb-1">
                {{ $menu->name }}
            </h3>

            <div class="flex items-center justify-between mb-3">
                <p class="font-display font-bold text-base text-forest-700">
                    Rp {{ number_format($menu->price) }}
                </p>
                <span class="status-badge text-[10px] font-semibold px-2 py-0.5 rounded-full
                    {{ $menu->is_available ? 'bg-forest-100 text-forest-700' : 'bg-red-100 text-red-600' }}">
                    {{ $menu->is_available ? '✓ Tersedia' : '✗ Habis' }}
                </span>
            </div>

            <div class="flex items-center justify-between text-[10px] text-forest-400 mb-3">
                <span><i class="fa-solid fa-chart-simple mr-1"></i>{{ $menu->total_sold ?? 0 }}× terjual bulan ini</span>
                <span>ID #{{ $menu->id }}</span>
            </div>

            <div class="flex gap-2">
                <button onclick="openMenuModal(
                            {{ $menu->id }},
                            @js($menu->name),
                            @js($menu->price),
                            @js($menu->category_id),
                            @js($menu->description),
                            @js($menu->stock),
                            @js($menu->is_available),
                            @js($menu->image ? asset('storage/' . $menu->image) : '')
                        )"
                        class="flex-1 flex items-center justify-center gap-1.5 bg-cream-100 hover:bg-cream-200
                               text-forest-700 text-xs font-semibold py-2 rounded-xl transition-colors border border-cream-200">
                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>Edit
                </button>
                <form action="{{ route('pengelola.menu.delete', $menu->id) }}"
                      method="POST" onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-9 h-9 flex items-center justify-center bg-red-50 hover:bg-red-100
                                   text-red-400 hover:text-red-500 rounded-xl border border-red-100 transition-colors">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pesan tidak ada hasil --}}
<div id="empty-filter-msg" style="display:none" class="text-center py-16">
    <div class="text-5xl mb-3">🔍</div>
    <p class="text-forest-600 font-semibold text-sm">Tidak ada menu ditemukan</p>
    <p class="text-forest-400 text-xs mt-1">Coba kata kunci atau kategori lain</p>
</div>

{{-- ═══════════════ MODAL (backend dari doc 13, struktur dari doc 14 diperluas) ════════════════════ --}}
<div id="menu-modal"
     style="display:none"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4">

    <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200 bg-forest-950">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 btn-primary rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-utensils text-white text-xs"></i>
                </div>
                <h3 class="font-display font-semibold text-cream-100" id="modal-heading">Tambah Menu Baru</h3>
            </div>
            <button type="button" onclick="closeMenuModal()"
                    class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-cream-200 text-sm"></i>
            </button>
        </div>

        <form id="menu-form" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">

            {{-- Body 2 Kolom --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-cream-200">

                {{-- Kolom Kiri --}}
                <div class="p-6 space-y-4">

                    <div>
                        <label class="block text-xs font-semibold text-forest-700 mb-1.5">
                            Nama Menu <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" id="menu-name" placeholder="Contoh: Nasi Gudeg Komplit"
                               class="w-full px-4 py-2.5 bg-cream-100 border border-cream-300 rounded-xl
                                      text-sm text-forest-800 focus:outline-none focus:border-forest-500
                                      focus:ring-2 focus:ring-forest-100 transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-forest-700 mb-1.5">
                                Harga <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-forest-500 text-xs font-semibold">Rp</span>
                                <input type="number" name="price" id="menu-price" placeholder="12000"
                                       class="w-full pl-9 pr-3 py-2.5 bg-cream-100 border border-cream-300 rounded-xl
                                              text-sm text-forest-800 focus:outline-none focus:border-forest-500 transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-forest-700 mb-1.5">Kategori</label>
                            <select name="category_id" id="menu-category"
                                    class="w-full px-3 py-2.5 bg-cream-100 border border-cream-300 rounded-xl
                                           text-sm text-forest-800 focus:outline-none focus:border-forest-500 transition-all">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-forest-700 mb-1.5">Stock</label>
                        <input type="number" name="stock" id="menu-stock" placeholder="Contoh: 50"
                               class="w-full px-4 py-2.5 bg-cream-100 border border-cream-300 rounded-xl
                                      text-sm text-forest-800 focus:outline-none focus:border-forest-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-forest-700 mb-1.5">Deskripsi</label>
                        <textarea name="description" id="menu-description" rows="4"
                                  placeholder="Deskripsi singkat menu..."
                                  class="w-full px-4 py-2.5 bg-cream-100 border border-cream-300 rounded-xl
                                         text-sm text-forest-800 resize-none focus:outline-none
                                         focus:border-forest-500 transition-all"></textarea>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="p-6 space-y-4">

                    <div>
                        <label class="block text-xs font-semibold text-forest-700 mb-1.5">Foto Menu</label>
                        <div class="relative border-2 border-dashed border-cream-300 bg-cream-100
                                    rounded-xl p-4 text-center hover:border-forest-400 transition-colors cursor-pointer">
                            <input type="file" name="image" id="menu-image" accept="image/*"
                                   class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                            <div id="upload-placeholder">
                                <i class="fa-solid fa-cloud-arrow-up text-forest-300 text-3xl mb-2 block"></i>
                                <p class="text-xs text-forest-600 font-medium">Klik atau drag gambar di sini</p>
                                <p class="text-[10px] text-forest-400 mt-0.5">JPG, PNG · Maks 2MB</p>
                            </div>
                            <img id="image-preview" class="hidden w-full h-40 object-cover rounded-xl">
                        </div>
                        <div id="file-info" class="hidden mt-2 flex items-center justify-between
                                                    bg-forest-50 border border-forest-200 rounded-xl px-3 py-2">
                            <p id="file-name" class="text-xs text-forest-700 font-medium truncate"></p>
                            <button type="button" onclick="clearImagePreview()"
                                    class="text-red-400 hover:text-red-600 text-xs ml-2 flex-shrink-0">
                                <i class="fa-solid fa-xmark"></i> Hapus
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-forest-700 mb-2">Status</label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="is_available" value="1" class="sr-only" checked>
                                <div class="status-opt border-2 border-forest-500 bg-forest-50 p-2.5 rounded-xl
                                            text-center text-xs font-semibold text-forest-700 transition-all">
                                    <i class="fa-solid fa-check mr-1"></i>Tersedia
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="is_available" value="0" class="sr-only">
                                <div class="status-opt border-2 border-cream-300 p-2.5 rounded-xl
                                            text-center text-xs font-semibold text-forest-500
                                            hover:border-red-300 transition-all">
                                    <i class="fa-solid fa-xmark mr-1"></i>Habis
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-6 py-4 bg-cream-100 border-t border-cream-200">
                <button type="button" onclick="closeMenuModal()"
                        class="flex-1 py-2.5 rounded-xl border border-cream-300 text-sm font-semibold
                               text-forest-700 hover:bg-cream-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 btn-primary text-white py-2.5 rounded-xl text-sm font-semibold shadow-lg">
                    <i class="fa-solid fa-save mr-1.5"></i>Simpan Menu
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Preview gambar ────────────────────────────────────────
document.getElementById('menu-image').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (event) {
        document.getElementById('image-preview').src = event.target.result;
        document.getElementById('image-preview').classList.remove('hidden');
        document.getElementById('upload-placeholder').classList.add('hidden');
        document.getElementById('file-info').classList.remove('hidden');
        document.getElementById('file-name').textContent = file.name;
    };
    reader.readAsDataURL(file);
});

// ── Toggle availability (AJAX) ────────────────────────────
document.querySelectorAll('.toggle-availability').forEach(toggle => {
    toggle.addEventListener('change', async function () {
        const menuId    = this.dataset.id;
        const isChecked = this.checked;
        const card      = this.closest('.group');
        const badge     = card.querySelector('.status-badge');

        try {
            const response = await fetch(`/pengelola/menu-management/toggle/${menuId}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });
            const data = await response.json();
            if (data.success) {
                if (badge) {
                    badge.textContent = isChecked ? '✓ Tersedia' : '✗ Habis';
                    badge.className = `status-badge text-[10px] font-semibold px-2 py-0.5 rounded-full ${
                        isChecked ? 'bg-forest-100 text-forest-700' : 'bg-red-100 text-red-600'
                    }`;
                }
            } else {
                this.checked = !isChecked;
            }
        } catch (error) {
            this.checked = !isChecked;
            alert('Gagal mengubah status menu, coba lagi.');
        }
    });
});

// ── Modal ─────────────────────────────────────────────────
function openMenuModal(
    id = null, name = '', price = '', category = '',
    description = '', stock = '', is_available = 1, image = ''
) {
    const modal = document.getElementById('menu-modal');
    modal.style.display = 'flex';
    const form = document.getElementById('menu-form');

    if (!id) { form.reset(); clearImagePreview(); }

    document.getElementById('menu-name').value        = name;
    document.getElementById('menu-price').value       = price;
    document.getElementById('menu-category').value    = category;
    document.getElementById('menu-description').value = description;
    document.getElementById('menu-stock').value       = stock;

    if (image) {
        document.getElementById('image-preview').src = image;
        document.getElementById('image-preview').classList.remove('hidden');
        document.getElementById('upload-placeholder').classList.add('hidden');
        document.getElementById('file-info').classList.remove('hidden');
        document.getElementById('file-name').textContent = 'Foto saat ini';
    } else {
        clearImagePreview();
    }

    const statusRadio = document.querySelector(`input[name="is_available"][value="${is_available}"]`);
    if (statusRadio) { statusRadio.checked = true; statusRadio.dispatchEvent(new Event('change')); }

    if (id) {
        document.getElementById('modal-heading').textContent = 'Edit Menu';
        form.action = `/pengelola/menu-management/update/${id}`;
        document.getElementById('form-method').value = 'PUT';
    } else {
        document.getElementById('modal-heading').textContent = 'Tambah Menu Baru';
        form.action = `/pengelola/menu-management/store`;
        document.getElementById('form-method').value = 'POST';
    }
}

function clearImagePreview() {
    document.getElementById('menu-image').value = '';
    document.getElementById('image-preview').classList.add('hidden');
    document.getElementById('upload-placeholder').classList.remove('hidden');
    document.getElementById('file-info').classList.add('hidden');
}

function closeMenuModal() {
    document.getElementById('menu-modal').style.display = 'none';
    clearImagePreview();
    document.querySelectorAll('.status-opt').forEach(el => {
        el.classList.remove('border-forest-500', 'bg-forest-50', 'text-forest-700');
        el.classList.add('border-cream-300', 'text-forest-500');
    });
}

document.getElementById('menu-modal').addEventListener('click', function(e) {
    if (e.target === this) closeMenuModal();
});

// ── Radio status style ────────────────────────────────────
document.querySelectorAll('input[name="is_available"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.status-opt').forEach(el => {
            el.classList.remove('border-forest-500', 'bg-forest-50', 'text-forest-700');
            el.classList.add('border-cream-300', 'text-forest-500');
        });
        const selectedEl = radio.nextElementSibling;
        if (selectedEl) {
            selectedEl.classList.remove('border-cream-300', 'text-forest-500');
            selectedEl.classList.add('border-forest-500', 'bg-forest-50', 'text-forest-700');
        }
    });
});

// ── Filter & Search ───────────────────────────────────────
const searchInput    = document.getElementById('search-menu');
const categorySelect = document.getElementById('filter-category');
const menuCards      = document.querySelectorAll('.menu-card');

function filterMenus() {
    const keyword  = searchInput.value.toLowerCase().trim();
    const category = categorySelect.value;
    let visibleCount = 0;

    menuCards.forEach(card => {
        const matchSearch   = card.dataset.name.includes(keyword);
        const matchCategory = category === '' || card.dataset.category === category;
        if (matchSearch && matchCategory) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    document.getElementById('empty-filter-msg').style.display = visibleCount === 0 ? 'block' : 'none';
}

searchInput.addEventListener('input', filterMenus);
categorySelect.addEventListener('change', filterMenus);
</script>
@endpush
