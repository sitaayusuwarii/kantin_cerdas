@extends('layouts.admin')
@section('title', 'Notifikasi Admin')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Semua aktivitas pembayaran & pesanan')

@section('content')

<div class="flex items-center justify-between mb-5">
    <span class="text-slate-500 text-sm">{{ $notifications->total() }} notifikasi</span>
    <button onclick="markAllRead()"
            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 border border-border text-slate-400 hover:text-white hover:border-primary-500 transition-all text-xs font-semibold">
        <i class="fa-solid fa-check-double"></i> Tandai Semua Dibaca
    </button>
</div>

<div class="space-y-3">
    @forelse($notifications as $notif)
    @php
        $isUnread = is_null($notif->read_at);
        $icon = match($notif->type) {
            'payment_received' => ['icon' => 'fa-money-bill-wave', 'color' => 'emerald'],
            'unpaid_reminder'  => ['icon' => 'fa-clock',           'color' => 'amber'],
            default            => ['icon' => 'fa-bell',            'color' => 'primary'],
        };
        $link = match($notif->type) {
            'payment_received' => route('admin.verification'),
            'unpaid_reminder'  => route('admin.unpaid-orders'),
            default            => route('admin.notifications'),
        };
    @endphp
    <a href="{{ $link }}"
       class="glass-card rounded-2xl p-4 flex items-start gap-4 hover:border-primary-500/40 transition-all block
              {{ $isUnread ? 'border-primary-500/20 bg-primary-500/5' : '' }}">

        {{-- Icon --}}
        <div class="w-10 h-10 rounded-xl bg-{{ $icon['color'] }}-500/10 border border-{{ $icon['color'] }}-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fa-solid {{ $icon['icon'] }} text-{{ $icon['color'] }}-400 text-sm"></i>
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <p class="text-white font-semibold text-sm {{ $isUnread ? 'font-bold' : '' }}">
                    {{ $notif->title }}
                    @if($isUnread)
                    <span class="inline-block w-2 h-2 rounded-full bg-primary-500 ml-1 mb-0.5"></span>
                    @endif
                </p>
                <span class="text-slate-600 text-xs flex-shrink-0">{{ $notif->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-slate-400 text-xs mt-0.5">{{ $notif->message }}</p>
        </div>
    </a>
    @empty
    <div class="glass-card rounded-2xl p-16 text-center">
        <div class="text-5xl mb-3">🔔</div>
        <p class="text-white font-semibold">Belum Ada Notifikasi</p>
        <p class="text-slate-500 text-sm mt-1">Notifikasi akan muncul saat ada aktivitas pembayaran.</p>
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