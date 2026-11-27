@extends('layouts.app')

@section('title', 'Ubah Data Arsip: ' . $arsip->kode_arsip)
@section('header-title', 'Ubah Data Arsip')
@section('header-subtitle', 'Perbarui metadata dokumen atau ganti berkas fisik')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('arsip.index') }}" class="hover:text-indigo-600">Data Arsip</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Ubah: {{ $arsip->kode_arsip }}</span>
    </nav>

    <!-- Main Card Form -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Perbarui Arsip Dokumen</h2>
                <p class="text-xs text-slate-500 mt-0.5">Ubah rincian identitas, klasifikasi, hierarki program via drawer, hal, atau file lampiran.</p>
            </div>
            <span class="font-mono text-xs font-bold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                {{ $arsip->kode_arsip }}
            </span>
        </div>

        <form action="{{ route('arsip.update', $arsip) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
            @csrf
            @method('PUT')

            <!-- Section 1: Identitas & Klasifikasi Arsip -->
            <div>
                <div class="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100">
                    <div class="w-2 h-2 rounded-full bg-indigo-600"></div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">1. Klasifikasi & Identitas Arsip</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kategori Arsip -->
                    <div>
                        <label for="id_kategori" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Kategori Dokumen <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <select id="id_kategori" name="id_kategori" required
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs cursor-pointer">
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id_kategori }}" {{ old('id_kategori', $arsip->id_kategori) == $kat->id_kategori ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tahun Dokumen Arsip (Master Data) -->
                    <div>
                        <label for="tahun_arsip_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Tahun Arsip
                        </label>
                        <div class="mt-1.5">
                            <select id="tahun_arsip_id" name="tahun_arsip_id"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs cursor-pointer">
                                <option value="">-- Pilih Tahun Arsip (Opsional) --</option>
                                @foreach($tahunArsips as $th)
                                    <option value="{{ $th->id }}" {{ old('tahun_arsip_id', $arsip->tahun_arsip_id) == $th->id ? 'selected' : '' }}>
                                        Tahun {{ $th->tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Nomor Item Arsip -->
                    <div>
                        <label for="no_item_arsip" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Nomor Item Arsip
                        </label>
                        <div class="mt-1.5">
                            <input type="text" id="no_item_arsip" name="no_item_arsip"
                                value="{{ old('no_item_arsip', $arsip->no_item_arsip) }}"
                                placeholder="Contoh: 001/IT/2026 atau 01/SP/I/2026"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                        </div>
                    </div>

                    <!-- Nomor Berkas -->
                    <div>
                        <label for="nomor_berkas" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Nomor Berkas
                        </label>
                        <div class="mt-1.5">
                            <input type="text" id="nomor_berkas" name="nomor_berkas"
                                value="{{ old('nomor_berkas', $arsip->nomor_berkas) }}"
                                placeholder="Contoh: 001 / B-001 / No.01"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                        </div>
                    </div>

                    <!-- Nomor Surat -->
                    <div>
                        <label for="nomor_surat" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Nomor Surat
                        </label>
                        <div class="mt-1.5">
                            <input type="text" id="nomor_surat" name="nomor_surat"
                                value="{{ old('nomor_surat', $arsip->nomor_surat) }}"
                                placeholder="Contoh: 900/304/DISKOMINFO/2025"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                        </div>
                    </div>

                    <!-- Tanggal Surat / Naskah -->
                    <div>
                        <label for="tanggal_surat" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Tanggal Surat / Naskah
                        </label>
                        <div class="mt-1.5">
                            <input type="date" id="tanggal_surat" name="tanggal_surat"
                                value="{{ old('tanggal_surat', $arsip->tanggal_surat ? $arsip->tanggal_surat->format('Y-m-d') : '') }}"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Tanggal asli yang tertera pada naskah/surat (opsional).</p>
                    </div>

                    <!-- Jumlah Surat -->
                    <div>
                        <label for="jumlah_surat" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Jumlah Surat / Lembar
                        </label>
                        <div class="mt-1.5">
                            <input type="number" id="jumlah_surat" name="jumlah_surat" min="1"
                                value="{{ old('jumlah_surat', $arsip->jumlah_surat ?? 1) }}"
                                placeholder="Contoh: 1"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Jumlah lembar / eksemplar naskah surat (standar Diskominfo, default 1).</p>
                    </div>

                    <!-- Kode Klasifikasi -->
                    <div>
                        <label for="kode_klasifikasi" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Kode Klasifikasi
                        </label>
                        <div class="mt-1.5">
                            <input type="text" id="kode_klasifikasi" name="kode_klasifikasi"
                                value="{{ old('kode_klasifikasi', $arsip->kode_klasifikasi) }}"
                                placeholder="Contoh: OT.00 / KU.01.02 / HM.01"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                        </div>
                    </div>

                    <!-- SKKAAD -->
                    <div>
                        <label for="skkad" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            SKKAAD (Klasifikasi Keamanan & Akses)
                        </label>
                        <div class="mt-1.5">
                            <select id="skkad" name="skkad"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs cursor-pointer">
                                <option value="">-- Pilih SKKAAD --</option>
                                <option value="Terbuka" {{ old('skkad', $arsip->skkad) === 'Terbuka' ? 'selected' : '' }}>Terbuka</option>
                                <option value="Tertutup" {{ old('skkad', $arsip->skkad) === 'Tertutup' ? 'selected' : '' }}>Tertutup</option>
                                <option value="Rahasia" {{ old('skkad', $arsip->skkad) === 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sub Jenis SPJ (Master Data) -->
                    <div>
                        <label for="sub_jenis_spj_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Sub Jenis SPJ
                        </label>
                        <div class="mt-1.5">
                            <select id="sub_jenis_spj_id" name="sub_jenis_spj_id" onchange="onSubJenisSpjChange(this)"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs cursor-pointer">
                                <option value="">-- Pilih Sub Jenis SPJ (Jika Arsip SPJ) --</option>
                                @foreach($subJenisSpjs as $spj)
                                    <option value="{{ $spj->id }}" data-nama="{{ $spj->nama_sub_jenis_spj }}" {{ old('sub_jenis_spj_id', $arsip->sub_jenis_spj_id) == $spj->id ? 'selected' : '' }}>
                                        {{ $spj->nama_sub_jenis_spj }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Sub Jenis Dokumen -->
                    <div class="sm:col-span-2">
                        <label for="sub_jenis" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Sub Jenis Arsip / Catatan Jenis
                        </label>
                        <div class="mt-1.5">
                            <input type="text" id="sub_jenis" name="sub_jenis"
                                value="{{ old('sub_jenis', $arsip->sub_jenis) }}"
                                placeholder="Contoh: Surat Edaran / Notulen Rapat / Bukti Pembayaran / SPJ GU"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Hirarki Program & Kegiatan (Cascading Side-Drawer Picker) -->
            <div>
                <div class="flex items-center justify-between pb-2 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">2. Program, Kegiatan & Sub Kegiatan</h3>
                    </div>
                    <span class="text-[11px] text-slate-400 font-medium">Pemilihan hierarki via Drawer</span>
                </div>

                <!-- Hidden inputs to submit selected values -->
                <input type="hidden" name="sub_kegiatan_id" id="sub_kegiatan_id" value="{{ old('sub_kegiatan_id', $arsip->sub_kegiatan_id) }}">
                <input type="hidden" name="program" id="program" value="{{ old('program', $arsip->program) }}">
                <input type="hidden" name="kegiatan" id="kegiatan" value="{{ old('kegiatan', $arsip->kegiatan) }}">
                <input type="hidden" name="sub_kegiatan" id="sub_kegiatan" value="{{ old('sub_kegiatan', $arsip->sub_kegiatan) }}">

                @php
                    $hasHierarchy = old('sub_kegiatan_id', $arsip->sub_kegiatan_id) || old('sub_kegiatan', $arsip->sub_kegiatan);
                    $existingTahun = $arsip->subKegiatan?->kegiatan?->program?->tahun_penetapan;
                @endphp

                <!-- State 1: Nothing Selected Yet -->
                <div id="hierarchy-empty-card" class="{{ $hasHierarchy ? 'hidden' : '' }} p-6 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/60 hover:bg-slate-50 text-center transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Hierarki Program Dipilih</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        Gunakan sliding drawer untuk memilih Tahun Penetapan, Program, Kegiatan, dan Sub Kegiatan secara konsisten dari master data.
                    </p>
                    <button type="button" onclick="openProgramPickerDrawer()"
                        class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-600/25 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Pilih Program, Kegiatan & Sub Kegiatan</span>
                    </button>
                </div>

                <!-- State 2: Hierarchy Selected (Card Display) -->
                <div id="hierarchy-selected-card" class="{{ $hasHierarchy ? '' : 'hidden' }} p-5 rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50/40 via-white to-purple-50/30 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-indigo-100">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-600 text-white uppercase tracking-wider">
                                Hierarki Terpilih
                            </span>
                            <span id="selected-card-year-badge" class="text-xs font-semibold text-slate-600">
                                {{ $existingTahun ? 'Tahun ' . $existingTahun : '' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <button type="button" onclick="openProgramPickerDrawer()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                <span>Ubah Pilihan</span>
                            </button>
                            <button type="button" onclick="resetHierarchySelection()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 transition-colors" title="Hapus Pilihan">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Reset</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3 Level Badges -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3">
                        <div class="p-3 rounded-xl bg-white border border-slate-200 shadow-xs">
                            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">1. Program</span>
                            <p id="selected-card-program" class="text-xs font-bold text-slate-800 mt-1 leading-snug">
                                {{ old('program', $arsip->program ?? '-') }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-white border border-slate-200 shadow-xs">
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">2. Kegiatan</span>
                            <p id="selected-card-kegiatan" class="text-xs font-bold text-slate-800 mt-1 leading-snug">
                                {{ old('kegiatan', $arsip->kegiatan ?? '-') }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-white border border-slate-200 shadow-xs">
                            <span class="text-[10px] font-bold text-purple-600 uppercase tracking-wider block">3. Sub Kegiatan</span>
                            <p id="selected-card-sub-kegiatan" class="text-xs font-bold text-slate-800 mt-1 leading-snug">
                                {{ old('sub_kegiatan', $arsip->sub_kegiatan ?? '-') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Detail Dokumen & Rincian Surat -->
            <div>
                <div class="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100">
                    <div class="w-2 h-2 rounded-full bg-emerald-600"></div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">3. Rincian Hal & Informasi Dokumen</h3>
                </div>

                <div class="space-y-4">
                    <!-- Judul Arsip -->
                    <div>
                        <label for="judul_arsip" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Judul / Nama Dokumen Arsip <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input type="text" id="judul_arsip" name="judul_arsip" required
                                value="{{ old('judul_arsip', $arsip->judul_arsip) }}"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Hal (contohnya surat pengantar) -->
                        <div>
                            <label for="hal" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Hal / Perihal
                            </label>
                            <div class="mt-1.5">
                                <input type="text" id="hal" name="hal"
                                    value="{{ old('hal', $arsip->hal) }}"
                                    placeholder="Contoh: Surat Pengantar / Undangan"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                            </div>
                        </div>

                        <!-- Tanggal Dokumen / Upload -->
                        <div>
                            <label for="tgl_upload" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Tanggal Registrasi / Upload <span class="text-rose-500">*</span>
                            </label>
                            <div class="mt-1.5">
                                <input type="date" id="tgl_upload" name="tgl_upload" required
                                    value="{{ old('tgl_upload', $arsip->tgl_upload ? $arsip->tgl_upload->format('Y-m-d') : date('Y-m-d')) }}"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                            </div>
                        </div>

                        <!-- Sumber / Asal Dokumen -->
                        <div>
                            <label for="sumber" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Sumber / Asal Surat
                            </label>
                            <div class="mt-1.5">
                                <input type="text" id="sumber" name="sumber"
                                    value="{{ old('sumber', $arsip->sumber) }}"
                                    placeholder="Contoh: Sekretariat Daerah / Instansi Pengirim"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                            </div>
                        </div>

                        <!-- Unit Pengolah / Bidang Pencipta -->
                        <div>
                            <label for="unit_pengolah" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Unit Pengolah / Bidang Pencipta
                            </label>
                            <div class="mt-1.5">
                                <input type="text" id="unit_pengolah" name="unit_pengolah"
                                    value="{{ old('unit_pengolah', $arsip->unit_pengolah) }}"
                                    placeholder="Contoh: Bidang Pengelolaan Arsip / Subbag Kepegawaian"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                            </div>
                            <p class="mt-1 text-[11px] text-slate-500">Unit kerja atau bidang yang memproses dan menciptakan naskah arsip (standar ANRI).</p>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Keterangan Tambahan
                        </label>
                        <div class="mt-1.5">
                            <textarea id="keterangan" name="keterangan" rows="2"
                                placeholder="Catatan atau keterangan ringkas terkait isi atau fisik arsip..."
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">{{ old('keterangan', $arsip->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Upload Berkas Fisik & Tautan Google Drive (Opsional saat Edit) -->
            <div>
                <div class="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100">
                    <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">4. Berkas Lampiran Dokumen</h3>
                </div>

                <div class="space-y-4">
                    <!-- URL Google Drive (Opsional) -->
                    <div>
                        <label for="google_drive_url" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            URL GOOGLE DRIVE (OPSIONAL)
                        </label>
                        <div class="mt-1.5">
                            <input type="url" id="google_drive_url" name="google_drive_url"
                                value="{{ old('google_drive_url', $arsip->google_drive_url) }}"
                                placeholder="https://drive.google.com/..."
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-xs">
                        </div>
                    </div>

                    <!-- Current file banner -->
                    @if($arsip->file_path)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs uppercase">
                                {{ $arsip->file_extension }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Berkas Saat Ini: {{ basename($arsip->file_path) }}</p>
                                <p class="text-[11px] text-slate-500">{{ $arsip->formatted_file_size }}</p>
                            </div>
                        </div>
                        <a href="{{ route('arsip.download', $arsip) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">
                            Unduh / Buka
                        </a>
                    </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Ganti Berkas Fisik (Opsional)
                        </label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-indigo-500 rounded-2xl p-6 text-center transition-colors bg-slate-50/50 relative">
                    <input type="file" id="file" name="file"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                        onchange="previewUploadedFileName(this)"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    </div>
                    
                    <p id="file-label-text" class="text-sm font-semibold text-slate-700">
                        Klik atau seret file ke area ini untuk mengganti berkas (Opsional)
                    </p>
                    <p class="text-xs text-slate-400 mt-1">
                        Biarkan kosong jika tidak ingin mengubah berkas dokumen. Format didukung: PDF, DOC/DOCX, XLS/XLSX, JPG, PNG (Maks 10MB).
                    </p>
                    <div id="file-preview-info" class="hidden mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold border border-indigo-200">
                        <span id="file-name-text"></span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('arsip.index') }}" class="px-5 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-600/25 rounded-xl transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Include Sliding Side-Drawer Component -->
@include('components.drawer-program-picker')

@push('scripts')
<script>
    function previewUploadedFileName(input) {
        const previewInfo = document.getElementById('file-preview-info');
        const fileNameText = document.getElementById('file-name-text');
        const labelText = document.getElementById('file-label-text');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const sizeInMb = (file.size / (1024 * 1024)).toFixed(2);
            fileNameText.textContent = `${file.name} (${sizeInMb} MB)`;
            previewInfo.classList.remove('hidden');
            labelText.textContent = 'Berkas pengganti dipilih:';
        } else {
            previewInfo.classList.add('hidden');
            labelText.textContent = 'Klik atau seret file ke area ini untuk mengganti berkas (Opsional)';
        }
    }

    function onProgramHierarchySelected(data) {
        document.getElementById('hierarchy-empty-card').classList.add('hidden');
        document.getElementById('hierarchy-selected-card').classList.remove('hidden');

        document.getElementById('selected-card-year-badge').textContent = 'Tahun ' + data.tahun;
        document.getElementById('selected-card-program').textContent = data.program;
        document.getElementById('selected-card-kegiatan').textContent = data.kegiatan;
        document.getElementById('selected-card-sub-kegiatan').textContent = data.subKegiatan;
    }

    function resetHierarchySelection() {
        document.getElementById('sub_kegiatan_id').value = '';
        document.getElementById('program').value = '';
        document.getElementById('kegiatan').value = '';
        document.getElementById('sub_kegiatan').value = '';

        document.getElementById('hierarchy-selected-card').classList.add('hidden');
        document.getElementById('hierarchy-empty-card').classList.remove('hidden');
    }

    function onSubJenisSpjChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const nama = selectedOption.getAttribute('data-nama');
        const subJenisInput = document.getElementById('sub_jenis');
        if (nama && (!subJenisInput.value || subJenisInput.value === '')) {
            subJenisInput.value = nama;
        }
    }
</script>
@endpush
@endsection
