<?php

namespace App\Models;

use App\Support\StrHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Arsip extends Model
{
    use HasFactory;

    /**
     * Nama tabel terkait.
     *
     * @var string
     */
    protected $table = 'arsip';

    /**
     * Primary key tabel.
     *
     * @var string
     */
    protected $primaryKey = 'id_arsip';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'id_kategori',
        'kode_arsip',
        'no_item_arsip',
        'nomor_berkas',
        'nomor_surat',
        'tanggal_surat',
        'jumlah_surat',
        'kode_klasifikasi',
        'skkad',
        'sub_jenis',
        'program',
        'kegiatan',
        'sub_kegiatan',
        'judul_arsip',
        'hal',
        'sumber',
        'unit_pengolah',
        'keterangan',
        'sub_kegiatan_id',
        'sub_jenis_spj_id',
        'tahun_arsip_id',
        'file_path',
        'google_drive_url',
        'tgl_upload',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tgl_upload' => 'date',
        'tanggal_surat' => 'date',
        'jumlah_surat' => 'integer',
    ];

    /**
     * Relasi ke pengunggah arsip.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke kategori arsip.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Relasi ke master sub kegiatan (membawa info kegiatan dan program).
     */
    public function subKegiatan(): BelongsTo
    {
        return $this->belongsTo(SubKegiatan::class, 'sub_kegiatan_id');
    }

    /**
     * Relasi ke master sub jenis SPJ.
     */
    public function subJenisSpj(): BelongsTo
    {
        return $this->belongsTo(SubJenisSpj::class, 'sub_jenis_spj_id');
    }

    /**
     * Relasi ke master tahun arsip.
     */
    public function tahunArsip(): BelongsTo
    {
        return $this->belongsTo(TahunArsip::class, 'tahun_arsip_id');
    }

    /**
     * Helper untuk mendapatkan ekstensi berkas.
     */
    public function getFileExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    /**
     * Accessor status berkas: 'Fisik & Digital', 'Fisik', 'Digital', atau 'Tercatat Saja'.
     */
    public function getStatusBerkasAttribute(): string
    {
        $hasFile = ! empty($this->file_path);
        $hasDrive = ! empty($this->google_drive_url);

        if ($hasFile && $hasDrive) {
            return 'Fisik & Digital';
        }
        if ($hasFile) {
            return 'Fisik';
        }
        if ($hasDrive) {
            return 'Digital';
        }

        return 'Tercatat Saja';
    }

    /**
     * Accessor keterangan berkas standar Diskominfo: 'Soft File' atau 'Arsip Kertas'.
     */
    public function getKeteranganMediaAttribute(): string
    {
        return (! empty($this->file_path) || ! empty($this->google_drive_url))
            ? 'Soft File'
            : 'Arsip Kertas';
    }

    /**
     * Scope untuk membatasi arsip berdasarkan hak akses pengguna.
     * Viewer tidak diizinkan melihat / mengakses arsip berstatus 'Rahasia'.
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?: auth()->user();

        if ($user && $user->role === 'viewer') {
            return $query->where(function (Builder $q) {
                $q->whereNull('skkad')->orWhere('skkad', '!=', 'Rahasia');
            });
        }

        return $query;
    }

    /**
     * Accessor punya_berkas: bernilai true jika file_path ATAU google_drive_url terisi (memiliki bukti berkas fisik atau digital).
     */
    public function getPunyaBerkasAttribute(): bool
    {
        return ! empty($this->file_path) || ! empty($this->google_drive_url);
    }

    /**
     * Scope untuk pencarian judul, kode arsip, nomor surat, nomor berkas, unit pengolah, dan atribut lainnya.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        $escaped = StrHelper::escapeLike($keyword);

        return $query->where(function (Builder $q) use ($escaped) {
            $q->where('judul_arsip', 'like', "%{$escaped}%")
                ->orWhere('kode_arsip', 'like', "%{$escaped}%")
                ->orWhere('nomor_surat', 'like', "%{$escaped}%")
                ->orWhere('nomor_berkas', 'like', "%{$escaped}%")
                ->orWhere('no_item_arsip', 'like', "%{$escaped}%")
                ->orWhere('kode_klasifikasi', 'like', "%{$escaped}%")
                ->orWhere('skkad', 'like', "%{$escaped}%")
                ->orWhere('sub_jenis', 'like', "%{$escaped}%")
                ->orWhere('program', 'like', "%{$escaped}%")
                ->orWhere('kegiatan', 'like', "%{$escaped}%")
                ->orWhere('sub_kegiatan', 'like', "%{$escaped}%")
                ->orWhere('hal', 'like', "%{$escaped}%")
                ->orWhere('sumber', 'like', "%{$escaped}%")
                ->orWhere('unit_pengolah', 'like', "%{$escaped}%")
                ->orWhere('keterangan', 'like', "%{$escaped}%");
        });
    }

    /**
     * Scope filter berdasarkan ID kategori.
     */
    public function scopeFilterKategori(Builder $query, $idKategori): Builder
    {
        if (empty($idKategori)) {
            return $query;
        }

        return $query->where('id_kategori', $idKategori);
    }

    /**
     * Scope filter berdasarkan tahun upload.
     */
    public function scopeFilterTahun(Builder $query, $tahun): Builder
    {
        if (empty($tahun)) {
            return $query;
        }

        return $query->whereYear('tgl_upload', $tahun);
    }

    /**
     * Scope filter berdasarkan status kepemilikan berkas fisik / digital.
     */
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (empty($status)) {
            return $query;
        }

        return match ($status) {
            'fisik' => $query->whereNotNull('file_path')->where('file_path', '!=', ''),
            'digital' => $query->whereNotNull('google_drive_url')->where('google_drive_url', '!=', ''),
            'fisik_digital' => $query->whereNotNull('file_path')->where('file_path', '!=', '')
                ->whereNotNull('google_drive_url')->where('google_drive_url', '!=', ''),
            // alias lama, agar tautan tersimpan tetap berfungsi
            'lengkap' => $query->whereNotNull('file_path')->where('file_path', '!=', '')
                ->whereNotNull('google_drive_url')->where('google_drive_url', '!=', ''),
            'tercatat' => $query->where(function (Builder $q) {
                $q->whereNull('file_path')->orWhere('file_path', '');
            })->where(function (Builder $q) {
                $q->whereNull('google_drive_url')->orWhere('google_drive_url', '');
            }),
            default => $query,
        };
    }
}
