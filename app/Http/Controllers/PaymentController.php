<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\NotificationService;
use App\Models\PaymentMethod;

class PaymentController extends Controller
{
    /**
     * GET /payment
     * Halaman form upload bukti bayar.
     * Bisa pre-fill order_number dari query: /payment?order=SC-006
     */
    public function index(Request $request): View
{
    $order = null;
    if ($request->filled('order')) {
        $order = Order::with('items.menu')
                      ->where('order_number', $request->order)
                      ->where('user_id', auth()->id())
                      ->first();
    }

    $paymentMethods = PaymentMethod::where('is_active', true)->get(); // ← add this

    return view('customer.payment', compact('order', 'paymentMethods')); // ← add to compact
}

    /**
     * POST /payment/upload
     * Simpan bukti pembayaran ke database + storage.
     */
    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'order_number' => ['required', 'string', 'exists:orders,order_number'],
            'amount'       => ['required', 'numeric', 'min:1000'],
            'method'       => ['required', 'in:transfer_bri,transfer_bca,transfer_mandiri,gopay,ovo,dana,tunai'],
            'proof'        => ['required', 'file', 'image', 'max:5120'], // max 5MB
            'note'         => ['nullable', 'string', 'max:300'],
        ], [
            'order_number.exists' => 'Nomor pesanan tidak ditemukan.',
            'proof.required'      => 'Bukti pembayaran wajib diupload.',
            'proof.image'         => 'File harus berupa gambar (JPG, PNG).',
            'proof.max'           => 'Ukuran file maksimal 5MB.',
        ]);

        // Pastikan order milik user yang login
        $order = Order::where('order_number', $request->order_number)
                      ->where('user_id', auth()->id())
                      ->firstOrFail();

        // Upload file ke storage/app/public/payments/
        $path = $request->file('proof')->store('payments', 'public');

        // Simpan payment record
        Payment::create([
            'order_id'   => $order->id,
            'user_id'    => auth()->id(),
            'amount'     => $request->amount,
            'method'     => $request->method,
            'proof_path' => $path,
            'status'     => Payment::STATUS_MENUNGGU,
            'note'       => $request->note,
        ]);

        NotificationService::pembayaranBaru($order->order_number);

        return redirect()->route('customer.history')
                         ->with('success', 'Bukti pembayaran berhasil dikirim! Kami akan memverifikasi dalam 1×24 jam.');
    }
}
