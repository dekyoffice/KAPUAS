<?php

namespace Database\Seeders;

use App\Models\Kegiatan;
use App\Models\Program;
use App\Models\SubJenisSpj;
use App\Models\SubKegiatan;
use App\Models\TahunArsip;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tahun Arsip
        $tahunArsips = [2023, 2024, 2025, 2026];
        foreach ($tahunArsips as $thn) {
            TahunArsip::firstOrCreate(['tahun' => $thn]);
        }

        // 2. Sub Jenis SPJ
        $subJenisSpjs = [
            'SPJ Belanja Langsung',
            'SPJ Belanja Tidak Langsung',
            'SPJ Perjalanan Dinas',
            'SPJ Pengadaan Barang/Jasa',
            'SPJ Honorarium & Upah Kerja',
        ];
        foreach ($subJenisSpjs as $namaSpj) {
            SubJenisSpj::firstOrCreate(['nama_sub_jenis_spj' => $namaSpj]);
        }

        // 3. Hierarki Program, Kegiatan, Sub Kegiatan (2025 & 2026)
        $hierarkiData = [
            // Tahun 2026
            [
                'tahun' => 2026,
                'program' => 'Program Penunjang Urusan Pemerintahan Daerah',
                'kegiatans' => [
                    [
                        'nama' => 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah',
                        'sub_kegiatans' => [
                            'Penyusunan Dokumen Perencanaan Perangkat Daerah',
                            'Koordinasi dan Penyusunan DPA/DPPA-SKPD',
                            'Evaluasi Kinerja Perangkat Daerah Triwulanan',
                        ],
                    ],
                    [
                        'nama' => 'Administrasi Keuangan dan Aset Perangkat Daerah',
                        'sub_kegiatans' => [
                            'Penyediaan Gaji dan Tunjangan ASN',
                            'Koordinasi dan Pelaksanaan Akuntansi SKPD',
                            'Pengelolaan dan Penatausahaan Barang Milik Daerah',
                        ],
                    ],
                ],
            ],
            [
                'tahun' => 2026,
                'program' => 'Program Pengelolaan dan Pelestarian Kearsipan Daerah',
                'kegiatans' => [
                    [
                        'nama' => 'Pengelolaan Arsip Dinamis dan Statis Instansi',
                        'sub_kegiatans' => [
                            'Pencatatan, Pemberkasan, dan Penataan Arsip Aktif',
                            'Digitalisasi dan Alih Media Dokumen Berkas',
                            'Penyusutan dan Pemusnahan Arsip Inaktif',
                        ],
                    ],
                    [
                        'nama' => 'Peningkatan Sarana Prasarana Kearsipan Digital',
                        'sub_kegiatans' => [
                            'Pengadaan Perangkat Server dan Penyimpanan Cloud KAPUAS',
                            'Pemeliharaan Jaringan dan Keamanan Data Arsip',
                        ],
                    ],
                ],
            ],

            // Tahun 2025
            [
                'tahun' => 2025,
                'program' => 'Program Pembinaan dan Pengembangan Aparatur Sipil Negara',
                'kegiatans' => [
                    [
                        'nama' => 'Pengembangan Karir dan Disiplin Kepegawaian',
                        'sub_kegiatans' => [
                            'Bimtek Manajemen Kearsipan Elektronik',
                            'Monitoring dan Penegakan Kedisiplinan Kerja Pegawai',
                        ],
                    ],
                    [
                        'nama' => 'Pengelolaan Data dan Informasi Kepegawaian',
                        'sub_kegiatans' => [
                            'Pemutakhiran Berkas Nominatif Kenaikan Pangkat',
                            'Pendataan Arsip Pensiun dan Cuti ASN',
                        ],
                    ],
                ],
            ],
            [
                'tahun' => 2025,
                'program' => 'Program Tata Kelola Pemerintahan Berbasis Elektronik (SPBE)',
                'kegiatans' => [
                    [
                        'nama' => 'Pengembangan Sistem Aplikasi Layanan Publik dan Internal',
                        'sub_kegiatans' => [
                            'Rancang Bangun Sistem Informasi Pengarsipan KAPUAS',
                            'Integrasi Layanan Persuratan Digital Instansi',
                        ],
                    ],
                    [
                        'nama' => 'Tata Kelola Infrastruktur Teknologi Informasi',
                        'sub_kegiatans' => [
                            'Penyediaan Bandwidth Internet Instansi',
                            'Pengadaan Komputer dan Scanner Dokumen',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($hierarkiData as $progData) {
            $program = Program::firstOrCreate(
                [
                    'nama_program' => $progData['program'],
                    'tahun_penetapan' => $progData['tahun'],
                ]
            );

            foreach ($progData['kegiatans'] as $kegData) {
                $kegiatan = Kegiatan::firstOrCreate(
                    [
                        'program_id' => $program->id,
                        'nama_kegiatan' => $kegData['nama'],
                    ]
                );

                foreach ($kegData['sub_kegiatans'] as $subNama) {
                    SubKegiatan::firstOrCreate(
                        [
                            'kegiatan_id' => $kegiatan->id,
                            'nama_sub_kegiatan' => $subNama,
                        ]
                    );
                }
            }
        }
    }
}
