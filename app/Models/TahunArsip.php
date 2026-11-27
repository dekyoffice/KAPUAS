<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunArsip extends Model
{
    use HasFactory;

    protected $table = 'tahun_arsips';

    protected $fillable = [
        'tahun',
    ];

    /**
     * Relasi ke arsip yang memilih tahun arsip ini.
     */
    public function arsip(): HasMany
    {
        return $this->hasMany(Arsip::class, 'tahun_arsip_id');
    }
}
