<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = Order::where('user_id', $userId)
            ->with(['items.menu', 'payment'])
            ->latest();

        // Filter status
        if ($request->status && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Filter bulan
        if ($request->bulan) {
            $query->whereMonth('created_at', date('m', strtotime($request->bulan)))
                  ->whereYear('created_at', date('Y', strtotime($request->bulan)));
        }

        $orders = $query->paginate(10)->withQueryString();

        // Summary chips
        $total      = Order::where('user_id', $userId)->count();
        $selesai    = Order::where('user_id', $userId)->where('status', 'selesai')->count();
        $diproses   = Order::where('user_id', $userId)->whereIn('status', ['baru', 'diproses'])->count();
        $dibatalkan = Order::where('user_id', $userId)->where('status', 'dibatalkan')->count();

        return view('customer.history', compact(
            'orders', 'total', 'selesai', 'diproses', 'dibatalkan'
        ));
    }
}