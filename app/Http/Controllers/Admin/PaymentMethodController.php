<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::orderBy('sort_order')->get();
        return view('admin.payment-methods', compact('methods'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'account_name'   => 'nullable|string|max:100',
            'instructions'   => 'nullable|string|max:500',
            'qris_image'     => 'nullable|image|max:2048',
            'is_active'      => 'boolean',
            'sort_order'     => 'integer',
        ]);

        $data = $request->only(['name', 'account_number', 'account_name', 'instructions', 'sort_order']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('qris_image')) {
            if ($paymentMethod->qris_image) {
                Storage::disk('public')->delete($paymentMethod->qris_image);
            }
            $data['qris_image'] = $request->file('qris_image')->store('qris', 'public');
        }

        $paymentMethod->update($data);

        return response()->json(['success' => true, 'message' => 'Metode pembayaran diperbarui.']);
    }

    public function toggleActive(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);
        return response()->json(['success' => true, 'is_active' => $paymentMethod->is_active]);
    }

    public function store(Request $request)
{
    $request->validate([
        'code'           => 'required|string|unique:payment_methods,code|alpha_dash',
        'name'           => 'required|string|max:100',
        'type'           => 'required|in:bank_transfer,ewallet,qris,cash',
        'account_number' => 'nullable|string|max:50',
        'account_name'   => 'nullable|string|max:100',
        'instructions'   => 'nullable|string|max:500',
        'qris_image'     => 'nullable|image|max:2048',
        'sort_order'     => 'integer',
    ]);

    $data = $request->only(['code', 'name', 'type', 'account_number', 'account_name', 'instructions', 'sort_order']);
    $data['is_active']  = true;
    $data['logo_icon']  = match($request->type) {
        'bank_transfer' => 'fa-solid fa-building-columns',
        'ewallet'       => 'fa-solid fa-wallet',
        'qris'          => 'fa-solid fa-qrcode',
        'cash'          => 'fa-solid fa-money-bill-wave',
        default         => 'fa-solid fa-credit-card',
    };

    if ($request->hasFile('qris_image')) {
        $data['qris_image'] = $request->file('qris_image')->store('qris', 'public');
    }

    PaymentMethod::create($data);

    return response()->json(['success' => true, 'message' => 'Metode pembayaran berhasil ditambahkan.']);
}

public function destroy(PaymentMethod $paymentMethod)
{
    if ($paymentMethod->qris_image) {
        Storage::disk('public')->delete($paymentMethod->qris_image);
    }
    $paymentMethod->delete();
    return response()->json(['success' => true, 'message' => 'Metode pembayaran dihapus.']);
}
}