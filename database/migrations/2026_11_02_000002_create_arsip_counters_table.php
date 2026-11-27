<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arsip_counters', function (Blueprint $table) {
            $table->string('tahun', 4)->primary();
            $table->unsignedInteger('terakhir')->default(0);
        });

        // Isi data awal dari nomor tertinggi per tahun di tabel arsip
        // Format kode_arsip: ARS-{tahun}-{nomor} misal ARS-2026-0001
        $arsips = DB::table('arsip')
            ->whereNotNull('kode_arsip')
            ->where('kode_arsip', '!=', '')
            ->get(['kode_arsip']);

        $highestPerYear = [];

        foreach ($arsips as $arsip) {
            if (preg_match('/^ARS-(\d{4})-(\d+)$/', $arsip->kode_arsip, $matches)) {
                $year = $matches[1];
                $number = (int) $matches[2];

                if (!isset($highestPerYear[$year]) || $number > $highestPerYear[$year]) {
                    $highestPerYear[$year] = $number;
                }
            }
        }

        foreach ($highestPerYear as $year => $maxNum) {
            DB::table('arsip_counters')->insert([
                'tahun' => (string) $year,
                'terakhir' => $maxNum,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_counters');
    }
};
