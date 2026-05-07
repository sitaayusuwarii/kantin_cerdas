<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    // =========================================================================
    // INDEX — Daftar Tagihan Pending Milik Customer
    // =========================================================================

    /**
     * Tampilkan daftar order yang pembayarannya masih 'pending'.
     *
     * Alur:
     * 1. Ambil semua payment milik user yang login dengan status 'pending'.
     * 2. Eager load relasi 'order' untuk tampilkan nomor & total order.
     * 3. Filter lewat whereHas agar hanya payment milik order user ini.
     *
     * Eager Loading:
     * - order: untuk tampilkan order_number dan total_price di UI
     *
     * Data ke view('customer.payment'):
     * - $pendingPayments : Collection payment yang belum dibayar/diverifikasi
     */
    public function index(): View
    {
        // Query payment pending milik user yang login
        // Gunakan whereHas untuk validasi kepemilikan lewat relasi order
        $pendingPayments = Payment::query()
            ->where('status', 'pending')
            ->whereHas(
                'order',
                fn ($q) => $q->where('user_id', Auth::id()) // Owner check via relasi
            )
            ->with('order') // Eager load: tampilkan detail order di UI
            ->latest()
            ->get();

        return view('customer.payment', compact('pendingPayments'));
    }

    // =========================================================================
    // SHOW FORM — Form Upload Bukti Bayar untuk 1 Order
    // =========================================================================

    /**
     * Tampilkan form upload bukti pembayaran untuk satu order spesifik.
     *
     * Guard:
     * 1. Order harus milik user yang login.
     * 2. Payment harus berstatus 'pending' atau 'rejected'
     *    (rejected = boleh re-upload setelah ditolak admin).
     * 3. Jika sudah 'accepted', redirect ke halaman detail order.
     *
     * Data ke view('customer.payment-upload'):
     * - $order   : Data order
     * - $payment : Data payment (berisi alasan penolakan jika rejected)
     */
    public function show(int $orderId): View|RedirectResponse
    {
        // Guard 1: Order harus milik user yang login
        $order = Order::query()
            ->where('user_id', Auth::id())
            ->with(['payment', 'orderItems.menu'])
            ->findOrFail($orderId);

        $payment = $order->payment;

        // Guard 2: Harus ada payment record
        if (! $payment) {
            return redirect()
                ->route('customer.history')
                ->with('error', 'Data pembayaran tidak ditemukan.');
        }

        // Guard 3: Jika sudah accepted, arahkan ke detail order
        if ($payment->isAccepted()) {
            return redirect()
                ->route('customer.order.show', $order->id)
                ->with('info', 'Pembayaran Anda sudah diterima.');
        }

        return view('customer.payment-upload', compact('order', 'payment'));
    }

    // =========================================================================
    // UPLOAD — Proses Upload Bukti Pembayaran (POST)
    // =========================================================================

    /**
     * Proses upload file bukti pembayaran secara aman.
     *
     * Keamanan file upload:
     * - PaymentRequest memvalidasi: file, image, mimes:jpg,jpeg,png, max:2048.
     * - Menggunakan Storage::putFile() yang auto-generate nama file UUID
     *   (mencegah path traversal dan nama file berbahaya dari client).
     * - File disimpan di 'storage/app/public/payments/' (disk 'public').
     * - Nama asli file dari client TIDAK pernah digunakan.
     *
     * Alur:
     * 1. Validasi via PaymentRequest (otomatis).
     * 2. Guard: Order harus milik user yang login.
     * 3. Guard: Payment harus berstatus 'pending' atau 'rejected'.
     * 4. Hapus file lama jika ada (mencegah file orphan di storage).
     * 5. Simpan file baru ke storage/app/public/payments/.
     * 6. Update payment record: payment_proof, payment_method, paid_at.
     * 7. Status payment TETAP 'pending' — admin yang akan memverifikasi.
     * 8. Redirect dengan pesan sukses.
     *
     * @throws \Throwable
     */
    public function upload(PaymentRequest $request, int $orderId): RedirectResponse
    {
        // Guard 1: Order harus milik user yang login
        $order = Order::query()
            ->where('user_id', Auth::id())
            ->with('payment')
            ->findOrFail($orderId);

        $payment = $order->payment;

        // Guard 2: Payment harus ada
        if (! $payment) {
            return redirect()
                ->route('customer.payment.index')
                ->with('error', 'Data pembayaran tidak ditemukan.');
        }

        // Guard 3: Hanya boleh upload jika status 'pending' atau 'rejected'
        if ($payment->isAccepted()) {
            return redirect()
                ->route('customer.order.show', $order->id)
                ->with('info', 'Pembayaran sudah diterima, tidak perlu upload ulang.');
        }

        try {
            // Langkah 4: Hapus file lama jika ada (cleanup orphan files)
            if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
                Storage::disk('public')->delete($payment->payment_proof);
            }

            // Langkah 5: Simpan file ke storage dengan nama UUID yang aman
            // store() adalah alias putFile() — auto-generate nama unik
            // Path hasil: "payments/X7k9mN2pQr1vB4wZ.jpg"
            $filePath = $request->file('payment_proof')
                ->store('payments', 'public');

            // Langkah 6 & 7: Update payment record
            $payment->update([
                'payment_method' => $request->payment_method,
                'payment_proof'  => $filePath,
                'paid_at'        => now(),
                'status'         => 'pending', // Tetap pending, admin yang verifikasi
                'admin_note'     => null,       // Reset catatan penolakan jika re-upload
            ]);

            return redirect()
                ->route('customer.history')
                ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');

        } catch (\Throwable $e) {
            Log::error('Payment upload failed', [
                'user_id'  => Auth::id(),
                'order_id' => $orderId,
                'error'    => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal mengupload bukti pembayaran. Silakan coba lagi.');
        }
    }
}