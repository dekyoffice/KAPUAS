<?php $__env->startSection('title', 'Master Tahun Arsip'); ?>
<?php $__env->startSection('header-title', 'Master Tahun Arsip'); ?>
<?php $__env->startSection('header-subtitle', 'Kelola daftar tahun anggaran / tahun penciptaan berkas arsip'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <!-- Top Action & Search Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Tahun Arsip</h2>
            <p class="text-xs text-slate-500">Kelola tahun penciptaan arsip. Data tahun yang masih memiliki arsip terhubung tidak dapat dihapus.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Search Form -->
            <form method="GET" action="<?php echo e(route('master-data.tahun-arsip.index')); ?>" class="relative">
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Cari tahun..."
                    class="w-full sm:w-48 pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-slate-800">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>

            <button type="button" onclick="openCreateTahunModal()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-600/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Tahun Arsip</span>
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
                        <th class="px-6 py-3.5">Tahun Arsip</th>
                        <th class="px-6 py-3.5">Jumlah Berkas Arsip</th>
                        <th class="px-6 py-3.5">Tanggal Ditambahkan</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $tahunArsips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-xs font-bold text-slate-400">
                            <?php echo e($tahunArsips->firstItem() + $index); ?>

                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-slate-100 text-slate-800">
                                <?php echo e($item->tahun); ?>

                            </span>
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
                                <button type="button" title="Ubah Tahun"
                                    onclick="openEditTahunModal(<?php echo \Illuminate\Support\Js::from(route('master-data.tahun-arsip.update', $item))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($item->tahun)->toHtml() ?>)"
                                    class="p-2 rounded-lg text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <button type="button" title="Hapus Tahun Arsip"
                                    onclick="confirmDelete(<?php echo \Illuminate\Support\Js::from(route('master-data.tahun-arsip.destroy', $item))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Hapus Tahun '.$item->tahun.'?')->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Apakah Anda yakin ingin menghapus data tahun ini? Data tidak dapat dihapus jika masih digunakan oleh berkas arsip.')->toHtml() ?>)"
                                    class="p-2 rounded-lg text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="p-12 text-center text-xs text-slate-500">
                            <?php echo e($search ? 'Tidak ada tahun arsip yang cocok dengan pencarian.' : 'Belum ada data tahun arsip.'); ?>

                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            <?php echo e($tahunArsips->links()); ?>

        </div>
    </div>

</div>

<!-- Modal Tambah/Edit Tahun Arsip -->
<div id="tahun-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
        <h3 class="text-base font-bold text-slate-800" id="tahun-modal-title">Tambah Tahun Arsip</h3>
        <form id="tahun-modal-form" method="POST" action="<?php echo e(route('master-data.tahun-arsip.store')); ?>" class="mt-4 space-y-4">
            <?php echo csrf_field(); ?>
            <div id="tahun-method-spoof"></div>

            <div>
                <label for="modal-tahun" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Tahun <span class="text-rose-500">*</span>
                </label>
                <div class="mt-1.5">
                    <input type="number" id="modal-tahun" name="tahun" required min="1900" max="2100"
                        placeholder="Contoh: <?php echo e(date('Y')); ?>"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeTahunModal()" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
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
    function openCreateTahunModal() {
        const modal = document.getElementById('tahun-modal');
        const form = document.getElementById('tahun-modal-form');
        const title = document.getElementById('tahun-modal-title');
        const input = document.getElementById('modal-tahun');
        const spoof = document.getElementById('tahun-method-spoof');

        form.action = "<?php echo e(route('master-data.tahun-arsip.store')); ?>";
        title.textContent = "Tambah Tahun Arsip Baru";
        input.value = new Date().getFullYear();
        spoof.innerHTML = "";

        modal.classList.remove('hidden');
        input.focus();
    }

    function openEditTahunModal(url, currentYear) {
        const modal = document.getElementById('tahun-modal');
        const form = document.getElementById('tahun-modal-form');
        const title = document.getElementById('tahun-modal-title');
        const input = document.getElementById('modal-tahun');
        const spoof = document.getElementById('tahun-method-spoof');

        form.action = url;
        title.textContent = "Ubah Tahun Arsip";
        input.value = currentYear;
        spoof.innerHTML = '<?php echo method_field("PUT"); ?>';

        modal.classList.remove('hidden');
        input.focus();
    }

    function closeTahunModal() {
        document.getElementById('tahun-modal').classList.add('hidden');
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\KAPUASS\resources\views/master-data/tahun-arsip.blade.php ENDPATH**/ ?>