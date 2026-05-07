<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Hanya customer yang terautentikasi yang boleh upload bukti bayar.
     */
    public function authorize(): bool
    {
        return $this->user()?->isCustomer() ?? false;
    }

    /**
     * Validasi upload bukti pembayaran.
     *
     * Keamanan:
     * - 'image' rule memastikan file adalah gambar (lewat getimagesize).
     * - 'mimes' membatasi ekstensi ke jpg/jpeg/png saja.
     * - 'max:2048' membatasi ukuran file ke 2MB (dalam kilobyte).
     * - Kombinasi ketiganya mencegah upload file berbahaya (PHP disguised as image, dll).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payment_method' => [
                'required',
                'string',
                'max:50',
            ],
            'payment_proof' => [
                'required',
                'file',
                'image',                          // Validasi getimagesize()
                'mimes:jpg,jpeg,png',             // Hanya JPG dan PNG
                'max:2048',                       // Maksimum 2MB
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_method.required' => 'Metode pembayaran wajib diisi.',
            'payment_proof.required'  => 'Bukti pembayaran wajib diupload.',
            'payment_proof.image'     => 'File harus berupa gambar.',
            'payment_proof.mimes'     => 'Format gambar harus JPG atau PNG.',
            'payment_proof.max'       => 'Ukuran gambar maksimum 2MB.',
        ];
    }
}