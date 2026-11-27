<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $program = $this->route('program');
        $programId = is_object($program) ? $program->id : $program;

        return [
            'nama_program' => [
                'required',
                'string',
                'max:255',
                Rule::unique('programs')->where(function ($query) {
                    return $query->where('tahun_penetapan', $this->tahun_penetapan);
                })->ignore($programId),
            ],
            'tahun_penetapan' => ['required', 'integer', 'digits:4', 'min:2000', 'max:2100'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_program.required' => 'Nama program wajib diisi.',
            'nama_program.unique' => 'Program dengan nama tersebut sudah ada di tahun penetapan yang sama.',
            'tahun_penetapan.required' => 'Tahun penetapan program wajib diisi.',
            'tahun_penetapan.digits' => 'Tahun penetapan harus 4 digit angka (misal: 2026).',
        ];
    }
}
