<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    /**
     * Hanya customer yang terautentikasi yang boleh memodifikasi cart.
     */
    public function authorize(): bool
    {
        return $this->user()?->isCustomer() ?? false;
    }

    /**
     * Aturan validasi untuk tambah/update item di keranjang.
     *
     * 'menu_id' harus ada di tabel menus, tersedia (is_available = true),
     * dan stoknya > 0. Validasi ini mencegah penambahan menu yang tidak valid.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'menu_id'  => [
                'required',
                'integer',
                // Validasi exists + kondisi tambahan: menu harus tersedia dan ada stok
                'exists:menus,id',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $menu = \App\Models\Menu::find($value);

                    if (! $menu) {
                        $fail('Menu tidak ditemukan.');
                        return;
                    }

                    if (! $menu->is_available) {
                        $fail("Menu \"{$menu->name}\" sedang tidak tersedia.");
                        return;
                    }

                    if ($menu->stock <= 0) {
                        $fail("Stok menu \"{$menu->name}\" sudah habis.");
                    }
                },
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:99',
            ],
            'note'     => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'menu_id.required'  => 'Menu wajib dipilih.',
            'menu_id.exists'    => 'Menu tidak ditemukan.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.integer'  => 'Jumlah harus berupa angka.',
            'quantity.min'      => 'Jumlah minimal 1.',
            'quantity.max'      => 'Jumlah maksimal 99 per item.',
            'note.max'          => 'Catatan maksimal 255 karakter.',
        ];
    }
}