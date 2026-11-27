<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'kegiatan_id' => ['required', 'exists:kegiatans,id'],
            'nama_sub_kegiatan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_kegiatans', 'nama_sub_kegiatan')
                    ->where('kegiatan_id', $this->kegiatan_id)
                    ->ignore($this->route('subKegiatan')?->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'kegiatan_id.required' => 'Kegiatan induk wajib dipilih.',
            'kegiatan_id.exists' => 'Kegiatan induk yang dipilih tidak valid.',
            'nama_sub_kegiatan.required' => 'Nama sub kegiatan wajib diisi.',
            'nama_sub_kegiatan.unique' => 'Sub kegiatan ini sudah ada pada kegiatan tersebut.',
        ];
    }
}
