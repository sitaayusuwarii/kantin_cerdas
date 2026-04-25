@extends('layouts.app')
@section('title', 'Keranjang — SmartCanteen')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ url('/menu') }}" class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left text-sm text-gray-600"></i>
        </a>
        <div>
            <h1 class="font-heading font-bold text-2xl text-canteen-dark flex items-center gap-2">
                Keranjang
                <span id="header-badge" class="text-base bg-primary-100 text-primary-600 px-2.5 py-0.5 rounded-full font-bold hidden">0</span>
            </h1>
            <p class="text-gray-400 text-sm mt-0.5">Review pesananmu sebelum checkout</p>
        </div>
    </div>

    {{-- ====== EMPTY STATE ====== --}}
    <div id="empty-state" class="hidden flex flex-col items-center justify-center py-24 text-center">
        <div class="relative mb-6">
            <div class="w-28 h-28 bg-orange-50 rounded-full flex items-center justify-center mx-auto">
                <i class="fa-solid fa-cart-shopping text-primary-200 text-5xl"></i>
            </div>
            <div class="absolute -bottom-1 -right-1 w-10 h-10 bg-yellow-50 border-4 border-white rounded-full flex items-center justify-center text-xl shadow">😢</div>
        </div>
        <h2 class="font-heading font-bold text-xl text-gray-700 mb-2">Keranjang Masih Kosong</h2>
        <p class="text-gray-400 text-sm max-w-xs leading-relaxed mb-6">Yuk tambahkan menu favoritmu dan nikmati makanan lezat dari kantin sekolah!</p>
        <a href="{{ url('/menu') }}" class="btn-primary text-white font-heading font-bold px-8 py-3.5 rounded-2xl shadow-lg flex items-center gap-2 text-sm">
            <i class="fa-solid fa-utensils"></i>Jelajahi Menu
        </a>
    </div>

    {{-- ====== CART CONTENT ====== --}}
    <div id="cart-content" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Items --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Cart Items Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-heading font-bold text-sm text-canteen-dark flex items-center gap-2">
                        <i class="fa-solid fa-basket-shopping text-primary-500"></i>
                        Item Pesanan (<span id="item-count">0</span>)
                    </h2>
                    <button onclick="clearAllCart()" class="text-xs text-red-400 hover:text-red-600 font-semibold transition-colors flex items-center gap-1.5 hover:bg-red-50 px-3 py-1.5 rounded-lg">
                        <i class="fa-solid fa-trash-can text-xs"></i>Hapus Semua
                    </button>
                </div>

                {{-- Items List --}}
                <div id="cart-items-list" class="divide-y divide-gray-50 px-2 py-2"></div>
            </div>

            {{-- Tambah Item --}}
            <a href="{{ url('/menu') }}"
               class="flex items-center gap-3 bg-white border-2 border-dashed border-primary-200 hover:border-primary-400 rounded-2xl p-4 transition-all group">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center group-hover:bg-orange-100 transition-colors">
                    <i class="fa-solid fa-plus text-primary-500 text-sm"></i>
                </div>
                <div>
                    <p class="font-semibold text-sm text-primary-500">Tambah item lagi</p>
                    <p class="text-xs text-gray-400">Kembali ke halaman menu</p>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 text-xs ml-auto group-hover:text-primary-400 transition-colors"></i>
            </a>

            {{-- Catatan Pesanan --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-note-sticky text-primary-500"></i>Catatan Pesanan
                </h3>
                <textarea id="order-note" rows="2"
                          placeholder="Catatan khusus untuk dapur, misal: extra sambal, tidak pakai bawang..."
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 resize-none transition-all"></textarea>
            </div>

            {{-- Waktu Pengambilan --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-heading font-bold text-sm text-canteen-dark mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-primary-500"></i>Waktu Pengambilan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    @foreach([
                        ['val'=>'1','time'=>'Istirahat 1','sub'=>'09:30 — 10:00'],
                        ['val'=>'2','time'=>'Istirahat 2','sub'=>'12:00 — 12:30'],
                        ['val'=>'3','time'=>'Pulang','sub'=>'14:30 — 15:00'],
                    ] as $i => $slot)
                    <label class="cursor-pointer">
                        <input type="radio" name="pickup" value="{{ $slot['val'] }}" class="sr-only pickup-radio" {{ $i===0?'checked':'' }}>
                        <div class="pickup-opt p-3 rounded-xl border-2 {{ $i===0 ? 'border-primary-400 bg-orange-50' : 'border-gray-200' }} hover:border-primary-300 transition-all text-center">
                            <p class="text-xs font-bold text-gray-700">{{ $slot['time'] }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $slot['sub'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <h2 class="font-heading font-bold text-base text-canteen-dark mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-primary-500"></i>Ringkasan Pesanan
                </h2>

                {{-- Breakdown --}}
                <div id="summary-breakdown" class="space-y-2 mb-4 text-sm max-h-40 overflow-y-auto pr-1"></div>

                {{-- Divider --}}
                <div class="border-t border-dashed border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Subtotal</span>
                        <span id="summary-subtotal" class="font-medium text-gray-700">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm text-green-600">
                        <span class="flex items-center gap-1"><i class="fa-solid fa-tag text-xs"></i>Biaya layanan</span>
                        <span class="font-semibold">Gratis</span>
                    </div>
                </div>

                <div class="border-t border-gray-200 mt-4 pt-4 flex justify-between items-center">
                    <span class="font-heading font-bold text-canteen-dark">Total</span>
                    <span id="summary-total" class="font-heading font-bold text-2xl text-primary-500">Rp 0</span>
                </div>

                {{-- Saldo --}}
                <div class="mt-4 p-3 bg-green-50 rounded-xl border border-green-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-wallet text-green-500 text-sm"></i>
                        <div>
                            <p class="text-xs font-medium text-gray-700">Saldo Tersedia</p>
                            <p class="text-[10px] text-gray-500">Rp 85.000</p>
                        </div>
                    </div>
                    <span id="saldo-status" class="text-xs font-semibold text-green-600 bg-green-100 px-2.5 py-1 rounded-lg">Cukup ✓</span>
                </div>

                {{-- CTA --}}
                <button id="checkout-btn"
                        onclick="proceedToOrder()"
                        class="btn-primary w-full text-white font-heading font-bold py-4 rounded-xl mt-5 text-sm shadow-lg flex items-center justify-center gap-2 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-check-circle"></i>
                    Lanjut ke Detail Pesanan
                </button>

                <a href="{{ url('/menu') }}" class="block text-center text-xs text-gray-400 hover:text-gray-600 mt-3 transition-colors">
                    ← Kembali pilih menu lagi
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); }
    .btn-primary:hover { filter: brightness(1.05); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(234,88,12,0.3); }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-12px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .item-row { animation: slideIn 0.25s ease both; }

    @keyframes removeRow {
        from { opacity: 1; transform: translateX(0); max-height: 80px; }
        to   { opacity: 0; transform: translateX(12px); max-height: 0; padding: 0; }
    }
    .removing { animation: removeRow 0.25s ease forwards; overflow: hidden; }
</style>
@endpush

@push('scripts')
<script>
// ====================================================
// LOAD CART FROM localStorage
// ====================================================
let cart = JSON.parse(localStorage.getItem('sc_cart') || '[]');

function saveCart() {
    localStorage.setItem('sc_cart', JSON.stringify(cart));
}

// ====================================================
// RENDER PAGE
// ====================================================
function renderPage() {
    const totalQty   = cart.reduce((s, i) => s + i.qty, 0);
    const totalPrice = cart.reduce((s, i) => s + (i.price * i.qty), 0);

    const isEmpty = cart.length === 0;
    document.getElementById('empty-state').classList.toggle('hidden', !isEmpty);
    document.getElementById('cart-content').classList.toggle('hidden', isEmpty);

    // Header badge
    const badge = document.getElementById('header-badge');
    badge.textContent = totalQty;
    badge.classList.toggle('hidden', totalQty === 0);

    if (isEmpty) return;

    // Item count
    document.getElementById('item-count').textContent = totalQty;

    // Items list
    const list = document.getElementById('cart-items-list');
    list.innerHTML = cart.map((item, idx) => `
        <div class="item-row flex items-center gap-3 p-3 hover:bg-gray-50/80 rounded-xl transition-colors" style="animation-delay:${idx * 50}ms" id="row-${item.id}">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-amber-50 rounded-xl flex items-center justify-center text-2xl shadow-sm flex-shrink-0">${item.emoji}</div>
            <div class="flex-1 min-w-0">
                <p class="font-heading font-bold text-sm text-canteen-dark truncate">${item.name}</p>
                <p class="text-xs text-gray-400 mt-0.5">@Rp ${item.price.toLocaleString('id-ID')}</p>
            </div>
            <div class="flex items-center gap-0 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                <button onclick="changeQty(${item.id}, -1)"
                        class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-red-400 hover:bg-red-50 font-bold text-lg transition-colors">−</button>
                <span class="font-heading font-bold text-sm text-canteen-dark w-8 text-center">${item.qty}</span>
                <button onclick="changeQty(${item.id}, 1)"
                        class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-primary-500 hover:bg-orange-50 font-bold text-lg transition-colors">+</button>
            </div>
            <div class="text-right flex-shrink-0 w-20">
                <p class="font-heading font-bold text-sm text-canteen-dark">Rp ${(item.price * item.qty).toLocaleString('id-ID')}</p>
            </div>
            <button onclick="removeItem(${item.id})"
                    class="w-8 h-8 flex items-center justify-center text-gray-300 hover:text-red-400 hover:bg-red-50 rounded-xl transition-colors flex-shrink-0">
                <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
        </div>
    `).join('');

    // Summary breakdown
    const breakdown = document.getElementById('summary-breakdown');
    breakdown.innerHTML = cart.map(item => `
        <div class="flex items-center justify-between gap-2">
            <span class="text-gray-500 text-xs flex items-center gap-1.5 truncate">
                <span>${item.emoji}</span>
                <span class="truncate">${item.name} ×${item.qty}</span>
            </span>
            <span class="font-medium text-gray-700 text-xs flex-shrink-0">Rp ${(item.price * item.qty).toLocaleString('id-ID')}</span>
        </div>
    `).join('');

    // Totals
    document.getElementById('summary-subtotal').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
    document.getElementById('summary-total').textContent    = 'Rp ' + totalPrice.toLocaleString('id-ID');

    // Saldo check (dummy: 85000)
    const saldo = 85000;
    const saldoEl = document.getElementById('saldo-status');
    if (totalPrice > saldo) {
        saldoEl.textContent = 'Kurang ✗';
        saldoEl.className = 'text-xs font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-lg';
    } else {
        saldoEl.textContent = 'Cukup ✓';
        saldoEl.className = 'text-xs font-semibold text-green-600 bg-green-100 px-2.5 py-1 rounded-lg';
    }
}

// ====================================================
// CHANGE QTY
// ====================================================
function changeQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        removeItem(id);
        return;
    }
    saveCart();
    renderPage();
}

// ====================================================
// REMOVE ITEM
// ====================================================
function removeItem(id) {
    const row = document.getElementById('row-' + id);
    if (row) {
        row.classList.add('removing');
        setTimeout(() => {
            cart = cart.filter(i => i.id !== id);
            saveCart();
            renderPage();
        }, 240);
    } else {
        cart = cart.filter(i => i.id !== id);
        saveCart();
        renderPage();
    }
}

// ====================================================
// CLEAR ALL
// ====================================================
function clearAllCart() {
    if (!confirm('Hapus semua item dari keranjang?')) return;
    cart = [];
    saveCart();
    renderPage();
}

// ====================================================
// PICKUP RADIO
// ====================================================
document.querySelectorAll('.pickup-radio').forEach(r => {
    r.addEventListener('change', () => {
        document.querySelectorAll('.pickup-opt').forEach(el => {
            el.classList.remove('border-primary-400', 'bg-orange-50');
            el.classList.add('border-gray-200');
        });
        r.nextElementSibling.classList.add('border-primary-400', 'bg-orange-50');
        r.nextElementSibling.classList.remove('border-gray-200');
    });
});

// ====================================================
// PROCEED TO ORDER — kirim ke /order dengan cart data
// ====================================================
function proceedToOrder() {
    if (cart.length === 0) {
        alert('Keranjang kosong! Tambahkan menu terlebih dahulu.');
        return;
    }
    const note   = document.getElementById('order-note').value;
    const pickup = document.querySelector('.pickup-radio:checked')?.value || '1';
    // Simpan ke localStorage agar bisa dibaca halaman /order
    localStorage.setItem('sc_order_cart',   JSON.stringify(cart));
    localStorage.setItem('sc_order_note',   note);
    localStorage.setItem('sc_order_pickup', pickup);
    window.location.href = '/order';
}

// ====================================================
// INIT
// ====================================================
document.addEventListener('DOMContentLoaded', renderPage);
</script>
@endpush
