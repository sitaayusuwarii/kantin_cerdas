@extends('layouts.admin')
@section('title', 'Notifikasi Admin')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Semua aktivitas pembayaran & pesanan')

@section('content')

<div class="flex items-center justify-between mb-5">
    <div>
        <span class="text-stone-700 font-semibold text-sm">{{ $notifications->total() }} notifikasi</span>
        @if($notifications->total() > 0)
        <span class="ml-2 text-xs text-stone-400">· klik untuk membuka</span>
        @endif
    </div>
    <button onclick="markAllRead()"
            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-stone-200
                   text-stone-500 hover:text-red-500 hover:border-red-200 hover:bg-red-50
                   transition-all text-xs font-semibold shadow-sm">
        <i class="fa-solid fa-trash text-xs"></i> Hapus Semua
    </button>
</div>

<div class="space-y-3">
    @forelse($notifications as $notif)
@php
    $isUnread = is_null($notif->read_at);
    $iconMap = match($notif->type) {
        'payment_new'      => ['icon' => 'fa-money-bill-wave', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200'],
        'payment_received' => ['icon' => 'fa-circle-check',    'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200'],
        'unpaid_reminder'  => ['icon' => 'fa-clock',           'bg' => 'bg-amber-100',   'text' => 'text-amber-600',   'border' => 'border-amber-200'],
        'order_new'        => ['icon' => 'fa-basket-shopping', 'bg' => 'bg-blue-100',    'text' => 'text-blue-600',    'border' => 'border-blue-200'],
        default            => ['icon' => 'fa-bell',            'bg' => 'bg-orange-100',  'text' => 'text-orange-600',  'border' => 'border-orange-200'],
    };
@endphp
<form action="{{ route('admin.notifications.mark-read', $notif->id) }}" method="POST" class="block">
    @csrf
    <button type="submit" class="w-full text-left group">
        <div class="flex items-start gap-4 p-4 rounded-2xl border transition-all duration-200
            {{ $isUnread
                ? 'bg-orange-50 border-orange-200 hover:bg-orange-100 shadow-sm'
                : 'bg-white border-stone-200 hover:bg-stone-50' }}">

            {{-- Icon --}}
            <div class="w-11 h-11 rounded-xl {{ $iconMap['bg'] }} border {{ $iconMap['border'] }}
                        flex items-center justify-center flex-shrink-0 mt-0.5
                        group-hover:scale-105 transition-transform">
                <i class="fa-solid {{ $iconMap['icon'] }} {{ $iconMap['text'] }} text-sm"></i>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <p class="text-stone-800 text-sm {{ $isUnread ? 'font-bold' : 'font-semibold' }} flex items-center gap-2">
                        {{ $notif->title }}
                        @if($isUnread)
                        <span class="inline-block w-2 h-2 rounded-full bg-orange-500 flex-shrink-0"></span>
                        @endif
                    </p>
                    <span class="text-stone-400 text-xs flex-shrink-0 mt-0.5">
                        {{ $notif->created_at->diffForHumans() }}
                    </span>
                </div>
                <p class="text-stone-500 text-xs leading-relaxed">{{ $notif->message }}</p>
            </div>

            {{-- Arrow --}}
            <div class="flex-shrink-0 mt-3 opacity-0 group-hover:opacity-100 transition-opacity">
                <i class="fa-solid fa-chevron-right text-stone-300 text-xs"></i>
            </div>
        </div>
    </button>
</form>
@empty
<div class="bg-white border border-stone-200 rounded-2xl p-16 text-center">
    <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i class="fa-solid fa-bell text-orange-400 text-2xl"></i>
    </div>
    <p class="text-stone-700 font-semibold">Belum Ada Notifikasi</p>
    <p class="text-stone-400 text-sm mt-1">Notifikasi akan muncul saat ada aktivitas pembayaran.</p>
</div>
@endforelse
</div>

{{-- Pagination --}}
@if($notifications->hasPages())
<div class="flex justify-center gap-2 mt-6">
    @if(!$notifications->onFirstPage())
    <a href="{{ $notifications->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs hover:border-primary-500 transition-all">
        <i class="fa-solid fa-chevron-left mr-1"></i> Sebelumnya
    </a>
    @endif
    @if($notifications->hasMorePages())
    <a href="{{ $notifications->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 text-xs hover:border-primary-500 transition-all">
        Berikutnya <i class="fa-solid fa-chevron-right ml-1"></i>
    </a>
    @endif
</div>
@endif

@push('scripts')
<script>
function markAllRead() {
    fetch('{{ route('admin.notifications.read-all') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.reload());
}
</script>
@endpush

@endsection