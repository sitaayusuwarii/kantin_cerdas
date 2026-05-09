<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Services\NotificationService; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;


class OrderController extends Controller
{
    public function index()
    {
        // Ambil cart aktif user
        $cart = Cart::with('items.menu')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong!');
        }

        $totalPrice = $cart->items->sum('subtotal');

        return view('customer.order', compact('cart', 'totalPrice'));
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'pickup' => ['required', 'in:istirahat_1,istirahat_2,pulang'],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();

        // Ambil cart aktif
        $cart = Cart::with('items.menu')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong!');
        }

        $total = $cart->items->sum('subtotal');

        $order = DB::transaction(function () use ($request, $user, $cart, $total) {

            $order = Order::create([
                'user_id'         => $user->id,
                'order_number'    => Order::generateOrderNumber(),
                'status'          => Order::STATUS_BARU,
                'pickup_schedule' => $request->pickup,
                'note'            => $request->note,
                'total_price'     => $total,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'menu_id'    => $item->menu_id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->menu->price,
                    'subtotal'   => $item->subtotal,
                ]);

                Menu::where('id', $item->menu_id)
                    ->increment('total_sold', $item->quantity);
            }

            $cart->items()->delete();
            $cart->update(['status' => 'checkout']);

            return $order; // penting
        });

        // Clear cache cart
        Cache::forget('cart_' . $user->id);

        // Kirim notifikasi ke pengelola
        NotificationService::orderBaru($order, $user);

    //    if ($user->telegram_chat_id) {
    //     (new \App\Http\Controllers\TelegramController)->sendMessage(
    //         $user->telegram_chat_id,
    //         "✅ *Pesanan Berhasil Dibuat!*\n\n" .
    //         "Pesanan *#{$order->order_number}* senilai *Rp " . number_format($order->total_price, 0, ',', '.') . "* telah diterima.\n\n" .
    //         "Silakan selesaikan pembayaran melalui menu *📋 Tagihan Saya* di bot ini, atau langsung upload bukti bayar di website.\n\n" .
    //         "Terima kasih! 🍽"
    //     );

    // }

        $redirectTo = $request->input('redirect_to');

        if ($redirectTo === 'telegram') {
            return redirect("https://t.me/KantinCerdasBot");
        }

        if ($redirectTo === 'payment') {
            return redirect()->route('customer.payment', ['order' => $order->order_number]);
        }

        // fallback jika redirect_to tidak diisi
        return redirect()->route('customer.history')
                        ->with('success', 'Pesanan berhasil dikonfirmasi!');
    }

    public function invoice(string $orderNumber): View
    {
        $order = Order::with(['user', 'items.menu.category', 'payment'])
            ->where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('customer.invoice', compact('order'));
    }

    public function management()
    {
        $orders = Order::with(['user', 'items.menu'])
            ->latest()
            ->get();

        return view('pengelola.orders', compact('orders'));
    }

    
}