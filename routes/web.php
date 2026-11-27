<?php

use App\Http\Controllers\ArsipController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard atau login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Autentikasi Tamu (Guest)
Route::middleware(['guest', 'no-store'])->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Area Terproteksi Autentikasi
Route::middleware(['auth', 'active', 'no-store'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Modul Arsip: Akses Admin & Operator untuk modifikasi data
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/arsip/create', [ArsipController::class, 'create'])->name('arsip.create');
        Route::post('/arsip', [ArsipController::class, 'store'])->name('arsip.store');
        Route::get('/arsip/{arsip}/edit', [ArsipController::class, 'edit'])->name('arsip.edit');
        Route::put('/arsip/{arsip}', [ArsipController::class, 'update'])->name('arsip.update');
        Route::delete('/arsip/{arsip}', [ArsipController::class, 'destroy'])->name('arsip.destroy');

        // Modul Kategori (Admin & Operator)
        Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // Modul Arsip: Akses semua peran (Admin, Operator, Viewer)
    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
    Route::get('/arsip/export-excel', [ArsipController::class, 'exportExcel'])->name('arsip.exportExcel');
    Route::get('/arsip/export-pdf', [ArsipController::class, 'exportPdf'])->name('arsip.exportPdf');
    Route::get('/arsip/{arsip}/view', [ArsipController::class, 'showFile'])->name('arsip.showFile');
    Route::get('/arsip/{arsip}/download', [ArsipController::class, 'download'])->name('arsip.download');
    Route::get('/arsip/{arsip}/download-excel', [ArsipController::class, 'downloadExcel'])->name('arsip.downloadExcel');

    // Modul Pengguna: Akses Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);

        // Modul Master Data: Akses Khusus Admin
        Route::prefix('master-data')->name('master-data.')->group(function () {
            // Hierarki Program
            Route::get('/hierarki-program', \App\Http\Controllers\Master\ProgramController::class . '@index')->name('program.index');
            Route::post('/program', [\App\Http\Controllers\Master\ProgramController::class, 'store'])->name('program.store');
            Route::put('/program/{program}', [\App\Http\Controllers\Master\ProgramController::class, 'update'])->name('program.update');
            Route::delete('/program/{program}', [\App\Http\Controllers\Master\ProgramController::class, 'destroy'])->name('program.destroy');

            // Kegiatan
            Route::post('/kegiatan', [\App\Http\Controllers\Master\KegiatanController::class, 'store'])->name('kegiatan.store');
            Route::put('/kegiatan/{kegiatan}', [\App\Http\Controllers\Master\KegiatanController::class, 'update'])->name('kegiatan.update');
            Route::delete('/kegiatan/{kegiatan}', [\App\Http\Controllers\Master\KegiatanController::class, 'destroy'])->name('kegiatan.destroy');

            // Sub Kegiatan
            Route::post('/sub-kegiatan', [\App\Http\Controllers\Master\SubKegiatanController::class, 'store'])->name('sub-kegiatan.store');
            Route::put('/sub-kegiatan/{subKegiatan}', [\App\Http\Controllers\Master\SubKegiatanController::class, 'update'])->name('sub-kegiatan.update');
            Route::delete('/sub-kegiatan/{subKegiatan}', [\App\Http\Controllers\Master\SubKegiatanController::class, 'destroy'])->name('sub-kegiatan.destroy');

            // Sub Jenis SPJ
            Route::resource('sub-jenis-spj', \App\Http\Controllers\Master\SubJenisSpjController::class)->except(['create', 'show', 'edit']);

            // Tahun Arsip
            Route::resource('tahun-arsip', \App\Http\Controllers\Master\TahunArsipController::class)->except(['create', 'show', 'edit']);
        });
    });

    // API Internal Master Data untuk Drawer Cascading Picker (Admin & Operator)
    Route::middleware('role:admin,operator')->prefix('api/master')->name('api.master.')->group(function () {
        Route::get('/tahun-program', [\App\Http\Controllers\Master\ProgramController::class, 'daftarTahun'])->name('tahun-program');
        Route::get('/programs', [\App\Http\Controllers\Master\ProgramController::class, 'byTahun'])->name('programs');
        Route::get('/kegiatans', [\App\Http\Controllers\Master\KegiatanController::class, 'byProgram'])->name('kegiatans');
        Route::get('/sub-kegiatans', [\App\Http\Controllers\Master\SubKegiatanController::class, 'byKegiatan'])->name('sub-kegiatans');
    });
});
