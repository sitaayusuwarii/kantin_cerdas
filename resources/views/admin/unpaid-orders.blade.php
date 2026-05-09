@extends('layouts.admin')
@section('title', 'Pesanan Belum Bayar')
@section('page-title', 'Pesanan Belum Bayar')
@section('page-subtitle', 'Monitor customer yang belum menyelesaikan pembayaran')

@section('content')

{{-- Filter Tanggal --}}
<form method="GET" class="glass-card rounded-2xl p-4 mb-6 flex items-center gap-3">
    <i class="fa-solid fa-calendar text-primary-400"></i>
    <input type="date" name="date" value="{{ $date }}"
           class="bg-transparent text-slate-300 text-sm outline-none border border-border rounded-lg px-3 py-2"
           onchange="this.form.submit()">
    <span class="text-slate-500 text-sm">{{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
</form>

{{-- Summary --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="glass-card rounded-2xl p-5">
        <p class="text-slate-400 text-xs mb-1">Belum Bayar</p>
        <p class="text-white font-bold text-3xl">{{ $summary['total'] }}</p>
        <p class="text-slate-500 text-xs mt-1">pesanan hari ini</p>
    </div>
    <div class="glass-card rounded-2xl p-5">
        <p class="text-slate-400 text-xs mb-1">Total Nilai</p>
        <p class="text-white font-bold text-2xl">Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}</p>
        <p class="text-slate-500 text-xs mt-1">potensi pemasukan</p>
    </div>
</div>

{{-- Tabel --}}
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-border flex items-center justify-between">
        <h2 class="text-white font-bold">Daftar Pesanan Belum Bayar</h2>
        <span class="text-xs text-amber-400 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full font-semibold">
            Kantin tutup 13:00
        </span>
    </div>

    @if($unpaidOrders->isEmpty())
        <div class="text-center py-16">
            <div class="text-5xl mb-3">✅</div>
            <p class="text-white font-semibold">Semua Sudah Bayar!</p>
            <p class="text-slate-500 text-sm mt-1">Tidak ada pesanan yang belum dibayar.</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-5 py-3 text-xs text-slate-500 uppercase">Customer</th>
                    <th class="text-left px-5 py-3 text-xs text-slate-500 uppercase">Order</th>
                    <th class="text-left px-5 py-3 text-xs text-slate-500 uppercase">Menu</th>
                    <th class="text-right px-5 py-3 text-xs text-slate-500 uppercase">Total</th>
                    <th class="text-center px-5 py-3 text-xs text-slate-500 uppercase">Waktu Order</th>
                    <th class="text-center px-5 py-3 text-xs text-slate-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @foreach($unpaidOrders as $order)
                <tr class="hover:bg-slate-800/30 transition-colors">
                    <td class="px-5 py-4">
                        <p class="text-white font-semibold">{{ $order->user->username }}</p>
                        <p class="text-slate-500 text-xs">{{ $order->user->class ?? '-' }}</p>
                        @if(!$order->user->telegram_chat_id)
                            <span class="text-xs text-red-400">⚠ Telegram belum terhubung</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-white font-mono font-semibold">#{{ $order->order_number }}</p>
                    </td>
                    <td class="px-5 py-4">
                        @foreach($order->items as $item)
                            <p class="text-slate-300 text-xs">{{ $item->menu->name }} ×{{ $item->quantity }}</p>
                        @endforeach
                    </td>
                    <td class="px-5 py-4 text-right">
                        <p class="text-white font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <p class="text-slate-300 text-xs">{{ $order->created_at->format('H:i') }} </p>
                        <p class="text-slate-500 text-xs">{{ $order->created_at->diffForHumans() }}</p>
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
                            <form action="{{ route('admin.unpaid-orders.cancel', $order) }}" method="POST"
                                  onsubmit="return confirm('Batalkan pesanan #{{ $order->order_number }}?')">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold hover:bg-red-500/20 transition-all">
                                    <i class="fa-solid fa-xmark mr-1"></i> Batalkan
                                </button>
                            </form>
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

@endsection