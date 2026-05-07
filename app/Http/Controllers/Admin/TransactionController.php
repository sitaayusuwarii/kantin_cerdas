<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // ── Stats (selalu dari seluruh data, bukan filter) ──────────────
        $stats = [
            'total'         => Payment::count(),
            'terverifikasi' => Payment::where('status', 'terverifikasi')->count(),
            'menunggu'      => Payment::where('status', 'menunggu')->count(),
            'ditolak'       => Payment::where('status', 'ditolak')->count(),
        ];

        // ── Query utama ─────────────────────────────────────────────────
        $query = Payment::with([
                'user:id,full_name,phone,kelas,class',
                'order:id,order_number,total_price,note',
                'order.items.menu:id,name',
            ])
            ->latest();

        // Filter: status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter: search (order_number atau nama user)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', fn($o) => $o->where('order_number', 'ilike', "%{$search}%"))
                  ->orWhereHas('user',  fn($u) => $u->where('full_name',   'ilike', "%{$search}%"));
            });
        }

        $payments = $query->paginate(10)->withQueryString();

        return view('admin.transactions', compact('stats', 'payments'));
    }

    /**
     * API endpoint untuk modal detail — return JSON
     */
    public function detail(Payment $payment)
    {
        $payment->load([
            'user:id,full_name,phone,kelas,class',
            'order.items.menu:id,name',
            'verifiedBy:id,full_name',
        ]);

        $orderItems = $payment->order?->items->map(fn($item) => [
            'name'     => $item->menu->name ?? '-',
            'quantity' => $item->quantity,
            'subtotal' => $item->subtotal,
        ]);

        return response()->json([
            'order_number'     => $payment->order?->order_number ?? '-',
            'status'           => $payment->status,
            'user_name'        => $payment->user->full_name,
            'user_class'       => $payment->user->kelas ?? $payment->user->class ?? null,
            'user_phone'       => $payment->user->phone,
            'amount'           => $payment->amount,
            'method'           => $payment->method,
            'proof_path'       => $payment->proof_path,
            'proof_url'        => $payment->proof_path
                                    ? Storage::url($payment->proof_path)
                                    : null,
            'rejection_reason' => $payment->rejection_reason,
            'note'             => $payment->note,
            'verified_by'      => $payment->verifiedBy?->full_name,
            'verified_at'      => $payment->verified_at?->format('d M Y, H:i'),
            'created_at'       => $payment->created_at->format('d M Y, H:i'),
            'order_items'      => $orderItems,
        ]);
    }

    /**
     * Export ke CSV
     */
    public function export(Request $request)
    {
        $query = Payment::with(['user:id,full_name,kelas,class', 'order:id,order_number'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', fn($o) => $o->where('order_number', 'ilike', "%{$search}%"))
                  ->orWhereHas('user',  fn($u) => $u->where('full_name',   'ilike', "%{$search}%"));
            });
        }

        $payments = $query->get();

        $methodLabels = [
            'transfer_bri'     => 'Transfer BRI',
            'transfer_bca'     => 'Transfer BCA',
            'transfer_mandiri' => 'Transfer Mandiri',
            'gopay'            => 'GoPay',
            'ovo'              => 'OVO',
            'dana'             => 'DANA',
            'tunai'            => 'Tunai',
        ];

        $statusLabels = [
            'terverifikasi' => 'Lunas',
            'menunggu'      => 'Pending',
            'ditolak'       => 'Ditolak',
        ];

        $filename = 'transaksi_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments, $methodLabels, $statusLabels) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['No. Pesanan', 'Nama', 'Kelas', 'Total', 'Metode', 'Status', 'Tanggal']);

            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->order?->order_number ?? '-',
                    $p->user->full_name,
                    $p->user->kelas ?? $p->user->class ?? '-',
                    $p->amount,
                    $methodLabels[$p->method]  ?? $p->method,
                    $statusLabels[$p->status]  ?? $p->status,
                    $p->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}