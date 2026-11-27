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
        Schema::table('arsip', function (Blueprint $table) {
            $table->foreignId('sub_kegiatan_id')->nullable()->after('sub_kegiatan')->constrained('sub_kegiatans')->onDelete('restrict');
            $table->foreignId('sub_jenis_spj_id')->nullable()->after('sub_jenis')->constrained('sub_jenis_spjs')->onDelete('restrict');
            $table->foreignId('tahun_arsip_id')->nullable()->after('tgl_upload')->constrained('tahun_arsips')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropForeign(['sub_kegiatan_id']);
            $table->dropForeign(['sub_jenis_spj_id']);
            $table->dropForeign(['tahun_arsip_id']);
            $table->dropColumn(['sub_kegiatan_id', 'sub_jenis_spj_id', 'tahun_arsip_id']);
        });
    }
};
