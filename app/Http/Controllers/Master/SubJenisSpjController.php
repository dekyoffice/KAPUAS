<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubJenisSpjRequest;
use App\Models\Arsip;
use App\Models\SubJenisSpj;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubJenisSpjController extends Controller
{
    /**
     * Tampilkan daftar master sub jenis SPJ.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = SubJenisSpj::withCount('arsip');

        if (!empty($search)) {
            $escaped = \App\Support\StrHelper::escapeLike($search);
            $query->where('nama_sub_jenis_spj', 'like', "%{$escaped}%");
        }

        $subJenisSpjs = $query->orderBy('nama_sub_jenis_spj')->paginate(10)->withQueryString();

        return view('master-data.sub-jenis-spj', [
            'subJenisSpjs' => $subJenisSpjs,
            'search' => $search,
        ]);
    }

    /**
     * Simpan sub jenis SPJ baru.
     */
    public function store(StoreSubJenisSpjRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        SubJenisSpj::create($validated);

        return redirect()->route('master-data.sub-jenis-spj.index')
            ->with('success', "Sub Jenis SPJ '{$validated['nama_sub_jenis_spj']}' berhasil ditambahkan.");
    }

    /**
     * Perbarui sub jenis SPJ dan sinkronkan teks sub_jenis pada seluruh arsip terkait.
     */
    public function update(StoreSubJenisSpjRequest $request, SubJenisSpj $subJenisSpj): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($subJenisSpj, $validated) {
            $subJenisSpj->update($validated);

            Arsip::where('sub_jenis_spj_id', $subJenisSpj->id)->update([
                'sub_jenis' => $subJenisSpj->nama_sub_jenis_spj,
            ]);
        });

        return redirect()->route('master-data.sub-jenis-spj.index')
            ->with('success', "Sub Jenis SPJ berhasil diperbarui menjadi '{$subJenisSpj->nama_sub_jenis_spj}'.");
    }

    /**
     * Hapus sub jenis SPJ dengan proteksi data arsip.
     */
    public function destroy(SubJenisSpj $subJenisSpj): RedirectResponse
    {
        $count = $subJenisSpj->arsip()->count();

        if ($count > 0) {
            return redirect()->route('master-data.sub-jenis-spj.index')
                ->with('error', "Sub Jenis SPJ '{$subJenisSpj->nama_sub_jenis_spj}' tidak dapat dihapus karena masih digunakan oleh {$count} berkas arsip.");
        }

        $nama = $subJenisSpj->nama_sub_jenis_spj;
        $subJenisSpj->delete();

        return redirect()->route('master-data.sub-jenis-spj.index')
            ->with('success', "Sub Jenis SPJ '{$nama}' berhasil dihapus.");
    }
}
