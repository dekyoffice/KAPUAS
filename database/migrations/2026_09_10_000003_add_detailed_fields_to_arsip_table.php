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
            $table->string('no_item_arsip', 100)->nullable()->after('kode_arsip');
            $table->string('kode_klasifikasi', 100)->nullable()->after('no_item_arsip');
            $table->string('skkad', 100)->nullable()->after('kode_klasifikasi');
            $table->string('sub_jenis', 150)->nullable()->after('skkad');
            $table->string('program', 255)->nullable()->after('sub_jenis');
            $table->string('kegiatan', 255)->nullable()->after('program');
            $table->string('sub_kegiatan', 255)->nullable()->after('kegiatan');
            $table->string('hal', 255)->nullable()->after('judul_arsip');
            $table->string('sumber', 255)->nullable()->after('hal');
            $table->text('keterangan')->nullable()->after('sumber');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropColumn([
                'no_item_arsip',
                'kode_klasifikasi',
                'skkad',
                'sub_jenis',
                'program',
                'kegiatan',
                'sub_kegiatan',
                'hal',
                'sumber',
                'keterangan',
            ]);
        });
    }
};
