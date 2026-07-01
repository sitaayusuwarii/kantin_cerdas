@extends('layouts.admin')
@section('title', 'Kelola Menu — Admin')
@section('page-title', 'Kelola Menu')
@section('page-subtitle', 'Monitor dan kelola semua menu dari seluruh tenant')

@section('content')

{{-- ── STAT CARDS ──────────────────────────────────────── --}}
@php
$totalMenu  = $menus->count();
$totalAvail = $menus->where('is_available', 1)->count();
$totalHabis = $menus->where('is_available', 0)->count();
$bestSeller = $menus->sortByDesc('total_sold')->first();
@endphp

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Total Menu',  $totalMenu,                            'bg-orange-100 text-orange-600',  'fa-utensils'],
        ['Tersedia',    $totalAvail,                           'bg-emerald-100 text-emerald-600', 'fa-check'],
        ['Habis/Nonaktif', $totalHabis,                       'bg-red-100 text-red-500',         'fa-xmark'],
        ['Best Seller', $bestSeller ? $bestSeller->name : '-', 'bg-amber-100 text-amber-600',    'fa-fire'],
    ] as [$lbl, $val, $cls, $icon])
    <div class="bg-white border border-border rounded-2xl p-4 shadow-sm flex items-center gap-3">
        @php [$bg, $text] = explode(' ', $cls) @endphp
        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $bg }} flex-shrink-0">
            <i class="fa-solid {{ $icon }} text-sm {{ $text }}"></i>
        </div>
        <div class="min-w-0">
            <p class="font-bold text-lg text-stone-800 truncate">{{ $val }}</p>
            <p class="text-xs text-stone-400">{{ $lbl }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ── TOOLBAR ──────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
    <div class="relative flex-1 max-w-xs">
        <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 text-xs"></i>
        <input type="text" id="search-menu" placeholder="Cari nama menu..."
               class="w-full pl-9 pr-4 py-2.5 bg-orange-50 border border-border rounded-xl
                      text-sm text-stone-700 placeholder-stone-400 focus:outline-none
                      focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
    </div>
    <select id="filter-category"
            class="bg-orange-50 border border-border text-stone-700 text-sm
                   px-3 py-2.5 rounded-xl focus:outline-none focus:border-primary-400 transition-all">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
        @endforeach
    </select>
    <select id="filter-tenant"
            class="bg-orange-50 border border-border text-stone-700 text-sm
                   px-3 py-2.5 rounded-xl focus:outline-none focus:border-primary-400 transition-all">
        <option value="">Semua Tenant</option>
        @foreach($tenants as $tenant)
            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
        @endforeach
    </select>
    <select id="filter-status"
            class="bg-orange-50 border border-border text-stone-700 text-sm
                   px-3 py-2.5 rounded-xl focus:outline-none focus:border-primary-400 transition-all">
        <option value="">Semua Status</option>
        <option value="1">Tersedia</option>
        <option value="0">Habis</option>
    </select>
</div>

{{-- ── MENU GRID ────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="menu-grid">
    @forelse($menus as $menu)
    <div class="menu-card bg-white rounded-2xl shadow-sm border border-border overflow-hidden
                group hover:shadow-md transition-all duration-200 hover:-translate-y-0.5"
         data-id="{{ $menu->id }}" 
         data-name="{{ strtolower($menu->name) }}"
         data-category="{{ $menu->category_id }}"
         data-tenant="{{ $menu->tenant_id }}"
         data-status="{{ $menu->is_available ? '1' : '0' }}">

        {{-- Image --}}
        <div class="relative h-36 overflow-hidden
                    {{ $menu->image ? '' : 'bg-gradient-to-br from-amber-300 to-orange-400' }}">
            @if($menu->image)
                <img src="{{ asset('storage/' . $menu->image) }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="text-5xl">🍽️</span>
                </div>
            @endif

            {{-- Kategori badge --}}
            <div class="absolute top-2.5 left-2.5">
                <span class="bg-white/90 text-stone-600 text-[10px] font-semibold px-2 py-0.5 rounded-full shadow-sm">
                    {{ $menu->category->name ?? '-' }}
                </span>
            </div>

            {{-- Status badge --}}
            <div class="absolute top-2.5 right-2.5">
               <span class="top-status-badge text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm
                {{ $menu->is_available ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }}">
                {{ $menu->is_available ? 'Aktif' : 'Habis' }}
            </span>
            </div>

            {{-- Toggle availability (admin bisa on/off tapi tidak bisa tambah/hapus) --}}
            <div class="absolute bottom-2.5 right-2.5">
                <label class="relative inline-flex items-center cursor-pointer" title="Toggle ketersediaan">
                    <input type="checkbox"
                           class="sr-only peer toggle-availability"
                           data-id="{{ $menu->id }}"
                           {{ $menu->is_available ? 'checked' : '' }}>
                    <div class="w-9 h-5 bg-white/40 peer-checked:bg-emerald-500 rounded-full transition-colors shadow"></div>
                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                </label>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-4">
            {{-- Tenant label --}}
            <div class="flex items-center gap-1.5 mb-2">
                <i class="fa-solid fa-store text-primary-400 text-[10px]"></i>
                <span class="text-[10px] font-semibold text-primary-500">
                    {{ $menu->tenant->name ?? '-' }}
                </span>
            </div>

            <h3 class="font-bold text-sm text-stone-800 leading-tight mb-1 truncate">
                {{ $menu->name }}
            </h3>

            @if($menu->description)
            <p class="text-[11px] text-stone-400 mb-2 line-clamp-2 leading-relaxed">
                {{ $menu->description }}
            </p>
            @endif

            <div class="flex items-center justify-between mb-3">
                <p class="font-bold text-base text-primary-500">
                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                </p>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                    {{ $menu->is_available ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }} status-badge">
                    {{ $menu->is_available ? '✓ Tersedia' : '✗ Habis' }}
                </span>
            </div>

            <div class="flex items-center justify-between text-[10px] text-stone-400 pt-2 border-t border-stone-100">
                <span><i class="fa-solid fa-chart-simple mr-1 text-primary-300"></i>{{ $menu->total_sold ?? 0 }}× terjual</span>
                <span>Stok: <strong class="text-stone-600">{{ $menu->stock }}</strong></span>
            </div>
            <button onclick="openDeleteModal({{ $menu->id }}, '{{ addslashes($menu->name) }}')"
                    class="mt-3 w-full flex items-center justify-center gap-1.5 py-1.5 rounded-xl
                        text-[11px] font-semibold text-red-500 border border-red-200
                        hover:bg-red-50 transition-colors">
                <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus Menu
            </button>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16">
        <div class="text-5xl mb-3">🍽️</div>
        <p class="text-stone-500 font-semibold">Belum ada menu</p>
    </div>
    @endforelse
</div>

{{-- Empty filter state --}}
<div id="empty-filter-msg" style="display:none" class="text-center py-16">
    <div class="text-5xl mb-3">🔍</div>
    <p class="text-stone-500 font-semibold text-sm">Tidak ada menu ditemukan</p>
    <p class="text-stone-400 text-xs mt-1">Coba kata kunci atau filter lain</p>
</div>

{{-- ── DELETE CONFIRMATION MODAL ─────────────────────── --}}
<div id="delete-overlay"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden"
     style="animation-duration:.15s">
    <div id="delete-modal"
         class="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 shadow-xl
                w-[340px] mx-4 p-7 text-center scale-95 opacity-0 transition-all duration-150"
         id="delete-modal">

        {{-- Icon --}}
        <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-5">
            <i class="fa-solid fa-trash-can text-2xl text-red-500"></i>
        </div>

        <p class="font-semibold text-base text-stone-800 mb-1.5">Hapus menu ini?</p>
        <p class="text-sm text-stone-500 mb-4 leading-relaxed">
            Kamu akan menghapus <strong id="delete-menu-name" class="text-stone-700 font-semibold"></strong>.
            Tindakan ini tidak dapat dibatalkan.
        </p>

        {{-- Warning note --}}
        <div class="flex items-start gap-2.5 bg-amber-50 border border-amber-200 rounded-xl
                    px-3.5 py-2.5 text-left mb-5">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xs mt-0.5 flex-shrink-0"></i>
            <p class="text-xs text-amber-800 leading-relaxed">
                Semua order item yang terkait dengan menu ini juga akan dihapus.
            </p>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-2.5">
            <button onclick="closeDeleteModal()"
                    class="flex-1 py-2.5 rounded-xl border border-stone-200 text-sm font-medium
                           text-stone-600 hover:bg-stone-50 transition-colors">
                Batal
            </button>
            <button id="delete-confirm-btn"
                    onclick="confirmDeleteMenu()"
                    class="flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white
                           text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-trash-can text-xs"></i>
                Hapus
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
document.querySelectorAll('.toggle-availability').forEach(toggle => {
    toggle.addEventListener('change', async function () {
        const menuId    = this.dataset.id;
        const isChecked = this.checked;
        const card      = this.closest('.menu-card');
        const badge     = card.querySelector('.status-badge');
        const topBadge  = card.querySelector('.top-status-badge');

        try {
            const res = await fetch(`/admin/menu-management/toggle/${menuId}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const data = await res.json();

            if (data.success) {
                const available = data.is_available == 1;

                this.checked = available;

                if (badge) {
                    badge.textContent = available ? '✓ Tersedia' : '✗ Habis';
                    badge.className = `text-[10px] font-semibold px-2 py-0.5 rounded-full status-badge ${
                        available ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600'
                    }`;
                }

                if (topBadge) {
                    topBadge.textContent = available ? 'Aktif' : 'Habis';
                    topBadge.className = `top-status-badge text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm ${
                        available ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'
                    }`;
                }

                card.dataset.status = available ? '1' : '0';
            } else {
                this.checked = !isChecked;
                alert(data.message ?? 'Gagal mengubah status menu.');
            }
        } catch (error) {
            this.checked = !isChecked;
            alert('Gagal mengubah status menu.');
        }
    });
});

let _deleteTargetId = null;

function openDeleteModal(menuId, menuName) {
    _deleteTargetId = menuId;
    document.getElementById('delete-menu-name').textContent = menuName;

    const overlay = document.getElementById('delete-overlay');
    const modal   = document.getElementById('delete-modal');

    overlay.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.classList.remove('scale-95', 'opacity-0');
        modal.classList.add('scale-100', 'opacity-100');
    });
}

function closeDeleteModal() {
    const overlay = document.getElementById('delete-overlay');
    const modal   = document.getElementById('delete-modal');

    modal.classList.remove('scale-100', 'opacity-100');
    modal.classList.add('scale-95', 'opacity-0');
    setTimeout(() => overlay.classList.add('hidden'), 150);
    _deleteTargetId = null;
}

async function confirmDeleteMenu() {
    if (!_deleteTargetId) return;

    const btn = document.getElementById('delete-confirm-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Menghapus...';

    try {
        const res = await fetch(`/admin/menu-management/${_deleteTargetId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });

        const data = await res.json();

        if (data.success) {
            // Hapus card dari DOM
            document.querySelector(`.menu-card[data-id="${_deleteTargetId}"]`)?.remove();
            closeDeleteModal();

            const remaining = document.querySelectorAll('.menu-card').length;
            if (remaining === 0) {
                document.getElementById('menu-grid').innerHTML =
                    `<div class="col-span-full text-center py-16">
                        <div class="text-5xl mb-3">🍽️</div>
                        <p class="text-stone-500 font-semibold">Belum ada menu</p>
                    </div>`;
            }
        } else {
            alert(data.message ?? 'Gagal menghapus menu.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-trash-can text-xs"></i> Hapus';
        }
    } catch (e) {
        alert('Terjadi kesalahan. Silakan coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-trash-can text-xs"></i> Hapus';
    }
}

// Tutup modal jika klik backdrop
document.getElementById('delete-overlay').addEventListener('click', function (e) {
    if (e.target === this) closeDeleteModal();
});

// ── SEARCH & FILTER ──────────────────────────────────────
const searchInput      = document.getElementById('search-menu');
const filterCategory   = document.getElementById('filter-category');
const filterTenant     = document.getElementById('filter-tenant');
const filterStatus     = document.getElementById('filter-status');
const emptyMsg         = document.getElementById('empty-filter-msg');

function applyFilters() {
    const search   = searchInput.value.toLowerCase().trim();
    const category = filterCategory.value;
    const tenant   = filterTenant.value;
    const status   = filterStatus.value;

    const cards = document.querySelectorAll('.menu-card');
    let visible = 0;

    cards.forEach(card => {
        const matchName     = card.dataset.name.includes(search);
        const matchCategory = !category || card.dataset.category === category;
        const matchTenant   = !tenant   || card.dataset.tenant   === tenant;
        const matchStatus   = !status   || card.dataset.status   === status;

        if (matchName && matchCategory && matchTenant && matchStatus) {
            card.style.display = '';
            visible++;
        } else {
            card.style.display = 'none';
        }
    });

    emptyMsg.style.display = visible === 0 ? 'block' : 'none';
}

searchInput.addEventListener('input', applyFilters);
filterCategory.addEventListener('change', applyFilters);
filterTenant.addEventListener('change', applyFilters);
filterStatus.addEventListener('change', applyFilters);

</script>
@endpush