<?php

namespace App\Console\Commands;

use App\Models\Arsip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SinkronHierarkiArsip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arsip:sinkron-hierarki';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menyelaraskan seluruh arsip yang punya sub_kegiatan_id / sub_jenis_spj_id dengan data master';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai sinkronisasi hierarki master data ke tabel arsip...');

        $updatedCount = 0;
        $totalChecked = 0;

        Arsip::with(['subKegiatan.kegiatan.program', 'subJenisSpj'])
            ->where(function ($query) {
                $query->whereNotNull('sub_kegiatan_id')
                      ->orWhereNotNull('sub_jenis_spj_id');
            })
            ->chunkById(100, function ($arsips) use (&$updatedCount, &$totalChecked) {
                foreach ($arsips as $arsip) {
                    $totalChecked++;
                    $updates = [];

                    // Sinkronisasi Program, Kegiatan, Sub Kegiatan
                    if ($arsip->subKegiatan) {
                        $correctSub = $arsip->subKegiatan->nama_sub_kegiatan;
                        $correctKeg = $arsip->subKegiatan->kegiatan->nama_kegiatan ?? null;
                        $correctProg = $arsip->subKegiatan->kegiatan->program->nama_program ?? null;

                        if ($arsip->sub_kegiatan !== $correctSub) {
                            $updates['sub_kegiatan'] = $correctSub;
                        }
                        if ($arsip->kegiatan !== $correctKeg) {
                            $updates['kegiatan'] = $correctKeg;
                        }
                        if ($arsip->program !== $correctProg) {
                            $updates['program'] = $correctProg;
                        }
                    }

                    // Sinkronisasi Sub Jenis SPJ
                    if ($arsip->subJenisSpj) {
                        $correctJenis = $arsip->subJenisSpj->nama_sub_jenis_spj;
                        if ($arsip->sub_jenis !== $correctJenis) {
                            $updates['sub_jenis'] = $correctJenis;
                        }
                    }

                    if (!empty($updates)) {
                        DB::table('arsip')
                            ->where('id_arsip', $arsip->id_arsip)
                            ->update($updates);
                        $updatedCount++;
                    }
                }
            }, 'id_arsip');

        $this->newLine();
        $this->info('--- Ringkasan Sinkronisasi Hierarki ---');
        $this->info("Jumlah arsip diperiksa  : {$totalChecked}");
        $this->info("Jumlah baris diperbarui : {$updatedCount}");

        return Command::SUCCESS;
    }
}
