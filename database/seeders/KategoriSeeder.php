<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            'Surat Masuk',
            'Surat Keluar',
            'Surat Pertanggungjawaban (SPJ)',
            'Kontrak & Kerjasama (MoU)',
            'Dokumen Kepegawaian',
            'Laporan Keuangan',
            'Dokumen Lainnya',
        ];

        foreach ($kategoris as $nama) {
            Kategori::firstOrCreate(['nama_kategori' => $nama]);
        }
    }
}
