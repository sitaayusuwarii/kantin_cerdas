@extends('layouts.admin')
@section('title', 'Pesanan Belum Bayar')
@section('page-title', 'Pesanan Belum Bayar')
@section('page-subtitle', 'Monitor customer yang belum menyelesaikan pembayaran')

@section('content')

{{-- Filter Tanggal --}}
<form method="GET" class="glass-card rounded-2xl p-4 mb-6 flex items-center gap-3">
    <i class="fa-solid fa-calendar text-primary-400"></i>
    <input type="date" name="date" value="{{ $date }}"
           class="bg-transparent text-stone-600 text-sm outline-none border border-orange-100 rounded-lg px-3 py-2"
           onchange="this.form.submit()">
    <span class="text-stone-400 text-sm">{{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
</form>

{{-- Summary --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="glass-card rounded-2xl p-5">
        <p class="text-stone-500 text-xs mb-1">Belum Bayar</p>
        <p class="text-stone-800 font-bold text-3xl">{{ $summary['total'] }}</p>
        <p class="text-stone-400 text-xs mt-1">pesanan hari ini</p>
    </div>
    <div class="glass-card rounded-2xl p-5">
        <p class="text-stone-500 text-xs mb-1">Total Nilai</p>
        <p class="text-stone-800 font-bold text-2xl">Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}</p>
        <p class="text-stone-400 text-xs mt-1">potensi pemasukan</p>
    </div>
</div>

{{-- Tabel --}}
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-orange-100 flex items-center justify-between">
        <h2 class="text-stone-800 font-bold">Daftar Pesanan Belum Bayar</h2>
        <span class="text-xs text-amber-400 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full font-semibold">
            Kantin tutup 13:00
        </span>
    </div>

    @if($unpaidOrders->isEmpty())
        <div class="text-center py-16">
            <div class="text-5xl mb-3">✅</div>
            <p class="text-stone-800 font-semibold">Semua Sudah Bayar!</p>
            <p class="text-stone-400 text-sm mt-1">Tidak ada pesanan yang belum dibayar.</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-orange-100">
                    <th class="text-left px-5 py-3 text-xs text-stone-400 uppercase">No</th>
                    <th class="text-left px-5 py-3 text-xs text-stone-400 uppercase">Order</th>
                    <th class="text-left px-5 py-3 text-xs text-stone-400 uppercase">Customer</th>
                    <th class="text-left px-5 py-3 text-xs text-stone-400 uppercase">Menu</th>
                    <th class="text-right px-5 py-3 text-xs text-stone-400 uppercase">Total</th>
                    <th class="text-center px-5 py-3 text-xs text-stone-400 uppercase">Waktu Order</th>
                    <th class="text-center px-5 py-3 text-xs text-stone-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @foreach($unpaidOrders as $order)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-5 py-4 text-stone-400 text-sm font-semibold">
                    {{ $loop->iteration }}
                </td>
                   <td class="px-5 py-4">
                    <span class="font-mono text-primary-400 text-sm font-semibold">
                        #{{ $order->order_number }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg overflow-hidden flex-shrink-0">
                            @if($order->user->photo)
                                <img src="{{ asset('storage/' . $order->user->photo) }}"
                                    class="w-full h-full object-cover" alt="foto">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-violet-500 to-fuchsia-600
                                            flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($order->user->full_name ?? $order->user->username, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-stone-800 font-semibold text-sm">{{ $order->user->full_name }}</p>
                            <p class="text-stone-400 text-xs">{{ $order->user->username }}</p>
                            <p class="text-stone-400 text-xs">{{ $order->user->class ?? '-' }}</p>
                            @if(!$order->user->telegram_chat_id)
                                <span class="text-xs text-red-400">⚠ Telegram belum terhubung</span>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Order column - SESUDAH --}}
             
                    <td class="px-5 py-4">
                        @foreach($order->items as $item)
                            <p class="text-stone-600 text-xs">{{ $item->menu->name }} ×{{ $item->quantity }}</p>
                        @endforeach
                    </td>
                    <td class="px-5 py-4 text-right">
                        <p class="text-stone-800 font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <p class="text-stone-600 text-xs">{{ $order->created_at->format('H:i') }} </p>
                        <p class="text-stone-400 text-xs">{{ $order->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Kirim Reminder --}}
                            <form action="{{ route('admin.unpaid-orders.reminder', $order) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold hover:bg-amber-500/20 transition-all"
                                        @if(!$order->user->telegram_chat_id) disabled title="Telegram belum terhubung" @endif>
                                    <i class="fa-solid fa-bell mr-1"></i> Remind
                                </button>
                            </form>
                            {{-- Cancel --}}
                           <button type="button"
                                onclick="openCancelModal('{{ $order->order_number }}', '{{ route('admin.unpaid-orders.cancel', $order) }}')"
                                class="px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold hover:bg-red-500/20 transition-all">
                            <i class="fa-solid fa-xmark mr-1"></i> Batalkan
                        </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-emerald-500 text-white px-4 py-3 rounded-xl shadow-lg text-sm font-semibold">
    ✅ {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-4 py-3 rounded-xl shadow-lg text-sm font-semibold">
    ❌ {{ session('error') }}
</div>
@endif

{{-- Modal Cancel --}}
<div id="modal-cancel" class="fixed inset-0 bg-black/40 z-[99999] hidden items-center justify-center">
    <div class="bg-white rounded-2xl w-full max-w-sm mx-4 overflow-hidden shadow-xl">

        <div class="bg-red-50 px-6 pt-6 pb-5 text-center border-b border-red-100">
            <div class="w-14 h-14 bg-white rounded-2xl border border-red-100 flex items-center justify-center mx-auto mb-3"
                 style="width:56px;height:56px">
                <i class="fa-solid fa-xmark text-red-500 text-2xl"></i>
            </div>
            <p class="font-bold text-stone-800 text-base mb-1">Batalkan Pesanan?</p>
            <p class="text-stone-400 text-sm" id="modal-cancel-order-number"></p>
        </div>

        <form id="modal-cancel-form" method="POST">
            @csrf
            <div class="px-6 py-5">
                <label class="block text-xs font-semibold text-stone-600 mb-2">
                    Alasan Pembatalan <span class="text-red-400">*</span>
                </label>
                <textarea name="cancel_reason"
                          id="cancel-reason-input"
                          rows="3"
                          placeholder="Contoh: Pembayaran tidak diterima setelah batas waktu..."
                          class="w-full text-sm px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50
                                 text-stone-700 focus:outline-none focus:border-red-400
                                 focus:ring-2 focus:ring-red-100 transition-all resize-none"></textarea>
                <p class="text-[10px] text-stone-400 mt-1">Alasan akan dikirim ke customer via Telegram</p>
            </div>

            <div class="px-6 pb-5 flex gap-3">
                <button type="button"
                        onclick="closeCancelModal()"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                               text-gray-500 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white
                               text-sm font-semibold transition-colors">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCancelModal(orderNumber, actionUrl) {
    document.getElementById('modal-cancel-order-number').textContent = 'Pesanan #' + orderNumber;
    document.getElementById('modal-cancel-form').action = actionUrl;
    document.getElementById('cancel-reason-input').value = '';
    const m = document.getElementById('modal-cancel');
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function closeCancelModal() {
    const m = document.getElementById('modal-cancel');
    m.classList.add('hidden');
    m.classList.remove('flex');
}
document.getElementById('modal-cancel').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
@endpush