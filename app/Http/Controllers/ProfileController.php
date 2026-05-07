<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * GET /profile/edit
     */
    public function edit(): View
{
    $user   = auth()->user();
    $layout = match($user->role) {
        'pengelola' => 'layouts.pengelola',
        'admin'     => 'layouts.admin',
        default     => 'layouts.app',
    };

    return view('profile.edit', [
        'user'   => $user,
        'layout' => $layout,
    ]);
}

    /**
     * PUT /profile/update
     */
    public function update(Request $request): RedirectResponse
{
    $user = $request->user();

    $request->validate([
        'username'  => ['nullable', 'string', 'max:50', 'alpha_dash',
                        Rule::unique('users', 'username')->ignore($user->id)],
        'full_name' => ['nullable', 'string', 'max:100'],
        'phone'     => ['nullable', 'string', 'max:20'],
        'class'     => ['nullable', 'string', 'max:20'],
        'photo'     => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
    ]);

    $data = $request->only(['username', 'full_name', 'phone']);

    // Class hanya disimpan untuk customer
    if ($user->role === 'customer') {
        $data['class'] = $request->class;
    }

    // Handle upload foto
    if ($request->hasFile('photo')) {
        if ($user->photo && !str_starts_with($user->photo, 'http')) {
            Storage::disk('public')->delete($user->photo);
        }
        $data['photo'] = $request->file('photo')->store('photos', 'public');
    }

    $user->update($data);

    if ($user->role === 'customer') {
        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

    return redirect()->route('pengelola.dashboard')->with('success', 'Profil berhasil diperbarui!');
}

    /**
     * PUT /profile/password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'password.confirmed'                => 'Konfirmasi password tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Password berhasil diubah!');
    }

    /**
     * DELETE /profile/photo
     * Hapus foto profil (kembali ke avatar inisial)
     */
    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->photo && !str_starts_with($user->photo, 'http')) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->update(['photo' => null]);

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    
}