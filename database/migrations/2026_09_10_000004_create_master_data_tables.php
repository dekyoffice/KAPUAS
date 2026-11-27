<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel programs
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program', 255);
            $table->year('tahun_penetapan');
            $table->timestamps();

            $table->unique(['nama_program', 'tahun_penetapan']);
        });

        // 2. Tabel kegiatans
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('restrict');
            $table->string('nama_kegiatan', 255);
            $table->timestamps();
        });

        // 3. Tabel sub_kegiatans
        Schema::create('sub_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->onDelete('restrict');
            $table->string('nama_sub_kegiatan', 255);
            $table->timestamps();
        });

        // 4. Tabel sub_jenis_spjs
        Schema::create('sub_jenis_spjs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sub_jenis_spj', 150)->unique();
            $table->timestamps();
        });

        // 5. Tabel tahun_arsips
        Schema::create('tahun_arsips', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_kegiatans');
        Schema::dropIfExists('kegiatans');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('sub_jenis_spjs');
        Schema::dropIfExists('tahun_arsips');
    }
};
