@extends('layouts.app')

@section('title', 'Kategori Dokumen')
@section('header-title', 'Klasifikasi Kategori')
@section('header-subtitle', 'Pengelompokan jenis dokumen arsip digital instansi')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Kategori Dokumen</h2>
            <p class="text-xs text-slate-500">Kelola kelompok jenis arsip. Dokumen yang masih terkait tidak dapat dihapus sembarangan.</p>
        </div>

        <button type="button" onclick="openCreateKategoriModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white shadow-sm transition-all hover:-translate-y-0.5 duration-150" style="background: linear-gradient(135deg, #003CAA, #0050C8)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Kategori Baru</span>
        </button>
    </div>

    <!-- Categories Grid -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="text-[11px] uppercase tracking-wider font-semibold border-b border-line" style="background: rgba(210,230,250,0.25); color: #1E293B">
                    <tr>
                        <th class="px-6 py-3.5 w-16">No</th>
                        <th class="px-6 py-3.5">Nama Kategori</th>
                        <th class="px-6 py-3.5">Jumlah Berkas Arsip</th>
                        <th class="px-6 py-3.5">Tanggal Dibuat</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kategoris as $index => $kat)
                    <tr class="hover:bg-ice/20 transition-colors duration-100">
                        <td class="px-6 py-4 text-xs font-bold text-slate-400">
                            {{ $kategoris->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900">
                            {{ $kat->nama_kategori }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border" style="background: rgba(0,80,200,0.07); color: #0050C8; border-color: rgba(0,80,200,0.2)">
                                {{ number_format($kat->arsip_count) }} dokumen
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $kat->created_at ? $kat->created_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" title="Ubah Nama Kategori"
                                    onclick="openEditKategoriModal(@js(route('kategori.update', $kat)), @js($kat->nama_kategori))"
                                    class="p-2 rounded-lg text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <button type="button" title="Hapus Kategori"
                                    onclick="confirmDelete(@js(route('kategori.destroy', $kat)), @js('Hapus Kategori '.$kat->nama_kategori.'?'), @js('Apakah Anda yakin ingin menghapus kategori ini? Kategori tidak dapat dihapus jika masih digunakan oleh data arsip.'))"
                                    class="p-2 rounded-lg text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-xs text-slate-500">
                            Belum ada kategori yang dibuat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-line bg-ice/10">
            {{ $kategoris->links() }}
        </div>
    </div>

</div>

<!-- Modal Tambah/Edit Kategori -->
<div id="kategori-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-navy/60 backdrop-blur-sm p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
        <h3 class="text-base font-bold text-slate-800" id="kategori-modal-title">Tambah Kategori Baru</h3>
        <form id="kategori-modal-form" method="POST" action="{{ route('kategori.store') }}" class="mt-4 space-y-4">
            @csrf
            <div id="kategori-method-spoof"></div>

            <div>
                <label for="modal-nama-kategori" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <div class="mt-1.5">
                    <input type="text" id="modal-nama-kategori" name="nama_kategori" required
                        placeholder="Contoh: Dokumen Pengadaan Barang"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-line text-ink rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:bg-white text-sm">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeKategoriModal()" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white rounded-lg shadow-sm transition-all hover:-translate-y-0.5 duration-150" style="background: linear-gradient(135deg, #003CAA, #0050C8)">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateKategoriModal() {
        const modal = document.getElementById('kategori-modal');
        const form = document.getElementById('kategori-modal-form');
        const title = document.getElementById('kategori-modal-title');
        const input = document.getElementById('modal-nama-kategori');
        const spoof = document.getElementById('kategori-method-spoof');

        form.action = "{{ route('kategori.store') }}";
        title.textContent = "Tambah Kategori Baru";
        input.value = "";
        spoof.innerHTML = "";

        modal.classList.remove('hidden');
        input.focus();
    }

    function openEditKategoriModal(url, currentName) {
        const modal = document.getElementById('kategori-modal');
        const form = document.getElementById('kategori-modal-form');
        const title = document.getElementById('kategori-modal-title');
        const input = document.getElementById('modal-nama-kategori');
        const spoof = document.getElementById('kategori-method-spoof');

        form.action = url;
        title.textContent = "Ubah Nama Kategori";
        input.value = currentName;
        spoof.innerHTML = '@method("PUT")';

        modal.classList.remove('hidden');
        input.focus();
    }

    function closeKategoriModal() {
        document.getElementById('kategori-modal').classList.add('hidden');
    }
</script>
@endpush
@endsection
