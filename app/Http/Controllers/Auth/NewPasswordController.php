<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Step 2 — Tampilkan form input OTP.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (!session('otp_phone')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp');
    }

    /**
     * Step 2 — Proses verifikasi OTP.
     * Kalau benar, set session otp_verified dan redirect ke form password.
     */
    public function verifyOtp(Request $request): RedirectResponse
{
    $request->validate([
        'otp' => ['required', 'numeric'],
    ]);

    // ← TAMBAH INI (cek expiry dulu)
    $createdAt = session('otp_created_at');
    if (!$createdAt || now()->timestamp - $createdAt > 60) {
        return back()->withErrors(['otp' => 'Kode OTP telah kadaluwarsa. Silakan kirim ulang.']);
    }

    $inputOtp  = $request->otp;
    $storedOtp = session('otp_secret');

    if (!$storedOtp || $inputOtp != $storedOtp) {
        return back()->withErrors(['otp' => 'Kode OTP salah atau sesi telah kadaluwarsa.']);
    }

    session(['otp_verified' => true]);

    return redirect()->route('password.reset.form');
}

    /**
     * Step 3 — Tampilkan form input password baru.
     * Hanya bisa diakses kalau OTP sudah diverifikasi.
     */
    public function showResetForm(Request $request): View|RedirectResponse
    {
        if (!session('otp_verified') || !session('otp_phone')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    /**
     * Step 3 — Simpan password baru.
     */
    public function store(Request $request): RedirectResponse
    {
        // Guard: harus sudah lewat verifikasi OTP
        if (!session('otp_verified') || !session('otp_phone')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $phone = session('otp_phone');
        $user  = User::where('phone', $phone)->first();

        if (!$user) {
            return back()->withErrors(['password' => 'User tidak ditemukan.']);
        }

        $user->forceFill([
            'password'       => Hash::make($request->password),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();

        // Bersihkan semua session OTP
        session()->forget(['otp_secret', 'otp_phone', 'otp_verified']);

        return redirect()->route('login')
            ->with('success_password', 'Reset password berhasil! Silakan login.');
    }
}