@extends('layouts.admin')
@section('title', 'Kelola Menu')
@section('page-title', 'Kelola Menu')
@section('page-subtitle', 'Tambah, edit, dan kelola daftar menu kantin')

@section('content')

{{-- Header Actions --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="relative">
            <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Cari menu..." class="pl-9 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-all w-52">
        </div>
        <select class="bg-white border border-gray-200 text-gray-600 text-sm px-3 py-2.5 rounded-xl focus:outline-none focus:border-brand-300 transition-all">
            <option>Semua Kategori</option>
            <option>Makanan</option>
            <option>Minuman</option>
            <option>Snack</option>
        </select>
    </div>
    <button onclick="openModal()" class="btn-brand text-white font-semibold text-sm px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-lg">
        <i class="fa-solid fa-plus"></i>Tambah Menu Baru
    </button>
</div>

{{-- Stats Bar --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
        <p class="font-heading font-bold text-xl text-gray-800">12</p>
        <p class="text-xs text-gray-400 mt-0.5">Total Menu</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
        <p class="font-heading font-bold text-xl text-emerald-600">10</p>
        <p class="text-xs text-gray-400 mt-0.5">Tersedia</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
        <p class="font-heading font-bold text-xl text-red-500">2</p>
        <p class="text-xs text-gray-400 mt-0.5">Habis</p>
    </div>
</div>

{{-- Menu Grid --}}
@php
$menus = [
    ['id'=>1,'name'=>'Nasi Gudeg Komplit','price'=>'Rp 12.000','cat'=>'Makanan','status'=>true,'emoji'=>'🍛','sold'=>48,'color'=>'from-yellow-400 to-orange-400'],
    ['id'=>2,'name'=>'Mie Goreng Spesial','price'=>'Rp 10.000','cat'=>'Makanan','status'=>true,'emoji'=>'🍜','sold'=>41,'color'=>'from-orange-400 to-red-400'],
    ['id'=>3,'name'=>'Nasi Ayam Geprek','price'=>'Rp 13.000','cat'=>'Makanan','status'=>true,'emoji'=>'🍗','sold'=>35,'color'=>'from-red-400 to-rose-500'],
    ['id'=>4,'name'=>'Bakso Urat Jumbo','price'=>'Rp 11.000','cat'=>'Makanan','status'=>true,'emoji'=>'🍲','sold'=>29,'color'=>'from-amber-400 to-yellow-500'],
    ['id'=>5,'name'=>'Es Teh Manis','price'=>'Rp 4.000','cat'=>'Minuman','status'=>true,'emoji'=>'🧋','sold'=>62,'color'=>'from-brown-400 to-amber-500'],
    ['id'=>6,'name'=>'Jus Alpukat','price'=>'Rp 8.000','cat'=>'Minuman','status'=>false,'emoji'=>'🥑','sold'=>18,'color'=>'from-green-400 to-emerald-500'],
    ['id'=>7,'name'=>'Pisang Goreng','price'=>'Rp 5.000','cat'=>'Snack','status'=>true,'emoji'=>'🍌','sold'=>24,'color'=>'from-yellow-300 to-amber-400'],
    ['id'=>8,'name'=>'Indomie Rebus','price'=>'Rp 7.000','cat'=>'Makanan','status'=>false,'emoji'=>'🍝','sold'=>20,'color'=>'from-orange-300 to-yellow-400'],
];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($menus as $menu)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition-shadow">
        {{-- Image Area --}}
        <div class="relative h-36 bg-gradient-to-br {{ $menu['color'] }} flex items-center justify-center">
            <span class="text-5xl group-hover:scale-110 transition-transform duration-300">{{ $menu['emoji'] }}</span>
            <div class="absolute top-3 left-3">
                <span class="bg-white/90 text-gray-600 text-[10px] font-semibold px-2 py-0.5 rounded-full">{{ $menu['cat'] }}</span>
            </div>
            {{-- Status Toggle --}}
            <div class="absolute top-3 right-3">
                <label class="relative inline-flex items-center cursor-pointer" title="{{ $menu['status'] ? 'Tersedia' : 'Habis' }}">
                    <input type="checkbox" class="sr-only peer" {{ $menu['status'] ? 'checked' : '' }}>
                    <div class="w-9 h-5 bg-gray-300 peer-checked:bg-emerald-500 rounded-full transition-colors shadow peer-checked:shadow-emerald-200"></div>
                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-4 shadow-sm"></div>
                </label>
            </div>
        </div>
        {{-- Content --}}
        <div class="p-4">
            <div class="flex items-start justify-between gap-2 mb-1">
                <h3 class="font-heading font-bold text-sm text-gray-800 leading-tight">{{ $menu['name'] }}</h3>
            </div>
            <div class="flex items-center justify-between mb-3">
                <p class="font-heading font-bold text-base text-brand-500">{{ $menu['price'] }}</p>
                <span class="text-[10px] {{ $menu['status'] ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }} font-semibold px-2 py-0.5 rounded-full">
                    {{ $menu['status'] ? '✓ Tersedia' : '✗ Habis' }}
                </span>
            </div>
            <p class="text-[10px] text-gray-400 mb-3 flex items-center gap-1">
                <i class="fa-solid fa-chart-simple text-gray-300"></i>{{ $menu['sold'] }}x terjual
            </p>
            {{-- Actions --}}
            <div class="flex gap-2">
                <button onclick="openEditModal({{ $menu['id'] }})" class="flex-1 flex items-center justify-center gap-1.5 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-semibold py-2 rounded-xl transition-colors border border-gray-200">
                    <i class="fa-solid fa-pen text-[10px]"></i>Edit
                </button>
                <button onclick="confirmDelete({{ $menu['id'] }}, '{{ $menu['name'] }}')" class="w-9 h-9 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-500 rounded-xl transition-colors border border-red-100">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ===== ADD/EDIT MODAL ===== --}}
<div id="add-menu-modal" class="fixed inset-0 bg-black/60 z-50 items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden animate-fade-up">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-heading font-bold text-gray-800" id="modal-title">Tambah Menu Baru</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-gray-500 text-sm"></i>
            </button>
        </div>
        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Menu <span class="text-red-400">*</span></label>
                <input type="text" placeholder="Contoh: Nasi Gudeg Komplit" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Harga <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs font-semibold">Rp</span>
                        <input type="text" placeholder="12.000" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kategori</label>
                    <select class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-400 transition-all">
                        <option>Makanan</option>
                        <option>Minuman</option>
                        <option>Snack</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <textarea rows="2" placeholder="Deskripsi singkat menu..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm resize-none focus:outline-none focus:border-brand-400 transition-all"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Gambar Menu</label>
                <div class="relative border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-brand-300 transition-colors cursor-pointer bg-gray-50">
                    <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    <i class="fa-solid fa-cloud-arrow-up text-gray-300 text-2xl mb-2 block"></i>
                    <p class="text-xs text-gray-500 font-medium">Klik atau drag gambar</p>
                    <p class="text-[10px] text-gray-400">JPG, PNG · Maks 2MB</p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="status" value="1" class="sr-only" checked>
                        <div class="status-opt p-2.5 rounded-xl border-2 border-emerald-400 bg-emerald-50 text-center text-xs font-semibold text-emerald-700">
                            <i class="fa-solid fa-check mr-1"></i>Tersedia
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="status" value="0" class="sr-only">
                        <div class="status-opt p-2.5 rounded-xl border-2 border-gray-200 text-center text-xs font-semibold text-gray-500 hover:border-red-300 transition-all">
                            <i class="fa-solid fa-xmark mr-1"></i>Habis
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
            <button onclick="closeModal()" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-colors bg-white">Batal</button>
            <button class="flex-1 btn-brand text-white py-2.5 rounded-xl text-sm font-semibold shadow-lg">
                <i class="fa-solid fa-save mr-1.5"></i>Simpan Menu
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal() {
        document.getElementById('add-menu-modal').classList.add('open');
        document.getElementById('modal-title').textContent = 'Tambah Menu Baru';
    }
    function openEditModal(id) {
        document.getElementById('add-menu-modal').classList.add('open');
        document.getElementById('modal-title').textContent = 'Edit Menu #' + id;
    }
    function closeModal() {
        document.getElementById('add-menu-modal').classList.remove('open');
    }
    function confirmDelete(id, name) {
        if (confirm('Hapus menu "' + name + '"? Tindakan ini tidak dapat dibatalkan.')) {
            alert('Menu dihapus (dummy)');
        }
    }
    document.getElementById('add-menu-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    document.querySelectorAll('input[name="status"]').forEach(r => {
        r.addEventListener('change', () => {
            document.querySelectorAll('.status-opt').forEach(el => {
                el.className = 'status-opt p-2.5 rounded-xl border-2 border-gray-200 text-center text-xs font-semibold text-gray-500 hover:border-red-300 transition-all';
            });
            r.nextElementSibling.className = 'status-opt p-2.5 rounded-xl border-2 border-emerald-400 bg-emerald-50 text-center text-xs font-semibold text-emerald-700';
        });
    });
</script>
@endpush
