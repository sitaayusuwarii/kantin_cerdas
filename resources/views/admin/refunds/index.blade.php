@extends('layouts.admin')

@section('title', 'Kelola Refund')
@section('page-title', 'Kelola Refund')
@section('page-subtitle', 'Daftar pengembalian dana yang perlu diproses')

@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
    <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
</div>
@endif

<div class="glass-card rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-border flex items-center justify-between">
        <div>
            <h2 class="font-bold text-stone-800">Daftar Refund Pending</h2>
            <p class="text-xs text-stone-400 mt-0.5">{{ $refunds->count() }} refund menunggu diproses</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#FFF3E8] text-stone-500 text-xs uppercase tracking-wide">
                    <th class="px-5 py-3 text-left">No Order</th>
                    <th class="px-5 py-3 text-left">Customer</th>
                    <th class="px-5 py-3 text-left">Total Pesanan</th>
                    <th class="px-5 py-3 text-left">Info Rekening</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Tanggal Dibatalkan</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($refunds as $refund)
                <tr class="table-row transition-colors">
                    <td class="px-5 py-4 font-semibold text-stone-800">
                        #{{ $refund->order->order_number }}
                    </td>
                    <td class="px-5 py-4 text-stone-600">
                        {{ $refund->user->full_name ?? $refund->user->name ?? '-' }}
                    </td>
                    <td class="px-5 py-4 text-stone-600">
                        Rp {{ number_format($refund->order->total_price, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4">
                        @if($refund->status === 'menunggu_info')
                            <span class="text-stone-400 italic text-xs">Belum diisi customer</span>
                        @else
                            <div class="text-xs text-stone-600 leading-relaxed">
                                <span class="font-semibold text-stone-800">{{ $refund->bank_name }}</span><br>
                                {{ $refund->account_number }}<br>
                                a/n {{ $refund->account_holder_name }}
                            </div>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($refund->status === 'menunggu_info')
                            <span class="badge px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">
                                Menunggu Info
                            </span>
                        @elseif($refund->status === 'menunggu_transfer')
                            <span class="badge px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">
                                Siap Transfer
                            </span>
                        @else
                            <span class="badge px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                                Selesai
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-stone-500 text-xs">
                        {{ $refund->created_at->translatedFormat('d M Y, H:i') }}
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($refund->status === 'menunggu_transfer')
                    <form method="POST" action="{{ route('admin.refunds.complete', $refund) }}" id="refund-form-{{ $refund->id }}">
                        @csrf
                        <button type="button"
                                onclick="openRefundModal('{{ $refund->id }}', '{{ $refund->order->order_number }}', '{{ $refund->bank_name }}', '{{ $refund->account_number }}', '{{ $refund->account_holder_name }}')"
                                class="px-3 py-1.5 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium transition-colors">
                            <i class="fa-solid fa-check mr-1"></i> Tandai Selesai
                        </button>
                    </form>
                    @else
                        <span class="text-stone-300 text-xs">—</span>
                    @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-stone-400">
                        <i class="fa-solid fa-circle-check text-2xl mb-2 block"></i>
                        Tidak ada refund yang perlu diproses saat ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{-- Modal Konfirmasi Refund --}}
<div id="refund-modal" class="fixed inset-0 bg-black/40 z-[99999] hidden items-center justify-center">
  <div class="bg-white rounded-[20px] border border-[#F4E6D2] w-full max-w-sm mx-4 overflow-hidden shadow-xl">

    <div class="bg-[#FFF3E8] px-7 pt-7 pb-5 text-center border-b border-[#F4E6D2]">
      <div class="bg-white rounded-2xl border border-[#F4E6D2] flex items-center justify-center mx-auto mb-3.5" style="width:60px;height:60px">
        <i class="fa-solid fa-money-bill-transfer text-orange-600 text-2xl"></i>
      </div>
      <p class="font-semibold text-stone-800 text-base mb-1">Sudah ditransfer?</p>
      <p class="text-sm text-gray-400 leading-relaxed">
        Pastikan dana untuk pesanan <span id="refund-modal-order" class="font-semibold text-stone-700"></span>
        sudah benar-benar ditransfer ke rekening customer di bawah ini.
      </p>
    </div>

    <div class="px-7 py-5">
      <div class="bg-[#FFF3E8] rounded-xl p-3.5 text-sm text-stone-600 leading-relaxed mb-5">
        <span id="refund-modal-bank" class="font-semibold text-stone-800"></span><br>
        <span id="refund-modal-number"></span><br>
        a/n <span id="refund-modal-holder"></span>
      </div>

      <div class="flex flex-col gap-2.5">
        <button onclick="confirmRefund()"
                class="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium transition-colors">
          Ya, sudah ditransfer
        </button>
        <button onclick="closeRefundModal()"
                class="w-full py-2.5 rounded-xl bg-[#FFF3E8] hover:bg-orange-100 text-orange-600 text-sm font-medium border border-[#F4E6D2] transition-colors">
          Batal
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let activeRefundId = null;

function openRefundModal(refundId, orderNumber, bank, number, holder) {
    activeRefundId = refundId;
    document.getElementById('refund-modal-order').textContent = '#' + orderNumber;
    document.getElementById('refund-modal-bank').textContent = bank;
    document.getElementById('refund-modal-number').textContent = number;
    document.getElementById('refund-modal-holder').textContent = holder;

    const m = document.getElementById('refund-modal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function closeRefundModal() {
    const m = document.getElementById('refund-modal');
    m.classList.add('hidden');
    m.classList.remove('flex');
    activeRefundId = null;
}

function confirmRefund() {
    if (activeRefundId) {
        document.getElementById('refund-form-' + activeRefundId).submit();
    }
}

document.getElementById('refund-modal').addEventListener('click', function(e) {
    if (e.target === this) closeRefundModal();
});
</script>
@endsection