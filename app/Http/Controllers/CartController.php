<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = Cart::with('items.menu')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        return view('customer.cart', compact('cart'));
    }
    
    public function add(Request $request, int $menuId): RedirectResponse
    {
        $menu = Menu::findOrFail($menuId);

        $cart = Cart::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'status' => 'active',
            ]
        );

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('menu_id', $menu->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += 1;
            $cartItem->subtotal = $cartItem->quantity * $menu->price;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'menu_id' => $menu->id,
                'quantity' => 1,
                'subtotal' => $menu->price,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Menu berhasil ditambahkan ke keranjang');
    }

    /**
     * Update qty
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $item = CartItem::findOrFail($id);

        $item->quantity = $request->quantity;
        $item->subtotal = $item->quantity * $item->menu->price;
        $item->save();

        return redirect()->back();
    }

    /**
     * Hapus item cart
     */
    public function remove(int $id): RedirectResponse
    {
        $item = CartItem::findOrFail($id);

        $item->delete();

        return redirect()->back()
            ->with('success', 'Item berhasil dihapus');
    }
}