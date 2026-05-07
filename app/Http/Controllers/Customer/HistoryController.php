<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HistoryController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * Tampilkan riwayat semua pesanan milik customer yang sedang login.
     *
     * Eager Loading (N+1 Prevention):
     * - orderItems.menu : Detail item pesanan + nama & gambar menu
     *                     Penting untuk tampilkan ringkasan item di setiap baris history
     * - payment         : Status & info pembayaran (1 query untuk semua payments)
     * - delivery        : Status & info pengiriman (1 query untuk semua deliveries)
     *
     * Tanpa eager loading, untuk 10 order dengan 3 item masing-masing:
     *   = 1 + 10 + 30 + 10 + 10 = 61 query (N+1 problem)
     * Dengan eager loading:
     *   = 1 + 1 + 1 + 1 + 1 = 5 query total
     *
     * Filter opsional via query parameter 'status'.
     *
     * Data ke view('customer.history'):
     * - $orders       : LengthAwarePaginator (terurut dari terbaru)
     * - $statusFilter : Status yang sedang aktif (untuk state UI filter)
     * - $statusList   : Daftar status untuk dropdown filter
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status');

        $orders = Order::query()
            ->where('user_id', Auth::id())                    // Hanya order milik user ini
            ->with([                                           // Eager load semua relasi
                'orderItems.menu',                            // N+1 prevention: item + menu
                'payment',                                    // N+1 prevention: payment
                'delivery',                                   // N+1 prevention: delivery
            ])
            ->withStatus($statusFilter)                       // Scope: filter by status (nullable-safe)
            ->recent()                                        // Scope: order by ordered_at desc
            ->paginate(self::PER_PAGE)
            ->withQueryString();                              // Pertahankan query string di link pagination

        // Daftar status sesuai enum ERD untuk dropdown filter UI
        $statusList = [
            ''            => 'Semua Pesanan',
            'baru'        => 'Pesanan Baru',
            'diproses'    => 'Sedang Diproses',
            'selesai'     => 'Selesai',
            'dibatalkan'  => 'Dibatalkan',
        ];

        return view('customer.history', compact('orders', 'statusFilter', 'statusList'));
    }
}