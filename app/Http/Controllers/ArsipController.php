<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArsipRequest;
use App\Http\Requests\UpdateArsipRequest;
use App\Models\Arsip;
use App\Models\Kategori;
use App\Models\SubJenisSpj;
use App\Models\SubKegiatan;
use App\Models\TahunArsip;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArsipController extends Controller
{
    /**
     * Tampilkan daftar arsip dengan filter dan pencarian.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $idKategori = $request->input('kategori');
        $tahun = $request->input('tahun');
        $status = $request->input('status');

        $query = Arsip::with(['user', 'kategori'])
            ->accessibleBy()
            ->search($search)
            ->filterKategori($idKategori)
            ->filterTahun($tahun)
            ->filterStatus($status)
            ->latest('tgl_upload')
            ->latest('id_arsip');

        $arsips = $query->paginate(10)->withQueryString();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        $availableYears = Arsip::accessibleBy()
            ->selectRaw('YEAR(tgl_upload) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (! in_array((int) date('Y'), $availableYears)) {
            array_unshift($availableYears, (int) date('Y'));
            rsort($availableYears);
        }

        return view('arsip.index', [
            'arsips' => $arsips,
            'kategoris' => $kategoris,
            'availableYears' => $availableYears,
            'search' => $search,
            'selectedKategori' => $idKategori,
            'selectedTahun' => $tahun,
            'selectedStatus' => $status,
        ]);
    }

    /**
     * Tampilkan formulir tambah arsip baru.
     */
    public function create(): View
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $subJenisSpjs = SubJenisSpj::orderBy('nama_sub_jenis_spj')->get();
        $tahunArsips = TahunArsip::orderBy('tahun', 'desc')->get();

        return view('arsip.create', compact('kategoris', 'subJenisSpjs', 'tahunArsips'));
    }

    /**
     * Simpan data arsip baru beserta berkas fisiknya.
     */
    public function store(StoreArsipRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $uploadYear = Carbon::parse($validated['tgl_upload'])->format('Y');

        // Sinkronisasi teks Program, Kegiatan, Sub Kegiatan dari Master Data
        $programText = $validated['program'] ?? null;
        $kegiatanText = $validated['kegiatan'] ?? null;
        $subKegiatanText = $validated['sub_kegiatan'] ?? null;
        if (! empty($validated['sub_kegiatan_id'])) {
            $subKeg = SubKegiatan::with('kegiatan.program')->find($validated['sub_kegiatan_id']);
            if ($subKeg) {
                $subKegiatanText = $subKeg->nama_sub_kegiatan;
                $kegiatanText = $subKeg->kegiatan->nama_kegiatan ?? null;
                $programText = $subKeg->kegiatan->program->nama_program ?? null;
            }
        }

        $subJenisText = $validated['sub_jenis'] ?? null;
        if (! empty($validated['sub_jenis_spj_id'])) {
            $spj = SubJenisSpj::find($validated['sub_jenis_spj_id']);
            if ($spj) {
                $subJenisText = $spj->nama_sub_jenis_spj;
            }
        }

        // Upload berkas fisik ke disk local (opsional jika menggunakan Google Drive)
        $storedFilePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalExt = $file->getClientOriginalExtension();
            $safeFileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'_'.time().'.'.$originalExt;
            $folderPath = 'arsip/'.$uploadYear;
            $storedFilePath = $file->storeAs($folderPath, $safeFileName, 'local');
        }

        try {
            $arsip = DB::transaction(function () use ($validated, $uploadYear, $storedFilePath, $programText, $kegiatanText, $subKegiatanText, $subJenisText) {
                $kodeArsip = $this->generateKodeArsip($uploadYear);

                return Arsip::create([
                    'id_user' => auth()->user()->id_user,
                    'id_kategori' => $validated['id_kategori'],
                    'sub_kegiatan_id' => $validated['sub_kegiatan_id'] ?? null,
                    'sub_jenis_spj_id' => $validated['sub_jenis_spj_id'] ?? null,
                    'tahun_arsip_id' => $validated['tahun_arsip_id'] ?? null,
                    'kode_arsip' => $kodeArsip,
                    'no_item_arsip' => $validated['no_item_arsip'] ?? null,
                    'nomor_berkas' => $validated['nomor_berkas'] ?? null,
                    'nomor_surat' => $validated['nomor_surat'] ?? null,
                    'tanggal_surat' => $validated['tanggal_surat'] ?? null,
                    'jumlah_surat' => $validated['jumlah_surat'] ?? 1,
                    'kode_klasifikasi' => $validated['kode_klasifikasi'] ?? null,
                    'skkad' => $validated['skkad'] ?? null,
                    'sub_jenis' => $subJenisText,
                    'program' => $programText,
                    'kegiatan' => $kegiatanText,
                    'sub_kegiatan' => $subKegiatanText,
                    'judul_arsip' => $validated['judul_arsip'],
                    'hal' => $validated['hal'] ?? null,
                    'sumber' => $validated['sumber'] ?? null,
                    'unit_pengolah' => $validated['unit_pengolah'] ?? null,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'file_path' => $storedFilePath,
                    'google_drive_url' => $validated['google_drive_url'] ?? null,
                    'tgl_upload' => $validated['tgl_upload'],
                ]);
            });
        } catch (\Throwable $e) {
            // Jika penyimpanan record gagal setelah berkas disimpan, hapus berkas fisik
            if ($storedFilePath && Storage::disk('local')->exists($storedFilePath)) {
                Storage::disk('local')->delete($storedFilePath);
            }
            throw $e;
        }

        return redirect()->route('arsip.index')
            ->with('success', "Arsip dengan kode {$arsip->kode_arsip} berhasil ditambahkan ke sistem.");
    }

    /**
     * Tampilkan formulir edit arsip.
     */
    public function edit(Arsip $arsip): View
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $subJenisSpjs = SubJenisSpj::orderBy('nama_sub_jenis_spj')->get();
        $tahunArsips = TahunArsip::orderBy('tahun', 'desc')->get();
        $arsip->load(['subKegiatan.kegiatan.program', 'subJenisSpj', 'tahunArsip']);

        return view('arsip.edit', compact('arsip', 'kategoris', 'subJenisSpjs', 'tahunArsips'));
    }

    /**
     * Perbarui data arsip dan ganti berkas fisik jika diunggah baru.
     */
    public function update(UpdateArsipRequest $request, Arsip $arsip): RedirectResponse
    {
        $validated = $request->validated();
        $storedFilePath = $arsip->file_path;

        // Jika ada berkas baru diunggah
        if ($request->hasFile('file')) {
            // Hapus berkas lama jika ada di disk
            if ($arsip->file_path && Storage::disk('local')->exists($arsip->file_path)) {
                Storage::disk('local')->delete($arsip->file_path);
            }

            $uploadYear = Carbon::parse($validated['tgl_upload'])->format('Y');
            $file = $request->file('file');
            $originalExt = $file->getClientOriginalExtension();
            $safeFileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'_'.time().'.'.$originalExt;
            $folderPath = 'arsip/'.$uploadYear;
            $storedFilePath = $file->storeAs($folderPath, $safeFileName, 'local');
        }

        // Sinkronisasi teks Program, Kegiatan, Sub Kegiatan dari Master Data
        $programText = $validated['program'] ?? $arsip->program;
        $kegiatanText = $validated['kegiatan'] ?? $arsip->kegiatan;
        $subKegiatanText = $validated['sub_kegiatan'] ?? $arsip->sub_kegiatan;
        if (! empty($validated['sub_kegiatan_id'])) {
            $subKeg = SubKegiatan::with('kegiatan.program')->find($validated['sub_kegiatan_id']);
            if ($subKeg) {
                $subKegiatanText = $subKeg->nama_sub_kegiatan;
                $kegiatanText = $subKeg->kegiatan->nama_kegiatan ?? null;
                $programText = $subKeg->kegiatan->program->nama_program ?? null;
            }
        }

        $subJenisText = $validated['sub_jenis'] ?? $arsip->sub_jenis;
        if (! empty($validated['sub_jenis_spj_id'])) {
            $spj = SubJenisSpj::find($validated['sub_jenis_spj_id']);
            if ($spj) {
                $subJenisText = $spj->nama_sub_jenis_spj;
            }
        }

        $arsip->update([
            'id_kategori' => $validated['id_kategori'],
            'sub_kegiatan_id' => $validated['sub_kegiatan_id'] ?? $arsip->sub_kegiatan_id,
            'sub_jenis_spj_id' => $validated['sub_jenis_spj_id'] ?? $arsip->sub_jenis_spj_id,
            'tahun_arsip_id' => $validated['tahun_arsip_id'] ?? $arsip->tahun_arsip_id,
            'no_item_arsip' => $validated['no_item_arsip'] ?? null,
            'nomor_berkas' => $validated['nomor_berkas'] ?? null,
            'nomor_surat' => $validated['nomor_surat'] ?? null,
            'tanggal_surat' => $validated['tanggal_surat'] ?? null,
            'jumlah_surat' => $validated['jumlah_surat'] ?? ($arsip->jumlah_surat ?? 1),
            'kode_klasifikasi' => $validated['kode_klasifikasi'] ?? null,
            'skkad' => $validated['skkad'] ?? null,
            'sub_jenis' => $subJenisText,
            'program' => $programText,
            'kegiatan' => $kegiatanText,
            'sub_kegiatan' => $subKegiatanText,
            'judul_arsip' => $validated['judul_arsip'],
            'hal' => $validated['hal'] ?? null,
            'sumber' => $validated['sumber'] ?? null,
            'unit_pengolah' => $validated['unit_pengolah'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
            'file_path' => $storedFilePath,
            'google_drive_url' => $validated['google_drive_url'] ?? null,
            'tgl_upload' => $validated['tgl_upload'],
        ]);

        return redirect()->route('arsip.index')
            ->with('success', "Data arsip '{$arsip->judul_arsip}' berhasil diperbarui.");
    }

    /**
     * Hapus arsip dari database dan hapus berkas fisik dari penyimpanan.
     */
    public function destroy(Arsip $arsip): RedirectResponse
    {
        $judul = $arsip->judul_arsip;
        $filePath = $arsip->file_path;

        // Hapus record arsip
        $arsip->delete();

        // Hapus file fisik dari storage disk jika ada
        if ($filePath && Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);
        }

        return redirect()->route('arsip.index')
            ->with('success', "Arsip '{$judul}' berhasil dihapus dari sistem.");
    }

    /**
     * Tampilkan / preview berkas dokumen arsip di tab baru browser.
     */
    public function showFile(Arsip $arsip): StreamedResponse|RedirectResponse
    {
        if (auth()->user()?->role === 'viewer' && $arsip->skkad === 'Rahasia') {
            abort(403, 'Akses ditolak. Pengguna peran Viewer tidak diizinkan mengakses arsip berkategori Rahasia.');
        }

        $filePath = $arsip->file_path;

        if ($filePath && Storage::disk('local')->exists($filePath)) {
            return Storage::disk('local')->response($filePath);
        }

        if ($arsip->google_drive_url) {
            return redirect()->away($arsip->google_drive_url);
        }

        Log::warning("Percobaan lihat berkas gagal: file '{$filePath}' untuk arsip ID {$arsip->id_arsip} tidak ditemukan.");

        return redirect()->back()
            ->with('error', 'Berkas fisik dokumen maupun tautan Google Drive tidak ditemukan.');
    }

    /**
     * Unduh berkas fisik arsip.
     */
    public function download(Arsip $arsip): StreamedResponse|RedirectResponse
    {
        if (auth()->user()?->role === 'viewer' && $arsip->skkad === 'Rahasia') {
            abort(403, 'Akses ditolak. Pengguna peran Viewer tidak diizinkan mengunduh arsip berkategori Rahasia.');
        }

        $filePath = $arsip->file_path;

        if ($filePath && Storage::disk('local')->exists($filePath)) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $downloadFileName = Str::slug($arsip->judul_arsip).'.'.$extension;

            return Storage::disk('local')->download($filePath, $downloadFileName);
        }

        if ($arsip->google_drive_url) {
            return redirect()->away($arsip->google_drive_url);
        }

        Log::warning("Percobaan unduh berkas gagal: file '{$filePath}' untuk arsip ID {$arsip->id_arsip} ({$arsip->judul_arsip}) tidak ditemukan di storage.");

        return redirect()->back()
            ->with('error', 'Berkas fisik dokumen maupun tautan Google Drive tidak ditemukan.');
    }

    /**
     * Ekspor data arsip tunggal ke file Excel (.xlsx) dengan format memanjang (horizontal).
     */
    public function downloadExcel(Arsip $arsip): \Symfony\Component\HttpFoundation\Response
    {
        if (auth()->user()?->role === 'viewer' && $arsip->skkad === 'Rahasia') {
            abort(403, 'Akses ditolak. Pengguna peran Viewer tidak diizinkan mengekspor arsip berkategori Rahasia.');
        }

        $arsip->load(['kategori', 'user', 'subKegiatan.kegiatan.program', 'subJenisSpj', 'tahunArsip']);

        return $this->generateExcelResponse(collect([$arsip]), 'arsip-'.Str::slug($arsip->kode_arsip ?: 'data'));
    }

    /**
     * Ekspor seluruh / hasil filter data arsip ke file Excel (.xlsx) dengan format memanjang.
     */
    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $query = Arsip::with(['kategori', 'user', 'subKegiatan.kegiatan.program', 'subJenisSpj', 'tahunArsip'])
            ->accessibleBy()
            ->latest('tgl_upload')
            ->latest('id_arsip');

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }
        if ($request->filled('kategori')) {
            $query->filterKategori($request->input('kategori'));
        }
        if ($request->filled('tahun')) {
            $query->filterTahun($request->input('tahun'));
        }
        if ($request->filled('status')) {
            $query->filterStatus($request->input('status'));
        }

        $arsips = $query->get();

        return $this->generateExcelResponse($arsips, 'rekap-data-arsip-'.date('Ymd-His'));
    }

    /**
     * Ekspor daftar arsip ke format PDF (A4 Landscape) dengan kop surat dan filter aktif.
     */
    public function exportPdf(Request $request): Response
    {
        $query = Arsip::with(['kategori', 'user', 'subKegiatan.kegiatan.program', 'subJenisSpj', 'tahunArsip'])
            ->accessibleBy()
            ->latest('tgl_upload')
            ->latest('id_arsip');

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }
        if ($request->filled('kategori')) {
            $query->filterKategori($request->input('kategori'));
        }
        if ($request->filled('tahun')) {
            $query->filterTahun($request->input('tahun'));
        }
        if ($request->filled('status')) {
            $query->filterStatus($request->input('status'));
        }

        $batas = config('arsip.pdf_max_rows', 2000);
        $total = (clone $query)->count();
        $terpotong = $total > $batas;

        // Batasi baris sesuai konfigurasi untuk mencegah kehabisan memori
        $arsips = $query->limit($batas)->get();

        $kategoriModel = $request->filled('kategori') ? Kategori::find($request->input('kategori')) : null;

        $logoPath = public_path('images/LOGO_KAPUAS.png');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('arsip.export-pdf', [
            'arsips' => $arsips,
            'total' => $total,
            'batas' => $batas,
            'terpotong' => $terpotong,
            'logoBase64' => $logoBase64,
            'filters' => [
                'search' => $request->input('search'),
                'kategori' => $kategoriModel?->nama_kategori,
                'tahun' => $request->input('tahun'),
                'status' => $request->input('status'),
            ],
            'printDate' => now()->locale('id')->translatedFormat('d F Y H:i:s'),
            'currentUser' => auth()->user(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('daftar-arsip-kapuas-'.date('Ymd-His').'.pdf');
    }

    /**
     * Helper privat: membuat spreadsheet horizontal dan me-return stream download response.
     */
    private function generateExcelResponse($arsips, string $baseFileName): \Symfony\Component\HttpFoundation\Response
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Arsip');

        // Header kolom persis sesuai standar revisi Diskominfo (9 kolom berurutan tanpa kolom 'No')
        $headers = [
            'A' => 'Nomor Item Arsip',
            'B' => 'Nomor Berkas',
            'C' => 'Kode Klasifikasi',
            'D' => 'Uraian Informasi Berkas',
            'E' => 'Uraian Informasi Arsip',
            'F' => 'Tanggal Surat',
            'G' => 'Jumlah Surat',
            'H' => 'Keterangan',
            'I' => 'SKKAAD',
        ];

        // Background biru/indigo khas (#3038A6)
        $headerBgColor = 'FF3038A6';
        $borderColor = 'FFB0B0B0';

        // Tulis Header Kolom
        foreach ($headers as $col => $title) {
            $sheet->setCellValue("{$col}1", $title);
        }

        // Style Header Row 1
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $headerBgColor],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Tulis Data Baris (Mulai dari Baris 2)
        $rowNum = 2;
        foreach ($arsips as $arsip) {
            // 1. Nomor Item Arsip (Col A)
            $noItem = (string) ($arsip->no_item_arsip ?: ($arsip->kode_arsip ?: ''));
            $sheet->setCellValueExplicit('A'.$rowNum, $noItem, DataType::TYPE_STRING);

            // 2. Nomor Berkas (Col B)
            $noBerkas = (string) ($arsip->nomor_berkas ?: '');
            $sheet->setCellValueExplicit('B'.$rowNum, $noBerkas, DataType::TYPE_STRING);

            // 3. Kode Klasifikasi (Col C)
            $kodeKlasifikasi = (string) ($arsip->kode_klasifikasi ?: '');
            $sheet->setCellValueExplicit('C'.$rowNum, $kodeKlasifikasi, DataType::TYPE_STRING);

            // 4. Uraian Informasi Berkas (Col D)
            $uraianBerkas = $arsip->sub_kegiatan
                ?: ($arsip->subKegiatan?->nama_sub_kegiatan
                    ?: ($arsip->sub_jenis
                        ?: ($arsip->subJenisSpj?->nama_sub_jenis_spj
                            ?: ($arsip->kategori?->nama_kategori ?: ($arsip->kegiatan ?: '')))));
            $sheet->setCellValueExplicit('D'.$rowNum, (string) $uraianBerkas, DataType::TYPE_STRING);

            // 5. Uraian Informasi Arsip (Col E - gabungkan Nomor Surat ke dalam uraian informasi arsip)
            $perihal = trim($arsip->judul_arsip ?: '');
            if (! empty($arsip->hal) && $arsip->hal !== $arsip->judul_arsip) {
                if (empty($perihal)) {
                    $perihal = trim($arsip->hal);
                } elseif (stripos($perihal, $arsip->hal) === false) {
                    $perihal .= ' - '.trim($arsip->hal);
                }
            }
            if (empty($perihal) && ! empty($arsip->keterangan)) {
                $perihal = trim($arsip->keterangan);
            }

            if (! empty($arsip->nomor_surat)) {
                $uraianArsip = trim($arsip->nomor_surat).($perihal !== '' ? ' - '.$perihal : '');
            } else {
                $uraianArsip = $perihal;
            }
            $sheet->setCellValueExplicit('E'.$rowNum, (string) $uraianArsip, DataType::TYPE_STRING);

            // 6. Tanggal Surat (Col F - format DD/MM/YYYY)
            $tglSurat = $arsip->tanggal_surat
                ? $arsip->tanggal_surat->format('d/m/Y')
                : ($arsip->tgl_upload ? $arsip->tgl_upload->format('d/m/Y') : '-');
            $sheet->setCellValueExplicit('F'.$rowNum, (string) $tglSurat, DataType::TYPE_STRING);

            // 7. Jumlah Surat (Col G - format "[angka] lembar")
            $jumlahSurat = (int) ($arsip->jumlah_surat ?: 1);
            $sheet->setCellValueExplicit('G'.$rowNum, $jumlahSurat.' lembar', DataType::TYPE_STRING);

            // 8. Keterangan (Col H - 'Soft File' jika ada berkas digital / Google Drive, 'Arsip Kertas' jika fisik saja)
            $keterangan = (! empty($arsip->file_path) || ! empty($arsip->google_drive_url))
                ? 'Soft File'
                : 'Arsip Kertas';
            $sheet->setCellValueExplicit('H'.$rowNum, $keterangan, DataType::TYPE_STRING);

            // 9. SKKAAD (Col I - Terbuka / Tertutup / Rahasia)
            $skkaadRaw = trim((string) ($arsip->skkad ?: ''));
            if (stripos($skkaadRaw, 'Biasa') !== false || strcasecmp($skkaadRaw, 'Terbuka') === 0) {
                $skkaad = 'Terbuka';
            } elseif (strcasecmp($skkaadRaw, 'Terbatas') === 0 || strcasecmp($skkaadRaw, 'Tertutup') === 0 || strcasecmp($skkaadRaw, 'Sangat Rahasia') === 0) {
                $skkaad = 'Tertutup';
            } elseif (strcasecmp($skkaadRaw, 'Rahasia') === 0) {
                $skkaad = 'Rahasia';
            } else {
                $skkaad = $skkaadRaw ?: '-';
            }
            $sheet->setCellValueExplicit('I'.$rowNum, $skkaad, DataType::TYPE_STRING);

            // Alignment per kolom data
            $sheet->getStyle('A'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('E'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
            $sheet->getStyle('F'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Font & Vertical Alignment untuk baris
            $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray([
                'font' => ['size' => 10, 'name' => 'Calibri'],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getRowDimension($rowNum)->setRowHeight(24);
            $rowNum++;
        }

        // Tentukan batas akhir border
        $lastRow = max(2, $rowNum - 1);

        // Border seluruh tabel
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => $borderColor],
                ],
            ],
        ]);

        // Auto-width pada seluruh 9 kolom agar teks tidak terpotong
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = $baseFileName.'.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Helper privat: generate kode arsip unik tahunan (format: ARS-YYYY-0001).
     */
    private function generateKodeArsip(string $year): string
    {
        // Jika counter tahun belum ada, inisialisasi dari nomor arsip tertinggi yang ada di database
        $counterExists = DB::table('arsip_counters')->where('tahun', $year)->exists();
        if (! $counterExists) {
            $latestArsip = Arsip::where('kode_arsip', 'like', "ARS-{$year}-%")
                ->orderBy('kode_arsip', 'desc')
                ->first();
            $maxNum = 0;
            if ($latestArsip && preg_match('/^ARS-\d{4}-(\d+)$/', $latestArsip->kode_arsip, $m)) {
                $maxNum = (int) $m[1];
            }
            DB::table('arsip_counters')->insertOrIgnore([
                'tahun' => $year,
                'terakhir' => $maxNum,
            ]);
        }

        $counter = DB::table('arsip_counters')
            ->where('tahun', $year)
            ->lockForUpdate()
            ->first();

        $nextNumber = ($counter->terakhir ?? 0) + 1;

        DB::table('arsip_counters')
            ->where('tahun', $year)
            ->update(['terakhir' => $nextNumber]);

        return sprintf('ARS-%s-%04d', $year, $nextNumber);
    }
}
