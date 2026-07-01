<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Menu;
use Illuminate\Http\Request;


class CartController extends Controller
{
    // ─────────────────────────────────────────
    // HELPER: ambil cart aktif milik user
    // ─────────────────────────────────────────
    private function getActiveCart(bool $withItems = false): ?Cart
    {
        $query = Cart::where('user_id', auth()->id())
                     ->where('status', 'active');

        if ($withItems) {
            // select hanya kolom yang dibutuhkan → lebih ringan
            $query->with(['items' => function ($q) {
                $q->select('id', 'cart_id', 'menu_id', 'quantity', 'subtotal')
                  ->with(['menu' => function ($q2) {
                      $q2->select('id', 'name', 'price', 'image', 'stock');
                  }]);
            }]);
        }

        return $query->first();
    }

    // ─────────────────────────────────────────
    // Tampil halaman cart
    // ─────────────────────────────────────────
    public function index()
    {
        $cart = $this->getActiveCart(withItems: true);

        return view('customer.cart', compact('cart'));
    }

    // ─────────────────────────────────────────
    // Tambah item ke cart
    // ─────────────────────────────────────────
  public function add(Request $request)
{
    if (!auth()->check()) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $request->validate([
        'menu_id'  => 'required|exists:menus,id',
        'quantity' => 'required|integer|min:1',
    ]);

    // Ambil price saja, tidak perlu load seluruh kolom
    $menu = Menu::select('id', 'price')->findOrFail($request->menu_id);

    $cart = Cart::firstOrCreate([
        'user_id' => auth()->id(),
        'status'  => 'active',
    ]);

    $existing = CartItem::where('cart_id', $cart->id)
        ->where('menu_id', $menu->id)
        ->select('id', 'quantity')
        ->first();

    if ($existing) {
        $newQty = $existing->quantity + $request->quantity;
        $existing->update([
            'quantity' => $newQty,
            'subtotal' => $newQty * $menu->price,
        ]);
    } else {
        CartItem::create([
            'cart_id'  => $cart->id,
            'menu_id'  => $menu->id,
            'quantity' => $request->quantity,
            'subtotal' => $request->quantity * $menu->price,
        ]);
    }

    $count = CartItem::where('cart_id', $cart->id)->sum('quantity');

    return response()->json([
        'success' => true,
        'message' => 'Menu berhasil ditambahkan ke keranjang!',
        'count'   => $count,
    ]);
}

    // ─────────────────────────────────────────
    // Update quantity — form submit (fallback)
    // ─────────────────────────────────────────
    public function update(Request $request, CartItem $cartItem)
    {
        $qty = (int) $request->quantity;

        if ($qty <= 0) {
            $cartItem->delete();
        } else {
            $cartItem->load('menu:id,price');
            $cartItem->update([
                'quantity' => $qty,
                'subtotal' => $qty * $cartItem->menu->price,
            ]);
        }

        return redirect()->back();
    }

    // ─────────────────────────────────────────
    // Hapus satu item — form submit (fallback)
    // ─────────────────────────────────────────
    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();
        return redirect()->back()->with('success', 'Item dihapus.');
    }

    // ─────────────────────────────────────────
    // AJAX: hapus item
    // ─────────────────────────────────────────
    // CartController.php
    public function removeAjax($id)
    {
        $item = CartItem::where('id', $id)
                        ->where('user_id', auth()->id()) // pastikan milik user ini
                        ->firstOrFail();
        $item->delete();

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────
    // AJAX: data cart untuk drawer menu page
    // ─────────────────────────────────────────
    public function data()
    {
        if (!auth()->check()) {
        return response()->json([
            'items'       => [],
            'total_qty'   => 0,
            'total_price' => 0,
        ]);
    }
    
        $cart = $this->getActiveCart(withItems: true);

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'items'       => [],
                'total_qty'   => 0,
                'total_price' => 0,
            ]);
        }

        $items = $cart->items->map(fn($item) => [
            'cart_item_id' => $item->id,
            'menu_id'      => $item->menu_id,
            'name'         => $item->menu->name,
            'price'        => $item->menu->price,
            'qty'          => $item->quantity,
            'subtotal'     => $item->subtotal,
            'stock'        => $item->menu->stock,
            'image'        => $item->menu->image
                                ? asset('storage/' . $item->menu->image)
                                : null,
        ]);

        return response()->json([
            'items'       => $items,
            'total_qty'   => $cart->items->sum('quantity'), // dari collection, bukan query baru
            'total_price' => $cart->items->sum('subtotal'),
        ]);
    }

    // ─────────────────────────────────────────
    // AJAX: update qty dari drawer / cart page
    // ─────────────────────────────────────────
    public function updateAjax(Request $request, CartItem $cartItem)
    {
        // Load relasi yang dibutuhkan saja — 1 query
        $cartItem->load(['cart:id,user_id', 'menu:id,price']);

        if ($cartItem->cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cartId = $cartItem->cart_id;
        $qty    = (int) $request->quantity;

        if ($qty <= 0) {
            $cartItem->delete();
        } else {
            $cartItem->update([
                'quantity' => $qty,
                'subtotal' => $qty * $cartItem->menu->price,
            ]);
        }

        // Agregat DB — 1 query, tidak load semua item
        $totalQty   = CartItem::where('cart_id', $cartId)->sum('quantity');
        $totalPrice = CartItem::where('cart_id', $cartId)->sum('subtotal');

        return response()->json([
            'success'     => true,
            'total_qty'   => $totalQty,
            'total_price' => $totalPrice,
        ]);
    }

    // ─────────────────────────────────────────
    // AJAX: kosongkan cart
    // ─────────────────────────────────────────
    public function clear()
    {
        $cart = Cart::select('id')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if ($cart) {
            CartItem::where('cart_id', $cart->id)->delete(); // delete massal, 1 query
        }

        return response()->json(['success' => true]);
    }
}