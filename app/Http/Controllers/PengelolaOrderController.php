<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PengelolaOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with([
                'user',
                'items.menu.category'
            ])
            ->latest()
            ->get();

        $totalOrders = Order::count();
        $newOrders = Order::where('status', 'baru')->count();
        $confirmedOrders = Order::where('status', 'dikonfirmasi')->count();
        $processedOrders = Order::where('status', 'diproses')->count();
        $completedOrders = Order::where('status', 'selesai')->count();

        return view('pengelola.orders', compact(
            'orders',
            'totalOrders',
            'newOrders',
            'confirmedOrders',
            'processedOrders',
            'completedOrders'
        ));
    }

    public function confirm(Order $order)
{
    $order->update([
        'status' => 'dikonfirmasi',
    ]);

    Delivery::create([
        'order_id' => $order->id,
        'status' => 'diproses',
        'processed_at' => now(),
    ]);

    return back()->with('success', 'Pesanan dikonfirmasi');
}

    public function process(Order $order)
    {
        $order->update([
            'status' => Order::STATUS_DIPROSES,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Pesanan diproses');
    }

    public function complete(Order $order)
    {
        $order->update([
            'status' => Order::STATUS_SELESAI,
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Pesanan selesai');
    }

    
}