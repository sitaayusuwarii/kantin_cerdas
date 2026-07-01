@extends('layouts.pengelola')
@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Semua pemberitahuan sistem')

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Header actions --}}
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm font-semibold text-gray-700">
            {{ $notifications->total() }} notifikasi
            <span class="text-xs font-normal text-gray-400 ml-1">masuk</span>
        </p>
        <form method="POST" action="{{ route('pengelola.notifications.read-all') }}">
            @csrf
            <button type="submit"
                    class="text-xs font-semibold text-gray-500 hover:text-red-500
                        flex items-center gap-1.5 bg-white border border-gray-200
                        hover:border-red-200 hover:bg-red-50 px-3 py-1.5 rounded-xl
                        transition-all shadow-sm">
                <i class="fa-solid fa-trash text-[10px]"></i>
                Hapus semua
            </button>
        </form>
    </div>

 {{-- List --}}
<div class="space-y-2">
    @forelse($notifications as $notif)
        @php
            $iconMap = match($notif->type) {
                'order_new'   => ['bg' => 'bg-orange-500', 'icon' => 'fa-basket-shopping'],
                'stock_low'   => ['bg' => 'bg-red-500', 'icon' => 'fa-triangle-exclamation'],
                'payment_new' => ['bg' => 'bg-emerald-500', 'icon' => 'fa-money-bill-wave'],
                default       => ['bg' => $notif->color, 'icon' => $notif->icon],
            };
        @endphp

        <div onclick="window.location.href='{{ route('pengelola.notifications.open', $notif) }}'"
             class="bg-white rounded-2xl px-4 py-4 flex items-start gap-3 shadow-sm cursor-pointer w-full text-left
                    {{ $notif->isRead() ? 'border border-gray-100 opacity-75' : 'border border-gray-100 border-l-4 border-l-green-400' }}
                    transition-all hover:shadow-md hover:-translate-y-0.5">

            <div class="w-10 h-10 {{ $iconMap['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm mt-0.5">
                <i class="fa-solid {{ $iconMap['icon'] }} text-white text-xs"></i>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                            {{ $notif->title }}
                            @if(!$notif->isRead())
                                <span class="inline-block w-2 h-2 bg-green-400 rounded-full flex-shrink-0"></span>
                            @endif
                        </p>

                        <p class="text-xs text-gray-800 mt-0.5 leading-relaxed">
                            {{ $notif->message }}
                        </p>

                        <p class="text-[10px] text-gray-400 mt-1.5 flex items-center gap-1">
                            <i class="fa-regular fa-clock text-[9px]"></i>
                            {{ $notif->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <form method="POST"
                          action="{{ route('pengelola.notifications.destroy', $notif) }}"
                          onclick="event.stopPropagation();">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-7 h-7 flex items-center justify-center text-gray-300
                                       hover:text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white border border-gray-100 rounded-2xl py-16 text-center shadow-sm">
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-bell-slash text-gray-400 text-xl"></i>
            </div>
            <p class="font-semibold text-gray-600 text-base">Tidak ada notifikasi</p>
            <p class="text-gray-400 text-xs mt-1">Semua pemberitahuan akan muncul di sini</p>
        </div>
    @endforelse
</div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="mt-5">
        {{ $notifications->links() }}
    </div>
    @endif

</div>

@endsection