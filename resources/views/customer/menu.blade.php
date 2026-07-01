@extends('layouts.app')
@section('title', 'Menu - SmartCanteen')

@section('content')

{{-- Floating Cart --}}
<a href="{{ url('/cart') }}"
   id="floating-cart-btn"
   class="fixed bottom-6 right-5 sm:right-6 z-30 inline-flex items-center gap-3 rounded-3xl bg-gradient-to-br from-orange-500 to-orange-600 px-5 py-4 font-heading font-bold text-white shadow-xl shadow-orange-200 transition hover:-translate-y-0.5 hover:shadow-2xl active:scale-95">
    <div class="relative flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15">
        <i class="fa-solid fa-cart-shopping text-lg"></i>
        <span id="cart-badge"
              class="absolute -right-2 -top-2 hidden h-6 min-w-6 items-center justify-center rounded-full bg-white px-1.5 text-[11px] font-black text-orange-600 shadow">0</span>
    </div>
    <div class="hidden text-left sm:block">
        <p class="text-[11px] leading-none text-orange-100" id="cart-btn-count">0 item</p>
        <p class="mt-1 text-base leading-none" id="cart-btn-price">Rp 0</p>
    </div>
</a>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 pb-28">

    {{-- Header --}}
    <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 px-5 py-7 shadow-xl shadow-orange-200/50 sm:px-8 lg:px-10">
        <div class="absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.22),transparent_40%)]"></div>
        <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-5 right-10 hidden text-white/10 lg:block">
            <i class="fa-solid fa-utensils text-[8rem]"></i>
        </div>

        <div class="relative grid gap-6 lg:grid-cols-[1fr_320px] lg:items-end">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/15 px-4 py-2 text-sm font-bold text-white backdrop-blur">
                    <i class="fa-solid fa-store"></i>
                    Menu Kantin
                </div>
                <h1 class="mt-5 font-heading text-3xl font-extrabold leading-tight text-white md:text-4xl">
                    Pilih menu favoritmu
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-orange-50 md:text-base">
                    Cari makanan, lihat deskripsi menu, pilih tenant, lalu masukkan pesanan ke keranjang.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-3 rounded-3xl border border-white/20 bg-white/15 p-4 text-white backdrop-blur">
                <div>
                    <p class="font-heading text-2xl font-extrabold">{{ $menus->count() }}</p>
                    <p class="mt-1 text-[11px] text-orange-100">Menu</p>
                </div>
                <div>
                    <p class="font-heading text-2xl font-extrabold">{{ $categories->count() }}</p>
                    <p class="mt-1 text-[11px] text-orange-100">Kategori</p>
                </div>
                <div>
                    <p class="font-heading text-2xl font-extrabold">{{ $tenants->count() }}</p>
                    <p class="mt-1 text-[11px] text-orange-100">Tenant</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Search and Filter --}}
    <section class="sticky top-20 z-20 mt-6 rounded-3xl border border-orange-100 bg-white/95 p-3 shadow-sm backdrop-blur">
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_230px_230px]">
            <div class="relative">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input id="search-input"
                       type="text"
                       placeholder="Cari menu, deskripsi, kategori, atau tenant..."
                       class="h-[52px] w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-11 pr-4 text-sm font-medium text-gray-700 placeholder:text-gray-400 focus:border-orange-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-orange-100">
            </div>

            <div class="relative">
                <select id="category-select"
                        class="h-[52px] w-full appearance-none rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3.5 pr-10 text-sm font-bold text-gray-700 focus:border-orange-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-orange-100">
                    <option value="Semua">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            </div>

            <div class="relative">
                <select id="tenant-select"
                        class="h-[52px] w-full appearance-none rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3.5 pr-10 text-sm font-bold text-gray-700 focus:border-orange-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-orange-100">
                    <option value="Semua">Semua Tenant</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            </div>
        </div>
    </section>

    {{-- Menu Grid --}}
    <section class="mt-6">
        <div id="menu-grid" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($menus as $menu)
                @php
                    $categoryName = $menu->category->name ?? 'Menu';
                    $tenantName = $menu->tenant->name ?? 'Tenant';
                    $description = trim($menu->description ?? '');
                    $imageUrl = $menu->image ? asset('storage/' . $menu->image) : '';
                @endphp

                <article class="menu-card group flex h-full flex-col overflow-hidden rounded-3xl border border-orange-100 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-xl"
                         data-id="{{ $menu->id }}"
                         data-name="{{ $menu->name }}"
                         data-cat="{{ $categoryName }}"
                         data-tenant="{{ $menu->tenant_id }}"
                         data-search="{{ \Illuminate\Support\Str::lower($menu->name . ' ' . $description . ' ' . $categoryName . ' ' . $tenantName) }}">

                    <div class="relative h-48 overflow-hidden bg-orange-50">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $menu->name }}"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-orange-100 to-orange-200 text-orange-500">
                                <i class="fa-solid fa-bowl-food text-5xl"></i>
                            </div>
                        @endif

                        <div class="absolute inset-x-0 top-0 flex items-start justify-between gap-2 p-3">
                            <div class="flex max-w-[75%] flex-col gap-1.5">
                                <span class="w-fit rounded-full bg-white/95 px-3 py-1 text-[11px] font-extrabold text-gray-700 shadow-sm">
                                    {{ $categoryName }}
                                </span>
                                <span class="w-fit rounded-full bg-orange-500 px-3 py-1 text-[11px] font-extrabold text-white shadow-sm">
                                    {{ $tenantName }}
                                </span>
                            </div>

                            @if($menu->stock <= 0)
                                <span class="rounded-full bg-gray-900 px-3 py-1 text-[11px] font-extrabold text-white shadow-sm">
                                    Habis
                                </span>
                            @elseif($menu->stock <= 5)
                                <span class="rounded-full bg-rose-500 px-3 py-1 text-[11px] font-extrabold text-white shadow-sm">
                                    Stok {{ $menu->stock }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col p-4">
                        <div class="flex-1">
                            <h3 class="font-heading text-lg font-extrabold leading-snug text-gray-950">
                                {{ $menu->name }}
                            </h3>

                            <p class="mt-2 min-h-[44px] text-sm leading-5 text-gray-500">
                                {{ $description !== '' ? $description : 'Deskripsi menu belum tersedia.' }}
                            </p>

                            <div class="mt-4 flex items-center justify-between rounded-2xl bg-orange-50 px-3 py-2">
                                <span class="text-xs font-bold text-gray-500">Stok tersedia</span>
                                <span class="text-sm font-extrabold {{ $menu->stock > 0 ? 'text-orange-600' : 'text-gray-400' }}">
                                    {{ $menu->stock }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400">Harga</p>
                                <p class="font-heading text-xl font-extrabold text-orange-600">
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </p>
                            </div>

                            @if($menu->stock > 0)
                                <button id="add-btn-{{ $menu->id }}"
                                        type="button"
                                        onclick="addToCart({{ $menu->id }}, @js($menu->name), {{ $menu->price }}, @js($imageUrl))"
                                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 px-4 py-3 text-sm font-extrabold text-white shadow-lg shadow-orange-100 transition hover:-translate-y-0.5 hover:shadow-orange-200 active:scale-95">
                                    <i class="fa-solid fa-cart-plus text-xs"></i>
                                    Tambah
                                </button>
                            @else
                                <button disabled
                                        type="button"
                                        class="inline-flex cursor-not-allowed items-center gap-2 rounded-2xl bg-gray-100 px-4 py-3 text-sm font-extrabold text-gray-400">
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-gray-200 bg-white p-12 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-gray-100 text-gray-400">
                        <i class="fa-solid fa-bowl-food text-2xl"></i>
                    </div>
                    <p class="mt-4 font-heading text-lg font-bold text-gray-600">Belum ada menu</p>
                    <p class="mt-1 text-sm text-gray-400">Menu dari tenant akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <div id="no-result" class="hidden rounded-3xl border border-dashed border-orange-200 bg-white p-12 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-orange-50 text-orange-400">
                <i class="fa-solid fa-magnifying-glass text-2xl"></i>
            </div>
            <p class="mt-4 font-heading text-lg font-bold text-gray-700">Menu tidak ditemukan</p>
            <p class="mt-1 text-sm text-gray-400">Coba ubah kata kunci, kategori, atau tenant.</p>
        </div>
    </section>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

function formatRp(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function updateBadge(qty, price) {
    const badge = document.getElementById('cart-badge');
    const count = document.getElementById('cart-btn-count');
    const total = document.getElementById('cart-btn-price');

    if (qty > 0) {
        badge.textContent = qty;
        badge.classList.remove('hidden');
        badge.classList.add('flex');
    } else {
        badge.classList.add('hidden');
        badge.classList.remove('flex');
    }

    if (count) count.textContent = qty + ' item';
    if (total) total.textContent = formatRp(price);
}

function loadCartBadge() {
    fetch('/cart/data', { headers: { 'X-CSRF-TOKEN': CSRF } })
        .then(response => response.json())
        .then(result => {
            const items = result.items || [];
            const qty = items.reduce((sum, item) => sum + Number(item.qty || 0), 0);
            const price = items.reduce((sum, item) => sum + (Number(item.price || 0) * Number(item.qty || 0)), 0);

            updateBadge(qty, price);
            localStorage.setItem('cart_count', qty);
            localStorage.setItem('cart_price', price);
        })
        .catch(() => {});
}

function addToCart(menuId, name, price, image) {
    const btn = document.getElementById('add-btn-' + menuId);
    const oldQty = parseInt(localStorage.getItem('cart_count')) || 0;
    const oldPrice = parseInt(localStorage.getItem('cart_price')) || 0;

    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-75');
        btn.innerHTML = '<i class="fa-solid fa-check text-xs"></i> Ditambah';
    }

    updateBadge(oldQty + 1, oldPrice + price);

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ menu_id: menuId, quantity: 1 }),
        redirect: 'follow',
    })
        .then(response => {
            if (response.redirected || response.status === 401) {
                window.location.href = '/login';
                return null;
            }
            return response.json();
        })
        .then(result => {
            if (!result) return;

            if (result.success) {
                loadCartBadge();
                return;
            }

            updateBadge(oldQty, oldPrice);
            if (result.message === 'Unauthenticated.' || result.redirect) {
                window.location.href = '/login';
                return;
            }
            alert('Gagal menambahkan ke keranjang.');
        })
        .catch(() => {
            updateBadge(oldQty, oldPrice);
            window.location.href = '/login';
        })
        .finally(() => {
            if (!btn) return;
            setTimeout(() => {
                btn.disabled = false;
                btn.classList.remove('opacity-75');
                btn.innerHTML = '<i class="fa-solid fa-cart-plus text-xs"></i> Tambah';
            }, 800);
        });
}

function filterMenu() {
    const categorySelect = document.getElementById('category-select');
    const tenantSelect = document.getElementById('tenant-select');
    const searchInput = document.getElementById('search-input');
    const noResult = document.getElementById('no-result');

    const activeCat = categorySelect ? categorySelect.value : 'Semua';
    const activeTenant = tenantSelect ? tenantSelect.value : 'Semua';
    const searchVal = searchInput ? searchInput.value.toLowerCase().trim() : '';
    let visible = 0;

    document.querySelectorAll('.menu-card').forEach(card => {
        const matchCat = activeCat === 'Semua' || card.dataset.cat === activeCat;
        const matchTenant = activeTenant === 'Semua' || card.dataset.tenant === activeTenant;
        const haystack = card.dataset.search || card.dataset.name || '';
        const matchSearch = !searchVal || haystack.includes(searchVal);
        const show = matchCat && matchTenant && matchSearch;

        card.classList.toggle('hidden', !show);
        if (show) visible++;
    });

    if (noResult) noResult.classList.toggle('hidden', visible > 0);
}

document.addEventListener('DOMContentLoaded', () => {
    const savedCount = parseInt(localStorage.getItem('cart_count')) || 0;
    const savedPrice = parseInt(localStorage.getItem('cart_price')) || 0;

    if (savedCount > 0) updateBadge(savedCount, savedPrice);
    loadCartBadge();

    document.getElementById('category-select')?.addEventListener('change', filterMenu);
    document.getElementById('tenant-select')?.addEventListener('change', filterMenu);
    document.getElementById('search-input')?.addEventListener('input', filterMenu);
});
</script>
@endpush
