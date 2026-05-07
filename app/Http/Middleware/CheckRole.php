<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verifikasi role user sebelum mengakses route yang dilindungi.
     *
     * Alur:
     * 1. Cek autentikasi → redirect login jika belum login
     * 2. Ambil daftar role yang diizinkan dari parameter middleware
     * 3. Cek role user → abort 403 jika tidak sesuai
     * 4. Lanjutkan request jika sesuai
     *
     * Penggunaan di routes:
     *   middleware('role:customer')
     *   middleware('role:admin,pengelola')  ← multi-role
     *
     * @param  Closure(Request): Response  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! in_array($request->user()->role, $roles, strict: true)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}