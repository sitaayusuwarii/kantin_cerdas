<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
  public function store(Request $request): RedirectResponse
{
    // 🔧 1. Normalisasi nomor HP
   $phone = preg_replace('/[^0-9]/', '', $request->phone);

    // Jika mulai dengan 0 → ganti ke 62
    if (str_starts_with($phone, '0')) {
        $phone = '62' . substr($phone, 1);
    }
    // Jika mulai dengan 8 → tambahkan 62 di depan
    elseif (str_starts_with($phone, '8')) {
        $phone = '62' . $phone;
    }
    // Jika sudah 62 → biarkan

    $request->merge([
        'phone' => $phone
    ]);

    // 🔧 3. Validasi
    $request->validate([
        'username' => ['required', 'string', 'max:255', 'unique:users,username'],
        'phone' => [
        'required',
        'regex:/^62[0-9]{8,13}$/',
        'unique:users,phone'
    ],
        'password' => ['required', 'confirmed'],
    ]);

    // 🔧 4. Simpan user
    $user = User::create([
        'full_name' => $request->username,  
        'username' => $request->username,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'role' => 'customer',
    ]);

        return redirect()->route('login')
        ->with('success', 'Akun berhasil dibuat, silakan login');   

    }
}
