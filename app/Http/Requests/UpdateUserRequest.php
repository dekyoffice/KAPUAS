<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Parameter user dari route: bisa berupa model User atau id
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id_user : $user;

        return [
            'nama_user' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,operator,viewer'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ];
    }

    /**
     * Pesan error kustom dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'nama_user.required' => 'Nama lengkap pengguna wajib diisi.',
            'nama_user.max' => 'Nama pengguna maksimal 150 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'role.required' => 'Peran pengguna (role) wajib dipilih.',
            'role.in' => 'Peran pengguna yang dipilih tidak valid.',
            'status.required' => 'Status pengguna wajib dipilih.',
            'status.in' => 'Status pengguna yang dipilih tidak valid.',
        ];
    }
}
