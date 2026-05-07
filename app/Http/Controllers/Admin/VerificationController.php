<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // ── Summary Stats ────────────────────────────────────────────────
        $summary = [
            'menunggu'      => Payment::where('status', 'menunggu')->count(),
            'verified_today' => Payment::where('status', 'terverifikasi')
                                ->whereDate('verified_at', $today)->count(),
            'rejected_today' => Payment::where('status', 'ditolak')
                                ->whereDate('updated_at', $today)->count(),
            'total_verified_amount' => Payment::where('status', 'terverifikasi')
                                ->whereDate('verified_at', $today)->sum('amount'),
        ];

        // ── Payments list ────────────────────────────────────────────────
        $query = Payment::with([
                'user:id,full_name,kelas,class,phone',
                'order:id,order_number,total_price,note',
                'order.items.menu:id,name',
            ])
            ->latest();

        // Filter status
        $statusFilter = $request->get('status', 'menunggu');
        if ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', fn($o) => $o->where('order_number', 'ilike', "%{$search}%"))
                  ->orWhereHas('user',  fn($u) => $u->where('full_name',   'ilike', "%{$search}%"));
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('admin.verification', compact('summary', 'payments', 'statusFilter'));
    }

    /**
     * Verifikasi pembayaran → status: terverifikasi
     */
    public function verify(Payment $payment)
    {
        if ($payment->status !== 'menunggu') {
            return response()->json(['success' => false, 'message' => 'Pembayaran ini sudah diproses.'], 422);
        }

        $payment->update([
            'status'      => 'terverifikasi',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        // ↓ Admin hanya ubah status ke "pembayaran_terverifikasi"
        //   Pengelola kantin yang nanti klik "Terima" untuk konfirmasi
        $payment->order?->update([
            'status' => 'pembayaran_terverifikasi',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diverifikasi.',
        ]);
    }

    /**
     * Tolak pembayaran → status: ditolak + simpan alasan
     */
    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min'      => 'Alasan minimal 5 karakter.',
        ]);

        if ($payment->status !== 'menunggu') {
            return response()->json(['success' => false, 'message' => 'Pembayaran ini sudah diproses.'], 422);
        }

        $payment->update([
            'status'           => 'ditolak',
            'rejection_reason' => $request->rejection_reason,
            'verified_by'      => Auth::id(),
        ]);

        // Update status order jadi dibatalkan
        $payment->order?->update([
            'status'       => 'dibatalkan',
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil ditolak.',
        ]);
    }
}