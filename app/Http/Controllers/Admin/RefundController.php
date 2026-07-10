<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;

class RefundController extends Controller
{
    public function index()
    {
        $refunds = RefundRequest::with(['order', 'user'])
            ->whereIn('status', ['menunggu_info', 'menunggu_transfer'])
            ->latest()
            ->get();

        return view('admin.refunds.index', compact('refunds'));
    }

    public function markComplete(RefundRequest $refund)
    {
        $refund->update([
            'status'       => 'selesai',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Refund ditandai selesai.');
    }
}