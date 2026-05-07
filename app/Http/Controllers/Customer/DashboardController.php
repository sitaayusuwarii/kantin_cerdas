<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard customer.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Top 3 menu favorit user
        // PERBAIKAN: Hapus ->with('category') dan ubah ->available() jadi ->orderable()
        $favoriteMenus = $user
            ->favoriteMenus()
            ->orderable()               // Scope dari model Menu buatan Claude
            ->limit(3)
            ->get();

        // Order aktif untuk ditampilkan sebagai notifikasi/ringkasan di dashboard
        $activeOrders = Order::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'paid', 'processing'])
            ->with(['payment', 'delivery']) // Eager load relasi satu-ke-satu
            ->recent()                      // Scope: order by created_at desc
            ->limit(5)
            ->get();

        return view('customer.home', compact('user', 'favoriteMenus', 'activeOrders'));
    }
}