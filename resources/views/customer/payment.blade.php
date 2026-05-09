@extends('layouts.app')
@section('title', 'Upload Bukti Bayar — SmartCanteen')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ url()->previous() }}"
           class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left text-sm text-gray-600"></i>
        </a>
        <div>
            <h1 class="font-heading font-bold text-2xl text-canteen-dark">Upload Bukti Bayar</h1>
            <p class="text-gray-400 text-sm">Unggah bukti transfer/pembayaranmu</p>
        </div>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6 flex items-start gap-3">
        <i class="fa-solid fa-circle-check text-green-500 mt-0.5 flex-shrink-0"></i>
        <p class="text-sm text-green-800 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
        <p class="text-sm font-semibold text-red-700 mb-2"><i class="fa-solid fa-circle-exclamation mr-1"></i>Terdapat kesalahan:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li class="text-xs text-red-600">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

   {{-- Info Banner --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-6">
        <p class="text-sm font-semibold text-blue-800 mb-2">
            <i class="fa-solid fa-circle-info text-blue-400 mr-1"></i>Metode Pembayaran Tersedia
        </p>
        @foreach($paymentMethods as $m)
            @if($m->isBankTransfer())
            <p class="text-xs text-blue-600 leading-relaxed">
                🏦 <strong>{{ $m->name }}</strong>: <span class="font-mono">{{ $m->account_number }}</span> a/n {{ $m->account_name }}
            </p>
            @endif
        @endforeach
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">

        {{-- 
            Route: POST /payment/upload → PaymentController@upload
            Pastikan di routes/web.php:
                Route::get('/payment',         [PaymentController::class, 'index'])->name('customer.payment');
                Route::post('/payment/upload', [PaymentController::class, 'upload'])->name('payment.upload');
        --}}
       <form action="{{ route('customer.payment.upload') }}" method="POST"
              enctype="multipart/form-data" id="payment-form">
            @csrf

            {{-- Order Number (pre-filled dari controller jika ada ?order=) --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nomor Pesanan <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="order_number"
                           placeholder="Contoh: SC-001"
                           value="{{ old('order_number', $order?->order_number) }}"
                           class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border
                                  {{ $errors->has('order_number') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}
                                  rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
                </div>
                <p class="text-xs text-gray-400 mt-1.5">
                    <i class="fa-solid fa-circle-info text-gray-300 mr-1"></i>
                    ID pesanan dapat ditemukan di halaman Riwayat
                </p>
                @error('order_number')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jumlah Transfer --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Jumlah Transfer <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-semibold">Rp</span>
                    <input type="text" name="amount" id="amount-display"
                           placeholder="25.000"
                           value="{{ old('amount') ? number_format(old('amount'), 0, ',', '.') : ($order ? number_format($order->total_price, 0, ',', '.') : '') }}"
                           class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border
                                  {{ $errors->has('amount') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}
                                  rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
                    {{-- Hidden input angka murni untuk validasi --}}
                    <input type="hidden" name="amount_raw" id="amount-raw"
                           value="{{ old('amount', $order?->total_price) }}">
                </div>
                @error('amount')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

           {{-- Metode Bayar --}}
            @foreach($paymentMethods as $method)
            <label class="cursor-pointer">
                <input type="radio" name="method" value="{{ $method->code }}"
                    class="sr-only payment-method-radio"
                    {{ old('method') === $method->code ? 'checked' : '' }}>
                <div class="payment-option p-3 rounded-xl border-2 text-center text-xs font-semibold text-gray-700
                            border-gray-200 hover:border-primary-300 transition-all">
                    <i class="{{ $method->logo_icon }} mb-1 block text-base"></i>
                    {{ $method->name }}
                    @if($method->account_number)
                    <p class="text-[10px] font-mono text-gray-400 mt-0.5">{{ $method->account_number }}</p>
                    @endif
                </div>
            </label>
            @endforeach

            {{-- Info rekening muncul saat dipilih --}}
            <div id="payment-info" class="hidden mt-4 p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm">
                <p id="info-text" class="text-blue-800"></p>
            </div>

            {{-- Upload Area --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Bukti Transfer <span class="text-red-400">*</span>
                </label>

                <div id="drop-zone"
                     class="relative border-2 border-dashed
                            {{ $errors->has('proof') ? 'border-red-300 bg-red-50' : 'border-primary-200 bg-orange-50/50' }}
                            rounded-2xl p-8 text-center hover:border-primary-400 hover:bg-orange-50 transition-all cursor-pointer group">
                    <input type="file" name="proof" accept="image/*" id="file-input"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div id="upload-placeholder">
                        <div class="w-14 h-14 btn-primary rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-cloud-arrow-up text-white text-2xl"></i>
                        </div>
                        <p class="font-heading font-bold text-sm text-canteen-dark mb-1">Klik atau drag & drop file</p>
                        <p class="text-xs text-gray-400">JPG, PNG · Maks. 5MB</p>
                    </div>
                    <div id="preview-container" class="hidden">
                        <img id="preview-img" src="" alt="Preview"
                             class="max-h-48 mx-auto rounded-xl shadow-md mb-3 object-contain">
                        <p id="file-name" class="text-xs font-semibold text-gray-600 mb-2"></p>
                        <button type="button" id="remove-file"
                                class="text-xs text-red-400 hover:text-red-600 font-semibold transition-colors">
                            <i class="fa-solid fa-trash-can mr-1"></i>Hapus & Pilih Lain
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">
                    <i class="fa-solid fa-shield-halved text-green-400 mr-1"></i>File dienkripsi dan aman
                </p>
                @error('proof')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="note" rows="2"
                          placeholder="Tambahkan catatan jika diperlukan..."
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 resize-none transition-all">{{ old('note') }}</textarea>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="btn-primary w-full text-white font-heading font-bold py-4 rounded-xl text-sm shadow-lg flex items-center justify-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>Kirim Bukti Pembayaran
            </button>

            <p class="text-center text-xs text-gray-400 mt-4">
                Butuh bantuan?
                <a href="https://telegram.me/{{ config('app.telegram_username', '6281234567890') }}"
                   class="text-green-500 font-semibold hover:text-green-600 transition-colors">
                    <i class="fa-brands fa-telegram mr-0.5"></i>Chat Telegram Kantin
                </a>
            </p>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); }
    .btn-primary:hover { filter: brightness(1.05); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(234,88,12,0.3); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ======= File upload preview =======
    const fileInput        = document.getElementById('file-input');
    const placeholder      = document.getElementById('upload-placeholder');
    const previewContainer = document.getElementById('preview-container');
    const previewImg       = document.getElementById('preview-img');
    const fileNameEl       = document.getElementById('file-name');
    const removeBtn        = document.getElementById('remove-file');
    const methodData = @json($paymentMethods->keyBy('code'));


    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        fileNameEl.textContent = file.name;
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (ev) => {
                previewImg.src = ev.target.result;
                previewImg.classList.remove('hidden');
            };
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
        previewImg.src  = '';
        placeholder.classList.remove('hidden');
        previewContainer.classList.add('hidden');
    });

    // ======= Payment method radio styling =======

    document.querySelectorAll('.payment-method-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            const method = methodData[radio.value];
            const infoBox = document.getElementById('payment-info');
            const infoText = document.getElementById('info-text');

            if (method && (method.account_number || method.instructions)) {
                let text = method.instructions ?? '';
                if (method.account_number) {
                    text = `${method.name}: <strong class="font-mono">${method.account_number}</strong> a/n ${method.account_name}<br><span class="text-xs text-blue-600">${method.instructions ?? ''}</span>`;
                }
                infoText.innerHTML = text;
                infoBox.classList.remove('hidden');
            } else {
                infoBox.classList.add('hidden');
            }
        });
    });

    // ======= Amount formatting (display vs raw) =======
    const amountDisplay = document.getElementById('amount-display');
    const amountRaw     = document.getElementById('amount-raw');

    amountDisplay.addEventListener('input', function () {
        // Hapus semua non-digit
        const digits = this.value.replace(/\D/g, '');
        amountRaw.value = digits;
        // Format dengan titik ribuan
        this.value = digits ? parseInt(digits).toLocaleString('id-ID') : '';
    });

    // Sebelum submit, pastikan name="amount" berisi angka murni
    document.getElementById('payment-form').addEventListener('submit', function () {
        const digits = amountDisplay.value.replace(/\D/g, '');
        amountDisplay.name = ''; // nonaktifkan display field
        amountRaw.name = 'amount'; // aktifkan raw field
        amountRaw.value = digits;
    });
});
</script>
@endpush