<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    // =========================================================================
    // INDEX — Tampilkan Halaman Keranjang
    // =========================================================================

    /**
     * Tampilkan isi keranjang aktif milik customer.
     *
     * Eager Loading:
     * - items.menu: Setiap cart item butuh data menu (nama, harga, gambar)
     *   Ini mencegah N+1 query di blade template.
     *
     * Data ke view('customer.cart'):
     * - $cart : Cart aktif dengan items & menu yang di-eager load, atau null
     */
    public function index(): View
    {
        // Query: ambil cart aktif user saat ini beserta semua relasinya
        // 1 query untuk cart, 1 query untuk items, 1 query untuk semua menu di items
        $cart = Cart::query()
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->with('items.menu') // Eager load: items → menu (mencegah N+1)
            ->first();

        return view('customer.cart', compact('cart'));
    }

    // =========================================================================
    // STORE — Tambah atau Update Item di Keranjang (AJAX/Fetch API)
    // =========================================================================

    /**
     * Tambahkan menu ke cart, atau update quantity jika sudah ada.
     *
     * Endpoint ini dirancang untuk dipanggil via AJAX/Fetch API dari frontend.
     * Mengembalikan JSON response agar mudah dikonsumsi JavaScript.
     *
     * Alur Logic:
     * 1. Validasi input via CartRequest (menu_id harus ada, tersedia, stok cukup).
     * 2. Cari atau buat cart aktif untuk user yang login (firstOrCreate).
     * 3. Ambil data menu dari DB untuk kalkulasi subtotal (BUKAN dari frontend!).
     *    ⚠️  Harga selalu diambil dari DB — mencegah manipulasi harga dari client.
     * 4. Cari cart_item yang sudah ada (jika menu sama, tambah quantity).
     *    Jika belum ada, buat baru.
     * 5. Hitung ulang subtotal: quantity × price (dari DB).
     * 6. Validasi final: total quantity tidak melebihi stok yang tersedia.
     * 7. Return JSON sukses dengan data cart terbaru.
     *
     * @return JsonResponse
     */
    public function store(CartRequest $request): JsonResponse
    {
        $userId   = Auth::id();
        $menuId   = $request->integer('menu_id');
        $quantity = $request->integer('quantity');
        $note     = $request->string('note')->toString();

        // Langkah 3: Ambil data menu dari DB (bukan dari request!)
        $menu = Menu::findOrFail($menuId);

        // Langkah 2: Cari cart aktif, atau buat baru jika belum ada
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId, 'status' => 'active'],
            ['user_id' => $userId, 'status' => 'active']
        );

        // Langkah 4: Cari item yang sudah ada di cart dengan menu yang sama
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('menu_id', $menuId)
            ->first();

        if ($existingItem) {
            // Item sudah ada → tambahkan quantity
            $newQuantity = $existingItem->quantity + $quantity;

            // Langkah 6: Validasi stok tidak terlampaui
            if ($newQuantity > $menu->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok menu \"{$menu->name}\" hanya tersisa {$menu->stock} item.",
                ], 422);
            }

            // Langkah 5: Hitung ulang subtotal dari DB
            $existingItem->update([
                'quantity' => $newQuantity,
                'note'     => $note ?: $existingItem->note,
                'subtotal' => $newQuantity * (float) $menu->price,
            ]);

        } else {
            // Item belum ada → buat baru
            // Langkah 6: Validasi stok
            if ($quantity > $menu->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok menu \"{$menu->name}\" hanya tersisa {$menu->stock} item.",
                ], 422);
            }

            // Langkah 5: Kalkulasi subtotal dari harga DB (bukan dari request!)
            CartItem::create([
                'cart_id'  => $cart->id,
                'menu_id'  => $menuId,
                'quantity' => $quantity,
                'note'     => $note ?: null,
                'subtotal' => $quantity * (float) $menu->price,
            ]);
        }

        // Langkah 7: Hitung total item di cart untuk update badge navbar
        $totalItems = CartItem::where('cart_id', $cart->id)->sum('quantity');

        return response()->json([
            'success'     => true,
            'message'     => "\"{$menu->name}\" berhasil ditambahkan ke keranjang.",
            'cart_count'  => (int) $totalItems,
        ]);
    }

    // =========================================================================
    // UPDATE — Update Quantity Item di Keranjang (AJAX)
    // =========================================================================

    /**
     * Update quantity satu item di cart.
     *
     * Alur:
     * 1. Pastikan cart_item milik user yang login (owner check via cart.user_id).
     * 2. Jika quantity = 0, hapus item (proxy ke destroy).
     * 3. Validasi quantity tidak melebihi stok menu.
     * 4. Hitung ulang subtotal dari harga menu di DB.
     * 5. Return JSON dengan data terbaru.
     */
    public function update(Request $request, int $itemId): JsonResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $quantity = $request->integer('quantity');

        // Guard: Pastikan item milik user yang login
        $item = CartItem::whereHas(
            'cart',
            fn ($q) => $q->where('user_id', Auth::id())->where('status', 'active')
        )->findOrFail($itemId);

        // Jika quantity 0, hapus item
        if ($quantity === 0) {
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item dihapus dari keranjang.',
                'deleted' => true,
            ]);
        }

        // Validasi stok
        $menu = $item->menu;
        if ($quantity > $menu->stock) {
            return response()->json([
                'success' => false,
                'message' => "Stok tersisa hanya {$menu->stock} item.",
            ], 422);
        }

        // Update dengan kalkulasi subtotal dari DB
        $item->update([
            'quantity' => $quantity,
            'subtotal' => $quantity * (float) $menu->price,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Keranjang diperbarui.',
            'subtotal' => $item->fresh()->formatted_subtotal,
        ]);
    }

    // =========================================================================
    // DESTROY — Hapus Item dari Keranjang (AJAX)
    // =========================================================================

    /**
     * Hapus satu item dari cart.
     * Guard: item harus milik cart yang milik user yang login.
     */
    public function destroy(int $itemId): JsonResponse
    {
        // Guard: Pastikan item milik user yang login
        $item = CartItem::whereHas(
            'cart',
            fn ($q) => $q->where('user_id', Auth::id())->where('status', 'active')
        )->findOrFail($itemId);

        $menuName = $item->menu->name;
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => "\"{$menuName}\" dihapus dari keranjang.",
        ]);
    }
}