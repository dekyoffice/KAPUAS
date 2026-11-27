<?php $__env->startSection('title', 'Kelola Pengguna'); ?>
<?php $__env->startSection('header-title', 'Manajemen Pengguna Sistem'); ?>
<?php $__env->startSection('header-subtitle', 'Pengelolaan akun, penetapan hak akses, dan audit pengguna'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <!-- Top Action & Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Akun Pengguna</h2>
                <p class="text-xs text-slate-500">Total pengguna: <span class="font-bold text-slate-700"><?php echo e($users->total()); ?> akun terdaftar</span></p>
            </div>

            <a href="<?php echo e(route('users.create')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white shadow-sm transition-all hover:-translate-y-0.5 duration-150" style="background: linear-gradient(135deg, #003CAA, #0050C8)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <span>Tambah Pengguna Baru</span>
            </a>
        </div>

        <!-- Filter Form -->
        <form action="<?php echo e(route('users.index')); ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-3 border-t border-slate-100">
            <div class="sm:col-span-7 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Cari nama pengguna atau email..."
                    class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-line rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:bg-white text-ink placeholder-ink-muted/60">
            </div>

            <div class="sm:col-span-3">
                <select name="role" class="w-full py-2 px-3 text-xs bg-slate-50 border border-line rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:bg-white text-ink cursor-pointer">
                    <option value="">-- Semua Peran (Role) --</option>
                    <option value="admin" <?php echo e($selectedRole === 'admin' ? 'selected' : ''); ?>>Admin</option>
                    <option value="operator" <?php echo e($selectedRole === 'operator' ? 'selected' : ''); ?>>Operator</option>
                    <option value="viewer" <?php echo e($selectedRole === 'viewer' ? 'selected' : ''); ?>>Viewer</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold text-white bg-navy hover:bg-navy-dark transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <span>Filter</span>
                </button>
                <?php if($search || $selectedRole): ?>
                <a href="<?php echo e(route('users.index')); ?>" title="Reset Filter" class="p-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- User Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <?php if($users->isEmpty()): ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Pengguna Tidak Ditemukan</h3>
            <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau filter peran.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="text-[11px] uppercase tracking-wider font-semibold border-b border-line" style="background: rgba(210,230,250,0.25); color: #1E293B">
                    <tr>
                        <th class="px-6 py-3.5">Nama Pengguna</th>
                        <th class="px-6 py-3.5">Alamat Email</th>
                        <th class="px-6 py-3.5">Peran (Role)</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Dokumen Diunggah</th>
                        <th class="px-6 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-ice/20 transition-colors duration-100">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full text-white font-bold text-xs flex items-center justify-center shrink-0" style="background: linear-gradient(135deg, #003CAA, #3296FA)">
                                    <?php echo e(strtoupper(substr($user->nama_user, 0, 2))); ?>

                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                        <?php echo e($user->nama_user); ?>

                                        <?php if($user->id_user === auth()->user()->id_user): ?>
                                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-600 border border-indigo-200">Anda</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400">Terdaftar: <?php echo e($user->created_at ? $user->created_at->format('d/m/Y') : '-'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-mono text-slate-700">
                            <?php echo e($user->email); ?>

                        </td>
                        <td class="px-6 py-4">
                            <?php if($user->isAdmin()): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold text-white" style="background: #003CAA">
                                    Admin
                                </span>
                            <?php elseif($user->isOperator()): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold text-white" style="background: #0050C8">
                                    Operator
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border border-ink-muted text-ink-muted">
                                    Viewer
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($user->isAktif()): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Aktif
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Nonaktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                            <?php echo e(number_format($user->arsip_count)); ?> arsip
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="<?php echo e(route('users.edit', $user)); ?>" title="Ubah Pengguna"
                                    class="p-2 rounded-lg text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>

                                <?php if($user->id_user !== auth()->user()->id_user): ?>
                                <button type="button" title="Hapus Pengguna"
                                    onclick="confirmDelete(<?php echo \Illuminate\Support\Js::from(route('users.destroy', $user))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Hapus Pengguna '.$user->nama_user.'?')->toHtml() ?>, <?php echo \Illuminate\Support\Js::from('Apakah Anda yakin ingin menghapus akun '.$user->nama_user.' ('.$user->email.')?')->toHtml() ?>)"
                                    class="p-2 rounded-lg text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-line bg-ice/10">
            <?php echo e($users->links()); ?>

        </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\KAPUASS\resources\views/users/index.blade.php ENDPATH**/ ?>