<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Services\NotificationService;

class MenuController extends Controller
{
  public function index()
    {
        $menus = Menu::with('category')->latest()->get();

        $categories = Category::all();

        return view('pengelola.menu-management', compact('menus', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'description' => 'nullable',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'category_id'    => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

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
            'category_id'     => $request->category_id,
            'image'        => $image,
            'seller_id'    => Auth::id(),
            'is_available' => $request->is_available ?? true,
        ]);

        return redirect()
            ->route('pengelola.menu-management') // ✅ fix
            ->with('success', 'Menu berhasil ditambahkan');
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name'        => 'required',
            'description' => 'nullable',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'category_id'    => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $image = $menu->image;
        if ($request->hasFile('image')) {
            // hapus gambar lama
            if ($image) Storage::disk('public')->delete($image);
            $image = $request->file('image')->store('menus', 'public');
        }

        $menu->update([
            'name'         => $request->name,
            'slug'         => Str::slug($request->name),
            'description'  => $request->description,
            'price'        => $request->price,
            'stock'        => $request->stock,
            'category_id'     => $request->category_id,
            'image'        => $image,
            'is_available' => $request->is_available,
        ]);

        if ($menu->stock <= 5) {
        NotificationService::stokHabis($menu->name);
        }

        return redirect()
            ->route('pengelola.menu-management') // ✅ fix
            ->with('success', 'Menu berhasil diupdate');
    }

    public function destroy(Menu $menu)
    {
        // hapus gambar saat delete
        if ($menu->image) Storage::disk('public')->delete($menu->image);
        
        $menu->delete();

        return redirect()
            ->route('pengelola.menu-management') // ✅ fix
            ->with('success', 'Menu berhasil dihapus');
    }

    public function toggle(Menu $menu)
{
    $menu->update([
        'is_available' => !$menu->is_available
    ]);

    return response()->json([
        'success'      => true,
        'is_available' => $menu->is_available
    ]);
}

// --- UNTUK HALAMAN CUSTOMER (TAMPILKAN HANYA YANG TERSEDIA) ---
public function customerMenu()
{
    $menus = Menu::with('category')
        ->where('is_available', true)
        ->where('stock', '>', 0)
        ->latest()
        ->get();

    $categories = Category::all();

    return view('customer.menu', compact('menus', 'categories'));
}
}