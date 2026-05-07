@extends('layouts.app')
@section('title', 'Tagihan — SmartCanteen')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="font-heading font-bold text-2xl md:text-3xl text-canteen-dark">Tagihan 📄</h1>
            <p class="text-gray-400 text-sm mt-0.5">Detail tagihan pesananmu</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <button onclick="window.print()"
                class="flex items-center gap-2 bg-white border border-gray-200 text-gray-600
                       hover:bg-gray-50 text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
                <i class="fa-solid fa-print text-sm"></i>Cetak
            </button>
            <a href="https://telegram.me/6281234567890?text=Halo, saya ingin konfirmasi pembayaran Order %23{{ $order->order_number }}"
               target="_blank"
               class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white
                      text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
                <i class="fa-brands fa-telegram"></i>Konfirmasi Telegram
            </a>
        </div>
    </div>

    {{-- Invoice Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Invoice Header --}}
        <div class="bg-gradient-to-br from-primary-500 to-primary-700 px-6 py-8 sm:px-10 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute right-12 bottom-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
            <div class="relative flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5">
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-bowl-food text-white text-sm"></i>
                        </div>
                        <span class="font-heading font-bold text-white text-lg">SmartCanteen</span>
                    </div>
                    <p class="text-orange-100 text-xs">SMAN 1 Contoh · Jl. Pendidikan No. 1</p>
                    <p class="text-orange-100 text-xs">Kota Yogyakarta, DIY 55123</p>
                </div>
                <div class="sm:text-right">
                    <p class="font-heading font-bold text-2xl sm:text-3xl text-white">
                        #{{ $order->order_number }}
                    </p>
                    @php
                        $paymentStatus = $order->payment?->status;
                    @endphp
                    @if($paymentStatus === 'accepted')
                        <span class="inline-flex items-center gap-1.5 bg-green-400/30 border border-green-300/40 text-white text-xs font-bold px-3 py-1.5 rounded-full mt-2">
                            <i class="fa-solid fa-circle text-[8px]"></i>LUNAS
                        </span>
                    @elseif($paymentStatus === 'pending')
                        <span class="inline-flex items-center gap-1.5 bg-yellow-400/30 border border-yellow-300/40 text-white text-xs font-bold px-3 py-1.5 rounded-full mt-2">
                            <i class="fa-solid fa-circle text-[8px]"></i>MENUNGGU VERIFIKASI
                        </span>
                    @elseif($paymentStatus === 'rejected')
                        <span class="inline-flex items-center gap-1.5 bg-red-400/30 border border-red-300/40 text-white text-xs font-bold px-3 py-1.5 rounded-full mt-2">
                            <i class="fa-solid fa-circle text-[8px]"></i>DITOLAK
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 bg-red-400/30 border border-red-300/40 text-white text-xs font-bold px-3 py-1.5 rounded-full mt-2">
                            <i class="fa-solid fa-circle text-[8px]"></i>BELUM LUNAS
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info Row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-0 border-b border-gray-100">
            @php
            $pickupLabels = [
                'istirahat_1' => 'Istirahat 1 (09:30)',
                'istirahat_2' => 'Istirahat 2 (12:00)',
                'pulang'      => 'Pulang (14:30)',
            ];
            $infoItems = [
                ['label' => 'Nama',          'value' => $order->user->name,                              'icon' => 'fa-user'],
                ['label' => 'Status',        'value' => ucfirst($order->status),                         'icon' => 'fa-circle-info'],
                ['label' => 'Tanggal',       'value' => $order->created_at->format('d M Y'),             'icon' => 'fa-calendar'],
                ['label' => 'Pengambilan',   'value' => $pickupLabels[$order->pickup_schedule] ?? '-',   'icon' => 'fa-clock'],
            ];
            @endphp
            @foreach($infoItems as $i => $info)
            <div class="px-5 py-4 {{ $i < 3 ? 'border-r border-gray-100' : '' }}">
                <p class="text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1 flex items-center gap-1">
                    <i class="fa-solid {{ $info['icon'] }} text-primary-300"></i>{{ $info['label'] }}
                </p>
                <p class="font-heading font-bold text-xs sm:text-sm text-canteen-dark">{{ $info['value'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Items Table --}}
        <div class="p-6 sm:p-8">
            <h3 class="font-heading font-bold text-sm text-gray-500 uppercase tracking-wider mb-4">
                Detail Item
            </h3>

            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-hidden rounded-xl border border-gray-100">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($order->items as $item)
                        <tr class="hover:bg-orange-50/50 transition-colors">
                            <td class="px-5 py-3.5 font-medium text-gray-800">
                                <div class="flex items-center gap-2">
                                    @if($item->menu?->image)
                                        <img src="{{ asset('storage/' . $item->menu->image) }}"
                                             class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <span class="text-lg">🍽️</span>
                                    @endif
                                    {{ $item->menu->name ?? 'Menu tidak tersedia' }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center text-gray-500">{{ $item->quantity }}</td>
                            <td class="px-4 py-3.5 text-right text-gray-500">
                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-heading font-semibold text-canteen-dark">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card List --}}
            <div class="sm:hidden space-y-3">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        @if($item->menu?->image)
                            <img src="{{ asset('storage/' . $item->menu->image) }}"
                                 class="w-10 h-10 rounded-xl object-cover flex-shrink-0">
                        @else
                            <span class="text-2xl">🍽️</span>
                        @endif
                        <div>
                            <p class="font-semibold text-sm text-gray-800">
                                {{ $item->menu->name ?? 'Menu tidak tersedia' }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $item->quantity }}x · Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <p class="font-heading font-bold text-sm text-canteen-dark">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </p>
                </div>
                @endforeach
            </div>

            {{-- Totals --}}
            <div class="mt-6 border-t border-dashed border-gray-200 pt-5 space-y-2.5">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal ({{ $order->items->sum('quantity') }} item)</span>
                    <span class="font-medium text-gray-700">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Pajak & Biaya (0%)</span>
                    <span class="font-medium text-gray-700">Rp 0</span>
                </div>

                @if($order->note)
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Catatan</span>
                    <span class="font-medium text-gray-700 text-right max-w-xs">{{ $order->note }}</span>
                </div>
                @endif

                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <span class="font-heading font-bold text-base text-canteen-dark">TOTAL</span>
                    <span class="font-heading font-bold text-2xl text-primary-500">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="mx-6 mb-6 sm:mx-8 p-4 bg-orange-50 rounded-xl border border-orange-100 flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <i class="fa-solid fa-circle-info text-primary-400 mt-0.5 flex-shrink-0"></i>
            <p class="text-xs text-gray-600 leading-relaxed">
                Silakan upload bukti pembayaran melalui menu <strong>Upload Bukti Bayar</strong>
                atau konfirmasi via Telegram. Pembayaran diterima melalui transfer BRI:
                <strong>1234567890</strong> a/n SmartCanteen.
            </p>
        </div>

        {{-- Action Buttons --}}
        @if(!$paymentStatus || $paymentStatus === 'rejected')
        <div class="px-6 pb-6 sm:px-8 flex flex-col sm:flex-row gap-3">
            <a href="{{ url('/payment') }}"
               class="btn-primary text-white font-heading font-bold py-3.5 rounded-xl flex-1
                      text-sm text-center shadow-lg flex items-center justify-center gap-2">
                <i class="fa-solid fa-upload"></i>Upload Bukti Bayar
            </a>
            <a href="https://telegram.me/6281234567890?text=Halo, konfirmasi pembayaran Order %23{{ $order->order_number }}"
               target="_blank"
               class="bg-green-500 hover:bg-green-600 text-white font-heading font-bold py-3.5
                      rounded-xl flex-1 text-sm text-center shadow transition-colors
                      flex items-center justify-center gap-2">
                <i class="fa-brands fa-telegram text-base"></i>Konfirmasi via Telegram
            </a>
        </div>
        @elseif($paymentStatus === 'pending')
        <div class="px-6 pb-6 sm:px-8">
            <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-100 text-center">
                <i class="fa-solid fa-clock text-yellow-500 mb-2 block text-xl"></i>
                <p class="text-sm font-semibold text-yellow-700">Menunggu Verifikasi Admin</p>
                <p class="text-xs text-yellow-600 mt-1">Bukti pembayaran sudah diterima dan sedang diverifikasi</p>
            </div>
        </div>
        @elseif($paymentStatus === 'accepted')
        <div class="px-6 pb-6 sm:px-8">
            <div class="p-4 bg-green-50 rounded-xl border border-green-100 text-center">
                <i class="fa-solid fa-circle-check text-green-500 mb-2 block text-xl"></i>
                <p class="text-sm font-semibold text-green-700">Pembayaran Dikonfirmasi</p>
                <p class="text-xs text-green-600 mt-1">Terima kasih, pembayaran kamu sudah lunas</p>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); }
    .btn-primary:hover { filter: brightness(1.05); transform: translateY(-1px); }

    @media print {
        header, footer, .no-print { display: none !important; }
        body { background: white !important; }
    }
</style>
@endpush