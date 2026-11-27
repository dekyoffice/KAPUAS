<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTahunArsipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $tahunArsip = $this->route('tahun_arsip');
        $id = is_object($tahunArsip) ? $tahunArsip->id : $tahunArsip;

        return [
            'tahun' => [
                'required',
                'integer',
                'digits:4',
                'min:2000',
                'max:2100',
                Rule::unique('tahun_arsips', 'tahun')->ignore($id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'tahun.required' => 'Tahun arsip wajib diisi.',
            'tahun.digits' => 'Tahun arsip harus 4 digit angka (misal: 2026).',
            'tahun.unique' => 'Tahun arsip ini sudah terdaftar di sistem.',
        ];
    }
}
