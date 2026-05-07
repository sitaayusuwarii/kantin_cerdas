<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CheckoutRequest;
use App\Models\Cart;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    // =========================================================================
    // SHOW CART PREVIEW — Pratinjau Sebelum Checkout
    // =========================================================================

    /**
     * Tampilkan halaman pratinjau pesanan sebelum konfirmasi checkout.
     *
     * Eager Loading:
     * - items.menu: untuk tampilkan detail item beserta harga & gambar menu
     *
     * Guard:
     * - Redirect ke halaman cart jika cart kosong atau tidak ada cart aktif.
     *
     * Data ke view('customer.checkout'):
     * - $cart : Cart aktif dengan items & menu
     */
    public function showCheckout(): View|RedirectResponse
    {
        $cart = Cart::query()
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->with('items.menu')
            ->first();

        // Guard: cart tidak ada atau kosong
        if (! $cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('customer.cart')
                ->with('warning', 'Keranjang Anda kosong. Tambahkan menu terlebih dahulu.');
        }

        return view('customer.checkout', compact('cart'));
    }

    // =========================================================================
    // CHECKOUT — Proses Checkout (POST, DB::transaction)
    // =========================================================================

    /**
     * Proses checkout: buat order, order_items, payment, delivery.
     *
     * ⚠️  KEAMANAN HARGA:
     * Semua kalkulasi harga dilakukan murni di backend berdasarkan data
     * dari tabel 'menus'. Tidak ada satu pun nilai harga yang diterima dari
     * request frontend (CheckoutRequest tidak mengandung field harga).
     *
     * Alur (semua dalam satu DB::transaction):
     * 1. Ambil cart aktif user beserta items dan relasi menu-nya.
     * 2. Guard: Cart harus ada dan tidak kosong.
     * 3. Guard: Semua menu harus masih tersedia dan stok cukup.
     * 4. Hitung ulang total_price dari harga menu di DB (bukan dari cart).
     * 5. Generate order_number unik (format: INV-YYYYMMDD-XXXX).
     * 6. Insert ke tabel 'orders'.
     * 7. Insert ke tabel 'order_items' (snapshot harga + subtotal dari DB).
     * 8. Insert record awal ke tabel 'payments' (status: pending).
     * 9. Insert record ke tabel 'deliveries'.
     * 10. Increment 'total_sold' di setiap menu yang dipesan.
     * 11. Decrement 'stock' di setiap menu yang dipesan.
     * 12. Update status 'carts' menjadi 'checkout'.
     * 13. Redirect ke halaman payment dengan flash message sukses.
     *
     * @throws \Throwable jika transaksi DB gagal di tengah jalan
     */
    public function checkout(CheckoutRequest $request): RedirectResponse
    {
        $userId = Auth::id();

        // Langkah 1: Ambil cart dengan eager load (1+2 query, bukan N+1)
        $cart = Cart::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->with('items.menu') // items (1 query) → menu (1 query)
            ->first();

        // Langkah 2: Guard — Cart harus ada dan tidak kosong
        if (! $cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('customer.cart')
                ->with('warning', 'Keranjang Anda kosong.');
        }

        // Langkah 3: Guard — Validasi stok sebelum transaksi dimulai
        $stockErrors = [];
        foreach ($cart->items as $item) {
            $menu = $item->menu;

            if (! $menu->is_available) {
                $stockErrors[] = "Menu \"{$menu->name}\" sudah tidak tersedia.";
                continue;
            }

            if ($item->quantity > $menu->stock) {
                $stockErrors[] = "Stok \"{$menu->name}\" tidak mencukupi (tersisa: {$menu->stock}).";
            }
        }

        if (! empty($stockErrors)) {
            return redirect()
                ->route('customer.cart')
                ->with('error', implode(' ', $stockErrors));
        }

        try {
            $order = DB::transaction(function () use ($cart, $request, $userId): Order {

                // ─────────────────────────────────────────────────────────────
                // Langkah 4: Hitung ulang total_price dari harga menu di DB
                // ─────────────────────────────────────────────────────────────
                $totalPrice = $cart->items->sum(
                    fn ($item) => $item->quantity * (float) $item->menu->price
                );

                // ─────────────────────────────────────────────────────────────
                // Langkah 5: Generate order_number unik
                // Format: INV-20240115-0001
                // Padded dengan 4 digit, di-lock dengan DB::select untuk
                // menghindari race condition di concurrent requests
                // ─────────────────────────────────────────────────────────────
                $orderNumber = $this->generateOrderNumber();

                // ─────────────────────────────────────────────────────────────
                // Langkah 6: Insert ke tabel orders
                // ─────────────────────────────────────────────────────────────
                $order = Order::create([
                    'user_id'         => $userId,
                    'order_number'    => $orderNumber,
                    'total_price'     => $totalPrice,
                    'status'          => 'baru',           // Enum ERD
                    'pickup_schedule' => $request->pickup_schedule,
                    'note'            => $request->note,
                    'ordered_at'      => now(),
                ]);

                // ─────────────────────────────────────────────────────────────
                // Langkah 7: Insert ke tabel order_items (snapshot harga dari DB)
                // Menggunakan insert() batch untuk efisiensi (1 query saja)
                // ─────────────────────────────────────────────────────────────
                $orderItemsData = [];
                $now            = now()->toDateTimeString();

                foreach ($cart->items as $cartItem) {
                    $menu       = $cartItem->menu;
                    $unitPrice  = (float) $menu->price; // ← dari DB, bukan dari cart!
                    $subtotal   = $cartItem->quantity * $unitPrice;

                    $orderItemsData[] = [
                        'order_id'   => $order->id,
                        'menu_id'    => $menu->id,
                        'quantity'   => $cartItem->quantity,
                        'unit_price' => $unitPrice,  // Snapshot harga saat ini
                        'subtotal'   => $subtotal,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Batch insert (1 query, tidak peduli berapa banyak item)
                OrderItem::insert($orderItemsData);

                // ─────────────────────────────────────────────────────────────
                // Langkah 8: Insert record awal ke tabel payments
                // ─────────────────────────────────────────────────────────────
                Payment::create([
                    'order_id' => $order->id,
                    'amount'   => $totalPrice,
                    'status'   => 'pending',
                ]);

                // ─────────────────────────────────────────────────────────────
                // Langkah 9: Insert ke tabel deliveries
                // ─────────────────────────────────────────────────────────────
                Delivery::create([
                    'order_id'      => $order->id,
                    'delivery_type' => $request->delivery_type, // Enum: pickup, delivery
                    'address'       => $request->address,
                    'status'        => 'pending',
                ]);

                // ─────────────────────────────────────────────────────────────
                // Langkah 10 & 11: Update stock dan total_sold menu
                // Menggunakan increment/decrement langsung (atomic, menghindari race condition)
                // ─────────────────────────────────────────────────────────────
                foreach ($cart->items as $cartItem) {
                    $cartItem->menu->increment('total_sold', $cartItem->quantity);
                    $cartItem->menu->decrement('stock', $cartItem->quantity);
                }

                // ─────────────────────────────────────────────────────────────
                // Langkah 12: Tutup cart dengan ubah status ke 'checkout'
                // ─────────────────────────────────────────────────────────────
                $cart->update(['status' => 'checkout']);

                return $order;
            });

            // Langkah 13: Redirect ke halaman payment
            return redirect()
                ->route('customer.payment.index')
                ->with('success', "Pesanan {$order->order_number} berhasil dibuat! Silakan lakukan pembayaran.");

        } catch (\Throwable $e) {
            // Rollback otomatis dilakukan oleh DB::transaction()
            Log::error('Checkout failed', [
                'user_id' => Auth::id(),
                'cart_id' => $cart->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('customer.cart')
                ->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }
    }

    // =========================================================================
    // DETAIL — Detail Satu Order
    // =========================================================================

    /**
     * Tampilkan detail satu order milik customer.
     *
     * Guard: Order harus milik user yang login (where user_id).
     *
     * Eager Loading (mencegah N+1):
     * - orderItems.menu: item beserta nama, harga, gambar menu
     * - payment        : data & status pembayaran
     * - delivery       : data & status pengiriman
     *
     * Data ke view('customer.order-detail'):
     * - $order : Order lengkap dengan semua relasi
     */
    public function show(int $id): View|RedirectResponse
    {
        $order = Order::query()
            ->where('user_id', Auth::id()) // Owner check
            ->with([
                'orderItems.menu', // Nested eager load
                'payment',
                'delivery',
            ])
            ->findOrFail($id);

        return view('customer.order-detail', compact('order'));
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Generate order number unik yang aman dari race condition.
     *
     * Format : INV-20240115-0001
     * Strategy:
     * - Hitung jumlah order hari ini menggunakan whereDate() + lockForUpdate()
     *   di dalam transaction untuk mencegah dua request mendapat nomor yang sama.
     * - Padded dengan 4 digit angka (max 9999 order per hari).
     */
    private function generateOrderNumber(): string
    {
        $today = now()->format('Ymd');
        $prefix = "INV-{$today}-";

        // Lock row count untuk mencegah race condition (karena dalam transaction)
        $countToday = Order::whereDate('ordered_at', today())
            ->lockForUpdate()
            ->count();

        $sequence = str_pad((string) ($countToday + 1), 4, '0', STR_PAD_LEFT);

        return $prefix . $sequence;
    }
}