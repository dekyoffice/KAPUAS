<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id'],
            'nama_kegiatan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kegiatans', 'nama_kegiatan')
                    ->where('program_id', $this->program_id)
                    ->ignore($this->route('kegiatan')?->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'program_id.required' => 'Program induk wajib dipilih.',
            'program_id.exists' => 'Program induk yang dipilih tidak valid.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'nama_kegiatan.unique' => 'Kegiatan ini sudah ada pada program tersebut.',
        ];
    }
}
