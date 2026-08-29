<?php

namespace App\Http\Requests\Api\Tefa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name'     => ['required', 'string', 'max:255'],
            'pic_name'       => ['required', 'string', 'max:255'],
            'username'       => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', Password::min(8)],
            'stand_location' => ['nullable', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'store_name.required' => 'Nama kantin wajib diisi.',
            'pic_name.required'   => 'Nama penanggung jawab wajib diisi.',
            'username.required'   => 'Email wajib diisi.',
            'username.email'      => 'Format email tidak valid.',
            'username.unique'     => 'Email sudah terdaftar.',
            'password.required'   => 'Password wajib diisi.',
            'password.min'        => 'Password minimal 8 karakter.',
        ];
    }
}
