<?php $__env->startSection('title', 'Master Sub Jenis SPJ'); ?>
<?php $__env->startSection('header-title', 'Master Sub Jenis SPJ'); ?>
<?php $__env->startSection('header-subtitle', 'Kelola klasifikasi rincian jenis SPJ untuk arsip keuangan & pertanggungjawaban'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <!-- Top Action & Search Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Sub Jenis SPJ</h2>
            <p class="text-xs text-slate-500">Kelola sub klasifikasi SPJ (misal: GU, LS Gaji, LS Barang/Jasa, TU, dll). Proteksi berlaku jika data masih terpakai.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Search Form -->
            <form method="GET" action="<?php echo e(route('master-data.sub-jenis-spj.index')); ?>" class="relative">
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Cari sub jenis SPJ..."
                    class="w-full sm:w-64 pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-slate-800">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>

            <button type="button" onclick="openCreateSubJenisModal()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-600/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Sub Jenis SPJ</span>
            </button>
        </div>
    </div>

    <!-- Table List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5 w-16">No</th>
                        <th class="px-6 py-3.5">Nama Sub Jenis SPJ</th>
                        <th class="px-6 py-3.5">Jumlah Berkas Arsip</th>
                        <th class="px-6 py-3.5">Tanggal Dibuat</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $subJenisSpjs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-xs font-bold text-slate-400">
                            <?php echo e($subJenisSpjs->firstItem() + $index); ?>

                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900">
                            <?php echo e($item->nama_sub_jenis_spj); ?>

                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo e($item->arsip_count > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-500'); ?>">
                                <?php echo e(number_format($item->arsip_count)); ?> dokumen
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            <?php echo e($item->created_at ? $item->created_at->format('d/m/Y') : '-'); ?>

                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" title="Ubah Nama Sub Jenis SPJ"
                                    onclick="openEditSubJenisModal(<?php echo \Illuminate\Support\Js::from(route('master-data.sub-jenis-spj.update', $item))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($item->nama_sub_jenis_spj)->toHtml() ?>)"
                                    class="p-2 rounded-lg text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <button type="button" title="Hapus Sub Jenis SPJ"
                                    onclick="confirmDelete(<?php echo \Illuminate\Support\Js::from(route('master-data.sub-jenis-spj.destroy', $item))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Hapus Sub Jenis SPJ '.$item->nama_sub_jenis_spj.'?')->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Apakah Anda yakin ingin menghapus data ini? Data tidak dapat dihapus jika masih digunakan oleh berkas arsip.')->toHtml() ?>)"
                                    class="p-2 rounded-lg text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="p-12 text-center text-xs text-slate-500">
                            <?php echo e($search ? 'Tidak ada sub jenis SPJ yang cocok dengan pencarian.' : 'Belum ada data sub jenis SPJ.'); ?>

                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            <?php echo e($subJenisSpjs->links()); ?>

        </div>
    </div>

</div>

<!-- Modal Tambah/Edit Sub Jenis SPJ -->
<div id="sub-jenis-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
        <h3 class="text-base font-bold text-slate-800" id="sub-jenis-modal-title">Tambah Sub Jenis SPJ</h3>
        <form id="sub-jenis-modal-form" method="POST" action="<?php echo e(route('master-data.sub-jenis-spj.store')); ?>" class="mt-4 space-y-4">
            <?php echo csrf_field(); ?>
            <div id="sub-jenis-method-spoof"></div>

            <div>
                <label for="modal-nama-sub-jenis-spj" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Nama Sub Jenis SPJ <span class="text-rose-500">*</span>
                </label>
                <div class="mt-1.5">
                    <input type="text" id="modal-nama-sub-jenis-spj" name="nama_sub_jenis_spj" required
                        placeholder="Contoh: SPJ GU (Ganti Uang)"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeSubJenisModal()" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm shadow-indigo-600/25 transition-all">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function openCreateSubJenisModal() {
        const modal = document.getElementById('sub-jenis-modal');
        const form = document.getElementById('sub-jenis-modal-form');
        const title = document.getElementById('sub-jenis-modal-title');
        const input = document.getElementById('modal-nama-sub-jenis-spj');
        const spoof = document.getElementById('sub-jenis-method-spoof');

        form.action = "<?php echo e(route('master-data.sub-jenis-spj.store')); ?>";
        title.textContent = "Tambah Sub Jenis SPJ Baru";
        input.value = "";
        spoof.innerHTML = "";

        modal.classList.remove('hidden');
        input.focus();
    }

    function openEditSubJenisModal(url, currentName) {
        const modal = document.getElementById('sub-jenis-modal');
        const form = document.getElementById('sub-jenis-modal-form');
        const title = document.getElementById('sub-jenis-modal-title');
        const input = document.getElementById('modal-nama-sub-jenis-spj');
        const spoof = document.getElementById('sub-jenis-method-spoof');

        form.action = url;
        title.textContent = "Ubah Sub Jenis SPJ";
        input.value = currentName;
        spoof.innerHTML = '<?php echo method_field("PUT"); ?>';

        modal.classList.remove('hidden');
        input.focus();
    }

    function closeSubJenisModal() {
        document.getElementById('sub-jenis-modal').classList.add('hidden');
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\KAPUASS\resources\views/master-data/sub-jenis-spj.blade.php ENDPATH**/ ?>