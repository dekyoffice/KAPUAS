<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubKegiatan extends Model
{
    use HasFactory;

    protected $table = 'sub_kegiatans';

    protected $fillable = [
        'kegiatan_id',
        'nama_sub_kegiatan',
    ];

    protected $appends = ['full_path'];

    /**
     * Relasi ke kegiatan induk.
     */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    /**
     * Relasi ke arsip yang memilih sub kegiatan ini.
     */
    public function arsip(): HasMany
    {
        return $this->hasMany(Arsip::class, 'sub_kegiatan_id');
    }

    /**
     * Accessor untuk hierarki lengkap: Program > Kegiatan > Sub Kegiatan.
     */
    public function getFullPathAttribute(): string
    {
        if ($this->relationLoaded('kegiatan') && $this->kegiatan && $this->kegiatan->relationLoaded('program') && $this->kegiatan->program) {
            return "{$this->kegiatan->program->nama_program} > {$this->kegiatan->nama_kegiatan} > {$this->nama_sub_kegiatan}";
        }

        if ($this->kegiatan && $this->kegiatan->program) {
            return "{$this->kegiatan->program->nama_program} > {$this->kegiatan->nama_kegiatan} > {$this->nama_sub_kegiatan}";
        }

        return $this->nama_sub_kegiatan;
    }
}
