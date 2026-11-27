<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    /**
     * Tampilkan daftar kategori arsip.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Kategori::withCount('arsip');

        if (!empty($search)) {
            $escaped = \App\Support\StrHelper::escapeLike($search);
            $query->where('nama_kategori', 'like', "%{$escaped}%");
        }

        $kategoris = $query->orderBy('nama_kategori')->paginate(10)->withQueryString();

        return view('kategori.index', [
            'kategoris' => $kategoris,
            'search' => $search,
        ]);
    }

    /**
     * Simpan kategori baru.
     */
    public function store(StoreKategoriRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Kategori::create([
            'nama_kategori' => $validated['nama_kategori'],
        ]);

        return redirect()->route('kategori.index')
            ->with('success', "Kategori '{$validated['nama_kategori']}' berhasil ditambahkan.");
    }

    /**
     * Perbarui data kategori.
     */
    public function update(StoreKategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $validated = $request->validated();

        $kategori->update([
            'nama_kategori' => $validated['nama_kategori'],
        ]);

        return redirect()->route('kategori.index')
            ->with('success', "Kategori berhasil diperbarui menjadi '{$kategori->nama_kategori}'.");
    }

    /**
     * Hapus kategori dengan proteksi keterikatan data arsip.
     */
    public function destroy(Kategori $kategori): RedirectResponse
    {
        $count = $kategori->arsip()->count();

        if ($count > 0) {
            return redirect()->route('kategori.index')
                ->with('error', "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena masih digunakan oleh {$count} berkas arsip.");
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        return redirect()->route('kategori.index')
            ->with('success', "Kategori '{$nama}' berhasil dihapus.");
    }
}
