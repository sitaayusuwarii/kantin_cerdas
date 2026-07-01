@extends($layout)

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')
@section('page-subtitle', 'Perbarui informasi akun kamu')

@section('content')
@php
    $displayName = $user->full_name ?? $user->name ?? $user->username ?? 'User';
    $initial = strtoupper(substr($displayName, 0, 1));
    $roleLabel = match($user->role ?? '') {
        'admin' => 'Admin',
        'pengelola' => 'Pengelola Kantin',
        'kasir' => 'Kasir',
        'customer' => 'Customer',
        default => ucfirst($user->role ?? 'User'),
    };
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 px-5 py-7 shadow-xl shadow-orange-200/50 sm:px-8 lg:px-10">
        <div class="absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.22),transparent_40%)]"></div>
        <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-6 right-10 hidden text-white/10 lg:block">
            <i class="fa-solid fa-user-gear text-[8rem]"></i>
        </div>

        <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-5">
                <div class="relative">
                    <div id="hero-avatar-wrap"
                         class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-[2rem] border-4 border-white/25 bg-white/15 text-white shadow-xl backdrop-blur">
                        @if($user->photo)
                            <img id="hero-photo-preview"
                                 src="{{ asset('storage/' . $user->photo) }}"
                                 alt="Foto profil"
                                 class="h-full w-full object-cover">
                        @else
                            <span id="hero-photo-initial" class="font-heading text-4xl font-extrabold">
                                {{ $initial }}
                            </span>
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-orange-600 shadow-lg">
                        <i class="fa-solid fa-pen text-sm"></i>
                    </div>
                </div>

                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/15 px-3 py-1.5 text-xs font-bold text-white backdrop-blur">
                        <i class="fa-solid fa-id-badge"></i>
                        {{ $roleLabel }}
                    </div>
                    <h1 class="mt-3 font-heading text-3xl font-extrabold leading-tight text-white md:text-4xl">
                        {{ $displayName }}
                    </h1>
                    <p class="mt-2 text-sm text-orange-50">
                        Kelola foto profil dan informasi akun kamu.
                    </p>
                </div>
            </div>

            <div class="rounded-3xl border border-white/20 bg-white/15 p-4 text-white backdrop-blur md:min-w-[260px]">
                <p class="text-xs font-bold uppercase tracking-wide text-orange-100">Username</p>
                <p class="mt-1 font-heading text-xl font-extrabold">
                    {{ '@' . ($user->username ?? 'username') }}
                </p>
                @if($user->phone)
                    <p class="mt-2 text-sm text-orange-100">
                        <i class="fa-solid fa-phone mr-1"></i>{{ $user->phone }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mt-6 rounded-3xl border border-emerald-100 bg-emerald-50 p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <p class="text-sm font-bold text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-[340px_1fr] lg:items-start">

        {{-- Photo Card --}}
        <aside class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6 lg:sticky lg:top-24">
            <div class="mb-5">
                <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Foto Profil</p>
                <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950">Avatar Akun</h2>
            </div>

            <div class="flex flex-col items-center text-center">
                <div id="avatar-wrap"
                     class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-400 to-orange-600 text-white shadow-xl shadow-orange-100">
                    @if($user->photo)
                        <img id="photo-preview"
                             src="{{ asset('storage/' . $user->photo) }}"
                             alt="Foto profil"
                             class="h-full w-full object-cover">
                    @else
                        <span id="photo-initial" class="font-heading text-5xl font-extrabold">
                            {{ $initial }}
                        </span>
                    @endif
                </div>

                <p class="mt-4 font-heading text-lg font-extrabold text-gray-950">{{ $displayName }}</p>
                <p class="mt-1 text-sm font-medium text-gray-500">{{ $roleLabel }}</p>

                <form method="POST"
                      action="{{ route('profile.update') }}"
                      enctype="multipart/form-data"
                      id="photo-form"
                      class="hidden">
                    @csrf
                    @method('PUT')
                    <input type="file"
                           name="photo"
                           id="photo-input"
                           accept="image/*"
                           onchange="previewPhoto(this)">
                </form>

                <button type="button"
                        onclick="document.getElementById('photo-input').click()"
                        class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 px-4 py-3 text-sm font-heading font-extrabold text-white shadow-lg shadow-orange-100 transition hover:-translate-y-0.5 hover:shadow-orange-200 active:scale-95">
                    <i class="fa-solid fa-upload"></i>
                    Pilih Foto Baru
                </button>

                <p class="mt-3 text-xs leading-5 text-gray-400">
                    Format JPG, PNG, atau WEBP. Maksimal 2MB. Setelah memilih foto, klik Simpan Perubahan.
                </p>

                @if($user->photo)
                    <button type="button"
                            onclick="document.getElementById('modal-hapus-foto').classList.remove('hidden')"
                            class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-rose-50 px-4 py-2.5 text-xs font-extrabold text-rose-500 transition hover:bg-rose-100">
                        <i class="fa-solid fa-trash-can"></i>
                        Hapus Foto
                    </button>
                @endif
            </div>
        </aside>

        {{-- Profile Form --}}
        <form method="POST"
              action="{{ route('profile.update') }}"
              enctype="multipart/form-data"
              id="main-form"
              class="rounded-[2rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
            @csrf
            @method('PUT')

            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-500">Data Akun</p>
                    <h2 class="mt-1 font-heading text-xl font-extrabold text-gray-950">Informasi Profil</h2>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-xs font-extrabold uppercase tracking-wide text-gray-600">
                        Username
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-extrabold text-gray-400">@</span>
                        <input type="text"
                               name="username"
                               value="{{ old('username', $user->username) }}"
                               placeholder="username_kamu"
                               class="w-full rounded-2xl border py-3.5 pl-9 pr-4 text-sm font-semibold text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-orange-100
                                      @error('username') border-rose-300 bg-rose-50 @else border-gray-200 bg-gray-50 focus:border-orange-300 focus:bg-white @enderror">
                    </div>
                    @error('username')
                        <p class="mt-1 text-xs text-rose-500">
                            <i class="fa-solid fa-circle-exclamation mr-1 text-[10px]"></i>{{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">Gunakan huruf, angka, titik, atau garis bawah.</p>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-extrabold uppercase tracking-wide text-gray-600">
                        Nama Lengkap
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                        <input type="text"
                               name="full_name"
                               value="{{ old('full_name', $user->full_name) }}"
                               placeholder="Nama lengkap kamu"
                               class="w-full rounded-2xl border py-3.5 pl-11 pr-4 text-sm font-semibold text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-orange-100
                                      @error('full_name') border-rose-300 bg-rose-50 @else border-gray-200 bg-gray-50 focus:border-orange-300 focus:bg-white @enderror">
                    </div>
                    @error('full_name')
                        <p class="mt-1 text-xs text-rose-500">
                            <i class="fa-solid fa-circle-exclamation mr-1 text-[10px]"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-xs font-extrabold uppercase tracking-wide text-gray-600">
                        Nomor HP
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                        <input type="text"
                               name="phone"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full rounded-2xl border py-3.5 pl-11 pr-4 text-sm font-semibold text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-orange-100
                                      @error('phone') border-rose-300 bg-rose-50 @else border-gray-200 bg-gray-50 focus:border-orange-300 focus:bg-white @enderror">
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs text-rose-500">
                            <i class="fa-solid fa-circle-exclamation mr-1 text-[10px]"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                @if(auth()->user()->role === 'customer')
                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wide text-gray-600">
                            Kelas
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-graduation-cap absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                            <input type="text"
                                   name="class"
                                   value="{{ old('class', $user->class) }}"
                                   placeholder="Contoh: XI RPL A"
                                   class="w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-11 pr-4 text-sm font-semibold text-gray-700 placeholder:text-gray-400 focus:border-orange-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-orange-100">
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6 rounded-3xl border border-orange-100 bg-orange-50 p-4">
                <div class="flex items-start gap-3">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-white text-orange-500">
                        <i class="fa-solid fa-circle-info text-sm"></i>
                    </div>
                    <p class="text-sm leading-6 text-gray-600">
                        Pastikan data profil benar. Untuk siswa, kelas akan dipakai saat memilih layanan antar ke kelas.
                    </p>
                </div>
            </div>

            <button type="submit"
                    class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 py-4 text-sm font-heading font-extrabold text-white shadow-lg shadow-orange-100 transition hover:-translate-y-0.5 hover:shadow-orange-200 active:scale-95">
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

{{-- Delete Photo Modal --}}
<div id="modal-hapus-foto"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     onclick="if(event.target === this) this.classList.add('hidden')">
    <div class="absolute inset-0 bg-black/45 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-sm rounded-[2rem] bg-white p-6 text-center shadow-2xl">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-rose-50 text-rose-500">
            <i class="fa-solid fa-trash-can text-2xl"></i>
        </div>
        <h3 class="font-heading text-xl font-extrabold text-gray-950">Hapus Foto Profil?</h3>
        <p class="mt-2 text-sm leading-6 text-gray-500">
            Foto profil akan dihapus dan diganti dengan inisial nama.
        </p>

        <div class="mt-6 grid grid-cols-2 gap-3">
            <button type="button"
                    onclick="document.getElementById('modal-hapus-foto').classList.add('hidden')"
                    class="rounded-2xl border border-gray-200 px-4 py-3 text-sm font-extrabold text-gray-500 transition hover:bg-gray-50">
                Batal
            </button>

            <form method="POST" action="{{ route('profile.photo.delete') }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full rounded-2xl bg-rose-500 px-4 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-rose-600">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const maxMB = 2;

    if (file.size > maxMB * 1024 * 1024) {
        alert('Ukuran file maksimal 2MB.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (event) {
        updateAvatarPreview('avatar-wrap', 'photo-preview', 'photo-initial', event.target.result);
        updateAvatarPreview('hero-avatar-wrap', 'hero-photo-preview', 'hero-photo-initial', event.target.result);

        const mainForm = document.getElementById('main-form');
        if (mainForm && !mainForm.contains(input)) {
            mainForm.appendChild(input);
        }
    };

    reader.readAsDataURL(file);
}

function updateAvatarPreview(wrapId, previewId, initialId, src) {
    const wrap = document.getElementById(wrapId);
    const initial = document.getElementById(initialId);
    let preview = document.getElementById(previewId);

    if (!wrap) return;
    if (initial) initial.style.display = 'none';

    if (preview) {
        preview.src = src;
        return;
    }

    preview = document.createElement('img');
    preview.id = previewId;
    preview.src = src;
    preview.alt = 'Preview foto profil';
    preview.className = 'h-full w-full object-cover';
    wrap.appendChild(preview);
}
</script>
@endpush
