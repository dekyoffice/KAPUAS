<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTahunArsipRequest;
use App\Models\TahunArsip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TahunArsipController extends Controller
{
    /**
     * Tampilkan daftar master tahun arsip.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = TahunArsip::withCount('arsip');

        if (!empty($search)) {
            $escaped = \App\Support\StrHelper::escapeLike($search);
            $query->where('tahun', 'like', "%{$escaped}%");
        }

        $tahunArsips = $query->orderBy('tahun', 'desc')->paginate(10)->withQueryString();

        return view('master-data.tahun-arsip', [
            'tahunArsips' => $tahunArsips,
            'search' => $search,
        ]);
    }

    /**
     * Simpan tahun arsip baru.
     */
    public function store(StoreTahunArsipRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        TahunArsip::create($validated);

        return redirect()->route('master-data.tahun-arsip.index')
            ->with('success', "Tahun Arsip '{$validated['tahun']}' berhasil ditambahkan ke master data.");
    }

    /**
     * Perbarui tahun arsip.
     */
    public function update(StoreTahunArsipRequest $request, TahunArsip $tahunArsip): RedirectResponse
    {
        $validated = $request->validated();

        $tahunArsip->update($validated);

        return redirect()->route('master-data.tahun-arsip.index')
            ->with('success', "Tahun Arsip berhasil diperbarui menjadi '{$tahunArsip->tahun}'.");
    }

    /**
     * Hapus tahun arsip dengan proteksi data arsip.
     */
    public function destroy(TahunArsip $tahunArsip): RedirectResponse
    {
        $count = $tahunArsip->arsip()->count();

        if ($count > 0) {
            return redirect()->route('master-data.tahun-arsip.index')
                ->with('error', "Tahun Arsip '{$tahunArsip->tahun}' tidak dapat dihapus karena masih digunakan oleh {$count} berkas arsip.");
        }

        $tahun = $tahunArsip->tahun;
        $tahunArsip->delete();

        return redirect()->route('master-data.tahun-arsip.index')
            ->with('success', "Tahun Arsip '{$tahun}' berhasil dihapus.");
    }
}
