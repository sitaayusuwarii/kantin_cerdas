<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('pengelola.profile.edit', [
            'user' => auth()->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'photo.image'   => 'File harus berupa gambar.',
            'photo.max'     => 'Ukuran foto maksimal 2MB.',
        ]);

        $user->name  = $request->name;
        $user->phone = $request->phone;

        if ($request->hasFile('photo')) {
            // Hapus foto lama kalau ada
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            // Simpan foto baru
            $path = $request->file('photo')->store('photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        return redirect()->route('pengelola.dashboard')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}