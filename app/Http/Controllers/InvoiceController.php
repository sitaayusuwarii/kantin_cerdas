<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function show(string $orderNumber)
    {
        $order = Order::with(['user', 'items.menu', 'payment'])
            ->where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('customer.invoice', compact('order'));
    }

    // Kalau dari menu /invoice tanpa order number — tampil invoice terbaru
    public function latest()
    {
        $order = Order::with(['user', 'items.menu', 'payment'])
            ->where('user_id', auth()->id())
            ->latest()
            ->firstOrFail();

        return view('customer.invoice', compact('order'));
    }
}