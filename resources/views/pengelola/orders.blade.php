@extends('layouts.pengelola')
@section('title', 'Pesanan Masuk')
@section('page-title', 'Pesanan Masuk')
@section('page-subtitle', $newOrders . ' pesanan baru menunggu konfirmasi')
@section('content')

{{-- Alert --}}
<div class="flex items-center gap-3 bg-forest-50 border border-forest-200 rounded-2xl p-4 mb-6">
    <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-bell text-white text-sm"></i>
    </div>
    <div class="flex-1">
        <p class="font-semibold text-forest-900 text-sm">
            {{ $newOrders }} pesanan baru masuk!
        </p>
        <p class="text-forest-600 text-xs">Konfirmasi segera agar dapur mulai memproses.</p>
    </div>
    <span class="badge-new bg-orange-500 text-white text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0">
        {{ $newOrders }} Baru
    </span>
</div>

{{-- Filter Tanggal --}}
<form method="GET" action="{{ route('pengelola.orders') }}"
      class="flex flex-wrap items-end gap-3 bg-white border border-cream-200 rounded-2xl p-4 mb-5">
    <div>
        <label class="block text-[11px] font-semibold text-forest-500 uppercase tracking-wide mb-1">Dari Tanggal</label>
        <input type="date" name="from" value="{{ $filterFrom }}"
               class="border border-cream-300 rounded-xl px-3 py-2 text-sm text-forest-800 focus:outline-none focus:ring-2 focus:ring-orange-400">
    </div>
    <div>
        <label class="block text-[11px] font-semibold text-forest-500 uppercase tracking-wide mb-1">Sampai Tanggal</label>
        <input type="date" name="to" value="{{ $filterTo }}"
               class="border border-cream-300 rounded-xl px-3 py-2 text-sm text-forest-800 focus:outline-none focus:ring-2 focus:ring-orange-400">
    </div>

    <button type="submit"
            class="bg-orange-500 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow hover:bg-orange-600 transition">
        <i class="fa-solid fa-filter mr-1.5"></i> Terapkan
    </button>

    @if($filterFrom !== today()->format('Y-m-d') || $filterTo !== today()->format('Y-m-d'))
        <a href="{{ route('pengelola.orders') }}"
           class="text-xs font-semibold text-forest-500 hover:text-red-500 px-2 py-2.5">
            <i class="fa-solid fa-xmark"></i> Reset ke Hari Ini
        </a>
    @endif
</form>

{{-- Filter Tabs --}}
<div class="flex flex-wrap gap-2 mb-5">
    @php
        $tabs = [
            'semua'           => 'Semua',
            'baru'            => 'Baru',
            'dikonfirmasi'    => 'Dikonfirmasi',
            'diproses'        => 'Diproses',
            'selesai_dimasak' => 'Siap Diambil',
            'selesai'         => 'Selesai',
        ];
        $tabCounts = [
            'semua'           => $totalOrders,
            'baru'            => $newOrders,
            'dikonfirmasi'    => $confirmedOrders,
            'diproses'        => $processedOrders,
            'selesai_dimasak' => $readyOrders,
            'selesai'         => $completedOrders,
        ];
    @endphp

    @foreach($tabs as $key => $label)
        <a href="{{ route('pengelola.orders', array_filter([
                'status' => $key,
                'from'   => $filterFrom,
                'to'     => $filterTo,
            ])) }}"
           class="filter-tab px-4 py-2 rounded-xl text-sm font-semibold {{ $activeStatus === $key ? 'bg-orange-500 text-white' : 'bg-white border border-cream-300' }}">
            {{ $label }} ({{ $tabCounts[$key] }})
        </a>
    @endforeach
</div>

{{-- ── DESKTOP TABLE ────────────────────────────────────── --}}
<div class="hidden md:block bg-white rounded-2xl shadow-sm border border-cream-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-cream-100/80 border-b border-cream-200">
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Pesanan</th>
                <th class="text-left px-4 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Siswa</th>
                <th class="text-left px-4 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Tipe</th>
                <th class="text-left px-4 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Menu</th>
                <th class="text-right px-4 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Total</th>
                <th class="text-center px-3 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Pickup</th>
                <th class="text-center px-3 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Status</th>
                <th class="text-center px-6 py-3.5 text-xs font-semibold text-forest-500 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-cream-200" id="desktop-tbody">
            @foreach($orders as $order)
            @php
                $customerName = $order->user->full_name ?? $order->user->name ?? $order->user->username ?? 'Pelanggan';

                if ($order->note && preg_match('/Nama pelanggan:\s*([^|]+)/i', $order->note, $matches)) {
                    $customerName = trim($matches[1]);
                }

                $displayNote = trim(preg_replace('/Nama pelanggan:\s*[^|]+(\|\s*)?/i', '', $order->note ?? ''));
            @endphp
            <tr class="order-row hover:bg-cream-100/50 transition-colors {{ $order->status === 'pembayaran_terverifikasi' ? 'bg-forest-50/40' : '' }}"
                 data-status="{{ $order->status }}">
                <td class="px-6 py-4">
                    <p class="font-display font-semibold text-sm text-forest-900 whitespace-nowrap">#{{ $order->order_number }}</p>
                    <p class="text-[10px] text-forest-400 mt-0.5 whitespace-nowrap">
                        {{ $order->created_at->format('d M Y') }}
                    </p>
                    <p class="text-[10px] text-forest-400 flex items-center gap-1 whitespace-nowrap">
                        <i class="fa-solid fa-clock text-forest-300"></i>
                        {{ $order->created_at->format('H:i') }}
                    </p>
                </td>
                <td class="px-4 py-4">
                    <p class="font-semibold text-xs text-forest-900">{{ $customerName }}</p>
                    @if($order->isDineIn() && $order->table_number)
                        <p class="text-[10px] text-teal-600 font-semibold">
                            <i class="fa-solid fa-chair"></i> Meja {{ $order->table_number }}
                        </p>
                    @else
                        <p class="text-[10px] text-forest-400">
                            {{ $order->user->class ?? $order->user->kelas ?? '-' }}
                        </p>
                    @endif
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center text-[10px] font-semibold px-2 py-1 rounded-full {{ $order->order_type_color }}">
                        {{ $order->order_type_label }}
                    </span>
                    @if($order->isDelivery() && $order->classroom)
                        <p class="text-[10px] text-purple-600 mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-location-dot"></i> {{ $order->classroom }}
                        </p>
                    @endif
                    @if($displayNote)
                        <p class="text-[10px] text-gray-400 mt-1 italic truncate max-w-[120px]" title="{{ $displayNote }}">
                            📝 {{ $displayNote }}
                        </p>
                    @endif
                </td>
                <td class="px-4 py-4">
                    <div class="space-y-0.5">
                        @foreach($order->items as $item)
                            <p class="text-xs text-forest-600">
                                🍽️ {{ $item->menu->name }} ×{{ $item->quantity }}
                            </p>
                        @endforeach
                    </div>
                </td>
                <td class="px-4 py-4 text-right font-display font-bold text-sm text-forest-900">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </td>
                <td class="px-3 py-4 text-center">
                   <span class="text-[10px] bg-cream-200 text-forest-600 font-medium px-2 py-1 rounded-lg">
                    {{ $order->pickup_display }}
                </span>
                </td>
                <td class="px-3 py-4 text-center">
                    <span class="inline-flex items-center gap-1 {{ $order->status_color }} text-[10px] font-bold px-2.5 py-1.5 rounded-full">
                        @if($order->status === 'pembayaran_terverifikasi')
                            <i class="fa-solid fa-circle badge-new text-[7px]"></i>
                        @endif
                        {{ $order->status_label }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($order->status === 'pembayaran_terverifikasi')
                        <form action="{{ route('pengelola.orders.confirm', $order->order_number) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="btn-primary text-white text-xs font-bold px-4 py-2 rounded-xl shadow">
                                <i class="fa-solid fa-check text-[10px]"></i> Terima
                            </button>
                        </form>
                    @elseif($order->status === 'dikonfirmasi')
                        <form action="{{ route('pengelola.orders.process', $order->order_number) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="bg-amber-500 text-white text-xs font-bold px-4 py-2 rounded-xl shadow">
                                Proses
                            </button>
                        </form>
                    @elseif($order->status === 'diproses')
                        <form action="{{ route('pengelola.orders.complete', $order->order_number) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="bg-emerald-600 text-white text-xs font-bold px-4 py-2 rounded-xl shadow">
                                Selesai
                            </button>
                        </form>
                    @else
                        <span class="text-emerald-600 font-semibold text-xs">✓ Selesai</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pesan kosong (desktop) --}}
    @if($orders->isEmpty())
    <div class="text-center py-16">
        <div class="text-5xl mb-3">📋</div>
        <p class="text-forest-600 font-semibold text-sm">Tidak ada pesanan</p>
    </div>
    @endif
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>

{{-- ── MOBILE CARDS ─────────────────────────────────────── --}}
<div class="md:hidden space-y-4" id="mobile-cards">
    @foreach($orders as $order)
        @php
            $customerName = $order->user->full_name ?? $order->user->name ?? $order->user->username ?? 'Pelanggan';

            if ($order->note && preg_match('/Nama pelanggan:\s*([^|]+)/i', $order->note, $matches)) {
                $customerName = trim($matches[1]);
            }

            $displayNote = trim(preg_replace('/Nama pelanggan:\s*[^|]+(\|\s*)?/i', '', $order->note ?? ''));

            $borderColor = match($order->status) {
                'pembayaran_terverifikasi' => 'border-l-green-500',
                'dikonfirmasi' => 'border-l-amber-500',
                'diproses'     => 'border-l-blue-500',
                'selesai'      => 'border-l-emerald-500',
                default        => 'border-l-gray-300',
            };
        @endphp
        <div class="order-row bg-cream-50 rounded-2xl shadow-sm border border-cream-200 overflow-hidden border-l-4 {{ $borderColor }}"
             data-status="{{ $order->status }}">
            <div class="flex items-center justify-between px-4 py-3 bg-cream-100/60 border-b border-cream-200">
                <div class="flex items-center gap-2">
                    <p class="font-display font-bold text-sm text-forest-900">#{{ $order->order_number }}</p>
                    <span class="text-[10px] text-forest-400 whitespace-nowrap">· {{ $order->created_at->format('d M, H:i') }}</span>
                </div>
                <span class="inline-flex items-center gap-1 {{ $order->status_color }} text-[10px] font-bold px-2.5 py-1 rounded-full">
                    @if($order->status === 'pembayaran_terverifikasi')
                        <i class="fa-solid fa-circle badge-new text-[7px]"></i>
                    @endif
                    {{ $order->status_label }}
                </span>
            </div>
            <div class="px-4 py-3.5">
                <div class="flex items-start justify-between gap-3 mb-2.5">
                    <div>
                        <p class="font-semibold text-sm text-forest-900">{{ $customerName }}</p>
                        <p class="text-xs text-forest-400">
                            @if($order->isDineIn() && $order->table_number)
                                Meja {{ $order->table_number }} ·
                            @else
                                {{ $order->user->class }} ·
                            @endif
                            {{ $order->pickup_display }}
                        </p>                    
                    </div>
                    <p class="font-display font-bold text-base text-forest-800 flex-shrink-0">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex items-center gap-1.5 mb-2 flex-wrap">
                    <span class="inline-flex items-center text-[10px] font-semibold px-2 py-1 rounded-full {{ $order->order_type_color }}">
                        {{ $order->order_type_label }}
                    </span>
                    @if($order->isDelivery() && $order->classroom)
                        <span class="inline-flex items-center gap-1 text-[10px] font-medium px-2 py-1 rounded-full bg-purple-100 text-purple-700">
                            <i class="fa-solid fa-location-dot"></i> {{ $order->classroom }}
                        </span>
                    @endif
                </div>
                @if($displayNote)
                    <p class="text-[10px] text-gray-400 italic mb-2">📝 {{ $displayNote }}</p>
                @endif
                <div class="bg-cream-100 rounded-xl p-3 mb-3 space-y-1">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-forest-700">{{ $item->menu->name }} ×{{ $item->quantity }}</span>
                            <span class="font-medium text-forest-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                @if($order->status === 'pembayaran_terverifikasi')
                    <form action="{{ route('pengelola.orders.confirm', $order->order_number) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="btn-primary w-full text-white text-sm font-bold py-2.5 rounded-xl shadow flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check"></i> Terima Pesanan
                        </button>
                    </form>
                @elseif($order->status === 'dikonfirmasi')
                    <form action="{{ route('pengelola.orders.process', $order->order_number) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full text-center text-sm text-white bg-amber-500 font-semibold py-2.5 rounded-xl">
                            <i class="fa-solid fa-fire-burner mr-1.5"></i> Mulai Proses
                        </button>
                    </form>
                @elseif($order->status === 'diproses')
                    <form action="{{ route('pengelola.orders.complete', $order->order_number) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full text-center text-sm text-white bg-emerald-600 font-semibold py-2.5 rounded-xl">
                            <i class="fa-solid fa-check-double mr-1.5"></i> Tandai Selesai
                        </button>
                    </form>
                @else
                    <p class="text-center text-emerald-600 font-semibold text-sm py-1">✓ Pesanan Selesai</p>
                @endif
            </div>
        </div>
    @endforeach

    {{-- Pesan kosong (mobile) --}}
    @if($orders->isEmpty())
    <div class="text-center py-16">
        <div class="text-5xl mb-3">📋</div>
        <p class="text-forest-600 font-semibold text-sm">Tidak ada pesanan</p>
    </div>
    @endif
</div>

@endsection