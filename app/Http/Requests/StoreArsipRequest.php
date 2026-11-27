<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArsipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'operator']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_kategori' => ['required', 'exists:kategori,id_kategori'],
            'sub_kegiatan_id' => ['required', 'exists:sub_kegiatans,id'],
            'sub_jenis_spj_id' => ['nullable', 'exists:sub_jenis_spjs,id'],
            'tahun_arsip_id' => ['nullable', 'exists:tahun_arsips,id'],
            'no_item_arsip' => ['nullable', 'string', 'max:100'],
            'nomor_berkas' => ['nullable', 'string', 'max:150'],
            'nomor_surat' => ['nullable', 'string', 'max:150'],
            'tanggal_surat' => ['nullable', 'date'],
            'jumlah_surat' => ['nullable', 'integer', 'min:1'],
            'kode_klasifikasi' => ['nullable', 'string', 'max:100'],
            'skkad' => ['nullable', 'string', Rule::in(['Terbuka', 'Tertutup', 'Rahasia'])],
            'sub_jenis' => ['nullable', 'string', 'max:150'],
            'program' => ['nullable', 'string', 'max:255'],
            'kegiatan' => ['nullable', 'string', 'max:255'],
            'sub_kegiatan' => ['nullable', 'string', 'max:255'],
            'judul_arsip' => ['required', 'string', 'max:255'],
            'hal' => ['nullable', 'string', 'max:255'],
            'sumber' => ['nullable', 'string', 'max:255'],
            'unit_pengolah' => ['nullable', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string'],
            'tgl_upload' => ['required', 'date'],
            'google_drive_url' => ['nullable', 'url:http,https', 'max:2048', 'starts_with:https://drive.google.com/,https://docs.google.com/'],
            'file' => ['nullable', 'required_without:google_drive_url', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
        ];
    }

    /**
     * Pesan error kustom dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'id_kategori.required' => 'Kategori arsip wajib dipilih.',
            'id_kategori.exists' => 'Kategori arsip yang dipilih tidak valid.',
            'sub_kegiatan_id.required' => 'Program, Kegiatan, dan Sub Kegiatan wajib dipilih melalui panel drawer.',
            'sub_kegiatan_id.exists' => 'Sub kegiatan yang dipilih tidak valid di data master.',
            'sub_jenis_spj_id.exists' => 'Sub jenis SPJ yang dipilih tidak valid.',
            'tahun_arsip_id.exists' => 'Tahun arsip yang dipilih tidak valid.',
            'judul_arsip.required' => 'Judul arsip wajib diisi.',
            'judul_arsip.max' => 'Judul arsip tidak boleh lebih dari 255 karakter.',
            'jumlah_surat.integer' => 'Jumlah surat harus berupa bilangan bulat.',
            'jumlah_surat.min' => 'Jumlah surat minimal 1.',
            'skkad.in' => 'Pilihan SKKAAD harus salah satu dari: Terbuka, Tertutup, atau Rahasia.',
            'tgl_upload.required' => 'Tanggal arsip wajib diisi.',
            'tgl_upload.date' => 'Format tanggal arsip tidak valid.',
            'google_drive_url.url' => 'Format URL Google Drive tidak valid (harus berupa URL lengkap, contoh: https://drive.google.com/...).',
            'google_drive_url.starts_with' => 'Tautan harus berasal dari Google Drive atau Google Docs.',
            'file.required_without' => 'Silakan unggah berkas lampiran fisik atau isi tautan URL Google Drive.',
            'file.file' => 'Berkas yang diunggah harus berupa dokumen valid.',
            'file.mimes' => 'Format berkas tidak didukung. Gunakan PDF, Word, Excel, atau gambar (JPG/PNG).',
            'file.max' => 'Ukuran berkas maksimal 10 MB.',
        ];
    }
}
