@extends('layouts.app')
@section('title', 'Keranjang - SmartCanteen')

@section('content')

@if(session('error'))
    <div class="bg-red-100 text-red-700 p-4 rounded mb-4">{{ session('error') }}</div>
@endif

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 pb-28">

    {{-- Header --}}
    <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 px-5 py-7 shadow-xl shadow-orange-200/50 sm:px-8 lg:px-10">
        <div class="absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.22),transparent_40%)]"></div>
        <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-6 right-10 hidden text-white/10 lg:block">
            <i class="fa-solid fa-cart-shopping text-[8rem]"></i>
        </div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ url('/menu') }}"
                   class="mb-5 inline-flex items-center gap-2 rounded-2xl border border-white/20 bg-white/15 px-4 py-2 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Kembali ke Menu
                </a>

                <h1 class="font-heading text-3xl font-extrabold leading-tight text-white md:text-4xl">
                    Keranjang Pesanan
                </h1>
                <p id="cart-header-count" class="mt-2 text-sm font-medium text-orange-50">
                    Memuat keranjang...
                </p>
            </div>

            <div class="w-full rounded-3xl border border-white/20 bg-white/15 p-4 text-white backdrop-blur sm:w-auto sm:min-w-[260px]">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15">
                        <i class="fa-solid fa-bag-shopping text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-orange-100">Total Sementara</p>
                        <p id="hero-total" class="mt-1 font-heading text-2xl font-extrabold">Rp 0</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Loading State --}}
    <div id="cart-loading" class="mt-8 rounded-[2rem] border border-orange-100 bg-white p-12 text-center shadow-sm">
        <div class="mx-auto mb-4 h-11 w-11 animate-spin rounded-full border-4 border-orange-100 border-t-orange-500"></div>
        <p class="text-sm font-semibold text-gray-500">Memuat keranjang...</p>
    </div>

    {{-- Empty State --}}
    <div id="cart-empty" class="hidden mt-8 rounded-[2rem] border border-dashed border-orange-200 bg-white p-10 text-center shadow-sm sm:p-14">
        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-[2rem] bg-orange-50 text-orange-300">
            <i class="fa-solid fa-cart-shopping text-4xl"></i>
        </div>
        <p class="mt-5 font-heading text-xl font-extrabold text-gray-800">Keranjang Kosong</p>
        <p class="mt-2 text-sm text-gray-500">Tambahkan menu favoritmu sebelum lanjut checkout.</p>
        <a href="{{ url('/menu') }}"
           class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-orange-100 transition hover:-translate-y-0.5 hover:shadow-orange-200 active:scale-95">
            <i class="fa-solid fa-bowl-food"></i>
            Lihat Menu
        </a>
    </div>

    {{-- Cart Content --}}
    <div id="cart-content" class="hidden mt-8 grid gap-6 lg:grid-cols-[1fr_380px] lg:items-start">

        {{-- Items List --}}
        <section class="rounded-[2rem] border border-orange-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="mb-4 flex items-center justify-between gap-3 px-1">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Daftar Menu</p>
                    <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950">Item Keranjang</h2>
                </div>
                <button onclick="clearCart()"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gray-50 px-3 py-2 text-xs font-bold text-gray-400 transition hover:bg-red-50 hover:text-red-500">
                    <i class="fa-solid fa-trash-can"></i>
                    Kosongkan
                </button>
            </div>

            <div id="cart-items-list" class="space-y-3"></div>
        </section>

        {{-- Summary --}}
        <aside class="lg:sticky lg:top-24">
            <div class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Checkout</p>
                        <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950">Ringkasan Pesanan</h2>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>

                <div class="mt-5 rounded-3xl bg-orange-50 p-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-500">Subtotal</span>
                            <span id="cart-subtotal" class="font-bold text-gray-900">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-500">Jumlah Item</span>
                            <span id="cart-qty-summary" class="font-bold text-gray-900">0 item</span>
                        </div>
                        <div class="border-t border-orange-100 pt-4">
                            <div class="flex items-end justify-between gap-3">
                                <span class="font-heading text-base font-extrabold text-gray-950">Total</span>
                                <span id="cart-total" class="text-right font-heading text-3xl font-extrabold text-orange-600">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button id="checkout-btn"
                        onclick="submitOrder()"
                        class="mt-5 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 py-4 text-base font-heading font-extrabold text-white shadow-lg shadow-orange-100 transition hover:-translate-y-0.5 hover:shadow-orange-200 active:scale-95">
                    <i class="fa-solid fa-bag-shopping"></i>
                    Pesan Sekarang
                </button>

                <div class="mt-4 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-white text-orange-500">
                            <i class="fa-solid fa-circle-info text-sm"></i>
                        </div>
                        <p class="text-xs leading-5 text-gray-500">
                            Setelah klik pesan, kamu akan diarahkan ke halaman detail pesanan untuk memilih tipe layanan dan jadwal.
                        </p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

{{-- Confirm Modal --}}
    <div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
        <div id="confirm-modal-backdrop" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

        <div id="confirm-modal-box"
             class="relative w-full max-w-sm rounded-[1.75rem] bg-white p-6 text-center shadow-2xl">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-red-500">
                <i class="fa-solid fa-trash-can text-2xl"></i>
            </div>

            <h3 class="mt-5 font-heading text-lg font-extrabold text-gray-950">
                Kosongkan Keranjang?
            </h3>
            <p class="mt-2 text-sm text-gray-500">
                Semua item di keranjang akan dihapus dan tidak bisa dikembalikan.
            </p>

            <div class="mt-6 flex gap-3">
                <button onclick="closeConfirmModal()"
                        class="flex-1 rounded-2xl bg-gray-100 py-3 text-sm font-extrabold text-gray-600 transition hover:bg-gray-200">
                    Batal
                </button>
                <button onclick="confirmClearCart()"
                        class="flex-1 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 py-3 text-sm font-extrabold text-white shadow-lg shadow-red-100 transition hover:-translate-y-0.5">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    @keyframes itemIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .cart-item {
        animation: itemIn 0.22s ease both;
    }

    @keyframes removeItem {
        from { opacity: 1; transform: translateX(0); max-height: 140px; }
        to { opacity: 0; transform: translateX(12px); max-height: 0; padding-top: 0; padding-bottom: 0; margin: 0; overflow: hidden; }
    }

    .removing {
        animation: removeItem 0.22s ease forwards;
        pointer-events: none;
    }

    #confirm-modal.flex #confirm-modal-backdrop {
        animation: fadeIn 0.18s ease both;
    }
    #confirm-modal.flex #confirm-modal-box {
        animation: popIn 0.22s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes popIn {
        from { opacity: 0; transform: scale(0.92) translateY(8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

let cartItems = [];
const pendingRemove = new Set();

function formatRp(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));
}

function getTotalQty() {
    return cartItems.reduce((sum, item) => sum + Number(item.qty || 0), 0);
}

function getTotalPrice() {
    return cartItems.reduce((sum, item) => sum + (Number(item.price || 0) * Number(item.qty || 0)), 0);
}

function loadCart() {
    return fetch('/cart/data', { headers: { 'X-CSRF-TOKEN': CSRF } })
        .then(response => response.json())
        .then(result => {
            cartItems = (result.items || []).filter(item => !pendingRemove.has(item.cart_item_id));
            renderCart();
            return result;
        });
}

function renderCart() {
    document.getElementById('cart-loading').classList.add('hidden');

    if (cartItems.length === 0) {
        document.getElementById('cart-empty').classList.remove('hidden');
        document.getElementById('cart-content').classList.add('hidden');
        document.getElementById('cart-header-count').textContent = 'Keranjang kosong';
        document.getElementById('hero-total').textContent = 'Rp 0';
        return;
    }

    document.getElementById('cart-empty').classList.add('hidden');
    document.getElementById('cart-content').classList.remove('hidden');
    syncSummary();

    const list = document.getElementById('cart-items-list');
    list.innerHTML = cartItems.map((item, index) => {
        const name = escapeHtml(item.name);
        const image = item.image
            ? `<img src="${escapeHtml(item.image)}" class="h-full w-full object-cover" alt="${name}">`
            : '<i class="fa-solid fa-bowl-food text-2xl text-orange-400"></i>';
        const isMinQty = Number(item.qty) <= 1;
        const isMaxQty = Number(item.qty) >= Number(item.stock || 0);

        return `
            <article class="cart-item rounded-3xl border border-gray-100 bg-white p-3 shadow-sm transition hover:border-orange-100 hover:shadow-md sm:p-4"
                     id="cart-row-${item.cart_item_id}"
                     style="animation-delay:${index * 0.04}s">
                <div class="flex gap-4">
                    <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-2xl border border-orange-50 bg-orange-50 flex items-center justify-center">
                        ${image}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate font-heading text-base font-extrabold text-gray-950">${name}</h3>
                                <p class="mt-1 text-sm font-bold text-orange-600">${formatRp(item.price)}</p>
                            </div>
                            <button onclick="removeItem(${item.cart_item_id})"
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-gray-50 text-gray-300 transition hover:bg-red-50 hover:text-red-500"
                                    aria-label="Hapus item">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>

                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400">Subtotal</p>
                                <p id="sub-${item.cart_item_id}" class="mt-0.5 font-heading text-lg font-extrabold text-gray-900">
                                    ${formatRp(item.price * item.qty)}
                                </p>
                            </div>

                            <div class="flex w-fit items-center rounded-2xl border border-gray-100 bg-gray-50 p-1">
                                <button id="minus-btn-${item.cart_item_id}"
                                        onclick="changeQty(${item.cart_item_id}, -1)"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl font-bold transition ${isMinQty ? 'text-red-400 hover:bg-red-50' : 'text-gray-500 hover:bg-white hover:text-red-500'}">
                                    ${isMinQty ? '<i class="fa-solid fa-trash text-xs"></i>' : '&minus;'}
                                </button>
                                <span id="qty-${item.cart_item_id}" class="w-10 text-center text-sm font-extrabold text-gray-900">
                                    ${item.qty}
                                </span>
                                <button id="plus-btn-${item.cart_item_id}"
                                        onclick="${isMaxQty ? '' : `changeQty(${item.cart_item_id}, 1)`}"
                                        ${isMaxQty ? 'disabled' : ''}
                                        class="flex h-9 w-9 items-center justify-center rounded-xl font-bold transition ${isMaxQty ? 'cursor-not-allowed text-gray-200' : 'text-gray-500 hover:bg-white hover:text-orange-600'}">
                                    +
                                </button>
                            </div>
                        </div>

                        <p class="mt-2 text-xs text-gray-400">Stok tersedia: ${item.stock}</p>
                    </div>
                </div>
            </article>
        `;
    }).join('');
}

function syncSummary() {
    const qty = getTotalQty();
    const price = getTotalPrice();

    document.getElementById('cart-header-count').textContent = qty + ' item dipilih';
    document.getElementById('cart-subtotal').textContent = formatRp(price);
    document.getElementById('cart-qty-summary').textContent = qty + ' item';
    document.getElementById('cart-total').textContent = formatRp(price);
    document.getElementById('hero-total').textContent = formatRp(price);

    localStorage.setItem('cart_count', qty);
    localStorage.setItem('cart_price', price);
}

function refreshRowControls(cartItemId, item) {
    const minusBtn = document.getElementById('minus-btn-' + cartItemId);
    const plusBtn = document.getElementById('plus-btn-' + cartItemId);

    if (minusBtn) {
        if (item.qty <= 1) {
            minusBtn.innerHTML = '<i class="fa-solid fa-trash text-xs"></i>';
            minusBtn.className = 'flex h-9 w-9 items-center justify-center rounded-xl font-bold transition text-red-400 hover:bg-red-50';
        } else {
            minusBtn.innerHTML = '&minus;';
            minusBtn.className = 'flex h-9 w-9 items-center justify-center rounded-xl font-bold transition text-gray-500 hover:bg-white hover:text-red-500';
        }
    }

    if (plusBtn) {
        if (item.qty >= item.stock) {
            plusBtn.disabled = true;
            plusBtn.setAttribute('onclick', '');
            plusBtn.className = 'flex h-9 w-9 items-center justify-center rounded-xl font-bold transition cursor-not-allowed text-gray-200';
        } else {
            plusBtn.disabled = false;
            plusBtn.setAttribute('onclick', `changeQty(${cartItemId}, 1)`);
            plusBtn.className = 'flex h-9 w-9 items-center justify-center rounded-xl font-bold transition text-gray-500 hover:bg-white hover:text-orange-600';
        }
    }
}

function changeQty(cartItemId, delta) {
    const item = cartItems.find(cartItem => cartItem.cart_item_id === cartItemId);
    if (!item) return;

    const oldQty = Number(item.qty);
    const newQty = oldQty + delta;

    if (newQty <= 0) {
        removeItem(cartItemId);
        return;
    }

    if (newQty > Number(item.stock || 0)) {
        const qtyEl = document.getElementById('qty-' + cartItemId);
        if (qtyEl) {
            qtyEl.classList.add('text-red-500');
            setTimeout(() => qtyEl.classList.remove('text-red-500'), 800);
        }
        return;
    }

    item.qty = newQty;

    const qtyEl = document.getElementById('qty-' + cartItemId);
    const subEl = document.getElementById('sub-' + cartItemId);

    if (qtyEl) qtyEl.textContent = newQty;
    if (subEl) subEl.textContent = formatRp(item.price * newQty);

    refreshRowControls(cartItemId, item);
    syncSummary();

    fetch('/cart/update-ajax/' + cartItemId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ quantity: newQty }),
    })
        .then(response => response.json())
        .then(result => {
            if (!result.success) {
                item.qty = oldQty;
                if (qtyEl) qtyEl.textContent = oldQty;
                if (subEl) subEl.textContent = formatRp(item.price * oldQty);
                refreshRowControls(cartItemId, item);
                syncSummary();
            }
        })
        .catch(() => {
            item.qty = oldQty;
            if (qtyEl) qtyEl.textContent = oldQty;
            if (subEl) subEl.textContent = formatRp(item.price * oldQty);
            refreshRowControls(cartItemId, item);
            syncSummary();
        });
}

function removeItem(cartItemId) {
    const row = document.getElementById('cart-row-' + cartItemId);
    pendingRemove.add(cartItemId);

    if (row) {
        row.classList.add('removing');
        setTimeout(() => row.remove(), 220);
    }

    cartItems = cartItems.filter(item => item.cart_item_id !== cartItemId);
    syncSummary();

    if (cartItems.length === 0) {
        setTimeout(() => {
            document.getElementById('cart-content').classList.add('hidden');
            document.getElementById('cart-empty').classList.remove('hidden');
            document.getElementById('cart-header-count').textContent = 'Keranjang kosong';
            document.getElementById('hero-total').textContent = 'Rp 0';
        }, 230);
    }

    fetch('/cart/remove-ajax/' + cartItemId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
    })
        .then(response => response.json())
        .then(result => {
            pendingRemove.delete(cartItemId);
            if (!result.success) loadCart();
        })
        .catch(() => {
            pendingRemove.delete(cartItemId);
            loadCart();
        });
}

function clearCart() {
    const modal = document.getElementById('confirm-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeConfirmModal() {
    const modal = document.getElementById('confirm-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function confirmClearCart() {
    closeConfirmModal();

    cartItems = [];
    renderCart();
    localStorage.setItem('cart_count', 0);
    localStorage.setItem('cart_price', 0);

    fetch('/cart/clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
    })
        .then(response => response.json())
        .then(result => {
            if (!result.success) loadCart();
        })
        .catch(() => loadCart());
}

// Klik backdrop untuk menutup modal
document.addEventListener('DOMContentLoaded', () => {
    loadCart();
    document.getElementById('confirm-modal-backdrop')
        .addEventListener('click', closeConfirmModal);
});

function submitOrder() {
    window.location.href = '/order';
}

document.addEventListener('DOMContentLoaded', () => {
    loadCart();
});
</script>
@endpush
