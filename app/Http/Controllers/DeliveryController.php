<?php

namespace App\Http\Controllers;

use App\Models\Delivery;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with([
            'order.user',
            'order.items.menu'
        ])->latest()->get();

        return view('pengelola.delivery', compact('deliveries'));
    }

    public function send(Delivery $delivery)
    {
        $delivery->update([
            'status' => 'dikirim',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Pesanan dikirim');
    }

    public function complete(Delivery $delivery)
    {
        $delivery->update([
            'status' => 'selesai',
            'completed_at' => now(),
        ]);

        // otomatis update order
        $delivery->order->update([
            'status' => 'selesai'
        ]);

        return back()->with('success', 'Pesanan selesai');
    }
}