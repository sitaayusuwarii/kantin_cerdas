<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminMenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['category', 'tenant'])
            ->latest()
            ->get();

        $categories = Category::all();
        $tenants = Tenant::where('is_active', true)->get();

        return view('admin.menu-management', compact('menus', 'categories', 'tenants'));
    }

    public function toggle(Menu $menu)
    {
        $menu->is_available = !$menu->is_available;
        $menu->save();

        return response()->json([
            'success' => true,
            'message' => 'Status menu berhasil diubah.',
            'is_available' => $menu->is_available ? 1 : 0,
        ]);
    }

    public function destroy(Menu $menu)
    {
        DB::transaction(function () use ($menu) {
            OrderItem::where('menu_id', $menu->id)->delete();

            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }

            $menu->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus.',
        ]);
    }
}