<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubJenisSpj extends Model
{
    use HasFactory;

    protected $table = 'sub_jenis_spjs';

    protected $fillable = [
        'nama_sub_jenis_spj',
    ];

    /**
     * Relasi ke arsip yang memilih sub jenis SPJ ini.
     */
    public function arsip(): HasMany
    {
        return $this->hasMany(Arsip::class, 'sub_jenis_spj_id');
    }
}
