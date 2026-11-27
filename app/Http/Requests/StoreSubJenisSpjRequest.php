<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubJenisSpjRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $subJenisSpj = $this->route('sub_jenis_spj');
        $id = is_object($subJenisSpj) ? $subJenisSpj->id : $subJenisSpj;

        return [
            'nama_sub_jenis_spj' => [
                'required',
                'string',
                'max:150',
                Rule::unique('sub_jenis_spjs', 'nama_sub_jenis_spj')->ignore($id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_sub_jenis_spj.required' => 'Nama sub jenis SPJ wajib diisi.',
            'nama_sub_jenis_spj.unique' => 'Nama sub jenis SPJ ini sudah terdaftar.',
        ];
    }
}
