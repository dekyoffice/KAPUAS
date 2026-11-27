<?php

namespace Database\Seeders;

use App\Models\Arsip;
use App\Models\Kategori;
use App\Models\SubJenisSpj;
use App\Models\SubKegiatan;
use App\Models\TahunArsip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ArsipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $operator = User::where('role', 'operator')->first() ?? $admin;

        if (! $admin) {
            return;
        }

        $kategoris = Kategori::all()->keyBy('nama_kategori');

        // Pastikan direktori storage local arsip ada
        $sampleDir = 'arsip/'.date('Y');
        Storage::disk('local')->makeDirectory($sampleDir);

        // Buat file sample dummy agar download berhasil
        $sampleFileName = 'sample_dokumen_arsip.pdf';
        $sampleRelativePath = $sampleDir.'/'.$sampleFileName;
        if (! Storage::disk('local')->exists($sampleRelativePath)) {
            $dummyPdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000010 00000 n\n0000000053 00000 n\n0000000102 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";
            Storage::disk('local')->put($sampleRelativePath, $dummyPdfContent);
        }

        $dummyArchives = [
            [
                'no_item' => '001/SM/I/2026',
                'kode_klasifikasi' => 'OT.00.01',
                'skkad' => 'Terbuka',
                'sub_jenis' => 'Surat Edaran',
                'program' => 'Program Pembinaan Disiplin ASN',
                'kegiatan' => 'Monitoring dan Evaluasi Kedisiplinan Pegawai',
                'sub_kegiatan' => 'Penerbitan Edaran Jam Kerja',
                'judul' => 'Surat Edaran Pelaksanaan Disiplin Kerja ASN 2026',
                'hal' => 'Surat Pengantar Edaran Disiplin Kerja',
                'sumber' => 'BKPSDM Kabupaten Kapuas',
                'unit_pengolah' => 'Sub Bagian Kepegawaian',
                'keterangan' => 'Arsip aktif dalam map ordner kepegawaian',
                'kategori' => 'Surat Masuk',
                'tgl' => Carbon::create(2026, 1, 10),
                'user' => $admin->id_user,
            ],
            [
                'no_item' => '002/SK/I/2026',
                'kode_klasifikasi' => 'PR.02.00',
                'skkad' => 'Tertutup',
                'sub_jenis' => 'Surat Permohonan',
                'program' => 'Program Sarana & Prasarana Digital',
                'kegiatan' => 'Pengadaan Infrastruktur Teknologi Informasi',
                'sub_kegiatan' => 'Pengadaan PC Server dan Scanner Kearsipan',
                'judul' => 'Surat Permohonan Pengadaan Sarana Prasarana IT',
                'hal' => 'Permohonan Pengadaan Unit Scanner',
                'sumber' => 'Seksi Teknologi Informasi',
                'unit_pengolah' => 'Bidang Teknologi Informasi',
                'keterangan' => 'Sudah diajukan ke Bagian Pengadaan Barang & Jasa',
                'kategori' => 'Surat Keluar',
                'tgl' => Carbon::create(2026, 1, 18),
                'user' => $operator->id_user,
            ],
            [
                'no_item' => '003/KEU/I/2026',
                'kode_klasifikasi' => 'KU.01.03',
                'skkad' => 'Tertutup',
                'sub_jenis' => 'Laporan Akuntabilitas',
                'program' => 'Program Akuntabilitas Kinerja Instansi',
                'kegiatan' => 'Penyusunan Laporan Keuangan Tahunan',
                'sub_kegiatan' => 'Rekonsiliasi Realisasi Anggaran Belanja',
                'judul' => 'Laporan Realisasi Anggaran Triwulan IV 2025',
                'hal' => 'Penyampaian LRA Triwulan IV',
                'sumber' => 'Sub Bagian Keuangan',
                'unit_pengolah' => 'Sub Bagian Keuangan',
                'keterangan' => 'Laporan final telah ditandatangani Kepala Badan',
                'kategori' => 'Laporan Keuangan',
                'tgl' => Carbon::create(2026, 1, 25),
                'user' => $admin->id_user,
            ],
            [
                'no_item' => '004/SPJ/II/2026',
                'kode_klasifikasi' => 'KU.02.01',
                'skkad' => 'Terbuka',
                'sub_jenis' => 'Kwitansi & SPJ',
                'program' => 'Program Peningkatan Kompetensi SDM',
                'kegiatan' => 'Bimbingan Teknis Tata Kelola Kearsipan',
                'sub_kegiatan' => 'Perjalanan Dinas Bimtek Regional',
                'judul' => 'SPJ Perjalanan Dinas Bimtek Digitalisasi Arsip',
                'hal' => 'Surat Pengantar Laporan SPJ',
                'sumber' => 'Pelaksana Perjalanan Dinas',
                'unit_pengolah' => 'Sub Bagian Keuangan',
                'keterangan' => 'Dilengkapi tiket transportasi, bukti hotel, dan kwitansi',
                'kategori' => 'Surat Pertanggungjawaban (SPJ)',
                'tgl' => Carbon::create(2026, 2, 5),
                'user' => $operator->id_user,
            ],
            [
                'no_item' => '005/MOU/II/2026',
                'kode_klasifikasi' => 'HK.01.02',
                'skkad' => 'Rahasia',
                'sub_jenis' => 'Surat Perjanjian Kerja',
                'program' => 'Program Pengelolaan Jaringan Komunikasi',
                'kegiatan' => 'Pemeliharaan Akses Jaringan Internet Terpadu',
                'sub_kegiatan' => 'Kerjasama ISP Fiber Optic Bandwidth Dedikasi',
                'judul' => 'Kontrak Pemeliharaan Jaringan Internet Instansi 2026',
                'hal' => 'Perjanjian Kerjasama Penyedia Layanan Internet',
                'sumber' => 'PT Telkom Indonesia Tbk',
                'unit_pengolah' => 'Bidang Teknologi Informasi',
                'keterangan' => 'Masa berlaku kontrak 12 bulan (1 Jan - 31 Des 2026)',
                'kategori' => 'Kontrak & Kerjasama (MoU)',
                'tgl' => Carbon::create(2026, 2, 14),
                'user' => $admin->id_user,
            ],
            [
                'no_item' => '006/KEP/III/2026',
                'kode_klasifikasi' => 'KP.04.01',
                'skkad' => 'Tertutup',
                'sub_jenis' => 'Surat Keputusan',
                'program' => 'Program Tata Kelola Organisasi Kearsipan',
                'kegiatan' => 'Pembentukan Tim Kerja Pengarsipan Elektronik',
                'sub_kegiatan' => 'Penetapan Surat Keputusan Tim Pelaksana',
                'judul' => 'Surat Keputusan Pengangkatan Tim Pelaksana Pengarsipan',
                'hal' => 'SK Penetapan Tim Digitalisasi KAPUAS',
                'sumber' => 'Bagian Organisasi & Tata Laksana',
                'unit_pengolah' => 'Sub Bagian Kepegawaian',
                'keterangan' => 'Salinan resmi terdistribusi ke seluruh unit kerja',
                'kategori' => 'Dokumen Kepegawaian',
                'tgl' => Carbon::create(2026, 3, 2),
                'user' => $admin->id_user,
            ],
            [
                'no_item' => '007/SPJ/III/2026',
                'kode_klasifikasi' => 'KU.02.04',
                'skkad' => 'Terbuka',
                'sub_jenis' => 'Kwitansi Konsumsi',
                'program' => 'Program Koordinasi Antar Perangkat Daerah',
                'kegiatan' => 'Rapat Koordinasi Evaluasi Triwulan I',
                'sub_kegiatan' => 'Penyediaan Konsumsi Rapat Kerja Wilayah',
                'judul' => 'SPJ Pengadaan Konsumsi Rapat Koordinasi Wilayah',
                'hal' => 'Bukti Pembayaran Katering Rapat',
                'sumber' => 'Bendahara Pengeluaran',
                'unit_pengolah' => 'Sub Bagian Keuangan',
                'keterangan' => 'Faktur pajak dan bukti transfer bank lengkap',
                'kategori' => 'Surat Pertanggungjawaban (SPJ)',
                'tgl' => Carbon::create(2026, 3, 20),
                'user' => $operator->id_user,
            ],
        ];

        $counter = 1;
        foreach ($dummyArchives as $doc) {
            $catId = isset($kategoris[$doc['kategori']])
                ? $kategoris[$doc['kategori']]->id_kategori
                : $kategoris->first()->id_kategori;

            $year = $doc['tgl']->format('Y');
            $kode = sprintf('ARS-%s-%04d', $year, $counter++);

            $subKegiatan = SubKegiatan::inRandomOrder()->first();
            $subJenisSpj = SubJenisSpj::inRandomOrder()->first();
            $tahunArsip = TahunArsip::firstOrCreate(['tahun' => (int) $year]);

            Arsip::updateOrCreate(
                ['kode_arsip' => $kode],
                [
                    'id_user' => $doc['user'],
                    'id_kategori' => $catId,
                    'sub_kegiatan_id' => $subKegiatan ? $subKegiatan->id : null,
                    'sub_jenis_spj_id' => $subJenisSpj ? $subJenisSpj->id : null,
                    'tahun_arsip_id' => $tahunArsip ? $tahunArsip->id : null,
                    'no_item_arsip' => $doc['no_item'],
                    'kode_klasifikasi' => $doc['kode_klasifikasi'],
                    'skkad' => $doc['skkad'],
                    'sub_jenis' => $doc['sub_jenis'],
                    'program' => $subKegiatan ? $subKegiatan->kegiatan->program->nama_program : $doc['program'],
                    'kegiatan' => $subKegiatan ? $subKegiatan->kegiatan->nama_kegiatan : $doc['kegiatan'],
                    'sub_kegiatan' => $subKegiatan ? $subKegiatan->nama_sub_kegiatan : $doc['sub_kegiatan'],
                    'judul_arsip' => $doc['judul'],
                    'hal' => $doc['hal'],
                    'sumber' => $doc['sumber'],
                    'unit_pengolah' => $doc['unit_pengolah'] ?? null,
                    'keterangan' => $doc['keterangan'],
                    'file_path' => $sampleRelativePath,
                    'tgl_upload' => $doc['tgl']->format('Y-m-d'),
                ]
            );
        }

        // Sinkronisasi nomor counter terakhir di tabel arsip_counters
        DB::table('arsip_counters')->updateOrInsert(
            ['tahun' => '2026'],
            ['terakhir' => $counter - 1]
        );
    }
}
