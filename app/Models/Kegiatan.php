<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatans';

    protected $fillable = [
        'program_id',
        'nama_kegiatan',
    ];

    /**
     * Relasi ke program induk.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Relasi ke daftar sub kegiatan.
     */
    public function subKegiatans(): HasMany
    {
        return $this->hasMany(SubKegiatan::class, 'kegiatan_id');
    }

    /**
     * Hitung total arsip yang terkait dengan kegiatan ini melalui seluruh sub kegiatan.
     */
    public function totalArsipCount(): int
    {
        return Arsip::whereIn('sub_kegiatan_id', $this->subKegiatans()->pluck('id'))->count();
    }
}
