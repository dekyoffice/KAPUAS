<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubKegiatanRequest;
use App\Models\Arsip;
use App\Models\SubKegiatan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubKegiatanController extends Controller
{
    /**
     * Simpan sub kegiatan baru.
     */
    public function store(StoreSubKegiatanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $subKegiatan = SubKegiatan::create($validated);

        return redirect()->back()
            ->with('success', "Sub Kegiatan '{$subKegiatan->nama_sub_kegiatan}' berhasil ditambahkan.");
    }

    /**
     * Perbarui sub kegiatan dan sinkronkan teks pada seluruh arsip terkait.
     */
    public function update(StoreSubKegiatanRequest $request, SubKegiatan $subKegiatan): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($subKegiatan, $validated) {
            $subKegiatan->update($validated);
            $subKegiatan->load('kegiatan.program');

            Arsip::where('sub_kegiatan_id', $subKegiatan->id)->update([
                'sub_kegiatan' => $subKegiatan->nama_sub_kegiatan,
                'kegiatan' => $subKegiatan->kegiatan->nama_kegiatan ?? null,
                'program' => $subKegiatan->kegiatan->program->nama_program ?? null,
            ]);
        });

        return redirect()->back()
            ->with('success', "Sub Kegiatan berhasil diperbarui menjadi '{$subKegiatan->nama_sub_kegiatan}'.");
    }

    /**
     * Hapus sub kegiatan dengan proteksi data arsip.
     */
    public function destroy(SubKegiatan $subKegiatan): RedirectResponse
    {
        $arsipCount = $subKegiatan->arsip()->count();

        if ($arsipCount > 0) {
            return redirect()->back()
                ->with('error', "Sub Kegiatan '{$subKegiatan->nama_sub_kegiatan}' tidak dapat dihapus karena masih digunakan oleh {$arsipCount} berkas arsip.");
        }

        $nama = $subKegiatan->nama_sub_kegiatan;
        $subKegiatan->delete();

        return redirect()->back()
            ->with('success', "Sub Kegiatan '{$nama}' berhasil dihapus.");
    }

    /**
     * API Internal: Daftar sub kegiatan berdasarkan kegiatan_id (untuk drawer).
     */
    public function byKegiatan(Request $request): JsonResponse
    {
        $kegiatanId = $request->input('kegiatan_id');

        $subKegiatans = SubKegiatan::with(['kegiatan.program'])
            ->where('kegiatan_id', $kegiatanId)
            ->orderBy('nama_sub_kegiatan')
            ->get();

        return response()->json($subKegiatans);
    }
}
