<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan halaman kelola user
     */
    public function index(Request $request)
    {
        $query = User::query()->where('role', '!=', 'admin'); // semua non-admin, atau hapus baris ini jika mau tampil semua

        // Filter role
        if ($request->filled('role') && $request->role !== 'semua') {
            $query->where('role', $request->role);
        }

        // Search nama / email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => User::count(),
            'customer' => User::where('role', 'customer')->count(),
            'admin'    => User::whereIn('role', ['admin', 'driver'])->count(),
            'driver'   => User::where('role', 'driver')->count(),
        ];

        return view('admin.kelola-user', compact('users', 'stats'));
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => ['required', Rule::in(['admin', 'customer', 'driver'])],
        ];

        // Kelas hanya wajib jika role = customer
        if ($request->role === 'customer') {
            $rules['phone'] = 'nullable|string|max:20';
            $rules['kelas'] = 'required|string|max:100'; // simpan di kolom phone sementara, atau buat kolom baru
        }

        $validated = $request->validate($rules, [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.min'      => 'Password minimal 6 karakter.',
            'role.required'     => 'Role wajib dipilih.',
            'kelas.required'    => 'Kelas wajib diisi untuk customer.',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'phone'    => $request->role === 'customer' ? ($request->kelas ?? null) : null,
            'balance'  => 0,
        ]);

        return redirect()->route('admin.users.index')
            ->with('toast_success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Update data user
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'  => ['required', Rule::in(['admin', 'customer', 'driver'])],
        ];

        if ($request->role === 'customer') {
            $rules['kelas'] = 'required|string|max:100';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $validated = $request->validate($rules, [
            'name.required'  => 'Nama wajib diisi.',
            'email.unique'   => 'Email sudah digunakan akun lain.',
            'kelas.required' => 'Kelas wajib diisi untuk customer.',
        ]);

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
            'phone' => $request->role === 'customer' ? ($request->kelas ?? $user->phone) : $user->phone,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('toast_success', 'Data user berhasil diperbarui!');
    }

    /**
     * Toggle suspend / aktifkan — karena tidak ada kolom status,
     * kita gunakan kolom photo sebagai flag "suspended" (atau tambah kolom baru).
     * Rekomendasi: tambah kolom `status` enum('active','suspended') ke tabel.
     * Untuk sekarang kita pakai workaround dengan prefix pada photo.
     *
     * LEBIH BAIK: jalankan migration tambah kolom status lalu hapus workaround ini.
     */
    public function toggleSuspend(User $user)
    {
        // Jika sudah ada kolom status di DB, ganti dengan:
        // $user->update(['status' => $user->status === 'suspended' ? 'active' : 'suspended']);

        // Workaround sementara pakai prefix di kolom photo:
        if (str_starts_with($user->photo ?? '', 'SUSPENDED|')) {
            $user->update(['photo' => ltrim(substr($user->photo, 10), '')]);
            $msg = "{$user->name} berhasil diaktifkan.";
        } else {
            $user->update(['photo' => 'SUSPENDED|' . ($user->photo ?? '')]);
            $msg = "{$user->name} berhasil disuspend.";
        }

        return redirect()->route('admin.users.index')->with('toast_success', $msg);
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('toast_success', "Akun {$name} berhasil dihapus.");
    }
}