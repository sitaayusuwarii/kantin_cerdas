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
            <button onclick="window.print()" class="flex items-center gap-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
                <i class="fa-solid fa-print text-sm"></i>Cetak
            </button>
            <a href="https://telegram.me/6281234567890?text=Halo, saya ingin konfirmasi pembayaran Order %23SC-001" target="_blank" class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
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
                    <p class="font-heading font-bold text-2xl sm:text-3xl text-white">#SC-001</p>
                    <span class="inline-flex items-center gap-1.5 bg-red-400/30 border border-red-300/40 text-white text-xs font-bold px-3 py-1.5 rounded-full mt-2">
                        <i class="fa-solid fa-circle text-[8px]"></i>BELUM LUNAS
                    </span>
                </div>
            </div>
        </div>

        {{-- Info Row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-0 border-b border-gray-100">
            @php
            $infoItems = [
                ['label' => 'Nama Siswa', 'value' => 'Ahmad Rizky', 'icon' => 'fa-user'],
                ['label' => 'Kelas', 'value' => 'XII IPA 2', 'icon' => 'fa-graduation-cap'],
                ['label' => 'Tanggal', 'value' => '19 Apr 2025', 'icon' => 'fa-calendar'],
                ['label' => 'Jatuh Tempo', 'value' => '20 Apr 2025', 'icon' => 'fa-clock'],
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
            <h3 class="font-heading font-bold text-sm text-gray-500 uppercase tracking-wider mb-4">Detail Item</h3>

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
                        @php
                        $tableItems = [
                            ['emoji' => '🍛', 'name' => 'Nasi Gudeg Komplit', 'qty' => 1, 'price' => 12000],
                            ['emoji' => '🧋', 'name' => 'Es Teh Manis', 'qty' => 2, 'price' => 4000],
                            ['emoji' => '🍌', 'name' => 'Pisang Goreng', 'qty' => 1, 'price' => 5000],
                        ];
                        @endphp
                        @foreach($tableItems as $item)
                        <tr class="hover:bg-orange-50/50 transition-colors">
                            <td class="px-5 py-3.5 font-medium text-gray-800 flex items-center gap-2">
                                <span class="text-lg">{{ $item['emoji'] }}</span>{{ $item['name'] }}
                            </td>
                            <td class="px-4 py-3.5 text-center text-gray-500">{{ $item['qty'] }}</td>
                            <td class="px-4 py-3.5 text-right text-gray-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-right font-heading font-semibold text-canteen-dark">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card List --}}
            <div class="sm:hidden space-y-3">
                @foreach($tableItems as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ $item['emoji'] }}</span>
                        <div>
                            <p class="font-semibold text-sm text-gray-800">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $item['qty'] }}x · Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <p class="font-heading font-bold text-sm text-canteen-dark">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>

            {{-- Totals --}}
            <div class="mt-6 border-t border-dashed border-gray-200 pt-5 space-y-2.5">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span><span class="font-medium text-gray-700">Rp 25.000</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Pajak & Biaya (0%)</span><span class="font-medium text-gray-700">Rp 0</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <span class="font-heading font-bold text-base text-canteen-dark">TOTAL</span>
                    <span class="font-heading font-bold text-2xl text-primary-500">Rp 25.000</span>
                </div>
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="mx-6 mb-6 sm:mx-8 p-4 bg-orange-50 rounded-xl border border-orange-100 flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <i class="fa-solid fa-circle-info text-primary-400 mt-0.5 flex-shrink-0"></i>
            <p class="text-xs text-gray-600 leading-relaxed">
                Silakan upload bukti pembayaran melalui menu <strong>Upload Bukti Bayar</strong> atau konfirmasi via Telegram. Pembayaran diterima melalui transfer BRI: <strong>1234567890</strong> a/n SmartCanteen.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="px-6 pb-6 sm:px-8 flex flex-col sm:flex-row gap-3">
            <a href="{{ url('/payment') }}" class="btn-primary text-white font-heading font-bold py-3.5 rounded-xl flex-1 text-sm text-center shadow-lg flex items-center justify-center gap-2">
                <i class="fa-solid fa-upload"></i>Upload Bukti Bayar
            </a>
            <a href="https://telegram.me/6281234567890" target="_blank" class="bg-green-500 hover:bg-green-600 text-white font-heading font-bold py-3.5 rounded-xl flex-1 text-sm text-center shadow transition-colors flex items-center justify-center gap-2">
                <i class="fa-brands fa-telegram text-base"></i>Konfirmasi via Telegram
            </a>
        </div>
    </div>
</div>
@endsection
