<?php $__env->startSection('title', 'Hierarki Program, Kegiatan & Sub Kegiatan'); ?>
<?php $__env->startSection('header-title', 'Master Hierarki Program'); ?>
<?php $__env->startSection('header-subtitle', 'Kelola struktur hierarki Program, Kegiatan, dan Sub Kegiatan per tahun penetapan'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <!-- Top Action Bar & Filter Tahun -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Hierarki Program Kerja</h2>
                <p class="text-xs text-slate-500">Struktur berjenjang Program > Kegiatan > Sub Kegiatan untuk klasifikasi pengarsipan.</p>
            </div>

            <!-- Filter Tahun Penetapan -->
            <form method="GET" action="<?php echo e(route('master-data.program.index')); ?>" class="flex items-center gap-2">
                <label for="filter-tahun" class="text-xs font-semibold text-slate-600 whitespace-nowrap">Tahun:</label>
                <div class="relative">
                    <select id="filter-tahun" name="tahun" onchange="this.form.submit()"
                        class="text-xs font-bold text-slate-800 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        <?php $__empty_1 = true; $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <option value="<?php echo e($year); ?>" <?php echo e($selectedYear == $year ? 'selected' : ''); ?>>
                                Tahun <?php echo e($year); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <option value="<?php echo e(date('Y')); ?>"><?php echo e(date('Y')); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
            </form>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="toggleAllTrees()" id="btn-toggle-all"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                <span id="toggle-all-text">Buka Semua</span>
            </button>

            <button type="button" onclick="openCreateProgramModal(<?php echo \Illuminate\Support\Js::from($selectedYear)->toHtml() ?>)"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-600/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Program (<?php echo e($selectedYear); ?>)</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <?php
        $totalProgram = $programs->count();
        $totalKegiatan = $programs->sum(fn($p) => $p->kegiatans->count());
        $totalSubKegiatan = $programs->sum(fn($p) => $p->kegiatans->sum(fn($k) => $k->subKegiatans->count()));
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500">Program (<?php echo e($selectedYear); ?>)</p>
                <p class="text-lg font-bold text-slate-900"><?php echo e($totalProgram); ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500">Total Kegiatan</p>
                <p class="text-lg font-bold text-slate-900"><?php echo e($totalKegiatan); ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500">Total Sub Kegiatan</p>
                <p class="text-lg font-bold text-slate-900"><?php echo e($totalSubKegiatan); ?></p>
            </div>
        </div>
    </div>

    <!-- Tree View Hierarki -->
    <div class="space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $progIndex => $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden transition-all program-tree-card" id="program-card-<?php echo e($program->id); ?>">
            
            <!-- Level 1: Header Program -->
            <div class="p-4 sm:p-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <button type="button" onclick="toggleProgram(<?php echo e($program->id); ?>)" class="mt-0.5 sm:mt-0 p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition-colors">
                        <svg class="w-5 h-5 transform transition-transform duration-200 toggle-icon-<?php echo e($program->id); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-100 text-indigo-800 uppercase tracking-wide">
                                Program
                            </span>
                            <span class="text-xs font-medium text-slate-500">Tahun <?php echo e($program->tahun_penetapan); ?></span>
                            <span class="text-xs font-medium text-slate-400">•</span>
                            <span class="text-xs font-medium text-slate-500"><?php echo e($program->kegiatans->count()); ?> Kegiatan</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mt-1 cursor-pointer" onclick="toggleProgram(<?php echo e($program->id); ?>)">
                            <?php echo e($program->nama_program); ?>

                        </h3>
                    </div>
                </div>

                <!-- Action Program -->
                <div class="flex items-center gap-1.5 self-end sm:self-center">
                    <button type="button" title="Tambah Kegiatan di Program Ini"
                        onclick="openCreateKegiatanModal(<?php echo \Illuminate\Support\Js::from($program->id)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($program->nama_program)->toHtml() ?>)"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/60 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>+ Kegiatan</span>
                    </button>

                    <button type="button" title="Ubah Program"
                        onclick="openEditProgramModal(<?php echo \Illuminate\Support\Js::from(route('master-data.program.update', $program))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($program->nama_program)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($program->tahun_penetapan)->toHtml() ?>)"
                        class="p-1.5 rounded-lg text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>

                    <button type="button" title="Hapus Program"
                        onclick="confirmDelete(<?php echo \Illuminate\Support\Js::from(route('master-data.program.destroy', $program))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Hapus Program?')->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Apakah Anda yakin ingin menghapus program ini beserta kegiatan & sub kegiatannya? Program tidak dapat dihapus jika masih ada arsip yang terhubung.')->toHtml() ?>)"
                        class="p-1.5 rounded-lg text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Program Children (Kegiatan & Sub Kegiatan) -->
            <div id="program-content-<?php echo e($program->id); ?>" class="p-4 sm:p-5 space-y-4">
                <?php $__empty_2 = true; $__currentLoopData = $program->kegiatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kegIndex => $kegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                <div class="border border-slate-200/80 rounded-xl bg-slate-50/50 overflow-hidden">
                    
                    <!-- Level 2: Header Kegiatan -->
                    <div class="p-3 sm:p-4 bg-slate-100/70 border-b border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-start sm:items-center gap-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                                Kegiatan
                            </span>
                            <h4 class="text-sm font-bold text-slate-800">
                                <?php echo e($kegiatan->nama_kegiatan); ?>

                            </h4>
                        </div>

                        <!-- Action Kegiatan -->
                        <div class="flex items-center gap-1.5 self-end sm:self-center">
                            <button type="button" title="Tambah Sub Kegiatan di Kegiatan Ini"
                                onclick="openCreateSubKegiatanModal(<?php echo \Illuminate\Support\Js::from($kegiatan->id)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($kegiatan->nama_kegiatan)->toHtml() ?>)"
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[11px] font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200/60 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>+ Sub Kegiatan</span>
                            </button>

                            <button type="button" title="Ubah Kegiatan"
                                onclick="openEditKegiatanModal(<?php echo \Illuminate\Support\Js::from(route('master-data.kegiatan.update', $kegiatan))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($kegiatan->nama_kegiatan)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($program->id)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($program->nama_program)->toHtml() ?>)"
                                class="p-1.5 rounded-md text-amber-600 hover:text-amber-800 bg-white hover:bg-amber-50 border border-slate-200 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>

                            <button type="button" title="Hapus Kegiatan"
                                onclick="confirmDelete(<?php echo \Illuminate\Support\Js::from(route('master-data.kegiatan.destroy', $kegiatan))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Hapus Kegiatan?')->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Apakah Anda yakin ingin menghapus kegiatan ini beserta sub kegiatannya? Kegiatan tidak dapat dihapus jika masih terhubung ke berkas arsip.')->toHtml() ?>)"
                                class="p-1.5 rounded-md text-rose-600 hover:text-rose-800 bg-white hover:bg-rose-50 border border-slate-200 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Level 3: Daftar Sub Kegiatan -->
                    <div class="p-3 sm:p-4 space-y-2">
                        <?php $__empty_3 = true; $__currentLoopData = $kegiatan->subKegiatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subIndex => $subKegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_3 = false; ?>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-2.5 rounded-lg bg-white border border-slate-200 hover:border-indigo-200 transition-colors gap-2">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-100 text-purple-800 uppercase tracking-wide">
                                    Sub Kegiatan
                                </span>
                                <span class="text-xs font-semibold text-slate-800">
                                    <?php echo e($subKegiatan->nama_sub_kegiatan); ?>

                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo e($subKegiatan->arsip_count > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-500'); ?>">
                                    <?php echo e($subKegiatan->arsip_count); ?> arsip
                                </span>
                            </div>

                            <!-- Action Sub Kegiatan -->
                            <div class="flex items-center gap-1.5 self-end sm:self-center">
                                <button type="button" title="Ubah Sub Kegiatan"
                                    onclick="openEditSubKegiatanModal(<?php echo \Illuminate\Support\Js::from(route('master-data.sub-kegiatan.update', $subKegiatan))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($subKegiatan->nama_sub_kegiatan)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($kegiatan->id)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($kegiatan->nama_kegiatan)->toHtml() ?>)"
                                    class="p-1 rounded text-amber-600 hover:text-amber-800 hover:bg-amber-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <button type="button" title="Hapus Sub Kegiatan"
                                    onclick="confirmDelete(<?php echo \Illuminate\Support\Js::from(route('master-data.sub-kegiatan.destroy', $subKegiatan))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Hapus Sub Kegiatan?')->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Apakah Anda yakin ingin menghapus sub kegiatan ini? Data tidak dapat dihapus jika masih digunakan oleh berkas arsip.')->toHtml() ?>)"
                                    class="p-1 rounded text-rose-600 hover:text-rose-800 hover:bg-rose-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_3): ?>
                        <div class="py-2 px-3 rounded-lg bg-white/60 border border-dashed border-slate-200 text-center text-xs text-slate-400">
                            Belum ada sub kegiatan. Klik <span class="font-bold text-purple-600">+ Sub Kegiatan</span> untuk menambahkan.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                <div class="py-6 px-4 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center text-xs text-slate-400">
                    Belum ada kegiatan pada program ini. Klik <span class="font-bold text-emerald-600">+ Kegiatan</span> untuk menambahkan kegiatan pertama.
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-500 mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Belum Ada Program untuk Tahun <?php echo e($selectedYear); ?></h3>
            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                Silakan tambahkan program kerja baru untuk tahun <?php echo e($selectedYear); ?> menggunakan tombol di bawah ini.
            </p>
            <button type="button" onclick="openCreateProgramModal(<?php echo \Illuminate\Support\Js::from($selectedYear)->toHtml() ?>)"
                class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Program Tahun <?php echo e($selectedYear); ?></span>
            </button>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal Program -->
<div id="program-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
        <h3 class="text-base font-bold text-slate-800" id="program-modal-title">Tambah Program Baru</h3>
        <form id="program-modal-form" method="POST" action="<?php echo e(route('master-data.program.store')); ?>" class="mt-4 space-y-4">
            <?php echo csrf_field(); ?>
            <div id="program-method-spoof"></div>

            <div>
                <label for="modal-tahun-penetapan" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Tahun Penetapan <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="modal-tahun-penetapan" name="tahun_penetapan" required min="1900" max="2100"
                    class="mt-1.5 w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm">
            </div>

            <div>
                <label for="modal-nama-program" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Nama Program <span class="text-rose-500">*</span>
                </label>
                <textarea id="modal-nama-program" name="nama_program" required rows="3"
                    placeholder="Contoh: Program Pengelolaan dan Pengembangan Sistem Kearsipan"
                    class="mt-1.5 w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('program-modal')" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm shadow-indigo-600/25 transition-all">
                    Simpan Program
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kegiatan -->
<div id="kegiatan-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
        <h3 class="text-base font-bold text-slate-800" id="kegiatan-modal-title">Tambah Kegiatan Baru</h3>
        <p class="text-xs text-slate-500 mt-1" id="kegiatan-modal-subtitle">Untuk program: ...</p>
        
        <form id="kegiatan-modal-form" method="POST" action="<?php echo e(route('master-data.kegiatan.store')); ?>" class="mt-4 space-y-4">
            <?php echo csrf_field(); ?>
            <div id="kegiatan-method-spoof"></div>
            <input type="hidden" id="modal-kegiatan-program-id" name="program_id" value="">

            <div>
                <label for="modal-nama-kegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Nama Kegiatan <span class="text-rose-500">*</span>
                </label>
                <textarea id="modal-nama-kegiatan" name="nama_kegiatan" required rows="3"
                    placeholder="Contoh: Pengelolaan Arsip Dinamis di Lingkungan Daerah"
                    class="mt-1.5 w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('kegiatan-modal')" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm shadow-emerald-600/25 transition-all">
                    Simpan Kegiatan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Sub Kegiatan -->
<div id="sub-kegiatan-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
        <h3 class="text-base font-bold text-slate-800" id="sub-kegiatan-modal-title">Tambah Sub Kegiatan Baru</h3>
        <p class="text-xs text-slate-500 mt-1" id="sub-kegiatan-modal-subtitle">Untuk kegiatan: ...</p>
        
        <form id="sub-kegiatan-modal-form" method="POST" action="<?php echo e(route('master-data.sub-kegiatan.store')); ?>" class="mt-4 space-y-4">
            <?php echo csrf_field(); ?>
            <div id="sub-kegiatan-method-spoof"></div>
            <input type="hidden" id="modal-sub-kegiatan-kegiatan-id" name="kegiatan_id" value="">

            <div>
                <label for="modal-nama-sub-kegiatan" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Nama Sub Kegiatan <span class="text-rose-500">*</span>
                </label>
                <textarea id="modal-nama-sub-kegiatan" name="nama_sub_kegiatan" required rows="3"
                    placeholder="Contoh: Pengadaan Sarana dan Prasarana Kearsipan"
                    class="mt-1.5 w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('sub-kegiatan-modal')" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 rounded-lg shadow-sm shadow-purple-600/25 transition-all">
                    Simpan Sub Kegiatan
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    let isAllExpanded = true;

    function toggleProgram(id) {
        const content = document.getElementById('program-content-' + id);
        const icon = document.querySelector('.toggle-icon-' + id);
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            if (icon) icon.classList.remove('-rotate-90');
        } else {
            content.classList.add('hidden');
            if (icon) icon.classList.add('-rotate-90');
        }
    }

    function toggleAllTrees() {
        const allContents = document.querySelectorAll('[id^="program-content-"]');
        const allIcons = document.querySelectorAll('[class*="toggle-icon-"]');
        const toggleText = document.getElementById('toggle-all-text');

        if (isAllExpanded) {
            allContents.forEach(c => c.classList.add('hidden'));
            allIcons.forEach(i => i.classList.add('-rotate-90'));
            toggleText.textContent = 'Buka Semua';
            isAllExpanded = false;
        } else {
            allContents.forEach(c => c.classList.remove('hidden'));
            allIcons.forEach(i => i.classList.remove('-rotate-90'));
            toggleText.textContent = 'Tutup Semua';
            isAllExpanded = true;
        }
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Program Modal Handlers
    function openCreateProgramModal(year) {
        const modal = document.getElementById('program-modal');
        const form = document.getElementById('program-modal-form');
        const title = document.getElementById('program-modal-title');
        const yearInput = document.getElementById('modal-tahun-penetapan');
        const nameInput = document.getElementById('modal-nama-program');
        const spoof = document.getElementById('program-method-spoof');

        form.action = "<?php echo e(route('master-data.program.store')); ?>";
        title.textContent = "Tambah Program Baru";
        yearInput.value = year || new Date().getFullYear();
        nameInput.value = "";
        spoof.innerHTML = "";

        modal.classList.remove('hidden');
        nameInput.focus();
    }

    function openEditProgramModal(url, name, year) {
        const modal = document.getElementById('program-modal');
        const form = document.getElementById('program-modal-form');
        const title = document.getElementById('program-modal-title');
        const yearInput = document.getElementById('modal-tahun-penetapan');
        const nameInput = document.getElementById('modal-nama-program');
        const spoof = document.getElementById('program-method-spoof');

        form.action = url;
        title.textContent = "Ubah Program";
        yearInput.value = year;
        nameInput.value = name;
        spoof.innerHTML = '<?php echo method_field("PUT"); ?>';

        modal.classList.remove('hidden');
        nameInput.focus();
    }

    // Kegiatan Modal Handlers
    function openCreateKegiatanModal(programId, programName) {
        const modal = document.getElementById('kegiatan-modal');
        const form = document.getElementById('kegiatan-modal-form');
        const title = document.getElementById('kegiatan-modal-title');
        const subtitle = document.getElementById('kegiatan-modal-subtitle');
        const progInput = document.getElementById('modal-kegiatan-program-id');
        const nameInput = document.getElementById('modal-nama-kegiatan');
        const spoof = document.getElementById('kegiatan-method-spoof');

        form.action = "<?php echo e(route('master-data.kegiatan.store')); ?>";
        title.textContent = "Tambah Kegiatan Baru";
        subtitle.textContent = "Untuk Program: " + programName;
        progInput.value = programId;
        nameInput.value = "";
        spoof.innerHTML = "";

        modal.classList.remove('hidden');
        nameInput.focus();
    }

    function openEditKegiatanModal(url, name, programId, programName) {
        const modal = document.getElementById('kegiatan-modal');
        const form = document.getElementById('kegiatan-modal-form');
        const title = document.getElementById('kegiatan-modal-title');
        const subtitle = document.getElementById('kegiatan-modal-subtitle');
        const progInput = document.getElementById('modal-kegiatan-program-id');
        const nameInput = document.getElementById('modal-nama-kegiatan');
        const spoof = document.getElementById('kegiatan-method-spoof');

        form.action = url;
        title.textContent = "Ubah Kegiatan";
        subtitle.textContent = "Untuk Program: " + programName;
        progInput.value = programId;
        nameInput.value = name;
        spoof.innerHTML = '<?php echo method_field("PUT"); ?>';

        modal.classList.remove('hidden');
        nameInput.focus();
    }

    // Sub Kegiatan Modal Handlers
    function openCreateSubKegiatanModal(kegiatanId, kegiatanName) {
        const modal = document.getElementById('sub-kegiatan-modal');
        const form = document.getElementById('sub-kegiatan-modal-form');
        const title = document.getElementById('sub-kegiatan-modal-title');
        const subtitle = document.getElementById('sub-kegiatan-modal-subtitle');
        const kegInput = document.getElementById('modal-sub-kegiatan-kegiatan-id');
        const nameInput = document.getElementById('modal-nama-sub-kegiatan');
        const spoof = document.getElementById('sub-kegiatan-method-spoof');

        form.action = "<?php echo e(route('master-data.sub-kegiatan.store')); ?>";
        title.textContent = "Tambah Sub Kegiatan Baru";
        subtitle.textContent = "Untuk Kegiatan: " + kegiatanName;
        kegInput.value = kegiatanId;
        nameInput.value = "";
        spoof.innerHTML = "";

        modal.classList.remove('hidden');
        nameInput.focus();
    }

    function openEditSubKegiatanModal(url, name, kegiatanId, kegiatanName) {
        const modal = document.getElementById('sub-kegiatan-modal');
        const form = document.getElementById('sub-kegiatan-modal-form');
        const title = document.getElementById('sub-kegiatan-modal-title');
        const subtitle = document.getElementById('sub-kegiatan-modal-subtitle');
        const kegInput = document.getElementById('modal-sub-kegiatan-kegiatan-id');
        const nameInput = document.getElementById('modal-nama-sub-kegiatan');
        const spoof = document.getElementById('sub-kegiatan-method-spoof');

        form.action = url;
        title.textContent = "Ubah Sub Kegiatan";
        subtitle.textContent = "Untuk Kegiatan: " + kegiatanName;
        kegInput.value = kegiatanId;
        nameInput.value = name;
        spoof.innerHTML = '<?php echo method_field("PUT"); ?>';

        modal.classList.remove('hidden');
        nameInput.focus();
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\KAPUASS\resources\views/master-data/hierarki-program.blade.php ENDPATH**/ ?>