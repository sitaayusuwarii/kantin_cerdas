@extends('layouts.app')
@section('title', 'Menu — SmartCanteen')

@section('content')

{{-- ===== CART DRAWER OVERLAY ===== --}}
<div id="cart-overlay" class="fixed inset-0 bg-black/50 z-40 hidden backdrop-blur-sm" onclick="closeCart()"></div>

{{-- ===== CART DRAWER (Slide dari kanan) ===== --}}
<div id="cart-drawer"
     class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white z-50 shadow-2xl transform translate-x-full transition-transform duration-300 ease-out flex flex-col">

    {{-- Drawer Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-white sticky top-0">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 btn-primary rounded-xl flex items-center justify-center shadow">
                <i class="fa-solid fa-cart-shopping text-white text-sm"></i>
            </div>
            <div>
                <h2 class="font-heading font-bold text-base text-canteen-dark leading-none">Keranjang</h2>
                <p id="cart-count-sub" class="text-xs text-gray-400 mt-0.5">0 item dipilih</p>
            </div>
        </div>
        <button onclick="closeCart()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
            <i class="fa-solid fa-xmark text-gray-500 text-sm"></i>
        </button>
    </div>

    {{-- Cart Items (scrollable) --}}
    <div class="flex-1 overflow-y-auto px-5 py-4" id="cart-items-container">

        {{-- Empty State --}}
        <div id="cart-empty" class="flex flex-col items-center justify-center h-full py-16 text-center">
            <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-cart-shopping text-primary-200 text-3xl"></i>
            </div>
            <p class="font-heading font-bold text-gray-700 mb-1">Keranjang Kosong</p>
            <p class="text-gray-400 text-sm">Tambahkan menu favoritmu dulu yuk!</p>
            <button onclick="closeCart()" class="mt-5 text-sm text-primary-500 font-semibold hover:text-primary-700 transition-colors">
                ← Pilih Menu
            </button>
        </div>

        {{-- Cart Items List --}}
        <div id="cart-items-list" class="space-y-3 hidden"></div>
    </div>

    {{-- Cart Footer --}}
    <div id="cart-footer" class="hidden border-t border-gray-100 bg-white px-5 py-4 space-y-3">
        {{-- Note --}}
        <div>
            <label class="text-xs font-semibold text-gray-600 mb-1.5 block flex items-center gap-1.5">
                <i class="fa-solid fa-note-sticky text-primary-400"></i>Catatan (opsional)
            </label>
            <input type="text" id="cart-note" placeholder="Contoh: tanpa sambal, extra nasi..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
        </div>

        {{-- Summary --}}
        <div class="bg-orange-50 rounded-xl p-3.5 space-y-1.5">
            <div class="flex justify-between text-xs text-gray-500">
                <span id="cart-item-summary">0 item</span>
                <span id="cart-subtotal-label">Rp 0</span>
            </div>
            <div class="flex justify-between items-center pt-1.5 border-t border-orange-100">
                <span class="font-heading font-bold text-canteen-dark text-sm">Total</span>
                <span id="cart-total-price" class="font-heading font-bold text-xl text-primary-500">Rp 0</span>
            </div>
        </div>

        {{-- CTA --}}
        <a href="{{ route('customer.cart.index') }}" id="checkout-btn"
           class="btn-primary w-full text-white font-heading font-bold py-3.5 rounded-xl text-sm shadow-lg flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-bag-shopping"></i>
            Lihat Keranjang & Pesan
        </a>

        <button onclick="clearCart()" class="w-full text-xs text-gray-400 hover:text-red-400 font-medium transition-colors py-1">
            <i class="fa-solid fa-trash-can mr-1"></i>Kosongkan keranjang
        </button>
    </div>
</div>

{{-- ===== FLOATING CART BUTTON ===== --}}
<button onclick="openCart()"
        id="floating-cart-btn"
        class="fixed bottom-6 right-5 sm:right-6 z-30 btn-primary text-white font-heading font-bold px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 transition-all hover:scale-105 active:scale-95">
    <div class="relative">
        <i class="fa-solid fa-cart-shopping text-lg"></i>
        <span id="cart-badge"
              class="absolute -top-2.5 -right-2.5 w-5 h-5 bg-white text-primary-500 text-[10px] font-black rounded-full flex items-center justify-center shadow hidden">0</span>
    </div>
    <div id="cart-btn-info" class="hidden sm:block text-left">
        <p class="text-[10px] text-orange-100 leading-none mb-0.5" id="cart-btn-count">0 item</p>
        <p class="text-sm leading-none" id="cart-btn-price">Rp 0</p>
    </div>
</button>

{{-- ===== MAIN CONTENT ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-28">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="font-heading font-bold text-2xl md:text-3xl text-canteen-dark mb-1">Menu Kantin 🍽️</h1>
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
            <button class="filter-btn active-filter btn-primary text-white text-sm font-semibold px-5 py-3 rounded-xl flex-shrink-0 transition-all" data-cat="Semua">Semua</button>
            <button class="filter-btn bg-white border border-gray-200 text-gray-600 hover:border-primary-300 text-sm font-medium px-5 py-3 rounded-xl flex-shrink-0 transition-all" data-cat="Makanan">Makanan</button>
            <button class="filter-btn bg-white border border-gray-200 text-gray-600 hover:border-primary-300 text-sm font-medium px-5 py-3 rounded-xl flex-shrink-0 transition-all" data-cat="Minuman">Minuman</button>
            <button class="filter-btn bg-white border border-gray-200 text-gray-600 hover:border-primary-300 text-sm font-medium px-5 py-3 rounded-xl flex-shrink-0 transition-all" data-cat="Snack">Snack</button>
        </div>
    </div>

    {{-- Menu Grid --}}
    <div id="menu-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @php
        $menus = [
            ['id'=>1,'name'=>'Nasi Gudeg Komplit','price'=>12000,'emoji'=>'🍛','cat'=>'Makanan','fav'=>true,'desc'=>'Nasi + gudeg + ayam suwir + kerupuk','color'=>'from-yellow-400 to-orange-400'],
            ['id'=>2,'name'=>'Mie Goreng Spesial','price'=>10000,'emoji'=>'🍜','cat'=>'Makanan','fav'=>true,'desc'=>'Mie goreng + telur + sayuran segar','color'=>'from-orange-400 to-red-400'],
            ['id'=>3,'name'=>'Nasi Ayam Geprek','price'=>13000,'emoji'=>'🍗','cat'=>'Makanan','fav'=>false,'desc'=>'Nasi + ayam geprek + sambal + lalapan','color'=>'from-red-400 to-orange-500'],
            ['id'=>4,'name'=>'Bakso Urat Jumbo','price'=>11000,'emoji'=>'🍲','cat'=>'Makanan','fav'=>false,'desc'=>'Bakso urat + mie + kuah kaldu sapi','color'=>'from-amber-400 to-yellow-500'],
            ['id'=>5,'name'=>'Es Teh Manis','price'=>4000,'emoji'=>'🧋','cat'=>'Minuman','fav'=>true,'desc'=>'Teh manis dingin segar','color'=>'from-amber-300 to-yellow-400'],
            ['id'=>6,'name'=>'Jus Alpukat','price'=>8000,'emoji'=>'🥑','cat'=>'Minuman','fav'=>false,'desc'=>'Jus alpukat + susu + gula rendah','color'=>'from-green-400 to-emerald-500'],
            ['id'=>7,'name'=>'Pisang Goreng','price'=>5000,'emoji'=>'🍌','cat'=>'Snack','fav'=>false,'desc'=>'3 pcs pisang goreng renyah','color'=>'from-yellow-300 to-amber-400'],
            ['id'=>8,'name'=>'Indomie Rebus','price'=>7000,'emoji'=>'🍝','cat'=>'Makanan','fav'=>false,'desc'=>'Indomie rebus + telur + sayur','color'=>'from-orange-300 to-yellow-400'],
            ['id'=>9,'name'=>'Air Mineral','price'=>3000,'emoji'=>'💧','cat'=>'Minuman','fav'=>false,'desc'=>'Air mineral 600ml dingin','color'=>'from-blue-300 to-cyan-400'],
            ['id'=>10,'name'=>'Tahu Goreng','price'=>4000,'emoji'=>'🟡','cat'=>'Snack','fav'=>false,'desc'=>'4 pcs tahu goreng + sambal kecap','color'=>'from-yellow-400 to-amber-500'],
            ['id'=>11,'name'=>'Nasi Goreng Spesial','price'=>12000,'emoji'=>'🍳','cat'=>'Makanan','fav'=>false,'desc'=>'Nasi goreng + telur + ayam + sayur','color'=>'from-orange-500 to-red-500'],
            ['id'=>12,'name'=>'Es Jeruk','price'=>5000,'emoji'=>'🍊','cat'=>'Minuman','fav'=>false,'desc'=>'Jeruk peras segar + gula aren','color'=>'from-orange-300 to-amber-400'],
        ];
        @endphp

        @foreach($menus as $menu)
        <div class="menu-card bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 group transition-all duration-200 hover:-translate-y-1 hover:shadow-lg"
             data-id="{{ $menu['id'] }}"
             data-name="{{ $menu['name'] }}"
             data-price="{{ $menu['price'] }}"
             data-emoji="{{ $menu['emoji'] }}"
             data-cat="{{ $menu['cat'] }}">

            {{-- Image Area --}}
            <div class="relative bg-gradient-to-br {{ $menu['color'] }} h-44 flex items-center justify-center overflow-hidden">
                <span class="text-6xl group-hover:scale-110 transition-transform duration-300 select-none">{{ $menu['emoji'] }}</span>

                @if($menu['fav'])
                <div class="absolute top-3 right-3 badge-favorite text-white text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1 shadow-md">
                    <i class="fa-solid fa-fire text-[9px]"></i>FAVORIT
                </div>
                @endif
                <div class="absolute top-3 left-3 bg-white/90 text-gray-600 text-[10px] font-semibold px-2.5 py-1 rounded-full">
                    {{ $menu['cat'] }}
                </div>

                {{-- Qty overlay (muncul kalau sudah ditambah ke cart) --}}
                <div id="qty-overlay-{{ $menu['id'] }}"
                     class="absolute bottom-3 right-3 hidden">
                    <div class="flex items-center gap-1.5 bg-white rounded-xl px-2 py-1 shadow-lg border border-orange-100">
                        <button onclick="changeQty({{ $menu['id'] }}, -1)" class="w-6 h-6 flex items-center justify-center text-primary-500 hover:text-primary-700 font-bold text-base leading-none transition-colors">−</button>
                        <span id="qty-display-{{ $menu['id'] }}" class="font-heading font-bold text-sm text-canteen-dark w-4 text-center">1</span>
                        <button onclick="changeQty({{ $menu['id'] }}, 1)" class="w-6 h-6 flex items-center justify-center text-primary-500 hover:text-primary-700 font-bold text-base leading-none transition-colors">+</button>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-4">
                <h3 class="font-heading font-bold text-sm text-canteen-dark mb-1 leading-tight">{{ $menu['name'] }}</h3>
                <p class="text-gray-400 text-xs mb-3 leading-relaxed">{{ $menu['desc'] }}</p>
                <div class="flex items-center justify-between gap-2">
                    <p class="font-heading font-bold text-lg text-primary-500">Rp {{ number_format($menu['price'], 0, ',', '.') }}</p>

                    {{-- Add to Cart Button --}}
                    <button id="add-btn-{{ $menu['id'] }}"
                            onclick="addToCart({{ $menu['id'] }}, '{{ $menu['name'] }}', {{ $menu['price'] }}, '{{ $menu['emoji'] }}')"
                            class="btn-primary text-white text-xs font-semibold px-3.5 py-2 rounded-xl flex items-center gap-1.5 shadow transition-all active:scale-95">
                        <i class="fa-solid fa-cart-plus text-xs"></i>
                        <span>Tambah</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- No Result --}}
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
    .badge-favorite { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
    .active-filter { background: linear-gradient(135deg, #f97316, #ea580c) !important; color: white !important; border-color: transparent !important; }
    #cart-drawer { will-change: transform; }
    #cart-drawer.open { transform: translateX(0); }

    /* Add to cart success flash */
    @keyframes cartPop {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    .cart-pop { animation: cartPop 0.3s ease; }

    /* Item added flash on card */
    @keyframes addedFlash {
        0% { box-shadow: 0 0 0 0 rgba(249,115,22,0.4); }
        70% { box-shadow: 0 0 0 8px rgba(249,115,22,0); }
        100% { box-shadow: 0 0 0 0 rgba(249,115,22,0); }
    }
    .flash-added { animation: addedFlash 0.6s ease; }
</style>
@endpush

@push('scripts')
<script>
// ====================================================
// CART STATE — menggunakan localStorage
// ====================================================
let cart = JSON.parse(localStorage.getItem('sc_cart') || '[]');

function saveCart() {
    localStorage.setItem('sc_cart', JSON.stringify(cart));
}

// ====================================================
// ADD TO CART
// ====================================================
function addToCart(id, name, price, emoji) {
    const existing = cart.find(i => i.id === id);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id, name, price, emoji, qty: 1 });
    }
    saveCart();
    updateCartUI();
    updateCardUI(id);

    // Flash animation on floating button
    const badge = document.getElementById('cart-badge');
    badge.classList.remove('cart-pop');
    void badge.offsetWidth; // reflow
    badge.classList.add('cart-pop');
}

// ====================================================
// CHANGE QTY (dari overlay di card)
// ====================================================
function changeQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        cart = cart.filter(i => i.id !== id);
        // Sembunyikan overlay & reset tombol
        document.getElementById('qty-overlay-' + id)?.classList.add('hidden');
        const btn = document.getElementById('add-btn-' + id);
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-cart-plus text-xs"></i><span>Tambah</span>';
            btn.classList.remove('bg-orange-100', 'text-primary-600');
            btn.classList.add('btn-primary');
        }
    } else {
        document.getElementById('qty-display-' + id).textContent = item.qty;
    }
    saveCart();
    updateCartUI();
    updateDrawerItems();
}

// ====================================================
// UPDATE CARD UI
// ====================================================
function updateCardUI(id) {
    const item = cart.find(i => i.id === id);
    const overlay = document.getElementById('qty-overlay-' + id);
    const btn = document.getElementById('add-btn-' + id);
    const display = document.getElementById('qty-display-' + id);

    if (!item) {
        overlay?.classList.add('hidden');
        return;
    }

    if (overlay) overlay.classList.remove('hidden');
    if (display) display.textContent = item.qty;

    if (btn) {
        btn.innerHTML = '<i class="fa-solid fa-check text-xs"></i><span class="hidden sm:inline">Ditambah</span><span class="font-bold">' + item.qty + '</span>';
        btn.classList.add('bg-orange-100', '!bg-none', 'text-primary-600', '!shadow-none');
        // Flash on card
        btn.closest('.menu-card')?.classList.add('flash-added');
        setTimeout(() => btn.closest('.menu-card')?.classList.remove('flash-added'), 600);
    }
}

// ====================================================
// UPDATE GLOBAL CART UI (badge + floating btn)
// ====================================================
function updateCartUI() {
    const totalQty   = cart.reduce((s, i) => s + i.qty, 0);
    const totalPrice = cart.reduce((s, i) => s + (i.price * i.qty), 0);

    // Badge
    const badge = document.getElementById('cart-badge');
    if (totalQty > 0) {
        badge.textContent = totalQty > 99 ? '99+' : totalQty;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }

    // Floating btn text
    document.getElementById('cart-btn-count').textContent = totalQty + ' item';
    document.getElementById('cart-btn-price').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
    document.getElementById('cart-count-sub').textContent = totalQty + ' item dipilih';

    // Drawer summary
    document.getElementById('cart-item-summary').textContent = totalQty + ' item';
    document.getElementById('cart-subtotal-label').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
    document.getElementById('cart-total-price').textContent   = 'Rp ' + totalPrice.toLocaleString('id-ID');

    updateDrawerItems();
}

// ====================================================
// UPDATE DRAWER ITEMS LIST
// ====================================================
function updateDrawerItems() {
    const container = document.getElementById('cart-items-list');
    const empty     = document.getElementById('cart-empty');
    const footer    = document.getElementById('cart-footer');

    if (cart.length === 0) {
        container.classList.add('hidden');
        empty.classList.remove('hidden');
        footer.classList.add('hidden');
        return;
    }

    empty.classList.add('hidden');
    container.classList.remove('hidden');
    footer.classList.remove('hidden');

    container.innerHTML = cart.map(item => `
        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl group">
            <div class="w-11 h-11 bg-white rounded-xl flex items-center justify-center text-2xl shadow-sm flex-shrink-0">${item.emoji}</div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-gray-800 truncate leading-tight">${item.name}</p>
                <p class="text-primary-500 font-heading font-bold text-xs mt-0.5">Rp ${(item.price).toLocaleString('id-ID')}</p>
            </div>
            <div class="flex items-center gap-1.5 bg-white rounded-xl px-2 py-1 shadow-sm border border-gray-100 flex-shrink-0">
                <button onclick="changeQty(${item.id}, -1)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-red-400 font-bold text-base transition-colors">−</button>
                <span class="font-heading font-bold text-sm text-canteen-dark w-5 text-center">${item.qty}</span>
                <button onclick="changeQty(${item.id}, 1)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-primary-500 font-bold text-base transition-colors">+</button>
            </div>
            <div class="text-right flex-shrink-0 w-16">
                <p class="font-heading font-bold text-sm text-canteen-dark">Rp ${(item.price * item.qty).toLocaleString('id-ID')}</p>
            </div>
        </div>
    `).join('');
}

// ====================================================
// DRAWER OPEN / CLOSE
// ====================================================
function openCart() {
    document.getElementById('cart-drawer').classList.add('open');
    document.getElementById('cart-overlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    updateDrawerItems();
}

function closeCart() {
    document.getElementById('cart-drawer').classList.remove('open');
    document.getElementById('cart-overlay').classList.add('hidden');
    document.body.style.overflow = '';
}

// ====================================================
// CLEAR CART
// ====================================================
function clearCart() {
    if (!confirm('Kosongkan keranjang?')) return;
    cart = [];
    saveCart();
    updateCartUI();
    // Reset semua card UI
    document.querySelectorAll('.menu-card').forEach(card => {
        const id = parseInt(card.dataset.id);
        document.getElementById('qty-overlay-' + id)?.classList.add('hidden');
        const btn = document.getElementById('add-btn-' + id);
        if (btn) btn.innerHTML = '<i class="fa-solid fa-cart-plus text-xs"></i><span>Tambah</span>';
    });
}

// ====================================================
// FILTER & SEARCH
// ====================================================
const filterBtns = document.querySelectorAll('.filter-btn');
const searchInput = document.getElementById('search-input');

function filterMenu() {
    const activeBtn   = document.querySelector('.filter-btn.active-filter');
    const activeCat   = activeBtn ? activeBtn.dataset.cat : 'Semua';
    const searchVal   = searchInput.value.toLowerCase().trim();
    const cards       = document.querySelectorAll('.menu-card');
    let visible       = 0;

    cards.forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const cat  = card.dataset.cat;
        const matchCat    = activeCat === 'Semua' || cat === activeCat;
        const matchSearch = !searchVal || name.includes(searchVal);
        if (matchCat && matchSearch) {
            card.classList.remove('hidden');
            visible++;
        } else {
            card.classList.add('hidden');
        }
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

// ====================================================
// INIT — restore cart state on page load
// ====================================================
document.addEventListener('DOMContentLoaded', () => {
    updateCartUI();
    // Re-apply card UI untuk item yang sudah di cart
    cart.forEach(item => updateCardUI(item.id));
});
</script>
@endpush
