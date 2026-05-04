@extends('layouts.pengelola')
@section('title', 'Pesanan Masuk')
@section('page-title', 'Pesanan Masuk')
@section('page-subtitle', $newOrders . ' pesanan baru menunggu konfirmasi')
@section('content')

{{-- Alert --}}
<div class="flex items-center gap-3 bg-forest-50 border border-forest-200 rounded-2xl p-4 mb-6">
    <div class="w-9 h-9 bg-forest-700 rounded-xl flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-bell text-white text-sm"></i>
    </div>
    <div class="flex-1">
    <p class="font-semibold text-forest-900 text-sm">
        {{ $newOrders }} pesanan baru masuk!
    </p>        
    <p class="text-forest-600 text-xs">Konfirmasi segera agar dapur mulai memproses.</p>
    </div>
        <span class="badge-new bg-forest-600 text-white text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0">
            {{ $newOrders }} Baru
        </span>
    </div>

{{-- Filter Tabs --}}
<div class="flex flex-wrap gap-2 mb-5">
    <button data-status="semua"
            class="filter-tab active-tab bg-forest-800 text-white px-4 py-2 rounded-xl text-sm font-semibold">
        Semua ({{ $totalOrders }})
    </button>
    <button data-status="baru"
            class="filter-tab bg-cream-50 border border-cream-300 px-4 py-2 rounded-xl text-sm font-semibold">
        Baru ({{ $newOrders }})
    </button>
    <button data-status="dikonfirmasi"
            class="filter-tab bg-cream-50 border border-cream-300 px-4 py-2 rounded-xl text-sm font-semibold">
        Dikonfirmasi ({{ $confirmedOrders }})
    </button>
    <button data-status="diproses"
            class="filter-tab bg-cream-50 border border-cream-300 px-4 py-2 rounded-xl text-sm font-semibold">
        Diproses ({{ $processedOrders }})
    </button>
    <button data-status="selesai"
            class="filter-tab bg-cream-50 border border-cream-300 px-4 py-2 rounded-xl text-sm font-semibold">
        Selesai ({{ $completedOrders }})
    </button>
</div>



{{-- ── DESKTOP TABLE ────────────────────────────────────── --}}
<div class="hidden md:block bg-cream-50 rounded-2xl shadow-sm border border-cream-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-cream-100/80 border-b border-cream-200">
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Pesanan</th>
                <th class="text-left px-4 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Siswa</th>
                <th class="text-left px-4 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Menu</th>
                <th class="text-right px-4 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Total</th>
                <th class="text-center px-3 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Pickup</th>
                <th class="text-center px-3 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Status</th>
                <th class="text-center px-6 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-cream-200">
           @foreach($orders as $order)
            <tr class="order-row hover:bg-cream-100/50 transition-colors {{ $order->status === 'baru' ? 'bg-forest-50/40' : '' }}"
                data-status="{{ $order->status }}">            
                <td class="px-6 py-4">
                    <p class="font-display font-semibold text-sm text-forest-900">#{{ $order->order_number }}</p>
                    <p class="text-[10px] text-forest-400 flex items-center gap-1 mt-0.5">
                        <i class="fa-solid fa-clock text-forest-300"></i>{{ $order->created_at->format('H:i') }}
                    </p>
                </td>
                <td class="px-4 py-4">
                    <p class="font-semibold text-xs text-forest-900">{{ $order->user->name }}</p>
                    <p class="text-[10px] text-forest-400">{{ $order->user->class }}</p>
                </td>
                <td class="px-4 py-4">
                    <div class="space-y-0.5">
                        @foreach($order->items as $item)
                        <p class="text-xs text-forest-600">
                            🍽️ {{ $item->menu->name }} ×{{ $item->quantity }}
                        </p>  
                        
                        {{-- Pesan tidak ada hasil --}}
                        <div id="empty-order-msg" style="display:none" class="text-center py-16">
                            <div class="text-5xl mb-3">📋</div>
                            <p class="text-forest-600 font-semibold text-sm">Tidak ada pesanan</p>
                        </div>

                         @endforeach
                    </div>

                </td>
                <td class="px-4 py-4 text-right font-display font-bold text-sm text-forest-900">
                    Rp {{ number_format($order->total_price,0,',','.') }}
                </td>
                <td class="px-3 py-4 text-center">
                    <span class="text-[10px] bg-cream-200 text-forest-600 font-medium px-2 py-1 rounded-lg">
                        {{ $order->pickup_label }}
                    </span>
                </td>
                <td class="px-3 py-4 text-center">
                    <span class="inline-flex items-center gap-1 {{ $order->status_color }}
                                 text-[10px] font-bold px-2.5 py-1.5 rounded-full">
                        @if($order->status === 'baru')
                        <i class="fa-solid fa-circle badge-new text-[7px]"></i>
                        @endif
                        {{ $order->status_label }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                @if($order->status === 'baru')
                    <form action="{{ route('pengelola.orders.confirm', $order->id) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="btn-primary text-white text-xs font-bold px-4 py-2 rounded-xl shadow">
                            <i class="fa-solid fa-check text-[10px]"></i>
                            Terima
                        </button>
                    </form>
                @elseif($order->status === 'dikonfirmasi')
                    <form action="{{ route('pengelola.orders.process', $order->id) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="bg-amber-500 text-white text-xs font-bold px-4 py-2 rounded-xl shadow">
                            Proses
                        </button>
                    </form>
                @elseif($order->status === 'diproses')
                    <form action="{{ route('pengelola.orders.complete', $order->id) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="bg-emerald-600 text-white text-xs font-bold px-4 py-2 rounded-xl shadow">
                            Selesai
                        </button>
                    </form>
                @else
                    <span class="text-emerald-600 font-semibold text-xs">
                        ✓ Selesai
                    </span>
                @endif
            </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ── MOBILE CARDS ─────────────────────────────────────── --}}
<div class="md:hidden space-y-4">
    @foreach($orders as $order)
        @php
        $borderColor =
            $order->status === 'baru'
                ? 'border-l-forest-500'
                : ($order->status === 'dikonfirmasi'
                    ? 'border-l-amber-500'
                    : 'border-l-emerald-500');
        @endphp
            <div class="order-row bg-cream-50 rounded-2xl shadow-sm border border-cream-200 overflow-hidden border-l-4 {{ $borderColor }}"
                data-status="{{ $order->status }}">            
                <div class="flex items-center justify-between px-4 py-3 bg-cream-100/60 border-b border-cream-200">
                <div class="flex items-center gap-2">
                    <p class="font-display font-bold text-sm text-forest-900">#{{ $order->order_number }}</p>
                    <span class="text-[10px] text-forest-400">· {{ $order->created_at->format('H:i') }}</span>
                </div>

                <div id="empty-order-msg" style="display:none" class="text-center py-16">
                <div class="text-5xl mb-3">📋</div>
                <p class="text-forest-600 font-semibold text-sm">Tidak ada pesanan</p>
            </div>

                <span class="inline-flex items-center gap-1 {{ $order->status_color }}
                            text-[10px] font-bold px-2.5 py-1 rounded-full">
                    @if($order->status === 'baru')<i class="fa-solid fa-circle badge-new text-[7px]"></i>@endif
                    {{ $order->status_label }}
                </span>
            </div>
            <div class="px-4 py-3.5">
                <div class="flex items-start justify-between gap-3 mb-2.5">
                    <div>
                        <p class="font-semibold text-sm text-forest-900">{{ $order->user->name }}</p>
                        <p class="text-xs text-forest-400">{{ $order->user->class }} · {{ $order->pickup_label }}</p>
                    </div>
                    <p class="font-display font-bold text-base text-forest-800 flex-shrink-0">
                        Rp {{ number_format($order->total_price,0,',','.') }}
                    </p>
                </div>
                <div class="bg-cream-100 rounded-xl p-3 mb-3 space-y-1">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-forest-700">{{ $item->menu->name }} ×{{ $item->quantity }}</span>
                        <span class="font-medium text-forest-600">Rp {{ number_format($item->subtotal,0,',','.') }}</span>
                    </div>
                    @endforeach
                </div>
                @if($order->status === 'baru')
                <form action="{{ route('pengelola.orders.confirm', $order->id) }}"
        method="POST">
        @csrf
        @method('PATCH')

    <button type="submit"
            class="btn-primary w-full text-white text-sm font-bold py-2.5 rounded-xl shadow flex items-center justify-center gap-2">
        <i class="fa-solid fa-check"></i>
        Terima Pesanan
    </button>
</form>
            @elseif($order->status === 'dikonfirmasi')
            <form action="{{ route('pengelola.orders.process', $order->id) }}"
                method="POST">
                @csrf
                @method('PATCH')

                <button type="submit"
                        class="w-full text-center text-sm text-white bg-amber-500 font-semibold py-2.5 rounded-xl transition-colors">
                    <i class="fa-solid fa-truck-fast mr-1.5"></i>
                    Proses Pengiriman
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
document.querySelectorAll('.filter-tab').forEach(btn => {
    btn.addEventListener('click', function () {

        // Update style tombol aktif
        document.querySelectorAll('.filter-tab').forEach(b => {
            b.classList.remove('bg-forest-800', 'text-white');
            b.classList.add('bg-cream-50', 'border', 'border-cream-300');
        });
        this.classList.add('bg-forest-800', 'text-white');
        this.classList.remove('bg-cream-50', 'border', 'border-cream-300');

        const status = this.dataset.status;
        const rows   = document.querySelectorAll('.order-row');
        let visible  = 0;

        rows.forEach(row => {
            const match = status === 'semua' || row.dataset.status === status;
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        document.getElementById('empty-order-msg').style.display
            = visible === 0 ? 'block' : 'none';
    });
});
</script>
@endpush
@endsection

