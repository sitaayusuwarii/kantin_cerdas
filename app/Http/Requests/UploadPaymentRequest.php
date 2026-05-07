<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentProofRequest extends FormRequest
{
    /**
     * Hanya customer yang terautentikasi yang boleh submit form ini.
     */
    public function authorize(): bool
    {
        return $this->user()?->isCustomer() ?? false;
    }

    /**
     * Aturan validasi untuk upload bukti pembayaran.
     * File dibatasi: maks 2MB, format jpg/jpeg/png/webp.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payment_method' => [
                'required',
                'string',
                'in:transfer_bca,transfer_mandiri,transfer_bri,qris',
            ],
            'proof_image' => [
                'required',
                'file',
                'image',                // Hanya file gambar
                'mimes:jpg,jpeg,png,webp',
                'max:2048',             // Maksimum 2MB (dalam kilobyte)
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_method.required' => 'Pilih metode pembayaran.',
            'payment_method.in'       => 'Metode pembayaran tidak valid.',
            'proof_image.required'    => 'Bukti pembayaran wajib diupload.',
            'proof_image.image'       => 'File harus berupa gambar.',
            'proof_image.mimes'       => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'proof_image.max'         => 'Ukuran gambar maksimum 2MB.',
        ];
    }
}