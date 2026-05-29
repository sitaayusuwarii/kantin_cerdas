@extends('layouts.app')
@section('title', 'Menu — SmartCanteen')

@section('content')

{{-- ===== FLOATING CART BUTTON ===== --}}
<a href="{{ url('/cart') }}"
   id="floating-cart-btn"
   class="fixed bottom-6 right-5 sm:right-6 z-30 btn-primary text-white font-heading font-bold px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 transition-all hover:scale-105 active:scale-95">
    <div class="relative">
        <i class="fa-solid fa-cart-shopping text-lg"></i>
        <span id="cart-badge"
              class="absolute -top-2.5 -right-2.5 w-5 h-5 bg-white text-primary-500 text-[10px] font-black rounded-full flex items-center justify-center shadow hidden">0</span>
    </div>
    <div class="hidden sm:block text-left">
        <p class="text-[10px] text-orange-100 leading-none mb-0.5" id="cart-btn-count">0 item</p>
        <p class="text-sm leading-none" id="cart-btn-price">Rp 0</p>
    </div>
</a>

{{-- ===== MAIN CONTENT ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-28">

    <div class="mb-8">
        <h1 class="font-heading font-bold text-2xl md:text-3xl text-canteen-dark mb-1">Menu Kantin </h1>
        <p class="text-gray-500 text-sm">Pilih menu favoritmu dan masukkan ke keranjang</p>
    </div>

    {{-- Search + Filter --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-8">
        <div class="relative flex-1">
            <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input id="search-input" type="text" placeholder="Cari menu..."
                   class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
        </div>
        <div class="flex gap-2 overflow-x-auto pb-1 sm:pb-0">
            <button class="filter-btn active-filter btn-primary text-white text-sm font-semibold px-5 py-3 rounded-xl flex-shrink-0 transition-all" data-cat="Semua">
                Semua
            </button>
            @foreach($categories as $category)
            <button class="filter-btn bg-white border border-gray-200 text-gray-600 hover:border-primary-300 text-sm font-medium px-5 py-3 rounded-xl flex-shrink-0 transition-all"
                    data-cat="{{ $category->name }}">
                {{ $category->name }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Menu Grid --}}
    <div id="menu-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($menus as $menu)
        <div class="menu-card bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 group transition-all duration-200 hover:-translate-y-1 hover:shadow-lg"
             data-id="{{ $menu->id }}"
             data-name="{{ $menu->name }}"
             data-price="{{ $menu->price }}"
             data-cat="{{ $menu->category->name ?? 'Menu' }}">

            <div class="relative h-44 overflow-hidden">
                @if($menu->image)
                    <img src="{{ asset('storage/' . $menu->image) }}"
                         alt="{{ $menu->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-orange-400 to-orange-500 flex items-center justify-center">
                        <span class="text-6xl">🍽️</span>
                    </div>
                @endif

                <div class="absolute top-3 left-3 bg-white/90 text-gray-600 text-[10px] font-semibold px-2.5 py-1 rounded-full">
                    {{ $menu->category->name ?? 'Menu' }}
                </div>

                @if($menu->stock <= 5)
                <div class="absolute top-3 right-3 bg-red-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md">
                    Stok {{ $menu->stock }}
                </div>
                @endif
            </div>

            <div class="p-4">
                <h3 class="font-heading font-bold text-sm text-canteen-dark mb-1 leading-tight">{{ $menu->name }}</h3>
                <p class="text-gray-400 text-xs mb-3 leading-relaxed line-clamp-2">{{ $menu->description }}</p>

                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs text-gray-400">Stok: <span class="font-semibold text-gray-600">{{ $menu->stock }}</span></p>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <p class="font-heading font-bold text-lg text-primary-500">
                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                    </p>

                    @if($menu->stock > 0)
                    <button id="add-btn-{{ $menu->id }}"
                            onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ $menu->image ? asset('storage/' . $menu->image) : '' }}')"
                            class="btn-primary text-white text-xs font-semibold px-3.5 py-2 rounded-xl flex items-center gap-1.5 shadow transition-all active:scale-95">
                        <i class="fa-solid fa-cart-plus text-xs"></i>
                        <span>Tambah</span>
                    </button>
                    @else
                    <button disabled class="bg-gray-200 text-gray-500 text-xs font-semibold px-3.5 py-2 rounded-xl cursor-not-allowed">
                        Stok Habis
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-bowl-food text-gray-300 text-2xl"></i>
            </div>
            <p class="font-heading font-bold text-gray-500 mb-1">Belum ada menu</p>
            <p class="text-gray-400 text-sm">Menu masih kosong</p>
        </div>
        @endforelse
    </div>

    <div id="no-result" class="hidden text-center py-16">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-bowl-food text-gray-300 text-2xl"></i>
        </div>
        <p class="font-heading font-bold text-gray-500 mb-1">Menu tidak ditemukan</p>
        <p class="text-gray-400 text-sm">Coba kata kunci lain</p>
    </div>
</div>

@endsection

@push('styles')
<style>
    .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); }
    .active-filter { background: linear-gradient(135deg, #f97316, #ea580c) !important; color: white !important; border-color: transparent !important; }

    @keyframes cartPop {
        0% { transform: scale(1); }
        50% { transform: scale(1.25); }
        100% { transform: scale(1); }
    }
    .cart-pop { animation: cartPop 0.3s ease; }
</style>
@endpush

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function formatRp(num) {
    return 'Rp ' + Number(num).toLocaleString('id-ID');
}

// ============================================================
// SYNC BADGE DARI SERVER
// ============================================================
function loadCartBadge() {
    fetch('/cart/data', { headers: { 'X-CSRF-TOKEN': CSRF } })
    .then(r => r.json())
    .then(res => {
        const items = res.items || [];
        const qty   = items.reduce((s, i) => s + i.qty, 0);
        const price = items.reduce((s, i) => s + i.price * i.qty, 0);
        updateBadge(qty, price);
        localStorage.setItem('cart_count', qty);
        localStorage.setItem('cart_price', price);
    });
}

function updateBadge(qty, price) {
    const badge = document.getElementById('cart-badge');
    if (qty > 0) {
        badge.textContent = qty;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
    document.getElementById('cart-btn-count').textContent = qty + ' item';
    document.getElementById('cart-btn-price').textContent = formatRp(price);
}

// ============================================================
// ADD TO CART
// ============================================================
function addToCart(menuId, name, price, image) {
    const btn = document.getElementById('add-btn-' + menuId);
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-check text-xs"></i><span>Ditambah!</span>';
    btn.classList.add('opacity-75');

    const oldQty   = parseInt(localStorage.getItem('cart_count')) || 0;
    const oldPrice = parseInt(localStorage.getItem('cart_price')) || 0;
    updateBadge(oldQty + 1, oldPrice + price);

    fetch('/cart/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ menu_id: menuId, quantity: 1 }),
        redirect: 'follow'
    })
    .then(res => {
    if (res.redirected) {
        window.location.href = '/login';
        return null;
    }
    if (res.status === 401) {
        window.location.href = '/login';
        return null;
    }
    return res.json();
})
    .then(res => {
        if (!res) return;
        if (res.success) {
            loadCartBadge();
        } else {
            updateBadge(oldQty, oldPrice);
            // Kalau server return unauthenticated di JSON
            if (res.message === 'Unauthenticated.' || res.redirect) {
                window.location.href = '/login';
                return;
            }
            alert('Gagal menambahkan ke keranjang!');
        }
    })
    .catch(() => {
        updateBadge(oldQty, oldPrice);
        window.location.href = '/login';  // error network → arahkan login
    })
    .finally(() => {
        setTimeout(() => {
            btn.disabled = false;
            btn.classList.remove('opacity-75');
            btn.innerHTML = '<i class="fa-solid fa-cart-plus text-xs"></i><span>Tambah</span>';
        }, 800);
    });
}

// ============================================================
// FILTER & SEARCH
// ============================================================
const filterBtns  = document.querySelectorAll('.filter-btn');
const searchInput = document.getElementById('search-input');

function filterMenu() {
    const activeBtn = document.querySelector('.filter-btn.active-filter');
    const activeCat = activeBtn ? activeBtn.dataset.cat : 'Semua';
    const searchVal = searchInput.value.toLowerCase().trim();
    let visible = 0;

    document.querySelectorAll('.menu-card').forEach(card => {
        const matchCat    = activeCat === 'Semua' || card.dataset.cat === activeCat;
        const matchSearch = !searchVal || card.dataset.name.toLowerCase().includes(searchVal);
        card.classList.toggle('hidden', !(matchCat && matchSearch));
        if (matchCat && matchSearch) visible++;
    });

    document.getElementById('no-result').classList.toggle('hidden', visible > 0);
}

filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        filterBtns.forEach(b => {
            b.classList.remove('active-filter');
            b.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-600');
        });
        btn.classList.add('active-filter');
        btn.classList.remove('bg-white', 'border', 'border-gray-200', 'text-gray-600');
        filterMenu();
    });
});

searchInput.addEventListener('input', filterMenu);

// ============================================================
// INIT
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    // Tampilkan badge dari localStorage dulu (instan)
    const savedCount = parseInt(localStorage.getItem('cart_count')) || 0;
    const savedPrice = parseInt(localStorage.getItem('cart_price')) || 0;
    if (savedCount > 0) updateBadge(savedCount, savedPrice);

    // Load akurat dari server
    loadCartBadge();
});
</script>
@endpush