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
            ])
            // ↓ hanya tampilkan yang sudah masuk pipeline pengiriman
            ->whereIn('status', ['diproses', 'dikirim', 'selesai'])
            // ↓ yang paling lama menunggu muncul paling atas
            ->oldest()
            ->get();

        return view('pengelola.delivery', compact('deliveries'));
    }

    public function send(Delivery $delivery)
    {
        abort_if($delivery->status !== 'diproses', 403, 'Status pengiriman tidak valid.');

        $delivery->update([
            'status'  => 'dikirim',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Pesanan sedang dikirim.');
    }

    public function complete(Delivery $delivery)
    {
        abort_if($delivery->status !== 'dikirim', 403, 'Pesanan belum dikirim.');

        $delivery->update([
            'status'       => 'selesai',
            'completed_at' => now(),
        ]);

        // ↓ Sinkronkan status order juga
        $delivery->order->update([
            'status'       => 'selesai',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Pesanan selesai.');
    }
}