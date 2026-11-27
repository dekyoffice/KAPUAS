<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Petakan "Biasa" atau "Biasa / Terbuka" menjadi "Terbuka"
        DB::table('arsip')
            ->where(function ($query) {
                $query->where('skkad', 'like', '%Biasa%')
                    ->orWhere('skkad', 'Biasa / Terbuka');
            })
            ->update(['skkad' => 'Terbuka']);

        // Petakan "Terbatas" atau "Sangat Rahasia" menjadi "Tertutup"
        DB::table('arsip')
            ->whereIn('skkad', ['Terbatas', 'Tertutup', 'Sangat Rahasia'])
            ->update(['skkad' => 'Tertutup']);

        // Pastikan Rahasia tetap Rahasia
        DB::table('arsip')
            ->where('skkad', 'Rahasia')
            ->update(['skkad' => 'Rahasia']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('arsip')
            ->where('skkad', 'Terbuka')
            ->update(['skkad' => 'Biasa / Terbuka']);

        DB::table('arsip')
            ->where('skkad', 'Tertutup')
            ->update(['skkad' => 'Terbatas']);
    }
};
