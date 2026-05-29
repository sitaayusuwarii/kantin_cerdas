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
        'order_type'  => ['required', 'in:dine_in,takeaway,delivery'],
        'pickup'      => ['required_unless:order_type,takeaway', 'nullable', 'in:istirahat_1,istirahat_2,pulang'],
        'pickup_time' => ['required_if:order_type,takeaway', 'nullable', 'date_format:H:i'],
        'classroom'   => ['required_if:order_type,delivery', 'nullable', 'string', 'max:100'],
        'note'        => ['nullable', 'string', 'max:500'],
    ]);
 
    $user = $request->user();
 
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
            'order_type'      => $request->order_type,
            // Slot waktu (dine_in & delivery pakai ini)
            'pickup_schedule' => $request->order_type !== 'takeaway' ? $request->pickup : null,
            // Jam bebas (takeaway pakai ini)
            'pickup_time'     => $request->order_type === 'takeaway' ? $request->pickup_time : null,
            // Kelas tujuan (hanya delivery) — fallback ke users.class jika tidak diisi
            'classroom'       => $request->order_type === 'delivery'
                                    ? ($request->classroom ?: $user->class)
                                    : null,
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
 
        return $order;
    });
 
    Cache::forget('cart_' . $user->id);
 
    $redirectTo = $request->input('redirect_to');
 
    NotificationService::orderBaru($order, $user, $redirectTo === 'telegram');
 
    if ($redirectTo === 'telegram') {
        return redirect("https://t.me/KantinCerdasBot");
    }
 
    if ($redirectTo === 'payment') {
        return redirect()->route('customer.payment', ['order' => $order->order_number]);
    }
 
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
        ->today()
        ->latest()
        ->get();

    return view('pengelola.orders', [
        'orders'          => $orders,
        'totalOrders'     => $orders->count(),
        'newOrders'       => $orders->where('status', 'baru')->count(),
        'confirmedOrders' => $orders->where('status', 'dikonfirmasi')->count(),
        'processedOrders' => $orders->where('status', 'diproses')->count(),
        'completedOrders' => $orders->where('status', 'selesai')->count(),
    ]);
}

    
}