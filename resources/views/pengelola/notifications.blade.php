@extends('layouts.pengelola')
@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Semua pemberitahuan sistem')

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Header actions --}}
    <div class="flex items-center justify-between mb-5">
        <p class="text-xs text-forest-500">
            {{ $notifications->total() }} notifikasi
        </p>
        <form method="POST" action="{{ route('pengelola.notifications.read-all') }}">
            @csrf
            <button type="submit"
                    class="text-xs font-semibold text-forest-600 hover:text-forest-800
                           flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-check-double text-[11px]"></i>
                Tandai semua dibaca
            </button>
        </form>
    </div>

    {{-- List --}}
    <div class="space-y-2">
        @forelse($notifications as $notif)
        <div class="bg-cream-50 border rounded-2xl px-4 py-3.5 flex items-start gap-3
                    transition-all shadow-sm
                    {{ $notif->isRead() ? 'border-cream-200 opacity-70' : 'border-forest-200 shadow-forest-100' }}">

            {{-- Icon --}}
            <div class="w-9 h-9 {{ $notif->color }} rounded-xl flex items-center justify-center
                        flex-shrink-0 shadow-sm mt-0.5">
                <i class="fa-solid {{ $notif->icon }} text-white text-xs"></i>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-forest-900 {{ !$notif->isRead() ? '' : 'font-medium' }}">
                            {{ $notif->title }}
                            @if(!$notif->isRead())
                            <span class="inline-block w-1.5 h-1.5 bg-red-400 rounded-full ml-1 mb-0.5"></span>
                            @endif
                        </p>
                        <p class="text-xs text-forest-500 mt-0.5">{{ $notif->message }}</p>
                        <p class="text-[10px] text-forest-400 mt-1.5">
                            <i class="fa-regular fa-clock text-[9px]"></i>
                            {{ $notif->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        @if($notif->url)
                        <a href="{{ $notif->url }}"
                           class="text-[10px] font-semibold text-forest-600 hover:text-forest-800
                                  bg-forest-100 hover:bg-forest-200 px-2.5 py-1 rounded-lg transition-colors">
                            Lihat
                        </a>
                        @endif
                        <form method="POST"
                              action="{{ route('pengelola.notifications.destroy', $notif) }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-7 h-7 flex items-center justify-center text-forest-400
                                           hover:text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-cream-50 border border-cream-200 rounded-2xl py-16 text-center">
            <div class="w-14 h-14 bg-cream-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-bell-slash text-forest-400 text-xl"></i>
            </div>
            <p class="font-display font-semibold text-forest-700 text-base">Tidak ada notifikasi</p>
            <p class="text-forest-400 text-xs mt-1">Semua pemberitahuan akan muncul di sini</p>
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