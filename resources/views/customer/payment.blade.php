@extends('layouts.app')
@section('title', 'Upload Bukti Bayar — SmartCanteen')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ url('/invoice') }}" class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left text-sm text-gray-600"></i>
        </a>
        <div>
            <h1 class="font-heading font-bold text-2xl text-canteen-dark">Upload Bukti Bayar</h1>
            <p class="text-gray-400 text-sm">Unggah bukti transfer/pembayaranmu</p>
        </div>
    </div>

    {{-- Info Banner --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-6 flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-blue-400 mt-0.5 flex-shrink-0"></i>
        <div>
            <p class="text-sm font-semibold text-blue-800 mb-1">Cara Pembayaran</p>
            <p class="text-xs text-blue-600 leading-relaxed">Transfer ke <strong>BRI 1234567890</strong> a/n SmartCanteen, lalu upload bukti di sini. Konfirmasi akan dikirim dalam 1x24 jam hari kerja.</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">

        <form action="#" method="POST" enctype="multipart/form-data" id="payment-form">
            @csrf

            {{-- Order ID --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    ID Pesanan <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="order_id" placeholder="Contoh: SC-001" value="SC-001"
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
                </div>
                <p class="text-xs text-gray-400 mt-1.5"><i class="fa-solid fa-circle-info text-gray-300 mr-1"></i>ID pesanan dapat ditemukan di halaman Tagihan</p>
            </div>

            {{-- Jumlah Transfer --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Jumlah Transfer <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-semibold">Rp</span>
                    <input type="text" name="amount" placeholder="25.000"
                        class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
                </div>
            </div>

            {{-- Metode Bayar --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach(['Transfer BRI', 'Transfer BCA', 'GoPay', 'OVO', 'DANA', 'Tunai'] as $i => $method)
                    <label class="cursor-pointer">
                        <input type="radio" name="method" value="{{ $method }}" class="sr-only" {{ $i === 0 ? 'checked' : '' }}>
                        <div class="payment-option p-3 rounded-xl border-2 {{ $i === 0 ? 'border-primary-400 bg-orange-50' : 'border-gray-200' }} text-center text-xs font-semibold text-gray-700 hover:border-primary-300 transition-all">
                            {{ $method }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Upload Area --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Bukti Transfer <span class="text-red-400">*</span>
                </label>

                {{-- Drop Zone --}}
                <div id="drop-zone" class="relative border-2 border-dashed border-primary-200 bg-orange-50/50 rounded-2xl p-8 text-center hover:border-primary-400 hover:bg-orange-50 transition-all cursor-pointer group">
                    <input type="file" name="proof" accept="image/*,application/pdf" id="file-input" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div id="upload-placeholder">
                        <div class="w-14 h-14 btn-primary rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-cloud-arrow-up text-white text-2xl"></i>
                        </div>
                        <p class="font-heading font-bold text-sm text-canteen-dark mb-1">Klik atau drag & drop file</p>
                        <p class="text-xs text-gray-400">JPG, PNG, PDF · Maks. 5MB</p>
                    </div>
                    <div id="preview-container" class="hidden">
                        <img id="preview-img" src="" alt="Preview" class="max-h-48 mx-auto rounded-xl shadow-md mb-3 object-contain">
                        <p id="file-name" class="text-xs font-semibold text-gray-600 mb-2"></p>
                        <button type="button" id="remove-file" class="text-xs text-red-400 hover:text-red-600 font-semibold transition-colors">
                            <i class="fa-solid fa-trash-can mr-1"></i>Hapus & Pilih Lain
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2"><i class="fa-solid fa-shield-halved text-green-400 mr-1"></i>File dienkripsi dan aman</p>
            </div>

            {{-- Catatan --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="note" rows="2" placeholder="Tambahkan catatan jika diperlukan..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 resize-none transition-all"></textarea>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-primary w-full text-white font-heading font-bold py-4 rounded-xl text-sm shadow-lg flex items-center justify-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>Kirim Bukti Pembayaran
            </button>

            <p class="text-center text-xs text-gray-400 mt-4">
                Butuh bantuan? <a href="https://telegram.me/6281234567890" class="text-green-500 font-semibold hover:text-green-600 transition-colors"><i class="fa-brands fa-telegram mr-0.5"></i>Chat Telegram Kantin</a>
            </p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const fileInput = document.getElementById('file-input');
    const placeholder = document.getElementById('upload-placeholder');
    const previewContainer = document.getElementById('preview-container');
    const previewImg = document.getElementById('preview-img');
    const fileName = document.getElementById('file-name');
    const removeBtn = document.getElementById('remove-file');
    const radios = document.querySelectorAll('input[name="method"]');

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        fileName.textContent = file.name;
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (ev) => { previewImg.src = ev.target.result; previewImg.classList.remove('hidden'); };
            reader.readAsDataURL(file);
        } else {
            previewImg.classList.add('hidden');
        }
        placeholder.classList.add('hidden');
        previewContainer.classList.remove('hidden');
    });

    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        previewImg.src = '';
        placeholder.classList.remove('hidden');
        previewContainer.classList.add('hidden');
    });

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.payment-option').forEach(el => {
                el.classList.remove('border-primary-400', 'bg-orange-50');
                el.classList.add('border-gray-200');
            });
            radio.nextElementSibling.classList.add('border-primary-400', 'bg-orange-50');
            radio.nextElementSibling.classList.remove('border-gray-200');
        });
    });
</script>
@endpush
