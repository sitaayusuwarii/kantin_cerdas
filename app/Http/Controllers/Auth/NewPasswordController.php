<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Tampilkan halaman form reset password (WAJIB ADA).
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            // Ambil token dari URL (reset-password/{token})
            'token' => $request->route('token'),
            // Ambil nomor HP dari parameter query (?phone=...)
            'phone' => $request->query('phone'),
        ]);
    }

    /**
     * Handle proses verifikasi OTP dan update password baru.
     */
   public function store(Request $request): RedirectResponse
{
    // 1. Validasi input
    $request->validate([
        'otp' => ['required', 'numeric'],
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    // Tambahkan ini untuk driver database agar session terbaru dipastikan sinkron
    $request->session()->save(); 

    // 2. Verifikasi OTP (Gunakan perbandingan longgar != dan pastikan tidak null)
    $inputOtp = $request->otp;
    $storedOtp = session('otp_secret');

    if (!$storedOtp || $inputOtp != $storedOtp) {
        return back()->withErrors(['otp' => 'Kode OTP salah atau sesi telah kadaluwarsa.']);
    }

    // 3. Cari User berdasarkan session phone
    $phone = session('otp_phone');
    $user = \App\Models\User::where('phone', $phone)->first();

    if (!$user) {
        return back()->withErrors(['otp' => 'User tidak ditemukan untuk nomor ini.']);
    }

    // 4. Update Password
    $user->forceFill([
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'remember_token' => \Illuminate\Support\Str::random(60),
    ])->save();

    // 5. Bersihkan Session
    session()->forget(['otp_secret', 'otp_phone']);

    return redirect()->route('login')->with('success_password', 'Reset password berhasil! Silakan login.');
}


}