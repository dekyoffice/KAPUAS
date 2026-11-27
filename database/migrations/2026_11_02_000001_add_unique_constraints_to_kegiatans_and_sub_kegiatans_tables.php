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
        // 1. Cek duplikat pada tabel kegiatans sebelum menambahkan constraint
        $dupKegiatan = DB::table('kegiatans')
            ->select('program_id', 'nama_kegiatan', DB::raw('count(*) as total'))
            ->groupBy('program_id', 'nama_kegiatan')
            ->having('total', '>', 1)
            ->get();

        if ($dupKegiatan->isNotEmpty()) {
            throw new \RuntimeException(
                "Tidak dapat menambahkan unique constraint. Ditemukan data duplikat pada tabel 'kegiatans': " .
                $dupKegiatan->toJson(JSON_PRETTY_PRINT)
            );
        }

        // 2. Cek duplikat pada tabel sub_kegiatans sebelum menambahkan constraint
        $dupSubKegiatan = DB::table('sub_kegiatans')
            ->select('kegiatan_id', 'nama_sub_kegiatan', DB::raw('count(*) as total'))
            ->groupBy('kegiatan_id', 'nama_sub_kegiatan')
            ->having('total', '>', 1)
            ->get();

        if ($dupSubKegiatan->isNotEmpty()) {
            throw new \RuntimeException(
                "Tidak dapat menambahkan unique constraint. Ditemukan data duplikat pada tabel 'sub_kegiatans': " .
                $dupSubKegiatan->toJson(JSON_PRETTY_PRINT)
            );
        }

        // 3. Tambahkan composite unique constraints
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->unique(['program_id', 'nama_kegiatan'], 'kegiatans_program_id_nama_kegiatan_unique');
        });

        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->unique(['kegiatan_id', 'nama_sub_kegiatan'], 'sub_kegiatans_kegiatan_id_nama_sub_kegiatan_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->dropUnique('sub_kegiatans_kegiatan_id_nama_sub_kegiatan_unique');
        });

        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropUnique('kegiatans_program_id_nama_kegiatan_unique');
        });
    }
};
