<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = User::withCount('orders')
            ->orderByDesc('created_at');

        // Search
        if ($s = $request->search) {
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'ilike', "%$s%")
                ->orWhere('username', 'ilike', "%$s%")
                ->orWhere('phone', 'ilike', "%$s%")
                ->orWhere('kelas', 'ilike', "%$s%");
            });
        }

        // Filter status
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // Filter role
        if ($role = $request->role) {
            $query->where('role', $role);
        }

        $users = $query->paginate(15)->withQueryString();

        // Stats
        $totalUsers     = User::count();
        $totalSiswa     = User::where('role', 'customer')->count();
        $totalStaff     = User::where('role', 'pengelola')->count();
        $totalAdmin     = User::where('role', 'admin')->count();
        $totalSuspended = User::where('status', 'suspended')->count();

        return view('admin.kelola-user', compact(
            'users',
            'totalUsers',
            'totalSiswa',
            'totalStaff',
            'totalAdmin',
            'totalSuspended',
        ));
    }

    // ── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users',
            'phone'     => 'required|string|max:20',
            'kelas'     => 'nullable|string|max:100',
            'password'  => 'required|string|min:6',
            'role'      => ['required', Rule::in(['customer', 'pengelola', 'admin'])],
        ]);

        User::create([
            'full_name' => $data['full_name'],
            'username'  => $data['username'],
            'phone'     => $data['phone'],
            'kelas'     => $data['kelas'] ?? null,
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'status'    => 'active',
        ]);

        return back()->with('toast', ['msg' => 'User baru berhasil ditambahkan!', 'color' => 'emerald']);
    }

    // ── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'     => 'required|string|max:20',
            'kelas'     => 'nullable|string|max:100',
            'status'    => ['required', Rule::in(['active', 'suspended', 'inactive'])],
            'password'  => 'nullable|string|min:6',
        ]);

        $user->full_name = $data['full_name'];
        $user->username  = $data['username'];
        $user->phone     = $data['phone'];
        $user->kelas     = $data['kelas'] ?? null;
        $user->status    = $data['status'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('toast', ['msg' => 'Data user berhasil diperbarui.', 'color' => 'emerald']);
    }

    // ── Toggle Suspend ───────────────────────────────────────────────────────
    public function toggleSuspend(User $user)
    {
        $user->status = $user->status === 'suspended' ? 'active' : 'suspended';
        $user->save();

        $msg = $user->status === 'suspended'
            ? "{$user->full_name} berhasil disuspend."
            : "{$user->full_name} berhasil diaktifkan.";

        return back()->with('toast', ['msg' => $msg, 'color' => $user->status === 'suspended' ? 'amber' : 'emerald']);
    }

    // ── Destroy ──────────────────────────────────────────────────────────────
    public function destroy(User $user)
    {
        $name = $user->full_name;
        $user->delete();

        return back()->with('toast', ['msg' => "Akun {$name} berhasil dihapus.", 'color' => 'red']);
    }
}