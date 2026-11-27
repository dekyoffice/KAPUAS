<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Rekapitulasi Berkas Kearsipan KAPUAS</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1.2cm 1.2cm 1.2cm;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #1e293b;
            line-height: 1.25;
        }
        .header {
            text-align: center;
            border-bottom: 2.5px double #003caa;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 12pt;
            font-weight: bold;
            color: #003caa;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 9.5pt;
            font-weight: 600;
            color: #334155;
            margin: 0 0 3px 0;
        }
        .header p {
            font-size: 7.5pt;
            color: #64748b;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 7.5pt;
        }
        .meta-table td {
            padding: 1.5px 3px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 15px;
            table-layout: fixed;
            word-wrap: break-word;
        }
        thead {
            display: table-header-group;
        }
        tr {
            page-break-inside: avoid;
        }
        .data-table th, .data-table td {
            border: 0.5pt solid #cbd5e1;
            padding: 3px 4px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .data-table th {
            background-color: #003caa;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-semibold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
        }
        .badge-lengkap { background-color: #dcfce7; color: #166534; }
        .badge-fisik { background-color: #dbeafe; color: #1e40af; }
        .badge-digital { background-color: #f3e8ff; color: #6b21a8; }
        .badge-tercatat { background-color: #fef3c7; color: #92400e; }
        .footer-signatures {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .footer-signatures td {
            width: 50%;
            vertical-align: top;
            font-size: 8pt;
        }
        .sign-space {
            height: 48px;
        }
        .page-number:after {
            content: counter(page);
        }
        .footer-note {
            position: fixed;
            bottom: -0.8cm;
            left: 0;
            right: 0;
            font-size: 6.5pt;
            color: #94a3b8;
            border-top: 0.5pt solid #e2e8f0;
            padding-top: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if(!empty($logoBase64)): ?>
            <table style="width: 100%; border: none; margin-bottom: 0;">
                <tr>
                    <td style="width: 75px; text-align: center; vertical-align: middle; border: none; padding: 0;">
                        <img src="<?php echo e($logoBase64); ?>" alt="Logo KAPUAS" style="height: 60px;">
                    </td>
                    <td style="text-align: center; vertical-align: middle; border: none; padding: 0 10px;">
                        <h1>Katalog Arsip Pemerintah Untuk Administrasi dan Surat (KAPUAS)</h1>
                        <h2>DAFTAR REKAPITULASI BERKAS ARSIP PEMERINTAH DAERAH</h2>
                        <p>Sistem Manajemen Kearsipan Digital Resmi Berbasis Standar ANRI</p>
                    </td>
                    <td style="width: 75px; border: none; padding: 0;"></td>
                </tr>
            </table>
        <?php else: ?>
            <h1>Katalog Arsip Pemerintah Untuk Administrasi dan Surat (KAPUAS)</h1>
            <h2>DAFTAR REKAPITULASI BERKAS ARSIP PEMERINTAH DAERAH</h2>
            <p>Sistem Manajemen Kearsipan Digital Resmi Berbasis Standar ANRI</p>
        <?php endif; ?>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%; font-weight: bold;">Tanggal Cetak</td>
            <td style="width: 35%;">: <?php echo e($printDate); ?></td>
            <td style="width: 15%; font-weight: bold;">Kategori Filter</td>
            <td style="width: 35%;">: <?php echo e($filters['kategori'] ?? 'Semua Kategori'); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Dicetak Oleh</td>
            <td>: <?php echo e($currentUser->nama_user ?? 'Administrator'); ?> (<?php echo e(ucfirst($currentUser->role ?? 'admin')); ?>)</td>
            <td style="font-weight: bold;">Status Berkas</td>
            <td>: <?php echo e(!empty($filters['status']) ? ucfirst($filters['status']) : 'Semua Status'); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Jumlah Arsip</td>
            <td>: 
                <?php if(!empty($terpotong)): ?>
                    <?php echo e($arsips->count()); ?> dari <?php echo e($total); ?> Dokumen
                <?php else: ?>
                    <?php echo e($arsips->count()); ?> Dokumen
                <?php endif; ?>
            </td>
            <td style="font-weight: bold;">Tahun Dokumen</td>
            <td>: <?php echo e($filters['tahun'] ?? 'Semua Tahun'); ?></td>
        </tr>
    </table>

    <?php if(!empty($terpotong)): ?>
        <div style="background-color: #fef9c3; border: 1.5pt solid #f97316; padding: 6px 10px; margin-bottom: 10px; font-size: 8pt; color: #9a3412; font-weight: bold; border-radius: 3px;">
            PERHATIAN: Hanya <?php echo e($batas); ?> dari <?php echo e($total); ?> arsip yang dicetak. Persempit filter (kategori, tahun, status, atau kata kunci) agar seluruh data tercakup.
        </div>
    <?php endif; ?>

    <table class="data-table" style="table-layout: fixed; width: 100%;">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">Kode Arsip</th>
                <th style="width: 8%;">No. Berkas</th>
                <th style="width: 11%;">No. Surat</th>
                <th style="width: 7%;">Tgl Surat</th>
                <th style="width: 15%;">Judul & Uraian</th>
                <th style="width: 10%;">Kategori</th>
                <th style="width: 16%;">Program / Kegiatan / Sub Kegiatan</th>
                <th style="width: 8%;">Unit Pengolah</th>
                <th style="width: 7%;">Tgl Upload</th>
                <th style="width: 7%;">Status Berkas</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $arsips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $tglSuratVal = $item->tanggal_surat 
                        ? $item->tanggal_surat->format('d/m/Y') 
                        : '-';
                    $tglUploadVal = $item->tgl_upload
                        ? $item->tgl_upload->format('d/m/Y')
                        : '-';
                    $statusBadge = match($item->status_berkas) {
                        'Fisik & Digital' => 'badge-lengkap',
                        'Fisik' => 'badge-fisik',
                        'Digital' => 'badge-digital',
                        default => 'badge-tercatat',
                    };
                    $progNama = $item->subKegiatan?->kegiatan?->program?->nama_program ?? ($item->program ?: '-');
                    $kegNama = $item->subKegiatan?->kegiatan?->nama_kegiatan ?? ($item->kegiatan ?: '-');
                    $subKegNama = $item->subKegiatan?->nama_sub_kegiatan ?? ($item->sub_kegiatan ?: '-');
                ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td class="text-center font-semibold"><?php echo e($item->kode_arsip); ?></td>
                    <td><?php echo e($item->nomor_berkas ?: '-'); ?></td>
                    <td><?php echo e($item->nomor_surat ?: '-'); ?></td>
                    <td class="text-center"><?php echo e($tglSuratVal ?: '-'); ?></td>
                    <td>
                        <div class="font-semibold" style="color: #0f172a;"><?php echo e($item->judul_arsip); ?></div>
                        <?php if($item->hal): ?>
                            <div style="color: #475569; font-size: 6.5pt; margin-top: 1px;">Hal: <?php echo e($item->hal); ?></div>
                        <?php endif; ?>
                        <?php if($item->kode_klasifikasi): ?>
                            <div style="color: #475569; font-size: 6.5pt; margin-top: 1px;">Klasifikasi: <?php echo e($item->kode_klasifikasi); ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($item->kategori?->nama_kategori ?: '-'); ?></td>
                    <td>
                        <div style="font-size: 6.5pt; font-weight: bold; color: #003caa;"><?php echo e($progNama); ?></div>
                        <div style="font-size: 6.5pt; color: #1e293b;"><?php echo e($kegNama); ?></div>
                        <div style="font-size: 6.5pt; color: #475569;"><?php echo e($subKegNama); ?></div>
                    </td>
                    <td><?php echo e($item->unit_pengolah ?: '-'); ?></td>
                    <td class="text-center"><?php echo e($tglUploadVal); ?></td>
                    <td class="text-center">
                        <span class="badge <?php echo e($statusBadge); ?>"><?php echo e($item->status_berkas); ?></span>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="11" class="text-center" style="padding: 15px; color: #94a3b8;">
                        Tidak ada data arsip yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="footer-signatures">
        <tr>
            <td style="text-align: center;">
                <p>Mengetahui,</p>
                <p style="font-weight: bold; margin-top: 2px;">Pejabat Pengelola Kearsipan</p>
                <div class="sign-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">( .................................................... )</p>
                <p style="font-size: 7.5pt; color: #64748b;">NIP. ....................................................</p>
            </td>
            <td style="text-align: center;">
                <p>Dicetak pada: <?php echo e($printDate); ?></p>
                <p style="font-weight: bold; margin-top: 2px;">Petugas Pelaksana Administrasi</p>
                <div class="sign-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">( <?php echo e($currentUser->nama_user ?? 'Petugas Arsip'); ?> )</p>
                <p style="font-size: 7.5pt; color: #64748b;">Operator Sistem Informasi KAPUAS</p>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;">Sistem Informasi KAPUAS — Laporan Resmi Daftar Arsip</td>
                <td style="text-align: right;">Halaman <span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php /**PATH D:\KAPUASS\resources\views/arsip/export-pdf.blade.php ENDPATH**/ ?>