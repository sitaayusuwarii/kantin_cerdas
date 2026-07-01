<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Services\NotificationService;

class MenuController extends Controller
{
    // Ambil tenant milik pengelola yang login
    private function getTenant()
    {
        return Tenant::where('user_id', Auth::id())->firstOrFail();
    }

    public function index()
    {
        $tenant = $this->getTenant();

        // Hanya menu milik tenant ini
        $menus = Menu::with('category')
                    ->where('tenant_id', $tenant->id)
                    ->latest()
                    ->get();

        $categories = Category::all();

        return view('pengelola.menu-management', compact('menus', 'categories', 'tenant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'description' => 'nullable',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $tenant = $this->getTenant();

        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('menus', 'public');
        }

        Menu::create([
            'name'         => $request->name,
            'slug'         => Str::slug($request->name),
            'description'  => $request->description,
            'price'        => $request->price,
            'stock'        => $request->stock,
            'category_id'  => $request->category_id,
            'image'        => $image,
            'seller_id'    => Auth::id(),
            'tenant_id'    => $tenant->id, // tambah ini
            'is_available' => $request->is_available ?? true,
        ]);

        return redirect()
            ->route('pengelola.menu-management')
            ->with('success', 'Menu berhasil ditambahkan');
    }

    public function update(Request $request, Menu $menu)
    {
        // Pastikan menu ini milik tenant yang login
        $tenant = $this->getTenant();
        abort_if($menu->tenant_id !== $tenant->id, 403);

        $request->validate([
            'name'        => 'required',
            'description' => 'nullable',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $image = $menu->image;
        if ($request->hasFile('image')) {
            if ($image) Storage::disk('public')->delete($image);
            $image = $request->file('image')->store('menus', 'public');
        }

        $menu->update([
            'name'         => $request->name,
            'slug'         => Str::slug($request->name),
            'description'  => $request->description,
            'price'        => $request->price,
            'stock'        => $request->stock,
            'category_id'  => $request->category_id,
            'image'        => $image,
            'is_available' => $request->is_available,
        ]);

        if ($menu->stock <= 5) {
            NotificationService::stokHabis($menu->name);
        }

        return redirect()
            ->route('pengelola.menu-management')
            ->with('success', 'Menu berhasil diupdate');
    }

    public function destroy(Menu $menu)
    {
        // Pastikan menu ini milik tenant yang login
        $tenant = $this->getTenant();
        abort_if($menu->tenant_id !== $tenant->id, 403);

        if ($menu->image) Storage::disk('public')->delete($menu->image);
        $menu->delete();

        return redirect()
            ->route('pengelola.menu-management')
            ->with('success', 'Menu berhasil dihapus');
    }

    public function toggle(Menu $menu)
    {
        // Pastikan menu ini milik tenant yang login
        $tenant = $this->getTenant();
        abort_if($menu->tenant_id !== $tenant->id, 403);

        $menu->update(['is_available' => !$menu->is_available]);

        return response()->json([
            'success'      => true,
            'is_available' => $menu->is_available,
        ]);
    }

    // Customer menu — tampilkan semua tenant, dengan info tenant
    public function customerMenu()
    {
        $menus = Menu::with(['category', 'tenant'])
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->latest()
            ->get();

        $categories = Category::all();

        // Untuk filter by tenant di halaman customer
        $tenants = Tenant::where('is_active', true)->get();

        return view('customer.menu', compact('menus', 'categories', 'tenants'));
    }
}