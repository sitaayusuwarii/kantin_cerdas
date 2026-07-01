<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    // List semua tenant
    public function index()
    {
        $tenants = Tenant::with('user')
                        ->latest()
                        ->paginate(10);

        return view('admin.tenants.index', compact('tenants'));
    }

    // Form buat tenant baru
    public function create()
    {
        return view('admin.tenants.create');
    }

    // Simpan tenant baru + akun pengelola sekaligus
    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'logo'             => 'nullable|image|max:2048',
            // Akun pengelola
            'username'         => 'required|string|unique:users,username',
            'full_name'        => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat akun pengelola
            $user = User::create([
                'username'  => $request->username,
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
                'password'  => Hash::make($request->password),
                'role'      => 'pengelola',
            ]);

            // 2. Upload logo jika ada
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('tenants', 'public');
            }

            // 3. Buat tenant dan hubungkan ke user
            Tenant::create([
                'user_id'     => $user->id,
                'name'        => $request->name,
                'description' => $request->description,
                'logo'        => $logoPath,
                'is_active'   => true,
            ]);
        });

        return redirect()->route('admin.tenants.index')
                        ->with('success', 'Tenant berhasil ditambahkan.');
    }

    // Form edit tenant
    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    // Update tenant
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|max:2048',
            'is_active'   => 'boolean',
            // Update akun pengelola
            'full_name'   => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'password'    => 'nullable|string|min:6|confirmed',
            'username'    => 'required|string|unique:users,username,' . $tenant->user_id,
        ]);

        DB::transaction(function () use ($request, $tenant) {
            // Update akun pengelola
            $userData = [
                'username'  => $request->username,
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $tenant->user->update($userData);

            // Update logo jika ada
            $logoPath = $tenant->logo;
            if ($request->hasFile('logo')) {
                if ($logoPath) Storage::disk('public')->delete($logoPath);
                $logoPath = $request->file('logo')->store('tenants', 'public');
            }

            // Update tenant
            $tenant->update([
                'name'        => $request->name,
                'description' => $request->description,
                'logo'        => $logoPath,
                'is_active'   => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('admin.tenants.index')
                        ->with('success', 'Tenant berhasil diupdate.');
    }

    // Hapus tenant
    public function destroy(Tenant $tenant)
    {
        DB::transaction(function () use ($tenant) {
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }

            // Hapus akun pengelola sekaligus (cascade)
            $tenant->user->delete();
            $tenant->delete();
        });

        return redirect()->route('admin.tenants.index')
                        ->with('success', 'Tenant berhasil dihapus.');
    }

    // Toggle aktif/nonaktif tenant
    public function toggleStatus(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);

        $status = $tenant->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Tenant berhasil {$status}.");
    }
}