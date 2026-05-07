@extends('layouts.app')
@section('title', 'Keranjang — SmartCanteen')

@section('content')

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-28">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ url('/menu') }}" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors flex-shrink-0">
            <i class="fa-solid fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <div>
            <h1 class="font-heading font-bold text-2xl text-canteen-dark leading-none">Keranjang</h1>
            <p id="cart-header-count" class="text-gray-400 text-sm mt-0.5">Memuat...</p>
        </div>
    </div>

    {{-- Loading State --}}
    <div id="cart-loading" class="text-center py-16">
        <div class="w-10 h-10 border-4 border-primary-200 border-t-primary-500 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-400 text-sm">Memuat keranjang...</p>
    </div>

    {{-- Empty State --}}
    <div id="cart-empty" class="hidden text-center py-20">
        <div class="w-24 h-24 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-5">
            <i class="fa-solid fa-cart-shopping text-primary-200 text-4xl"></i>
        </div>
        <p class="font-heading font-bold text-gray-700 text-lg mb-1">Keranjang Kosong</p>
        <p class="text-gray-400 text-sm mb-6">Tambahkan menu favoritmu dulu yuk!</p>
        <a href="{{ url('/menu') }}"
           class="btn-primary inline-flex items-center gap-2 text-white font-heading font-bold px-6 py-3 rounded-xl shadow text-sm transition-all hover:scale-105 active:scale-95">
            <i class="fa-solid fa-bowl-food"></i>
            Lihat Menu
        </a>
    </div>

    {{-- Cart Content --}}
    <div id="cart-content" class="hidden space-y-4">

        {{-- Items List --}}
        <div id="cart-items-list" class="space-y-3"></div>

        {{-- Divider --}}
        <div class="border-t border-dashed border-gray-200 pt-4 space-y-4">

            {{-- Note --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <label class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-note-sticky text-primary-400"></i>Catatan Pesanan (opsional)
                </label>
                <input type="text" id="cart-note"
                       placeholder="Contoh: tanpa sambal, extra nasi..."
                       class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
            </div>

            {{-- Summary --}}
            <div class="bg-orange-50 rounded-2xl p-4 space-y-2">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span>
                    <span id="cart-subtotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Jumlah Item</span>
                    <span id="cart-qty-summary">0 item</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-orange-100">
                    <span class="font-heading font-bold text-canteen-dark">Total</span>
                    <span id="cart-total" class="font-heading font-bold text-2xl text-primary-500">Rp 0</span>
                </div>
            </div>

            {{-- Checkout Button --}}
            <button id="checkout-btn"
                    onclick="submitOrder()"
                    class="btn-primary w-full text-white font-heading font-bold py-4 rounded-2xl text-base shadow-lg flex items-center justify-center gap-2 transition-all hover:scale-[1.01] active:scale-95">
                <i class="fa-solid fa-bag-shopping"></i>
                Pesan Sekarang
            </button>

            {{-- Clear Cart --}}
            <button onclick="clearCart()"
                    class="w-full text-xs text-gray-400 hover:text-red-400 font-medium transition-colors py-1.5">
                <i class="fa-solid fa-trash-can mr-1"></i>Kosongkan keranjang
            </button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); }

    @keyframes itemIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .cart-item { animation: itemIn 0.2s ease both; }

    @keyframes removeItem {
        from { opacity: 1; max-height: 100px; }
        to   { opacity: 0; max-height: 0; padding: 0; margin: 0; overflow: hidden; }
    }
    .removing { animation: removeItem 0.22s ease forwards; pointer-events: none; }
</style>
@endpush

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let cartItems = [];
const pendingRemove = new Set();

function formatRp(num) {
    return 'Rp ' + Number(num).toLocaleString('id-ID');
}

function getTotalQty()   { return cartItems.reduce((s, i) => s + i.qty, 0); }
function getTotalPrice() { return cartItems.reduce((s, i) => s + i.price * i.qty, 0); }

// ============================================================
// LOAD DARI SERVER
// ============================================================
function loadCart() {
    return fetch('/cart/data', { headers: { 'X-CSRF-TOKEN': CSRF } })
    .then(r => r.json())
    .then(res => {
        cartItems = (res.items || []).filter(i => !pendingRemove.has(i.cart_item_id));
        renderCart();
        return res;
    });
}

// ============================================================
// RENDER CART
// ============================================================
function renderCart() {
    document.getElementById('cart-loading').classList.add('hidden');

    if (cartItems.length === 0) {
        document.getElementById('cart-empty').classList.remove('hidden');
        document.getElementById('cart-content').classList.add('hidden');
        document.getElementById('cart-header-count').textContent = 'Keranjang kosong';
        return;
    }

    document.getElementById('cart-empty').classList.add('hidden');
    document.getElementById('cart-content').classList.remove('hidden');

    const qty   = getTotalQty();
    const price = getTotalPrice();

    document.getElementById('cart-header-count').textContent = qty + ' item dipilih';
    document.getElementById('cart-subtotal').textContent     = formatRp(price);
    document.getElementById('cart-qty-summary').textContent  = qty + ' item';
    document.getElementById('cart-total').textContent        = formatRp(price);

    const list = document.getElementById('cart-items-list');
    list.innerHTML = cartItems.map((item, idx) => `
        <div class="cart-item bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3"
             id="cart-row-${item.cart_item_id}"
             style="animation-delay:${idx * 0.05}s">

            <div class="w-14 h-14 bg-gray-50 rounded-xl overflow-hidden flex items-center justify-center text-2xl flex-shrink-0 border border-gray-100">
                ${item.image
                    ? `<img src="${item.image}" class="w-full h-full object-cover" alt="${item.name}">`
                    : '🍽️'}
            </div>

            <div class="flex-1 min-w-0">
                <p class="font-heading font-semibold text-sm text-canteen-dark truncate">${item.name}</p>
                <p class="text-primary-500 font-bold text-sm mt-0.5">${formatRp(item.price)}</p>
                <p id="sub-${item.cart_item_id}" class="text-gray-400 text-xs mt-0.5">
                    Subtotal: ${formatRp(item.price * item.qty)}
                </p>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                <div class="flex items-center bg-gray-50 rounded-xl border border-gray-100 overflow-hidden">
                    <button onclick="changeQty(${item.cart_item_id}, -1)"
                            class="w-8 h-8 flex items-center justify-center font-bold transition-colors
                                   ${item.qty <= 1 ? 'text-red-400 hover:bg-red-50' : 'text-gray-400 hover:text-red-400 hover:bg-gray-100'}">
                        ${item.qty <= 1 ? '<i class="fa-solid fa-trash text-xs"></i>' : '−'}
                    </button>
                    <span id="qty-${item.cart_item_id}"
                          class="font-bold text-sm text-gray-800 w-7 text-center">
                        ${item.qty}
                    </span>
                    <button onclick="changeQty(${item.cart_item_id}, 1)"
                            class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-primary-500 hover:bg-gray-100 font-bold text-base transition-colors">
                        +
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function syncSummary() {
    const qty   = getTotalQty();
    const price = getTotalPrice();
    document.getElementById('cart-header-count').textContent = qty + ' item dipilih';
    document.getElementById('cart-subtotal').textContent     = formatRp(price);
    document.getElementById('cart-qty-summary').textContent  = qty + ' item';
    document.getElementById('cart-total').textContent        = formatRp(price);
    localStorage.setItem('cart_count', qty);
    localStorage.setItem('cart_price', price);
}

// ============================================================
// CHANGE QTY — OPTIMISTIC
// ============================================================
function changeQty(cartItemId, delta) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) return;

    const oldQty = item.qty;
    const newQty = oldQty + delta;

    if (newQty <= 0) {
        removeItem(cartItemId);
        return;
    }

    item.qty = newQty;

    const qtyEl = document.getElementById('qty-' + cartItemId);
    const subEl = document.getElementById('sub-' + cartItemId);
    const row   = document.getElementById('cart-row-' + cartItemId);

    if (qtyEl) qtyEl.textContent = newQty;
    if (subEl) subEl.textContent = 'Subtotal: ' + formatRp(item.price * newQty);

    // Update tombol minus
    if (row) {
        const minusBtn = row.querySelector('button:first-child');
        if (minusBtn) {
            if (newQty <= 1) {
                minusBtn.innerHTML = '<i class="fa-solid fa-trash text-xs"></i>';
                minusBtn.className = 'w-8 h-8 flex items-center justify-center font-bold transition-colors text-red-400 hover:bg-red-50';
            } else {
                minusBtn.innerHTML = '−';
                minusBtn.className = 'w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-400 hover:bg-gray-100 font-bold text-base transition-colors';
            }
        }
    }

    syncSummary();

    fetch('/cart/update-ajax/' + cartItemId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ quantity: newQty })
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success) {
            item.qty = oldQty;
            if (qtyEl) qtyEl.textContent = oldQty;
            if (subEl) subEl.textContent = 'Subtotal: ' + formatRp(item.price * oldQty);
            syncSummary();
        }
    })
    .catch(() => {
        item.qty = oldQty;
        if (qtyEl) qtyEl.textContent = oldQty;
        if (subEl) subEl.textContent = 'Subtotal: ' + formatRp(item.price * oldQty);
        syncSummary();
    });
}

// ============================================================
// REMOVE ITEM — OPTIMISTIC + PERMANENT DELETE
// ============================================================
function removeItem(cartItemId) {
    const row = document.getElementById('cart-row-' + cartItemId);
    pendingRemove.add(cartItemId);

    if (row) {
        row.classList.add('removing');
        setTimeout(() => row.remove(), 220);
    }

    cartItems = cartItems.filter(i => i.cart_item_id !== cartItemId);
    syncSummary();

    if (cartItems.length === 0) {
        setTimeout(() => {
            document.getElementById('cart-content').classList.add('hidden');
            document.getElementById('cart-empty').classList.remove('hidden');
            document.getElementById('cart-header-count').textContent = 'Keranjang kosong';
        }, 230);
    }

    fetch('/cart/remove-ajax/' + cartItemId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    })
    .then(r => r.json())
    .then(res => {
        pendingRemove.delete(cartItemId);
        if (!res.success) {
            loadCart(); // rollback jika gagal
        }
    })
    .catch(() => {
        pendingRemove.delete(cartItemId);
        loadCart();
    });
}

// ============================================================
// CLEAR CART
// ============================================================
function clearCart() {
    if (!confirm('Kosongkan semua keranjang?')) return;

    cartItems = [];
    renderCart();
    localStorage.setItem('cart_count', 0);
    localStorage.setItem('cart_price', 0);

    fetch('/cart/clear', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success) loadCart();
    });
}

// ============================================================
// SUBMIT ORDER
// ============================================================
function submitOrder() {
    window.location.href = '/order';
}

// ============================================================
// INIT
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    loadCart();
});
</script>
@endpush