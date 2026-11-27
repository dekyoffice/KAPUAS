<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgramRequest;
use App\Models\Arsip;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProgramController extends Controller
{
    /**
     * Tampilkan halaman hierarki program kearsipan (tree view).
     */
    public function index(Request $request): View
    {
        // Ambil daftar tahun penetapan yang tersedia
        $availableYears = Program::distinct()
            ->orderBy('tahun_penetapan', 'desc')
            ->pluck('tahun_penetapan')
            ->toArray();

        $selectedYear = (int) $request->input('tahun', !empty($availableYears) ? $availableYears[0] : date('Y'));

        // Ambil program lengkap dengan hierarki kegiatan dan sub kegiatan
        $programs = Program::with([
            'kegiatans.subKegiatans' => function ($query) {
                $query->withCount('arsip');
            }
        ])
        ->where('tahun_penetapan', $selectedYear)
        ->orderBy('nama_program')
        ->get();

        return view('master-data.hierarki-program', [
            'programs' => $programs,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
        ]);
    }

    /**
     * Simpan program baru.
     */
    public function store(StoreProgramRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Program::create($validated);

        return redirect()->route('master-data.program.index', ['tahun' => $validated['tahun_penetapan']])
            ->with('success', "Program '{$validated['nama_program']}' untuk tahun {$validated['tahun_penetapan']} berhasil ditambahkan.");
    }

    /**
     * Perbarui program dan sinkronkan teks nama program pada seluruh berkas arsip terkait.
     */
    public function update(StoreProgramRequest $request, Program $program): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($program, $validated) {
            $program->update($validated);

            $subKegiatanIds = $program->subKegiatans()->pluck('sub_kegiatans.id');
            if ($subKegiatanIds->isNotEmpty()) {
                Arsip::whereIn('sub_kegiatan_id', $subKegiatanIds)->update([
                    'program' => $program->nama_program,
                ]);
            }
        });

        return redirect()->route('master-data.program.index', ['tahun' => $validated['tahun_penetapan']])
            ->with('success', "Program berhasil diperbarui menjadi '{$program->nama_program}'.");
    }

    /**
     * Hapus program dengan pengecekan keterikatan arsip dan transaksi atomik.
     */
    public function destroy(Program $program): RedirectResponse
    {
        $subKegiatanIds = $program->subKegiatans()->pluck('sub_kegiatans.id');
        $arsipCount = Arsip::whereIn('sub_kegiatan_id', $subKegiatanIds)->count();

        if ($arsipCount > 0) {
            return redirect()->back()
                ->with('error', "Program '{$program->nama_program}' tidak dapat dihapus karena masih digunakan oleh {$arsipCount} data berkas arsip.");
        }

        $nama = $program->nama_program;
        $tahun = $program->tahun_penetapan;

        // Hapus berjenjang sub kegiatan & kegiatan dalam transaksi
        DB::transaction(function () use ($program) {
            foreach ($program->kegiatans as $kegiatan) {
                $kegiatan->subKegiatans()->delete();
            }
            $program->kegiatans()->delete();
            $program->delete();
        });

        return redirect()->route('master-data.program.index', ['tahun' => $tahun])
            ->with('success', "Program '{$nama}' berhasil dihapus.");
    }

    /**
     * API Internal: Daftar tahun penetapan program (untuk dropdown drawer).
     */
    public function daftarTahun(): JsonResponse
    {
        $years = Program::distinct()
            ->orderBy('tahun_penetapan', 'desc')
            ->pluck('tahun_penetapan');

        return response()->json($years);
    }

    /**
     * API Internal: Daftar program berdasarkan tahun penetapan.
     */
    public function byTahun(Request $request): JsonResponse
    {
        $tahun = $request->input('tahun');

        $programs = Program::where('tahun_penetapan', $tahun)
            ->orderBy('nama_program')
            ->get(['id', 'nama_program', 'tahun_penetapan']);

        return response()->json($programs);
    }
}
