<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Hanya customer yang terautentikasi yang boleh checkout.
     */
    public function authorize(): bool
    {
        return $this->user()?->isCustomer() ?? false;
    }

    /**
     * Validasi data checkout.
     *
     * pickup_schedule wajib sesuai enum ERD: istirahat_1, istirahat_2, pulang.
     * delivery_type wajib sesuai enum ERD: pickup, delivery.
     * address hanya wajib jika delivery_type = 'delivery'.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pickup_schedule' => [
                'required',
                'string',
                'in:istirahat_1,istirahat_2,pulang',
            ],
            'delivery_type' => [
                'required',
                'string',
                'in:pickup,delivery',
            ],
            'address' => [
                // Wajib diisi jika delivery_type = 'delivery'
                'required_if:delivery_type,delivery',
                'nullable',
                'string',
                'max:255',
            ],
            'note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pickup_schedule.required' => 'Jadwal pengambilan wajib dipilih.',
            'pickup_schedule.in'       => 'Jadwal pengambilan tidak valid.',
            'delivery_type.required'   => 'Jenis pengiriman wajib dipilih.',
            'delivery_type.in'         => 'Jenis pengiriman tidak valid.',
            'address.required_if'      => 'Alamat wajib diisi untuk pengiriman.',
            'note.max'                 => 'Catatan maksimal 500 karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'pickup_schedule' => 'Jadwal Pengambilan',
            'delivery_type'   => 'Jenis Pengiriman',
            'address'         => 'Alamat Pengiriman',
            'note'            => 'Catatan',
        ];
    }
}