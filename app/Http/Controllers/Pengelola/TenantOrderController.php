<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantOrderController extends Controller
{
    private function getTenant()
    {
        return Tenant::where('user_id', Auth::id())->firstOrFail();
    }

    public function index()
    {
        $tenant = $this->getTenant();

        $orders = Order::with([
                        'user',
                        'items' => fn($q) => $q->where('tenant_id', $tenant->id)
                                              ->with('menu'),
                    ])
                    ->whereHas('items', fn($q) => $q->where('tenant_id', $tenant->id))
                    ->whereDate('created_at', today())
                    ->latest()
                    ->get();

        return view('pengelola.order-items', compact('orders', 'tenant'));
    }

    public function updateStatus(Request $request, OrderItem $orderItem)
    {
        $tenant = $this->getTenant();
        abort_if($orderItem->tenant_id !== $tenant->id, 403);

        $request->validate([
            'tenant_status' => 'required|in:baru,diproses,selesai_dimasak',
        ]);

        $orderItem->update(['tenant_status' => $request->tenant_status]);

        $order   = $orderItem->order;
        $allDone = $order->items()
                         ->where('tenant_status', '!=', 'selesai_dimasak')
                         ->doesntExist();

        if ($allDone) {
            $order->update(['status' => 'selesai_dimasak']);
        }

        return response()->json([
            'success'  => true,
            'all_done' => $allDone,
        ]);
    }
}