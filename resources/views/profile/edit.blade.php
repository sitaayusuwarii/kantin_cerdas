@push('styles')
<style>
  /* Tombol utama — admin dark theme */
  .btn-primary {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: #ffffff;
    padding: 0.65rem 1.25rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border: none;
    cursor: pointer;
    transition: filter 0.2s, transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 14px rgba(249, 115, 22, 0.4);
}
.btn-primary:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
    box-shadow: 0 8px 22px rgba(249, 115, 22, 0.5);
}
.btn-primary:active {
    transform: translateY(0);
}
</style>
@endpush

@extends($layout)

@section('title', 'Edit Profil')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        
        <div>
            <h1 class="font-heading font-bold text-xl text-canteen-dark">Edit Profil</h1>
            <p class="text-gray-400 text-xs mt-0.5">Perbarui informasi akun kamu</p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
        <i class="fa-solid fa-circle-check flex-shrink-0"></i>{{ session('success') }}
    </div>
    @endif

  {{-- ── FOTO PROFIL ──────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">

    <h2 class="font-heading font-bold text-sm text-canteen-dark mb-4 flex items-center gap-2">
        <i class="fa-solid fa-camera text-primary-500 text-xs"></i>
        Foto Profil
    </h2>

    <div class="flex items-center gap-5">

        {{-- Avatar --}}
        <div class="relative flex-shrink-0">

            <div id="avatar-wrap"
                 class="w-20 h-20 rounded-2xl overflow-hidden bg-gradient-to-br
                        from-primary-400 to-primary-600 flex items-center justify-center shadow-md">

                {{-- Kalau ada foto --}}
                @if($user->photo)

                    <img id="photo-preview"
                         src="{{ asset('storage/' . $user->photo) }}"
                         alt="Foto profil"
                         class="w-full h-full object-cover">
                {{-- Kalau belum ada foto --}}
                @else
                    <span id="photo-initial"
                          class="font-heading font-bold text-white text-2xl">
                        {{ strtoupper(substr($user->full_name, 0, 1)) }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Upload actions --}}
        <div class="flex-1 min-w-0">

            <form method="POST"
                  action="{{ route('profile.update') }}"
                  enctype="multipart/form-data"
                  id="photo-form">

                @csrf
                @method('PUT')

                <input type="file"
                       name="photo"
                       id="photo-input"
                       accept="image/*"
                       class="hidden"
                       onchange="previewPhoto(this)">
            </form>

            <button type="button"
                    onclick="document.getElementById('photo-input').click()"
                    class="btn-primary text-white text-xs font-semibold px-4 py-2.5 rounded-xl
                           shadow flex items-center gap-2 mb-2">

                <i class="fa-solid fa-upload text-xs"></i>
                Unggah Foto
            </button>
            <p class="text-[10px] text-gray-400 leading-relaxed">
                JPG, PNG, WEBP · Maks 2MB<br>
                Foto akan tampil di navbar & profil kamu
            </p>
            {{-- Tombol hapus foto --}}
            @if($user->photo)
            <form method="POST"
                  action="{{ route('profile.photo.delete') }}"
                  class="mt-2">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Hapus foto profil?')"
                        class="text-xs text-red-400 hover:text-red-600 font-medium transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                    Hapus foto
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

    {{-- ── DATA PROFIL ──────────────────────────────── --}}
    <form method="POST" action="{{ route('profile.update') }}"
          enctype="multipart/form-data" id="main-form">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
            <h2 class="font-heading font-bold text-sm text-canteen-dark mb-5 flex items-center gap-2">
                <i class="fa-solid fa-user text-primary-500 text-xs"></i>Informasi Profil
            </h2>

            <div class="space-y-4">

                {{-- Username --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Username
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-medium">@</span>
                        <input type="text" name="username"
                               value="{{ old('username', $user->username) }}"
                               placeholder="username_kamu"
                               class="w-full pl-8 pr-4 py-3 bg-gray-50 border
                                      @error('username') border-red-400 bg-red-50 @else border-gray-200 @enderror
                                      rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary-400
                                      focus:ring-2 focus:ring-primary-100 transition-all">
                    </div>
                    @error('username')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>{{ $message }}
                    </p>
                    @enderror
                    <p class="text-[10px] text-gray-400 mt-1">Huruf, angka, titik, dan garis bawah saja</p>
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Nama Lengkap
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="full_name"
                               value="{{ old('full_name', $user->full_name) }}"
                               placeholder="Nama lengkap kamu"
                               class="w-full pl-9 pr-4 py-3 bg-gray-50 border
                                      @error('full_name') border-red-400 bg-red-50 @else border-gray-200 @enderror
                                      rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary-400
                                      focus:ring-2 focus:ring-primary-100 transition-all">
                    </div>
                    @error('full_name')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Nomor HP --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Nomor HP
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="phone"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full pl-9 pr-4 py-3 bg-gray-50 border
                                      @error('phone') border-red-400 bg-red-50 @else border-gray-200 @enderror
                                      rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary-400
                                      focus:ring-2 focus:ring-primary-100 transition-all">
                    </div>
                    @error('phone')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Kelas --}}
                @if(auth()->user()->role === 'customer')
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Kelas
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-graduation-cap absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="class"
                               value="{{ old('class', $user->class) }}"
                               placeholder="Kelas"
                               class="w-full pl-9 pr-4 py-3 bg-gray-50 border border-gray-200
                                      rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary-400
                                      focus:ring-2 focus:ring-primary-100 transition-all">
                    </div>
                </div>
                @endif

            <button type="submit"
                    class="btn-primary w-full text-white font-heading font-bold py-3.5 rounded-xl
                           shadow-lg flex items-center justify-center gap-2 text-sm mt-6">
                <i class="fa-solid fa-floppy-disk"></i>Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;

    const file  = input.files[0];
    const maxMB = 2;

    if (file.size > maxMB * 1024 * 1024) {
        alert('Ukuran file maksimal 2MB.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const wrap    = document.getElementById('avatar-wrap');
        const initial = document.getElementById('photo-initial');
        const preview = document.getElementById('photo-preview');

        // Sembunyikan inisial jika ada
        if (initial) initial.style.display = 'none';

        if (preview) {
            preview.src = e.target.result;
        } else {
            const img      = document.createElement('img');
            img.id         = 'photo-preview';
            img.src        = e.target.result;
            img.alt        = 'Preview';
            img.className  = 'w-full h-full object-cover';
            wrap.appendChild(img);
        }

        // Pindahkan input file ke form utama & submit otomatis
        document.getElementById('main-form').appendChild(input);
        // Tombol simpan sudah ada — user tetap bisa submit manual
    };
    reader.readAsDataURL(file);
}
</script>
@endpush