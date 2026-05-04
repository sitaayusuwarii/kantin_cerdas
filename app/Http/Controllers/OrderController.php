<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\NotificationService; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('customer.order');
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'cart'   => ['required', 'string'],
            'pickup' => ['required', 'in:istirahat_1,istirahat_2,pulang'],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        $cartItems = json_decode($request->cart, true);

        if (empty($cartItems)) {
            return back()->withErrors(['cart' => 'Keranjang kosong.']);
        }

        $menuIds = collect($cartItems)->pluck('id')->toArray();
        $menus   = Menu::available()->whereIn('id', $menuIds)->get()->keyBy('id');

        foreach ($cartItems as $item) {
            if (!isset($menus[$item['id']])) {
                return back()->withErrors([
                    'cart' => "Menu '{$item['name']}' tidak tersedia atau sudah habis.",
                ]);
            }
        }

        $total = collect($cartItems)->sum(function ($item) use ($menus) {
            return $menus[$item['id']]->price * $item['qty'];
        });

        $user  = $request->user();
        $order = null; // ← deklarasi di luar transaksi

        DB::transaction(function () use ($request, $user, $cartItems, $menus, $total, &$order) {
            // &$order pakai reference agar bisa diakses di luar

            $order = Order::create([
                'user_id'         => $user->id,
                'order_number'    => Order::generateOrderNumber(),
                'status'          => Order::STATUS_BARU,
                'pickup_schedule' => $request->pickup,
                'note'            => $request->note,
                'total_price'     => $total,
            ]);

            foreach ($cartItems as $item) {
                $menu = $menus[$item['id']];
                OrderItem::create([
                    'order_id'   => $order->id,
                    'menu_id'    => $menu->id,
                    'quantity'   => $item['qty'],
                    'unit_price' => $menu->price,
                    'subtotal'   => $menu->price * $item['qty'],
                ]);
            }

            foreach ($cartItems as $item) {
                Menu::where('id', $item['id'])
                    ->increment('total_sold', $item['qty']);
            }
        });

        // Sekarang $order sudah bisa diakses di sini
        NotificationService::orderBaru($order->order_number, $order->id);

        return redirect()->route('customer.history')
            ->with('success', 'Pesanan berhasil dibuat! Tunggu konfirmasi dari kantin.');
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