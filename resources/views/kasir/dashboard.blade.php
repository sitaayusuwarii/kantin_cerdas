@extends('layouts.app')
@section('title', 'Kasir - SmartCanteen')

@section('content')
@php
    $hour = now()->format('H');
    if ($hour < 12) {
        $greeting = 'Selamat Pagi';
        $icon = 'fa-sun';
    } elseif ($hour < 15) {
        $greeting = 'Selamat Siang';
        $icon = 'fa-cloud-sun';
    } elseif ($hour < 18) {
        $greeting = 'Selamat Sore';
        $icon = 'fa-cloud';
    } else {
        $greeting = 'Selamat Malam';
        $icon = 'fa-moon';
    }

    $statusLabels = [
        'pending' => 'Menunggu',
        'menunggu_pembayaran' => 'Menunggu Bayar',
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'pembayaran_terverifikasi' => 'Terverifikasi',
        'diproses' => 'Diproses',
        'selesai_dimasak' => 'Selesai Dimasak',
        'dikirim' => 'Dikirim',
        'selesai' => 'Selesai',
    ];

    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-700',
        'menunggu_pembayaran' => 'bg-red-100 text-red-700',
        'menunggu_konfirmasi' => 'bg-orange-100 text-orange-700',
        'pembayaran_terverifikasi' => 'bg-sky-100 text-sky-700',
        'diproses' => 'bg-indigo-100 text-indigo-700',
        'selesai_dimasak' => 'bg-teal-100 text-teal-700',
        'dikirim' => 'bg-cyan-100 text-cyan-700',
        'selesai' => 'bg-green-100 text-green-700',
    ];

    $menuPayload = $menus->map(fn ($menu) => [
        'id' => $menu->id,
        'name' => $menu->name,
        'price' => (int) $menu->price,
        'category_id' => $menu->category_id,
        'category_name' => $menu->category->name ?? 'Tanpa Kategori',
        'tenant_id' => $menu->tenant_id ?? $menu->tenant->id ?? null,
        'tenant_name' => $menu->tenant->name ?? $menu->tenant->tenant_name ?? 'Tenant',
        'image' => $menu->image ? asset('storage/' . $menu->image) : null,
    ])->values();

    $tenantOptions = $menus->map(fn ($menu) => [
        'id' => $menu->tenant_id ?? $menu->tenant->id ?? null,
        'name' => $menu->tenant->name ?? $menu->tenant->tenant_name ?? 'Tenant',
    ])
        ->filter(fn ($tenant) => ! empty($tenant['id']))
        ->unique('id')
        ->sortBy('name')
        ->values();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-100 text-green-700 px-4 py-3 rounded-2xl text-sm font-medium flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-2xl text-sm">
            <p class="font-bold mb-1">Pesanan belum bisa disimpan.</p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="bg-white rounded-3xl shadow-sm border border-orange-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-br from-orange-500 via-orange-600 to-amber-700 px-6 py-6 md:px-8 md:py-7 text-white">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/15 border border-white/20 px-3 py-1.5 rounded-full text-sm text-orange-50 mb-4">
                        <i class="fa-solid {{ $icon }}"></i>
                        {{ $greeting }}
                    </div>
                    <h1 class="font-heading font-extrabold text-2xl md:text-3xl leading-tight">
                        Kasir Pemesanan Langsung
                    </h1>
                    <p class="text-orange-50 text-sm mt-2 max-w-2xl">
                        Pilih menu, atur jumlah pesanan, pilih tipe layanan, lalu konfirmasi pembayaran dalam satu halaman.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 min-w-full lg:min-w-[560px]">
                    <div class="bg-white/15 border border-white/15 rounded-2xl p-3">
                        <p class="text-xs text-orange-100">Pesanan</p>
                        <p class="font-heading font-bold text-xl">{{ $totalPesananHariIni }}</p>
                    </div>
                    <div class="bg-white/15 border border-white/15 rounded-2xl p-3">
                        <p class="text-xs text-orange-100">Pendapatan</p>
                        <p class="font-heading font-bold text-base">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/15 border border-white/15 rounded-2xl p-3">
                        <p class="text-xs text-orange-100">Aktif</p>
                        <p class="font-heading font-bold text-xl">{{ $pesananAktif }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        <section class="xl:col-span-8 bg-white rounded-2xl shadow-sm border border-orange-100 overflow-hidden">
            <div class="p-5 border-b border-orange-50">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h2 class="font-heading font-bold text-lg text-canteen-dark flex items-center gap-2">
                            <i class="fa-solid fa-utensils text-primary-500"></i>
                            Pilih Menu
                        </h2>
                        <p class="text-xs text-gray-400 mt-1">Klik tambah untuk memasukkan menu ke pesanan kasir.</p>
                    </div>

                    <div class="relative w-full lg:w-80">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input id="menuSearch" type="text" placeholder="Cari menu atau tenant..."
                               class="w-full rounded-2xl border border-orange-100 pl-10 pr-4 py-3 text-sm focus:border-primary-400 focus:ring-primary-100">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                    <div>
                        <label for="categoryFilter" class="text-xs font-bold text-gray-600 mb-1.5 block">Kategori</label>
                        <select id="categoryFilter"
                                class="w-full rounded-2xl border border-orange-100 px-4 py-3 text-sm focus:border-primary-400 focus:ring-primary-100">
                            <option value="all">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tenantFilter" class="text-xs font-bold text-gray-600 mb-1.5 block">Tenant</label>
                        <select id="tenantFilter"
                                class="w-full rounded-2xl border border-orange-100 px-4 py-3 text-sm focus:border-primary-400 focus:ring-primary-100">
                            <option value="all">Semua Tenant</option>
                            @foreach($tenantOptions as $tenant)
                                <option value="{{ $tenant['id'] }}">{{ $tenant['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="p-5">
                <div id="menuGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($menus as $menu)
                        <article class="menu-card group rounded-2xl border border-orange-100 bg-white hover:border-primary-200 hover:shadow-md transition overflow-hidden"
                                 data-category="{{ $menu->category_id }}"
                                 data-tenant="{{ $menu->tenant_id ?? $menu->tenant->id ?? '' }}"
                                 data-keywords="{{ strtolower($menu->name . ' ' . ($menu->tenant->name ?? $menu->tenant->tenant_name ?? '') . ' ' . ($menu->category->name ?? '')) }}">
                            <div class="aspect-[4/3] bg-orange-50 overflow-hidden">
                                @if($menu->image)
                                    <img src="{{ asset('storage/' . $menu->image) }}"
                                         alt="{{ $menu->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center text-primary-500">
                                            <i class="fa-solid fa-bowl-food text-2xl"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="font-heading font-bold text-sm text-canteen-dark truncate">{{ $menu->name }}</h3>
                                        <p class="text-[11px] text-gray-400 truncate mt-0.5">
                                            {{ $menu->tenant->name ?? $menu->tenant->tenant_name ?? 'Tenant' }}
                                        </p>
                                    </div>
                                    <span class="bg-orange-50 text-primary-600 text-[10px] font-bold px-2 py-1 rounded-full shrink-0">
                                        {{ $menu->category->name ?? 'Menu' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-3 mt-4">
                                    <p class="font-heading font-extrabold text-primary-600">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </p>
                                    <button type="button"
                                            class="add-menu-btn btn-primary text-white text-xs font-bold px-3 py-2 rounded-xl shadow-sm flex items-center gap-1.5"
                                            data-menu-id="{{ $menu->id }}">
                                        <i class="fa-solid fa-plus text-[11px]"></i>
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full text-center py-14 bg-orange-50 rounded-2xl border border-orange-100">
                            <div class="w-14 h-14 rounded-2xl bg-white mx-auto flex items-center justify-center text-primary-500 mb-3">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                            <p class="font-heading font-bold text-gray-700">Belum ada menu tersedia</p>
                            <p class="text-sm text-gray-400 mt-1">Aktifkan menu dari halaman pengelola tenant terlebih dahulu.</p>
                        </div>
                    @endforelse
                </div>

                <div id="emptyFilterState" class="hidden text-center py-14 bg-orange-50 rounded-2xl border border-orange-100">
                    <p class="font-heading font-bold text-gray-700">Menu tidak ditemukan</p>
                    <p class="text-sm text-gray-400 mt-1">Coba kata kunci atau kategori lain.</p>
                </div>
            </div>
        </section>

        <aside class="xl:col-span-4 xl:sticky xl:top-6 space-y-6">
            <form id="cashierOrderForm" action="{{ route('kasir.order.store') }}" method="POST"
                  class="bg-white rounded-2xl shadow-sm border border-orange-100 overflow-hidden">
                @csrf
                <input type="hidden" name="items" id="orderItemsInput" value="[]">

                <div class="p-5 border-b border-orange-50 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-heading font-bold text-lg text-canteen-dark flex items-center gap-2">
                            <i class="fa-solid fa-cash-register text-primary-500"></i>
                            Pesanan Kasir
                        </h2>
                        <p class="text-xs text-gray-400 mt-1">Ringkasan menu yang dipilih pelanggan.</p>
                    </div>
                    <button id="clearCartBtn" type="button"
                            class="text-xs font-bold text-red-500 hover:text-red-600 disabled:text-gray-300"
                            disabled>
                        Hapus
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <div id="cartItems" class="space-y-3">
                        <div class="text-center py-8 bg-orange-50 rounded-2xl border border-orange-100">
                            <div class="w-12 h-12 bg-white rounded-2xl shadow-sm mx-auto flex items-center justify-center text-primary-500 mb-3">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                            <p class="font-heading font-bold text-sm text-gray-700">Keranjang masih kosong</p>
                            <p class="text-xs text-gray-400 mt-1">Tambahkan menu dari daftar sebelah kiri.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-600 mb-1.5 block">Nama Pelanggan</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                   placeholder="Contoh: Sita"
                                   class="w-full rounded-2xl border border-orange-100 px-4 py-3 text-sm focus:border-primary-400 focus:ring-primary-100">
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-600 mb-1.5 block">Tipe Pesanan</label>
                            <select id="orderType" name="order_type"
                                    class="w-full rounded-2xl border border-orange-100 px-4 py-3 text-sm focus:border-primary-400 focus:ring-primary-100">
                                <option value="dine_in" {{ old('order_type') === 'dine_in' ? 'selected' : '' }}>Dine in</option>
                                <option value="takeaway" {{ old('order_type') === 'takeaway' ? 'selected' : '' }}>Take away</option>
                                <option value="antar_kelas" {{ old('order_type') === 'antar_kelas' ? 'selected' : '' }}>Antar ke kelas</option>
                            </select>
                        </div>
                    </div>

                    <div id="locationField">
                        <label id="locationLabel" class="text-xs font-bold text-gray-600 mb-1.5 block">Nomor Meja</label>
                        <input id="tableNumberInput" type="text" name="table_number" value="{{ old('table_number') }}"
                               placeholder="Contoh: Meja 05"
                               class="w-full rounded-2xl border border-orange-100 px-4 py-3 text-sm focus:border-primary-400 focus:ring-primary-100">
                    </div>

                    <div>
                        <label for="pickupOption" class="text-xs font-bold text-gray-600 mb-1.5 block">Waktu Pengambilan/Pengantaran</label>
                        <select id="pickupOption" name="pickup_option"
                                class="w-full rounded-2xl border border-orange-100 px-4 py-3 text-sm focus:border-primary-400 focus:ring-primary-100">
                            <option value="sekarang" {{ old('pickup_option') === 'sekarang' ? 'selected' : '' }}>Sekarang</option>
                            <option value="istirahat_1" {{ old('pickup_option') === 'istirahat_1' ? 'selected' : '' }}>Istirahat 1</option>
                            <option value="istirahat_2" {{ old('pickup_option') === 'istirahat_2' ? 'selected' : '' }}>Istirahat 2</option>
                            <option value="pulang" {{ old('pickup_option') === 'pulang' ? 'selected' : '' }}>Pulang</option>
                            <option value="atur_jam" {{ old('pickup_option') === 'atur_jam' ? 'selected' : '' }}>Atur jam sendiri</option>
                        </select>
                    </div>

                    <div id="pickupTimeField" class="hidden">
                        <label for="pickupTimeInput" class="text-xs font-bold text-gray-600 mb-1.5 block">Jam</label>
                        <input id="pickupTimeInput" type="time" name="pickup_time" value="{{ old('pickup_time') }}"
                               class="w-full rounded-2xl border border-orange-100 px-4 py-3 text-sm focus:border-primary-400 focus:ring-primary-100">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-600 mb-1.5 block">Metode Pembayaran</label>
                        <select name="payment_method"
                                class="w-full rounded-2xl border border-orange-100 px-4 py-3 text-sm focus:border-primary-400 focus:ring-primary-100">
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->code }}" {{ old('payment_method') === $method->code ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-600 mb-1.5 block">Catatan</label>
                        <textarea name="note" rows="2"
                                  placeholder="Contoh: tidak pedas, tanpa es"
                                  class="w-full rounded-2xl border border-orange-100 px-4 py-3 text-sm focus:border-primary-400 focus:ring-primary-100">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="bg-orange-50/70 border-t border-orange-100 p-5">
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Total item</span>
                            <span id="totalQty" class="font-bold text-canteen-dark">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total bayar</span>
                            <span id="grandTotal" class="font-heading font-extrabold text-xl text-primary-600">Rp 0</span>
                        </div>
                    </div>

                    <button id="submitOrderBtn" type="submit"
                            class="btn-primary w-full text-white font-bold py-3 rounded-2xl shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            disabled>
                        <i class="fa-solid fa-circle-check"></i>
                        Simpan Pesanan
                    </button>
                </div>
            </form>

        </aside>
    </div>

   
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menus = @json($menuPayload);
        const cart = new Map();
        const formatter = new Intl.NumberFormat('id-ID');

        const searchInput = document.getElementById('menuSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const tenantFilter = document.getElementById('tenantFilter');
        const menuCards = document.querySelectorAll('.menu-card');
        const emptyFilterState = document.getElementById('emptyFilterState');
        const cartItems = document.getElementById('cartItems');
        const orderItemsInput = document.getElementById('orderItemsInput');
        const totalQty = document.getElementById('totalQty');
        const grandTotal = document.getElementById('grandTotal');
        const submitOrderBtn = document.getElementById('submitOrderBtn');
        const clearCartBtn = document.getElementById('clearCartBtn');
        const orderType = document.getElementById('orderType');
        const locationField = document.getElementById('locationField');
        const locationLabel = document.getElementById('locationLabel');
        const tableNumberInput = document.getElementById('tableNumberInput');
        const pickupOption = document.getElementById('pickupOption');
        const pickupTimeField = document.getElementById('pickupTimeField');
        const pickupTimeInput = document.getElementById('pickupTimeInput');
        const pickupOptionLabel = document.querySelector('label[for="pickupOption"]');

        const rupiah = (value) => `Rp ${formatter.format(value)}`;

        const syncPickupTimeField = () => {
            const showCustomTime = pickupOption.value === 'atur_jam';

            pickupTimeField.classList.toggle('hidden', !showCustomTime);
            pickupTimeInput.required = showCustomTime;

            if (!showCustomTime) {
                pickupTimeInput.value = '';
            }
        };

        const syncLocationField = () => {
            if (orderType.value === 'takeaway') {
                locationField.classList.add('hidden');
                tableNumberInput.value = '';
                tableNumberInput.required = false;
                pickupOptionLabel.textContent = 'Waktu Pengambilan';
                return;
            }

            locationField.classList.remove('hidden');
            tableNumberInput.required = true;

            if (orderType.value === 'antar_kelas') {
                locationLabel.textContent = 'Kelas Tujuan';
                tableNumberInput.placeholder = 'Contoh: XI RPL 1';
                pickupOptionLabel.textContent = 'Waktu Pengantaran';
            } else {
                locationLabel.textContent = 'Nomor Meja';
                tableNumberInput.placeholder = 'Contoh: Meja 05';
                pickupOptionLabel.textContent = 'Waktu Pengambilan';
            }
        };

        const applyFilters = () => {
            const keyword = searchInput.value.trim().toLowerCase();
            const selectedCategory = categoryFilter.value;
            const selectedTenant = tenantFilter.value;
            let visibleCount = 0;

            menuCards.forEach((card) => {
                const matchCategory = selectedCategory === 'all' || card.dataset.category === selectedCategory;
                const matchTenant = selectedTenant === 'all' || card.dataset.tenant === selectedTenant;
                const matchKeyword = !keyword || card.dataset.keywords.includes(keyword);
                const show = matchCategory && matchTenant && matchKeyword;

                card.classList.toggle('hidden', !show);
                if (show) visibleCount += 1;
            });

            emptyFilterState.classList.toggle('hidden', visibleCount > 0);
        };

        const renderCart = () => {
            const items = Array.from(cart.values());

            if (items.length === 0) {
                cartItems.innerHTML = `
                    <div class="text-center py-8 bg-orange-50 rounded-2xl border border-orange-100">
                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm mx-auto flex items-center justify-center text-primary-500 mb-3">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <p class="font-heading font-bold text-sm text-gray-700">Keranjang masih kosong</p>
                        <p class="text-xs text-gray-400 mt-1">Tambahkan menu dari daftar sebelah kiri.</p>
                    </div>
                `;
            } else {
                cartItems.innerHTML = items.map((item) => `
                    <div class="rounded-2xl border border-orange-100 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-heading font-bold text-sm text-canteen-dark truncate">${item.name}</p>
                                <p class="text-xs text-gray-400 truncate">${item.tenant_name}</p>
                                <p class="text-xs font-bold text-primary-600 mt-1">${rupiah(item.price)}</p>
                            </div>
                            <button type="button" class="remove-cart-item text-red-500 hover:text-red-600" data-menu-id="${item.id}" aria-label="Hapus item">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <div class="inline-flex items-center rounded-xl border border-orange-100 overflow-hidden">
                                <button type="button" class="decrease-cart-item px-3 py-2 text-gray-600 hover:bg-orange-50" data-menu-id="${item.id}">-</button>
                                <span class="px-3 py-2 text-sm font-bold min-w-10 text-center">${item.qty}</span>
                                <button type="button" class="increase-cart-item px-3 py-2 text-gray-600 hover:bg-orange-50" data-menu-id="${item.id}">+</button>
                            </div>
                            <p class="font-heading font-bold text-sm text-canteen-dark">${rupiah(item.price * item.qty)}</p>
                        </div>
                    </div>
                `).join('');
            }

            const payload = items.map((item) => ({ id: item.id, qty: item.qty }));
            const qty = items.reduce((sum, item) => sum + item.qty, 0);
            const total = items.reduce((sum, item) => sum + (item.price * item.qty), 0);

            orderItemsInput.value = JSON.stringify(payload);
            totalQty.textContent = qty;
            grandTotal.textContent = rupiah(total);
            submitOrderBtn.disabled = items.length === 0;
            clearCartBtn.disabled = items.length === 0;
        };

        document.querySelectorAll('.add-menu-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const menu = menus.find((item) => item.id === Number(button.dataset.menuId));
                if (!menu) return;

                const current = cart.get(menu.id);
                cart.set(menu.id, { ...menu, qty: current ? current.qty + 1 : 1 });
                renderCart();
            });
        });

        cartItems.addEventListener('click', (event) => {
            const button = event.target.closest('button[data-menu-id]');
            if (!button) return;

            const id = Number(button.dataset.menuId);
            const item = cart.get(id);
            if (!item) return;

            if (button.classList.contains('increase-cart-item')) {
                item.qty += 1;
                cart.set(id, item);
            }

            if (button.classList.contains('decrease-cart-item')) {
                item.qty -= 1;
                item.qty > 0 ? cart.set(id, item) : cart.delete(id);
            }

            if (button.classList.contains('remove-cart-item')) {
                cart.delete(id);
            }

            renderCart();
        });

        clearCartBtn.addEventListener('click', () => {
            cart.clear();
            renderCart();
        });

        searchInput.addEventListener('input', applyFilters);
        categoryFilter.addEventListener('change', applyFilters);
        tenantFilter.addEventListener('change', applyFilters);
        orderType.addEventListener('change', syncLocationField);
        pickupOption.addEventListener('change', syncPickupTimeField);

        syncLocationField();
        syncPickupTimeField();
        renderCart();
    });
</script>
@endsection
