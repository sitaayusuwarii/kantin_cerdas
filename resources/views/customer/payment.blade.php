@extends('layouts.app')
@section('title', 'Upload Bukti Bayar - SmartCanteen')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 px-5 py-7 shadow-xl shadow-orange-200/50 sm:px-8 lg:px-10">
        <div class="absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.22),transparent_40%)]"></div>
        <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-6 right-10 hidden text-white/10 lg:block">
            <i class="fa-solid fa-cloud-arrow-up text-[8rem]"></i>
        </div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ url()->previous() }}"
                   class="mb-5 inline-flex items-center gap-2 rounded-2xl border border-white/20 bg-white/15 px-4 py-2 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Kembali
                </a>
                <h1 class="font-heading text-3xl font-extrabold leading-tight text-white md:text-4xl">
                    Upload Bukti Bayar
                </h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-orange-50">
                    Masukkan nomor pesanan, pilih metode pembayaran, lalu unggah bukti transfer.
                </p>
            </div>

            @if($order)
                <div class="rounded-3xl border border-white/20 bg-white/15 p-4 text-white backdrop-blur sm:min-w-[280px]">
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-100">Tagihan</p>
                    <p class="mt-1 font-heading text-2xl font-extrabold">#{{ $order->order_number }}</p>
                    <p class="mt-1 text-lg font-extrabold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
            @endif
        </div>
    </section>

    @if(session('success'))
        <div class="mt-6 rounded-3xl border border-emerald-100 bg-emerald-50 p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <p class="text-sm font-bold text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mt-6 rounded-3xl border border-rose-100 bg-rose-50 p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-white text-rose-500">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <div>
                    <p class="text-sm font-extrabold text-rose-700">Terdapat kesalahan:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-xs text-rose-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('customer.payment.upload') }}" method="POST" enctype="multipart/form-data" id="payment-form">
        @csrf

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start">
            <section class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Data Pembayaran</p>
                    <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950">Informasi Transfer</h2>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wide text-gray-600">
                            Nomor Pesanan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text"
                                   name="order_number"
                                   placeholder="Contoh: SC-001"
                                   value="{{ old('order_number', $order?->order_number) }}"
                                   {{ $order ? 'readonly' : '' }}
                                   class="w-full rounded-2xl border py-3.5 pl-11 pr-4 text-sm font-semibold text-gray-700 focus:outline-none focus:ring-4 focus:ring-orange-100
                                          {{ $order ? 'bg-gray-100 cursor-not-allowed' : 'bg-gray-50 focus:bg-white' }}
                                          {{ $errors->has('order_number') ? 'border-rose-300 bg-rose-50' : 'border-gray-200 focus:border-orange-300' }}">
                        </div>
                        <p class="mt-1.5 text-xs text-gray-400">Nomor pesanan bisa dilihat di Riwayat Pesanan.</p>
                        @error('order_number')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wide text-gray-600">
                            Jumlah Transfer <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-extrabold text-gray-500">Rp</span>
                            <input type="text"
                                   name="amount"
                                   id="amount-display"
                                   placeholder="25.000"
                                   value="{{ old('amount') ? number_format(old('amount'), 0, ',', '.') : ($order ? number_format($order->total_price, 0, ',', '.') : '') }}"
                                   {{ $order ? 'readonly' : '' }}
                                   class="w-full rounded-2xl border py-3.5 pl-12 pr-4 text-sm font-semibold text-gray-700 focus:outline-none focus:ring-4 focus:ring-orange-100
                                          {{ $order ? 'bg-gray-100 cursor-not-allowed' : 'bg-gray-50 focus:bg-white' }}
                                          {{ $errors->has('amount') ? 'border-rose-300 bg-rose-50' : 'border-gray-200 focus:border-orange-300' }}">
                            <input type="hidden" name="amount_raw" id="amount-raw" value="{{ old('amount', $order?->total_price) }}">
                        </div>
                        @error('amount')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="mb-3 block text-xs font-extrabold uppercase tracking-wide text-gray-600">
                        Metode Bayar <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($paymentMethods as $method)
                            <label class="cursor-pointer">
                                <input type="radio"
                                       name="method"
                                       value="{{ $method->code }}"
                                       class="sr-only payment-method-radio"
                                       {{ old('method') === $method->code ? 'checked' : '' }}>
                                <div class="payment-option h-full rounded-3xl border-2 border-gray-100 bg-gray-50 p-4 transition hover:border-orange-200 hover:bg-orange-50">
                                    <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-orange-500 shadow-sm">
                                        <i class="{{ $method->logo_icon }}"></i>
                                    </div>
                                    <p class="font-heading text-sm font-extrabold text-gray-950">{{ $method->name }}</p>
                                    @if($method->account_number)
                                        <p class="mt-1 font-mono text-xs font-bold text-gray-500">{{ $method->account_number }}</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div id="payment-info" class="hidden mt-4 rounded-3xl border border-blue-100 bg-blue-50 p-4 text-sm">
                        <div id="info-text" class="text-blue-800"></div>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="mb-2 block text-xs font-extrabold uppercase tracking-wide text-gray-600">
                        Bukti Transfer <span class="text-rose-500">*</span>
                    </label>
                    <div id="drop-zone"
                         class="group relative rounded-[2rem] border-2 border-dashed p-8 text-center transition
                                {{ $errors->has('proof') ? 'border-rose-300 bg-rose-50' : 'border-orange-200 bg-orange-50/60 hover:border-orange-300 hover:bg-orange-50' }}">
                        <input type="file"
                               name="proof"
                               accept="image/*"
                               id="file-input"
                               class="absolute inset-0 h-full w-full cursor-pointer opacity-0">

                        <div id="upload-placeholder">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-100 transition group-hover:scale-105">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                            </div>
                            <p class="font-heading text-base font-extrabold text-gray-950">Klik atau drag file ke sini</p>
                            <p class="mt-1 text-sm text-gray-500">Format JPG/PNG, maksimal 5MB.</p>
                        </div>

                        <div id="preview-container" class="hidden">
                            <img id="preview-img" src="" alt="Preview" class="mx-auto mb-3 max-h-56 rounded-3xl border border-white object-contain shadow-md">
                            <p id="file-name" class="mb-3 text-sm font-bold text-gray-700"></p>
                            <button type="button" id="remove-file" class="rounded-2xl bg-white px-4 py-2 text-xs font-extrabold text-rose-500 shadow-sm transition hover:bg-rose-50">
                                <i class="fa-solid fa-trash-can mr-1"></i>Hapus dan pilih lain
                            </button>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-400">Pastikan nominal, rekening tujuan, dan tanggal pembayaran terlihat jelas.</p>
                    @error('proof')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <label class="mb-2 block text-xs font-extrabold uppercase tracking-wide text-gray-600">Catatan Opsional</label>
                    <textarea name="note"
                              rows="3"
                              placeholder="Tambahkan catatan jika diperlukan..."
                              class="w-full resize-none rounded-3xl border border-gray-200 bg-gray-50 px-4 py-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-orange-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-orange-100">{{ old('note') }}</textarea>
                </div>

                <button type="submit"
                        class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 py-4 text-sm font-heading font-extrabold text-white shadow-lg shadow-orange-100 transition hover:-translate-y-0.5 hover:shadow-orange-200 active:scale-95">
                    <i class="fa-solid fa-paper-plane"></i>
                    Kirim Bukti Pembayaran
                </button>
            </section>

            <aside class="lg:sticky lg:top-24">
                <div class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <p class="font-heading text-lg font-extrabold text-gray-950">Panduan Upload</p>
                            <p class="text-xs text-gray-500">Ikuti agar pembayaran cepat diverifikasi.</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        <div class="rounded-3xl bg-orange-50 p-4">
                            <p class="text-xs font-extrabold uppercase tracking-wide text-orange-600">Langkah 1</p>
                            <p class="mt-1 text-sm font-semibold text-gray-700">Transfer sesuai nominal tagihan.</p>
                        </div>
                        <div class="rounded-3xl bg-orange-50 p-4">
                            <p class="text-xs font-extrabold uppercase tracking-wide text-orange-600">Langkah 2</p>
                            <p class="mt-1 text-sm font-semibold text-gray-700">Pilih metode pembayaran yang kamu gunakan.</p>
                        </div>
                        <div class="rounded-3xl bg-orange-50 p-4">
                            <p class="text-xs font-extrabold uppercase tracking-wide text-orange-600">Langkah 3</p>
                            <p class="mt-1 text-sm font-semibold text-gray-700">Upload bukti transfer yang jelas.</p>
                        </div>
                    </div>

                    @if($order)
                        <div class="mt-5 rounded-3xl border border-gray-100 bg-gray-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Pesanan Dipilih</p>
                            <p class="mt-2 font-heading text-lg font-extrabold text-gray-950">#{{ $order->order_number }}</p>
                            <p class="mt-1 text-sm font-bold text-orange-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .payment-option.is-active {
        border-color: #f97316 !important;
        background-color: #fff7ed !important;
        box-shadow: 0 12px 30px rgba(249, 115, 22, 0.12);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('file-input');
    const placeholder = document.getElementById('upload-placeholder');
    const previewContainer = document.getElementById('preview-container');
    const previewImg = document.getElementById('preview-img');
    const fileNameEl = document.getElementById('file-name');
    const removeBtn = document.getElementById('remove-file');
    const methodData = @json($paymentMethods->keyBy('code'));

    fileInput.addEventListener('change', event => {
        const file = event.target.files[0];
        if (!file) return;

        fileNameEl.textContent = file.name;
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.classList.add('hidden');
        }

        placeholder.classList.add('hidden');
        previewContainer.classList.remove('hidden');
    });

    removeBtn.addEventListener('click', event => {
        event.preventDefault();
        event.stopPropagation();
        fileInput.value = '';
        previewImg.src = '';
        placeholder.classList.remove('hidden');
        previewContainer.classList.add('hidden');
    });

    function updatePaymentInfo(radio) {
        document.querySelectorAll('.payment-option').forEach(option => option.classList.remove('is-active'));
        radio.nextElementSibling.classList.add('is-active');

        const method = methodData[radio.value];
        const infoBox = document.getElementById('payment-info');
        const infoText = document.getElementById('info-text');

        if (method && method.type === 'qris' && method.qris_image) {
            infoText.innerHTML = `
                <p class="mb-3 font-bold">Scan QRIS berikut untuk membayar:</p>
                <a href="/storage/${method.qris_image}" target="_blank" class="block">
                    <img src="/storage/${method.qris_image}"
                         class="mx-auto h-52 w-52 rounded-3xl border border-blue-100 bg-white object-contain p-2 shadow-sm"
                         title="Klik untuk perbesar">
                </a>
                <div class="mt-3 text-center">
                    <a href="/storage/${method.qris_image}" download="QRIS.jpg"
                       class="inline-flex items-center gap-2 rounded-2xl bg-blue-100 px-4 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-200">
                        <i class="fa-solid fa-download"></i> Unduh QRIS
                    </a>
                </div>
                <p class="mt-3 text-center text-xs text-blue-600">${method.instructions ?? ''}</p>
            `;
            infoBox.classList.remove('hidden');
            return;
        }

        if (method && (method.account_number || method.instructions)) {
            let text = method.instructions ?? '';
            if (method.account_number) {
                text = `${method.name}: <strong class="font-mono">${method.account_number}</strong> a/n ${method.account_name ?? '-'}<br><span class="text-xs text-blue-600">${method.instructions ?? ''}</span>`;
            }
            infoText.innerHTML = text;
            infoBox.classList.remove('hidden');
            return;
        }

        infoBox.classList.add('hidden');
    }

    document.querySelectorAll('.payment-method-radio').forEach(radio => {
        radio.addEventListener('change', () => updatePaymentInfo(radio));
        if (radio.checked) updatePaymentInfo(radio);
    });

    const amountDisplay = document.getElementById('amount-display');
    const amountRaw = document.getElementById('amount-raw');

    if (amountDisplay && !amountDisplay.readOnly) {
        amountDisplay.addEventListener('input', function () {
            const digits = this.value.replace(/\D/g, '');
            amountRaw.value = digits;
            this.value = digits ? parseInt(digits).toLocaleString('id-ID') : '';
        });
    }

    document.getElementById('payment-form').addEventListener('submit', function () {
        const digits = amountDisplay.value.replace(/\D/g, '');
        amountDisplay.name = '';
        amountRaw.name = 'amount';
        amountRaw.value = digits;
    });
});
</script>
@endpush
