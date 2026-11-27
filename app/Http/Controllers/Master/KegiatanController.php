<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKegiatanRequest;
use App\Models\Arsip;
use App\Models\Kegiatan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KegiatanController extends Controller
{
    /**
     * Simpan kegiatan baru.
     */
    public function store(StoreKegiatanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $kegiatan = Kegiatan::create($validated);

        return redirect()->back()
            ->with('success', "Kegiatan '{$kegiatan->nama_kegiatan}' berhasil ditambahkan ke dalam program.");
    }

    /**
     * Perbarui data kegiatan dan sinkronkan teks kegiatan pada seluruh arsip terkait.
     */
    public function update(StoreKegiatanRequest $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($kegiatan, $validated) {
            $kegiatan->update($validated);
            $kegiatan->load('program');

            $subKegiatanIds = $kegiatan->subKegiatans()->pluck('id');
            if ($subKegiatanIds->isNotEmpty()) {
                Arsip::whereIn('sub_kegiatan_id', $subKegiatanIds)->update([
                    'kegiatan' => $kegiatan->nama_kegiatan,
                    'program' => $kegiatan->program->nama_program ?? null,
                ]);
            }
        });

        return redirect()->back()
            ->with('success', "Kegiatan berhasil diperbarui menjadi '{$kegiatan->nama_kegiatan}'.");
    }

    /**
     * Hapus kegiatan dengan proteksi data arsip dan transaksi atomik.
     */
    public function destroy(Kegiatan $kegiatan): RedirectResponse
    {
        $subKegiatanIds = $kegiatan->subKegiatans()->pluck('id');
        $arsipCount = Arsip::whereIn('sub_kegiatan_id', $subKegiatanIds)->count();

        if ($arsipCount > 0) {
            return redirect()->back()
                ->with('error', "Kegiatan '{$kegiatan->nama_kegiatan}' tidak dapat dihapus karena masih digunakan oleh {$arsipCount} data berkas arsip.");
        }

        $nama = $kegiatan->nama_kegiatan;

        DB::transaction(function () use ($kegiatan) {
            $kegiatan->subKegiatans()->delete();
            $kegiatan->delete();
        });

        return redirect()->back()
            ->with('success', "Kegiatan '{$nama}' berhasil dihapus.");
    }

    /**
     * API Internal: Daftar kegiatan berdasarkan program_id (untuk drawer).
     */
    public function byProgram(Request $request): JsonResponse
    {
        $programId = $request->input('program_id');

        $kegiatans = Kegiatan::where('program_id', $programId)
            ->orderBy('nama_kegiatan')
            ->get(['id', 'program_id', 'nama_kegiatan']);

        return response()->json($kegiatans);
    }
}
