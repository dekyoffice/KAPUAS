<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';

    protected $fillable = [
        'nama_program',
        'tahun_penetapan',
    ];

    /**
     * Relasi ke daftar kegiatan di bawah program ini.
     */
    public function kegiatans(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'program_id');
    }

    /**
     * Relasi has-many-through ke sub kegiatan.
     */
    public function subKegiatans(): HasManyThrough
    {
        return $this->hasManyThrough(SubKegiatan::class, Kegiatan::class, 'program_id', 'kegiatan_id');
    }

    /**
     * Hitung total arsip yang terkait dengan program ini melalui seluruh sub kegiatan.
     */
    public function totalArsipCount(): int
    {
        return Arsip::whereIn('sub_kegiatan_id', $this->subKegiatans()->pluck('sub_kegiatans.id'))->count();
    }
}
