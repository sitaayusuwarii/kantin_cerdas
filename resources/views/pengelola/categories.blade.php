@extends('layouts.pengelola')

@section('title', 'Kelola Kategori')
@section('page-title', 'Kelola Kategori')
@section('page-subtitle', 'Tambah dan kelola kategori menu kantin')

@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <div class="bg-forest-100 text-forest-700 text-xs font-bold px-4 py-2 rounded-full">
            Total: {{ $categories->count() }}
        </div>
    </div>

    <button onclick="openModal()"
        class="btn-primary text-white font-semibold text-sm px-5 py-2.5 rounded-xl
               flex items-center gap-2 shadow-lg">
        <i class="fa-solid fa-plus text-xs"></i>
        Tambah Kategori
    </button>
</div>

{{-- TABLE --}}
<div class="bg-cream-50 rounded-2xl shadow-sm border border-cream-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-cream-100 border-b border-cream-200">
                <th class="text-left px-6 py-4">No</th>
                <th class="text-left px-6 py-4">Nama Kategori</th>
                <th class="text-center px-6 py-4">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-cream-200">
            @forelse($categories as $category)
            <tr class="hover:bg-cream-100/50 transition">
                <td class="px-6 py-4">
                    {{ $loop->iteration }}
                </td>

                <td class="px-6 py-4 font-semibold text-forest-800">
                    {{ $category->name }}
                </td>

                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">

                        {{-- EDIT --}}
                        <button
                            onclick="openEditModal(
                                {{ $category->id }},
                                '{{ addslashes($category->name) }}'
                            )"
                            class="px-4 py-2 rounded-xl bg-cream-100 hover:bg-cream-200
                                   text-forest-700 text-xs font-semibold border border-cream-200">
                            <i class="fa-solid fa-pen-to-square mr-1"></i>
                            Edit
                        </button>

                        {{-- DELETE --}}
                        <form method="POST"
                              action="{{ route('pengelola.categories.delete', $category->id) }}"
                              onsubmit="return confirm('Yakin hapus kategori ini?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="w-9 h-9 flex items-center justify-center
                                       bg-red-50 hover:bg-red-100 text-red-500 rounded-xl">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>

                    </div>
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="3" class="text-center py-12 text-forest-400">
                    Belum ada kategori
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL --}}
<div id="category-modal"
     class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4">

    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-6 py-4 bg-forest-900">
            <h3 class="text-white font-semibold" id="modal-title">
                Tambah Kategori
            </h3>

            <button onclick="closeModal()"
                class="text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form id="category-form"
              method="POST"
              action="{{ route('pengelola.categories.store') }}"
              class="p-6 space-y-4">

            @csrf

            <div id="method-field"></div>

            <div>
                <label class="block text-sm font-medium mb-2">
                    Nama Kategori
                </label>

                <input type="text"
                       name="name"
                       id="category-name"
                       placeholder="Contoh: Makanan"
                       class="w-full px-4 py-3 border border-cream-300 rounded-xl focus:outline-none focus:border-forest-500">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button"
                        onclick="closeModal()"
                        class="flex-1 py-3 border border-cream-300 rounded-xl font-semibold">
                    Batal
                </button>

                <button type="submit"
                        class="flex-1 py-3 btn-primary text-white rounded-xl font-semibold">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>

function openModal() {

    document.getElementById('modal-title').textContent =
        'Tambah Kategori';

    document.getElementById('category-form').action =
        '/pengelola/categories/store';

    document.getElementById('method-field').innerHTML = '';

    document.getElementById('category-name').value = '';

    showModal();
}

function openEditModal(id, name) {

    document.getElementById('modal-title').textContent =
        'Edit Kategori';

    document.getElementById('category-form').action =
        `/pengelola/categories/update/${id}`;

    document.getElementById('method-field').innerHTML =
        '@method("PUT")';

    document.getElementById('category-name').value =
        name;

    showModal();
}

function showModal() {

    const modal = document.getElementById('category-modal');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {

    const modal = document.getElementById('category-modal');

    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

document.getElementById('category-modal')
    .addEventListener('click', function(e) {

    if (e.target.id === 'category-modal') {
        closeModal();
    }
});

</script>
@endpush