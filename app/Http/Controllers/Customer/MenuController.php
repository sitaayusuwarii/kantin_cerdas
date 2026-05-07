<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    private const PER_PAGE = 9;

    /**
     * Tampilkan halaman daftar menu dengan fitur Search, Filter, Sort, dan Pagination.
     *
     * Data yang di-passing ke view('customer.menu'):
     * - $menus      : LengthAwarePaginator (menu dengan relasi category, sudah difilter)
     * - $categories : Collection semua kategori (untuk UI dropdown filter)
     * - $filters    : Array input filter aktif (untuk mempertahankan state form UI)
     *
     * Query Parameters:
     * - search   : string  → filter nama/deskripsi menu
     * - category : string  → filter berdasarkan slug kategori
     * - sort     : string  → 'price_asc' atau 'price_desc'
     *
     * Strategi N+1 Prevention:
     * - Satu query untuk categories (dipakai di dropdown UI)
     * - Satu query menu dengan JOIN category via with('category')
     * - Tidak ada lazy loading di blade template
     */
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. Ambil daftar kategori unik langsung dari tabel menus 
        // (karena di ERD baru, category cuma varchar, bukan tabel terpisah)
        $categories = \App\Models\Menu::select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        // 2. Query data menu
        $query = \App\Models\Menu::query();

        // 3. Filter berdasarkan pencarian nama/deskripsi (pakai scopeSearch dari Model)
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 4. Filter berdasarkan kategori (pakai scopeByCategory dari Model)
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // 5. Urutkan berdasarkan harga jika ada request sort
        if ($request->filled('sort')) {
            $query->sortByPrice($request->sort);
        }

        // 6. Eksekusi query (Hapus ->with('category') dan ganti ->available() jadi ->orderable())
        // Kita pakai paginate agar halamannya rapi kalau menunya banyak
        $menus = $query->orderable()->paginate(12);

        return view('customer.menu', compact('menus', 'categories'));
    }
}