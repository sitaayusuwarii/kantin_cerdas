<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // =========================================================================
    // LOGIN
    // =========================================================================

    /**
     * Tampilkan form login.
     * Redirect ke halaman sesuai role jika user sudah login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     *
     * Alur:
     * 1. LoginRequest::authenticate() menangani validasi field, rate limiting,
     *    dan Auth::attempt() — controller tetap bersih (skinny controller).
     * 2. Redirect berdasarkan role setelah berhasil login.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        return $this->redirectByRole();
    }

    // =========================================================================
    // REGISTER
    // =========================================================================

    /**
     * Tampilkan form registrasi.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.register');
    }

    /**
     * Proses registrasi customer baru.
     *
     * Alur:
     * 1. RegisterRequest memvalidasi semua field (sudah otomatis).
     * 2. Buat user baru dengan role 'customer' sebagai default.
     * 3. Login otomatis.
     * 4. Redirect ke dashboard customer.
     */

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        // 1. Simpan hasil pembuatan akun ke dalam variabel $user
        $user = User::create([
            'username' => $validated['username'],
            'phone'    => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role'     => 'customer', 
        ]);

        // 2. Lakukan login otomatis menggunakan variabel $user tadi
        Auth::login($user);

        // 3. Alihkan langsung ke route 'customer.home' (beranda customer)
        return redirect()->route('customer.home')->with('success', 'Pendaftaran berhasil! Selamat datang.');
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================

    /**
     * Proses logout.
     *
     * Alur:
     * 1. Logout dari Auth guard.
     * 2. Invalidate session (hapus semua data session).
     * 3. Regenerate CSRF token untuk keamanan.
     * 4. Redirect ke login.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda berhasil logout.');
    }

    // =========================================================================
    // PRIVATE HELPER
    // =========================================================================

    /**
     * Redirect ke halaman yang sesuai berdasarkan role user yang sedang login.
     */
    private function redirectByRole(): RedirectResponse
    {
        return match (Auth::user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'pengelola' => redirect()->route('pengelola.dashboard'),
            default   => redirect()->route('customer.home'),
        };
    }
}