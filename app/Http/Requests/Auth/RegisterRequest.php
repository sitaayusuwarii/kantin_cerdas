<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username'     => ['required', 'string', 'max:255', 'unique:users,username'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            // Jika form frontend kamu punya input konfirmasi password, tambahkan 'confirmed' di array bawah ini
            'password'     => ['required', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required'     => 'Username wajib diisi.',
            'username.unique'       => 'Username sudah terdaftar, silakan gunakan yang lain.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.unique'   => 'Nomor HP sudah terdaftar.',
            'password.required'     => 'Password wajib diisi.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.', // Berguna jika nanti memakai 'confirmed'
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'username'     => 'Username',
            'phone' => 'Nomor HP',
            'password'     => 'Password',
        ];
    }
}