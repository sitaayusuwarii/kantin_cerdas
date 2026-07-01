@extends('layouts.app')
@section('title', 'Riwayat Pesanan - SmartCanteen')

@section('content')
@php
    $statusStyles = [
        'selesai' => ['bg' => 'bg-emerald-100 text-emerald-700', 'icon' => 'fa-check-circle', 'label' => 'Selesai'],
        'selesai_dimasak' => ['bg' => 'bg-emerald-100 text-emerald-700', 'icon' => 'fa-bowl-food', 'label' => 'Siap Diambil'],
        'diproses' => ['bg' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-fire-burner', 'label' => 'Diproses'],
        'dikonfirmasi' => ['bg' => 'bg-blue-100 text-blue-700', 'icon' => 'fa-circle-check', 'label' => 'Dikonfirmasi'],
        'baru' => ['bg' => 'bg-sky-100 text-sky-700', 'icon' => 'fa-clock', 'label' => 'Baru'],
        'pembayaran_terverifikasi' => ['bg' => 'bg-sky-100 text-sky-700', 'icon' => 'fa-clock', 'label' => 'Baru'],
        'dibatalkan' => ['bg' => 'bg-rose-100 text-rose-700', 'icon' => 'fa-circle-xmark', 'label' => 'Dibatalkan'],
    ];

    $paymentStyles = [
        'accepted' => ['bg' => 'bg-emerald-50 text-emerald-700', 'icon' => 'fa-check', 'label' => 'Lunas'],
        'terverifikasi' => ['bg' => 'bg-emerald-50 text-emerald-700', 'icon' => 'fa-check', 'label' => 'Lunas'],
        'paid' => ['bg' => 'bg-emerald-50 text-emerald-700', 'icon' => 'fa-check', 'label' => 'Lunas'],
        'pending' => ['bg' => 'bg-amber-50 text-amber-700', 'icon' => 'fa-clock', 'label' => 'Verifikasi'],
        'menunggu' => ['bg' => 'bg-amber-50 text-amber-700', 'icon' => 'fa-clock', 'label' => 'Verifikasi'],
        'rejected' => ['bg' => 'bg-rose-50 text-rose-700', 'icon' => 'fa-xmark', 'label' => 'Ditolak'],
        'ditolak' => ['bg' => 'bg-rose-50 text-rose-700', 'icon' => 'fa-xmark', 'label' => 'Ditolak'],
    ];

    $summaryCards = [
        ['label' => 'Total Pesanan', 'value' => $total, 'icon' => 'fa-bag-shopping', 'class' => 'bg-orange-50 text-orange-600'],
        ['label' => 'Selesai', 'value' => $selesai, 'icon' => 'fa-circle-check', 'class' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Diproses', 'value' => $diproses, 'icon' => 'fa-fire-burner', 'class' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Dibatalkan', 'value' => $dibatalkan, 'icon' => 'fa-circle-xmark', 'class' => 'bg-rose-50 text-rose-600'],
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 px-5 py-7 shadow-xl shadow-orange-200/50 sm:px-8 lg:px-10">
        <div class="absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.22),transparent_40%)]"></div>
        <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-6 right-10 hidden text-white/10 lg:block">
            <i class="fa-solid fa-clock-rotate-left text-[8rem]"></i>
        </div>

        <div class="relative grid gap-6 lg:grid-cols-[1fr_280px] lg:items-end">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/15 px-4 py-2 text-sm font-bold text-white backdrop-blur">
                    <i class="fa-solid fa-receipt"></i>
                    Riwayat Pesanan
                </div>
                <h1 class="mt-5 font-heading text-3xl font-extrabold leading-tight text-white md:text-4xl">
                    Semua transaksi kamu
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-orange-50 md:text-base">
                    Pantau status pesanan, cek invoice, dan lanjutkan pembayaran jika masih ada tagihan.
                </p>
            </div>

            <form method="GET" action="{{ route('customer.history') }}" class="rounded-3xl border border-white/20 bg-white/15 p-4 backdrop-blur">
                <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-orange-100">Filter Status</label>
                <div class="relative">
                    <select name="status" onchange="this.form.submit()"
                            class="w-full appearance-none rounded-2xl border border-white/20 bg-white px-4 py-3 pr-10 text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-white/20">
                        <option value="semua" {{ request('status') === 'semua' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru</option>
                        <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    <i class="fa-solid fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                </div>
            </form>
        </div>
    </section>

    <section class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach($summaryCards as $card)
            <div class="rounded-3xl border border-orange-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="font-heading text-2xl font-extrabold text-gray-950">{{ $card['value'] }}</p>
                        <p class="mt-1 text-xs font-bold text-gray-500">{{ $card['label'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $card['class'] }}">
                        <i class="fa-solid {{ $card['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    <section class="mt-6 overflow-hidden rounded-[2rem] border border-orange-100 bg-white shadow-sm">
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-orange-50/70">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-orange-700">Pesanan</th>
                        <th class="px-4 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-orange-700">Tanggal</th>
                        <th class="px-4 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-orange-700">Menu</th>
                        <th class="px-4 py-4 text-right text-xs font-extrabold uppercase tracking-wide text-orange-700">Total</th>
                        <th class="px-4 py-4 text-center text-xs font-extrabold uppercase tracking-wide text-orange-700">Status</th>
                        <th class="px-4 py-4 text-center text-xs font-extrabold uppercase tracking-wide text-orange-700">Pembayaran</th>
                        <th class="px-6 py-4 text-center text-xs font-extrabold uppercase tracking-wide text-orange-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        @php
                            $status = $statusStyles[$order->status] ?? $statusStyles['baru'];
                            $paymentStatus = $order->payment?->status;
                            $payment = $paymentStyles[$paymentStatus] ?? ['bg' => 'bg-rose-50 text-rose-700', 'icon' => 'fa-xmark', 'label' => 'Belum Bayar'];
                            $menuNames = $order->items->take(2)->map(fn($i) => $i->menu->name ?? '-')->join(', ');
                            if ($order->items->count() > 2) $menuNames .= ' +' . ($order->items->count() - 2) . ' lainnya';
                        @endphp
                        <tr class="group transition hover:bg-orange-50/40">
                            <td class="px-6 py-4">
                                <p class="font-heading font-extrabold text-gray-950">#{{ $order->order_number }}</p>
                                <p class="mt-1 text-xs text-gray-400">{{ $order->items->sum('quantity') }} item</p>
                            </td>
                            <td class="px-4 py-4 text-xs font-semibold text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $menuNames }}</td>
                            <td class="px-4 py-4 text-right font-heading font-extrabold text-gray-950">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-extrabold {{ $status['bg'] }}">
                                    <i class="fa-solid {{ $status['icon'] }} text-[10px]"></i>{{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-extrabold {{ $payment['bg'] }}">
                                    <i class="fa-solid {{ $payment['icon'] }} text-[10px]"></i>{{ $payment['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ url('/invoice/' . $order->order_number) }}"
                                       class="rounded-xl bg-orange-50 px-3 py-2 text-xs font-extrabold text-orange-600 transition hover:bg-orange-100">
                                        Detail
                                    </a>
                                    @if(!$order->payment || in_array($order->payment->status, ['ditolak', 'rejected']))
                                        <a href="{{ url('/payment?order=' . $order->order_number) }}"
                                           class="rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 px-3 py-2 text-xs font-extrabold text-white shadow-sm">
                                            Bayar
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-orange-50 text-orange-300">
                                    <i class="fa-solid fa-box-open text-2xl"></i>
                                </div>
                                <p class="mt-4 font-heading text-lg font-extrabold text-gray-700">Belum ada pesanan</p>
                                <a href="{{ route('customer.menu') }}" class="mt-2 inline-block text-sm font-bold text-orange-600">Pesan menu sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 p-4 md:hidden">
            @forelse($orders as $order)
                @php
                    $status = $statusStyles[$order->status] ?? $statusStyles['baru'];
                    $paymentStatus = $order->payment?->status;
                    $payment = $paymentStyles[$paymentStatus] ?? ['bg' => 'bg-rose-50 text-rose-700', 'icon' => 'fa-xmark', 'label' => 'Belum Bayar'];
                    $menuNames = $order->items->take(2)->map(fn($i) => $i->menu->name ?? '-')->join(', ');
                    if ($order->items->count() > 2) $menuNames .= ' +' . ($order->items->count() - 2) . ' lainnya';
                @endphp
                <article class="rounded-3xl border border-gray-100 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-heading text-base font-extrabold text-gray-950">#{{ $order->order_number }}</p>
                            <p class="mt-1 text-xs font-medium text-gray-400">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1.5 text-xs font-extrabold {{ $status['bg'] }}">{{ $status['label'] }}</span>
                    </div>
                    <p class="mt-4 rounded-2xl bg-gray-50 p-3 text-sm leading-6 text-gray-600">{{ $menuNames }}</p>
                    <div class="mt-4 flex items-end justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-extrabold {{ $payment['bg'] }}">
                                <i class="fa-solid {{ $payment['icon'] }} text-[9px]"></i>{{ $payment['label'] }}
                            </span>
                            <p class="mt-2 font-heading text-xl font-extrabold text-orange-600">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ url('/invoice/' . $order->order_number) }}" class="rounded-xl bg-orange-50 px-3 py-2 text-xs font-extrabold text-orange-600">Detail</a>
                            @if(!$order->payment || in_array($order->payment->status, ['ditolak', 'rejected']))
                                <a href="{{ url('/payment?order=' . $order->order_number) }}" class="rounded-xl bg-orange-500 px-3 py-2 text-xs font-extrabold text-white">Bayar</a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="py-14 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-orange-50 text-orange-300">
                        <i class="fa-solid fa-box-open text-2xl"></i>
                    </div>
                    <p class="mt-4 font-heading text-lg font-extrabold text-gray-700">Belum ada pesanan</p>
                    <a href="{{ route('customer.menu') }}" class="mt-2 inline-block text-sm font-bold text-orange-600">Pesan menu sekarang</a>
                </div>
            @endforelse
        </div>
    </section>

    @if($orders->hasPages())
        <div class="mt-6 rounded-3xl border border-orange-100 bg-white p-4 shadow-sm">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
