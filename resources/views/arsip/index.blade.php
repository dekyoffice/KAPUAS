@extends('layouts.app')

@section('title', 'Data Arsip')
@section('header-title', 'Manajemen Data Arsip')
@section('header-subtitle', 'Pencarian, pemfilteran, pengunggahan, dan pengelolaan berkas digital')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4">
        
        <!-- Header row with title & add button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Dokumen Arsip</h2>
                <p class="text-xs text-slate-500">Total terdata: <span class="font-bold text-slate-700">{{ $arsips->total() }} dokumen</span></p>
            </div>

            <div class="flex items-center flex-wrap gap-2">
                <!-- Tombol Ekspor Excel -->
                <a href="{{ route('arsip.exportExcel', request()->query()) }}" title="Unduh semua/hasil filter sebagai berkas Excel (.xlsx)"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all shadow-xs hover:-translate-y-0.5 duration-150">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Ekspor Excel</span>
                </a>

                <!-- Tombol Cetak PDF -->
                <a href="{{ route('arsip.exportPdf', request()->query()) }}" target="_blank" title="Cetak daftar arsip ke dokumen PDF resmi (A4 Landscape)"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-all shadow-xs hover:-translate-y-0.5 duration-150">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak PDF</span>
                </a>

                @if(auth()->user()->canManageArsip())
                <a href="{{ route('arsip.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white shadow-sm transition-all hover:-translate-y-0.5 duration-150" style="background: linear-gradient(135deg, #003CAA, #0050C8)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Arsip Baru</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('arsip.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-3 border-t border-slate-100">
            
            <!-- Search Keyword Input -->
            <div class="sm:col-span-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul, nomor surat, nomor berkas, unit..."
                    class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-line rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:bg-white text-ink placeholder-ink-muted/60">
            </div>

            <!-- Category Filter Dropdown -->
            <div class="sm:col-span-3">
                <select name="kategori" class="w-full py-2 px-3 text-xs bg-slate-50 border border-line rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:bg-white text-ink cursor-pointer">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id_kategori }}" {{ $selectedKategori == $kat->id_kategori ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter Dropdown -->
            <div class="sm:col-span-2">
                <select name="status" class="w-full py-2 px-3 text-xs bg-slate-50 border border-line rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:bg-white text-ink cursor-pointer">
                    <option value="">-- Semua Status --</option>
                    <option value="fisik_digital" {{ ($selectedStatus ?? '') === 'fisik_digital' ? 'selected' : '' }}>Fisik & Digital</option>
                    <option value="fisik" {{ ($selectedStatus ?? '') === 'fisik' ? 'selected' : '' }}>Fisik Saja</option>
                    <option value="digital" {{ ($selectedStatus ?? '') === 'digital' ? 'selected' : '' }}>Digital Saja</option>
                    <option value="tercatat" {{ ($selectedStatus ?? '') === 'tercatat' ? 'selected' : '' }}>Tercatat Saja</option>
                </select>
            </div>

            <!-- Year Filter Dropdown -->
            <div class="sm:col-span-1">
                <select name="tahun" class="w-full py-2 px-2 text-xs bg-slate-50 border border-line rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:bg-white text-ink cursor-pointer">
                    <option value="">Thn</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ $selectedTahun == $yr ? 'selected' : '' }}>
                            {{ $yr }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold text-white bg-navy hover:bg-navy-dark transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <span>Filter</span>
                </button>
                @if($search || $selectedKategori || $selectedTahun || !empty($selectedStatus))
                <a href="{{ route('arsip.index') }}" title="Reset Filter" class="p-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        @if($arsips->isEmpty())
        <!-- Empty State -->
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background: rgba(0,80,200,0.08); color: #0050C8">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Data Arsip Tidak Ditemukan</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Tidak ada dokumen yang cocok dengan kata kunci atau kriteria filter yang Anda pilih.
            </p>
            <div class="mt-5 flex items-center justify-center gap-3">
                @if($search || $selectedKategori || $selectedTahun)
                <a href="{{ route('arsip.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Reset Filter Pencarian
                </a>
                @endif
                @if(auth()->user()->canManageArsip())
                <a href="{{ route('arsip.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white transition-colors" style="background: #0050C8">
                    Tambah Arsip Baru
                </a>
                @endif
            </div>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 border-collapse">
                <thead class="text-[11px] uppercase tracking-wider font-bold border-b border-line" style="background: rgba(210,230,250,0.25); color: #1E293B">
                    <tr>
                        <th class="px-3 py-3 text-center w-12 whitespace-nowrap">No</th>
                        <th class="px-3 py-3 whitespace-nowrap">No Item Arsip</th>
                        <th class="px-3 py-3 whitespace-nowrap">Kategori Surat</th>
                        <th class="px-3 py-3 whitespace-nowrap">Nomor Berkas</th>
                        <th class="px-3 py-3 whitespace-nowrap">Nomor Surat</th>
                        <th class="px-3 py-3 whitespace-nowrap">Tgl Surat</th>
                        <th class="px-3 py-3 whitespace-nowrap">Kode Klasifikasi</th>
                        <th class="px-3 py-3 whitespace-nowrap">SKKAAD</th>
                        <th class="px-3 py-3 whitespace-nowrap">Sub Jenis</th>
                        <th class="px-4 py-3 min-w-[160px]">Program</th>
                        <th class="px-4 py-3 min-w-[160px]">Kegiatan</th>
                        <th class="px-4 py-3 min-w-[160px]">Sub Kegiatan</th>
                        <th class="px-3 py-3 whitespace-nowrap">Tgl Registrasi</th>
                        <th class="px-4 py-3 min-w-[200px]">Hal</th>
                        <th class="px-3 py-3 min-w-[130px]">Unit Pengolah</th>
                        <th class="px-3 py-3 min-w-[130px]">Sumber</th>
                        <th class="px-3 py-3 min-w-[140px]">Keterangan</th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">Status Berkas</th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">File Dokumen</th>
                        <th class="px-3 py-3 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($arsips as $index => $arsip)
                    <tr class="hover:bg-ice/20 transition-colors duration-100">
                        <!-- 1. Urutan nomor -->
                        <td class="px-3 py-3.5 text-center font-bold text-slate-400 whitespace-nowrap">
                            {{ $arsips->firstItem() + $index }}
                        </td>

                        <!-- 2. No item arsip -->
                        <td class="px-3 py-3.5 font-mono font-bold whitespace-nowrap" style="color: #0050C8">
                            {{ $arsip->no_item_arsip ?: $arsip->kode_arsip }}
                        </td>

                        <!-- 3. Kategori Surat -->
                        <td class="px-3 py-3.5 whitespace-nowrap">
                            @if($arsip->kategori)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border" style="background: rgba(210,230,250,0.5); color: #0050C8; border-color: rgba(0,80,200,0.2)">
                                    {{ $arsip->kategori->nama_kategori }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <!-- 4. Nomor Berkas -->
                        <td class="px-3 py-3.5 font-mono text-slate-700 whitespace-nowrap">
                            {{ $arsip->nomor_berkas ?: '-' }}
                        </td>

                        <!-- 5. Nomor Surat -->
                        <td class="px-3 py-3.5 font-mono text-slate-800 font-semibold whitespace-nowrap">
                            {{ $arsip->nomor_surat ?: '-' }}
                        </td>

                        <!-- 5b. Tanggal Surat -->
                        <td class="px-3 py-3.5 whitespace-nowrap font-mono text-slate-700">
                            {{ $arsip->tanggal_surat ? $arsip->tanggal_surat->format('d M Y') : '-' }}
                        </td>

                        <!-- 6. Kode klasifikasi -->
                        <td class="px-3 py-3.5 font-mono text-slate-700 whitespace-nowrap">
                            {{ $arsip->kode_klasifikasi ?: '-' }}
                        </td>

                        <!-- 7. SKKAD -->
                        <td class="px-3 py-3.5 whitespace-nowrap">
                            @if($arsip->skkad)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border" style="background: rgba(210,230,250,0.5); color: #0050C8; border-color: rgba(0,80,200,0.2)">
                                    {{ $arsip->skkad }}
                                </span>
                            @else
                                <span class="text-ink-muted">-</span>
                            @endif
                        </td>

                        <!-- 8. Sub jenis -->
                        <td class="px-3 py-3.5 whitespace-nowrap font-medium text-slate-800">
                            {{ $arsip->sub_jenis ?: ($arsip->kategori->nama_kategori ?? '-') }}
                        </td>

                        <!-- 9. Program -->
                        <td class="px-4 py-3.5 text-slate-700">
                            {{ $arsip->program ?: '-' }}
                        </td>

                        <!-- 10. Kegiatan -->
                        <td class="px-4 py-3.5 text-slate-700">
                            {{ $arsip->kegiatan ?: '-' }}
                        </td>

                        <!-- 11. Sub kegiatan -->
                        <td class="px-4 py-3.5 text-slate-700">
                            {{ $arsip->sub_kegiatan ?: '-' }}
                        </td>

                        <!-- 12. Tanggal Registrasi / Upload -->
                        <td class="px-3 py-3.5 whitespace-nowrap">
                            @if($arsip->tgl_upload)
                                <div class="font-semibold text-slate-800">{{ $arsip->tgl_upload->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $arsip->tgl_upload->translatedFormat('D, d M Y') }}</div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <!-- 13. Hal contohnya surat pengantar -->
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-900 leading-tight">
                                {{ $arsip->hal ?: $arsip->judul_arsip }}
                            </div>
                            @if($arsip->hal && $arsip->judul_arsip && $arsip->hal !== $arsip->judul_arsip)
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $arsip->judul_arsip }}</div>
                            @endif
                        </td>

                        <!-- 14. Unit Pengolah -->
                        <td class="px-3 py-3.5 text-slate-700">
                            {{ $arsip->unit_pengolah ?: '-' }}
                        </td>

                        <!-- 15. Sumber -->
                        <td class="px-3 py-3.5 text-slate-700">
                            {{ $arsip->sumber ?: '-' }}
                        </td>

                        <!-- 16. Keterangan -->
                        <td class="px-3 py-3.5 text-slate-500">
                            {{ $arsip->keterangan ?: '-' }}
                        </td>

                        <!-- 17. Status Berkas -->
                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                            @php
                                $badgeStyle = match($arsip->status_berkas) {
                                    'Fisik & Digital' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Fisik' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'Digital' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    default => 'bg-amber-50 text-amber-700 border-amber-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeStyle }}">
                                {{ $arsip->status_berkas }}
                            </span>
                        </td>

                        <!-- 12. File dokumen -->
                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                            <div class="inline-flex flex-col items-center gap-1.5">
                                @if($arsip->file_path)
                                {{-- Tombol download file asli (PDF/dll) --}}
                                <a href="{{ route('arsip.download', $arsip) }}" title="Unduh Berkas {{ strtoupper($arsip->file_extension) }}"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold border transition-all hover:-translate-y-0.5 duration-150" style="color: #0050C8; background: rgba(0,80,200,0.07); border-color: rgba(0,80,200,0.2)">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>{{ strtoupper($arsip->file_extension ?: 'FILE') }}</span>
                                </a>
                                @endif

                                @if($arsip->google_drive_url)
                                {{-- Tombol Google Drive --}}
                                <a href="{{ $arsip->google_drive_url }}" target="_blank" title="Buka Tautan Google Drive"
                                    class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold border transition-all hover:-translate-y-0.5 duration-150 text-emerald-700 bg-emerald-50 border-emerald-200">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 24 24" fill="currentColor"><path d="M7.71 3.5L1.15 15l3.43 6 6.55-11.5M9.73 15L6.3 21h13.12l3.43-6m-.57-1L15.42 2.5H8.58l6.85 11.5z"/></svg>
                                    <span>G-DRIVE</span>
                                </a>
                                @endif

                                {{-- Tombol download Excel --}}
                                <a href="{{ route('arsip.downloadExcel', $arsip) }}" title="Unduh Data sebagai Excel"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border transition-all hover:-translate-y-0.5 duration-150" style="color: #1d6f42; background: rgba(29,111,66,0.08); border-color: rgba(29,111,66,0.25)">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8.5 19l-2-3.5h1.3l1.2 2.2 1.2-2.2h1.3l-2 3.5 2.1 3.5h-1.3l-1.3-2.3-1.3 2.3H6.4l2.1-3.5zm5 3.5v-7h1.2v5.9h3V22.5H13.5z"/></svg>
                                    <span>XLS</span>
                                </a>
                            </div>
                        </td>

                        <!-- Aksi View, Edit & Hapus -->
                        <td class="px-3 py-3.5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1">
                                {{-- Tombol View Dokumen (Semua Peran: Admin, Operator, Viewer) --}}
                                <a href="{{ route('arsip.showFile', $arsip) }}" target="_blank" title="Lihat Berkas Dokumen"
                                    class="p-1.5 rounded-lg text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200/60 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>

                                @if(auth()->user()->canManageArsip())
                                <a href="{{ route('arsip.edit', $arsip) }}" title="Ubah Data Arsip"
                                    class="p-1.5 rounded-lg text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>

                                <button type="button" title="Hapus Arsip"
                                    onclick="confirmDelete(@js(route('arsip.destroy', $arsip)), @js('Hapus Arsip '.$arsip->kode_arsip.'?'), @js('Apakah Anda yakin ingin menghapus arsip \''.$arsip->judul_arsip.'\'?'))"
                                    class="p-1.5 rounded-lg text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $arsips->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
