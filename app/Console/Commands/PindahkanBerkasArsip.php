<?php

namespace App\Console\Commands;

use App\Models\Arsip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PindahkanBerkasArsip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arsip:pindahkan-berkas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pindahkan berkas lama dari disk public ke disk local (privat)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pemindahan berkas arsip dari disk public ke disk local...');

        $dipindah = 0;
        $dilewati = 0;
        $tidakDitemukan = 0;

        $publicDisk = Storage::disk('public');
        $localDisk = Storage::disk('local');

        $arsips = Arsip::whereNotNull('file_path')->where('file_path', '!=', '')->get();
        $processedPaths = [];

        foreach ($arsips as $arsip) {
            $path = $arsip->file_path;
            $processedPaths[$path] = true;

            // Jika sudah ada di disk local, lewati (idempoten)
            if ($localDisk->exists($path)) {
                $dilewati++;
                continue;
            }

            // Jika ada di disk public, salin ke disk local
            if ($publicDisk->exists($path)) {
                $stream = $publicDisk->readStream($path);
                if ($stream !== false && $localDisk->writeStream($path, $stream)) {
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                    $dipindah++;
                    $this->line("Dipindah: {$path}");
                } else {
                    $this->error("Gagal menyalin: {$path}");
                }
            } else {
                $tidakDitemukan++;
                $this->warn("Tidak ditemukan di public maupun local: {$path} (Arsip ID: {$arsip->id_arsip})");
            }
        }

        // Cek juga berkas di storage/app/public/arsip yang mungkin belum terdaftar di record arsip
        $allPublicFiles = $publicDisk->allFiles('arsip');
        foreach ($allPublicFiles as $file) {
            if (isset($processedPaths[$file])) {
                continue;
            }

            if ($localDisk->exists($file)) {
                $dilewati++;
                continue;
            }

            $stream = $publicDisk->readStream($file);
            if ($stream !== false && $localDisk->writeStream($file, $stream)) {
                if (is_resource($stream)) {
                    fclose($stream);
                }
                $dipindah++;
                $this->line("Dipindah (file direktori): {$file}");
            }
        }

        $this->newLine();
        $this->info('--- Ringkasan Pemindahan Berkas ---');
        $this->info("Jumlah berkas dipindah        : {$dipindah}");
        $this->info("Jumlah berkas dilewati        : {$dilewati}");
        $this->info("Jumlah berkas tidak ditemukan : {$tidakDitemukan}");

        return Command::SUCCESS;
    }
}
