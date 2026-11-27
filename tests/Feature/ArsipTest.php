<?php

namespace Tests\Feature;

use App\Models\Arsip;
use App\Models\Kategori;
use App\Models\SubKegiatan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Tests\TestCase;

class ArsipTest extends TestCase
{
    protected User $admin;

    protected User $operator;

    protected Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first();
        $this->operator = User::where('role', 'operator')->first();
        $this->kategori = Kategori::first();
    }

    public function test_operator_can_upload_new_arsip_with_auto_generated_code(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $file = UploadedFile::fake()->create('dokumen_rahasia.pdf', 500, 'application/pdf');
        $subKegiatan = SubKegiatan::first();

        $response = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'no_item_arsip' => '099/TEST/IX/2026',
            'kode_klasifikasi' => 'OT.00.99',
            'skkad' => 'Terbuka',
            'sub_jenis' => 'Surat Keputusan',
            'program' => 'Program Pengujian Sistem',
            'kegiatan' => 'Uji Validasi Form Arsip',
            'sub_kegiatan' => 'Otomasi Fitur Pengarsipan',
            'judul_arsip' => 'Surat Keputusan Bupati Kapuas No 123 Tahun 2026',
            'hal' => 'Surat Pengantar Keputusan Bupati',
            'sumber' => 'Sekretariat Daerah',
            'keterangan' => 'Dokumen uji coba otomatis',
            'tgl_upload' => '2026-09-10',
            'file' => $file,
        ]);

        $response->assertRedirect(route('arsip.index'));
        $response->assertSessionHas('success');

        // Pastikan tersimpan di DB
        $arsip = Arsip::where('judul_arsip', 'Surat Keputusan Bupati Kapuas No 123 Tahun 2026')->first();
        $this->assertNotNull($arsip);
        $this->assertStringStartsWith('ARS-2026-', $arsip->kode_arsip);
        $this->assertEquals('099/TEST/IX/2026', $arsip->no_item_arsip);
        $this->assertEquals('OT.00.99', $arsip->kode_klasifikasi);
        $this->assertEquals('Surat Pengantar Keputusan Bupati', $arsip->hal);

        // Pastikan file fisik tersimpan di disk local dan TIDAK ada di disk public
        Storage::disk('local')->assertExists($arsip->file_path);
        Storage::disk('public')->assertMissing($arsip->file_path);
    }

    public function test_archive_search_and_filter_work(): void
    {
        $response = $this->actingAs($this->admin)->get('/arsip?search=Surat');
        $response->assertStatus(200);

        $responseCat = $this->actingAs($this->admin)->get('/arsip?kategori='.$this->kategori->id_kategori);
        $responseCat->assertStatus(200);
    }

    public function test_download_archive_works_for_all_roles(): void
    {
        Storage::fake('local');

        // Buat file dan record arsip
        $fakePath = 'arsip/2026/test_download.pdf';
        Storage::disk('local')->put($fakePath, 'Dummy file content for test');

        $arsip = Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $this->kategori->id_kategori,
            'kode_arsip' => 'ARS-2026-9999',
            'judul_arsip' => 'Test Unduh Dokumen Publik',
            'file_path' => $fakePath,
            'tgl_upload' => '2026-09-10',
        ]);

        $viewer = User::where('role', 'viewer')->first();

        // Viewer mendownload
        $response = $this->actingAs($viewer)->get("/arsip/{$arsip->id_arsip}/download");
        $response->assertStatus(200);

        // Guest ditolak
        auth()->logout();
        $responseGuest = $this->get("/arsip/{$arsip->id_arsip}/download");
        $responseGuest->assertRedirect('/login');
    }

    public function test_two_sequential_archives_get_sequential_codes(): void
    {
        $subKegiatan = SubKegiatan::first();

        $res1 = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Berurutan 1',
            'google_drive_url' => 'https://drive.google.com/file/d/test1/view',
            'tgl_upload' => '2026-05-01',
        ]);
        $res1->assertRedirect();
        $arsip1 = Arsip::where('judul_arsip', 'Arsip Berurutan 1')->first();

        $res2 = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Berurutan 2',
            'google_drive_url' => 'https://drive.google.com/file/d/test2/view',
            'tgl_upload' => '2026-05-01',
        ]);
        $res2->assertRedirect();
        $arsip2 = Arsip::where('judul_arsip', 'Arsip Berurutan 2')->first();

        $num1 = (int) substr($arsip1->kode_arsip, strrpos($arsip1->kode_arsip, '-') + 1);
        $num2 = (int) substr($arsip2->kode_arsip, strrpos($arsip2->kode_arsip, '-') + 1);

        $this->assertEquals($num1 + 1, $num2);
    }

    public function test_deleting_last_archive_does_not_reuse_code(): void
    {
        $subKegiatan = SubKegiatan::first();

        $res1 = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Yang Akan Dihapus',
            'google_drive_url' => 'https://drive.google.com/file/d/test-del/view',
            'tgl_upload' => '2026-05-01',
        ]);
        $res1->assertRedirect();
        $arsip1 = Arsip::where('judul_arsip', 'Arsip Yang Akan Dihapus')->first();
        $num1 = (int) substr($arsip1->kode_arsip, strrpos($arsip1->kode_arsip, '-') + 1);

        // Hapus arsip tersebut
        $this->actingAs($this->admin)->delete("/arsip/{$arsip1->id_arsip}")->assertRedirect();

        // Buat arsip baru berikutnya
        $res2 = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Setelah Penghapusan',
            'google_drive_url' => 'https://drive.google.com/file/d/test-after/view',
            'tgl_upload' => '2026-05-01',
        ]);
        $res2->assertRedirect();
        $arsip2 = Arsip::where('judul_arsip', 'Arsip Setelah Penghapusan')->first();
        $num2 = (int) substr($arsip2->kode_arsip, strrpos($arsip2->kode_arsip, '-') + 1);

        // Nomor tidak boleh sama (harus lebih besar dari nomor arsip yang dihapus)
        $this->assertEquals($num1 + 1, $num2);
    }

    public function test_search_by_nomor_surat_returns_matching_archive(): void
    {
        Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $this->kategori->id_kategori,
            'kode_arsip' => 'ARS-2026-7777',
            'nomor_surat' => '005/DISPOSISI-KHUSUS/2026',
            'judul_arsip' => 'Surat Khusus Yang Dicari',
            'tgl_upload' => '2026-05-01',
        ]);

        $response = $this->actingAs($this->admin)->get('/arsip?search=DISPOSISI-KHUSUS');
        $response->assertStatus(200);
        $response->assertSee('005/DISPOSISI-KHUSUS/2026');
        $response->assertSee('Surat Khusus Yang Dicari');
    }

    public function test_store_arsip_validation_rules(): void
    {
        $subKegiatan = SubKegiatan::first();

        // 1. Tanpa berkas dan tanpa Drive URL harus gagal (required_without)
        $res1 = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Tanpa Dokumen',
            'tgl_upload' => '2026-05-01',
        ]);
        $res1->assertSessionHasErrors('file');

        // 2. URL Google Drive yang tidak valid (bukan domain google drive/docs) harus gagal
        $res2 = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Link Dropbox',
            'google_drive_url' => 'https://dropbox.com/s/sample-file',
            'tgl_upload' => '2026-05-01',
        ]);
        $res2->assertSessionHasErrors('google_drive_url');

        // 3. Tipe file yang tidak diizinkan (.exe) harus ditolak
        $invalidFile = UploadedFile::fake()->create('malicious.exe', 100, 'application/x-msdownload');
        $res3 = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip File Terlarang',
            'file' => $invalidFile,
            'tgl_upload' => '2026-05-01',
        ]);
        $res3->assertSessionHasErrors('file');
    }

    public function test_update_arsip_validation_flexibility(): void
    {
        $subKegiatan = SubKegiatan::first();

        // Arsip lama yang SUDAH memiliki file_path fisik
        $arsipDenganFile = Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'kode_arsip' => 'ARS-2026-5555',
            'judul_arsip' => 'Arsip Berkas Tersimpan',
            'file_path' => 'arsip/2026/sample.pdf',
            'tgl_upload' => '2026-05-01',
        ]);

        // Update tanpa upload baru & tanpa drive URL harus BERHASIL (karena file lama masih ada)
        $updateRes1 = $this->actingAs($this->operator)->put("/arsip/{$arsipDenganFile->id_arsip}", [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Berkas Tersimpan Diperbarui',
            'tgl_upload' => '2026-05-01',
        ]);
        $updateRes1->assertRedirect();
        $this->assertEquals('Arsip Berkas Tersimpan Diperbarui', $arsipDenganFile->fresh()->judul_arsip);

        // Arsip lama yang TIDAK memiliki file_path fisik dan drive URL dikosongkan harus GAGAL
        $arsipLinkOnly = Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'kode_arsip' => 'ARS-2026-5556',
            'judul_arsip' => 'Arsip Tautan Google',
            'google_drive_url' => 'https://drive.google.com/file/d/test-url/view',
            'file_path' => null,
            'tgl_upload' => '2026-05-01',
        ]);

        $updateRes2 = $this->actingAs($this->operator)->put("/arsip/{$arsipLinkOnly->id_arsip}", [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Dihapus Linknya',
            'google_drive_url' => '',
            'tgl_upload' => '2026-05-01',
        ]);
        $updateRes2->assertSessionHasErrors('google_drive_url');
    }

    public function test_export_excel_returns_valid_spreadsheet_response(): void
    {
        $response = $this->actingAs($this->admin)->get(route('arsip.exportExcel'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_status_berkas_and_punya_berkas_accessors(): void
    {
        $arsipLengkap = new Arsip([
            'file_path' => 'berkas/surat.pdf',
            'google_drive_url' => 'https://drive.google.com/file/d/test/view',
        ]);
        $this->assertEquals('Fisik & Digital', $arsipLengkap->status_berkas);
        $this->assertTrue($arsipLengkap->punya_berkas);

        $arsipFisik = new Arsip([
            'file_path' => 'berkas/surat.pdf',
            'google_drive_url' => null,
        ]);
        $this->assertEquals('Fisik', $arsipFisik->status_berkas);
        $this->assertTrue($arsipFisik->punya_berkas);

        $arsipDigital = new Arsip([
            'file_path' => null,
            'google_drive_url' => 'https://drive.google.com/file/d/test/view',
        ]);
        $this->assertEquals('Digital', $arsipDigital->status_berkas);
        $this->assertTrue($arsipDigital->punya_berkas);

        $arsipTercatat = new Arsip([
            'file_path' => null,
            'google_drive_url' => null,
        ]);
        $this->assertEquals('Tercatat Saja', $arsipTercatat->status_berkas);
        $this->assertFalse($arsipTercatat->punya_berkas);
    }

    public function test_scope_filter_status_filters_records_correctly(): void
    {
        $subKegiatan = SubKegiatan::first();

        // 1. Fisik & Digital
        Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'kode_arsip' => 'ARS-FILTER-01',
            'judul_arsip' => 'Arsip Filter Fisik Digital',
            'file_path' => 'arsip/2026/f1.pdf',
            'google_drive_url' => 'https://drive.google.com/file/d/link1/view',
            'tgl_upload' => '2026-05-01',
        ]);

        // 2. Fisik Saja
        Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'kode_arsip' => 'ARS-FILTER-02',
            'judul_arsip' => 'Arsip Filter Fisik',
            'file_path' => 'arsip/2026/f2.pdf',
            'google_drive_url' => null,
            'tgl_upload' => '2026-05-01',
        ]);

        // 3. Digital Saja
        Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'kode_arsip' => 'ARS-FILTER-03',
            'judul_arsip' => 'Arsip Filter Digital',
            'file_path' => null,
            'google_drive_url' => 'https://drive.google.com/file/d/link2/view',
            'tgl_upload' => '2026-05-01',
        ]);

        // Filter fisik_digital hanya mengembalikan arsip yang punya file DAN link
        $this->assertTrue(Arsip::filterStatus('fisik_digital')->where('kode_arsip', 'ARS-FILTER-01')->exists());
        $this->assertFalse(Arsip::filterStatus('fisik_digital')->where('kode_arsip', 'ARS-FILTER-02')->exists());
        $this->assertFalse(Arsip::filterStatus('fisik_digital')->where('kode_arsip', 'ARS-FILTER-03')->exists());

        // Alias lengkap mengembalikan hasil yang sama persis
        $this->assertTrue(Arsip::filterStatus('lengkap')->where('kode_arsip', 'ARS-FILTER-01')->exists());
        $this->assertFalse(Arsip::filterStatus('lengkap')->where('kode_arsip', 'ARS-FILTER-02')->exists());

        $this->assertTrue(Arsip::filterStatus('fisik')->where('kode_arsip', 'ARS-FILTER-02')->exists());
        $this->assertFalse(Arsip::filterStatus('fisik')->where('kode_arsip', 'ARS-FILTER-03')->exists());

        $this->assertTrue(Arsip::filterStatus('digital')->where('kode_arsip', 'ARS-FILTER-03')->exists());
        $this->assertFalse(Arsip::filterStatus('digital')->where('kode_arsip', 'ARS-FILTER-02')->exists());
    }

    public function test_dashboard_page_returns_ok_for_all_roles(): void
    {
        $viewer = User::where('role', 'viewer')->first();

        $this->actingAs($this->admin)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->operator)->get('/dashboard')->assertStatus(200);
        $this->actingAs($viewer)->get('/dashboard')->assertStatus(200);
    }

    public function test_store_and_update_with_tanggal_surat_and_unit_pengolah(): void
    {
        $subKegiatan = SubKegiatan::first();

        $response = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Surat Undangan Rapat Kerja',
            'nomor_surat' => '005/UND/2026',
            'tanggal_surat' => '2026-04-15',
            'unit_pengolah' => 'Sekretariat / Subbag Umum',
            'google_drive_url' => 'https://drive.google.com/file/d/rapat-kerja/view',
            'tgl_upload' => '2026-05-01',
        ]);

        $response->assertRedirect('/arsip');
        $arsip = Arsip::where('judul_arsip', 'Surat Undangan Rapat Kerja')->first();
        $this->assertNotNull($arsip);
        $this->assertEquals('2026-04-15', $arsip->tanggal_surat?->format('Y-m-d'));
        $this->assertEquals('Sekretariat / Subbag Umum', $arsip->unit_pengolah);

        // Update
        $updateRes = $this->actingAs($this->operator)->put("/arsip/{$arsip->id_arsip}", [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Surat Undangan Rapat Kerja Revisi',
            'nomor_surat' => '005/UND-REV/2026',
            'tanggal_surat' => '2026-04-16',
            'unit_pengolah' => 'Bidang Anggaran',
            'google_drive_url' => 'https://drive.google.com/file/d/rapat-kerja-rev/view',
            'tgl_upload' => '2026-05-01',
        ]);

        $updateRes->assertRedirect('/arsip');
        $arsip->refresh();
        $this->assertEquals('2026-04-16', $arsip->tanggal_surat?->format('Y-m-d'));
        $this->assertEquals('Bidang Anggaran', $arsip->unit_pengolah);
    }

    public function test_export_pdf_returns_valid_pdf_response(): void
    {
        $response = $this->actingAs($this->admin)->get(route('arsip.exportPdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_pdf_view_shows_warning_when_truncated(): void
    {
        $arsips = collect([
            new Arsip([
                'kode_arsip' => 'ARS-TEST-01',
                'judul_arsip' => 'Arsip Contoh Terpotong',
                'status_berkas' => 'Fisik',
            ]),
        ]);

        $viewTerpotong = view('arsip.export-pdf', [
            'arsips' => $arsips,
            'total' => 2500,
            'batas' => 2000,
            'terpotong' => true,
            'filters' => [],
            'printDate' => '02 November 2026',
            'currentUser' => $this->admin,
        ])->render();

        $this->assertStringContainsString('PERHATIAN', $viewTerpotong);
        $this->assertStringContainsString('2500', $viewTerpotong);
        $this->assertStringContainsString('2000', $viewTerpotong);

        $viewTidakTerpotong = view('arsip.export-pdf', [
            'arsips' => $arsips,
            'total' => 10,
            'batas' => 2000,
            'terpotong' => false,
            'filters' => [],
            'printDate' => '02 November 2026',
            'currentUser' => $this->admin,
        ])->render();

        $this->assertStringNotContainsString('PERHATIAN', $viewTidakTerpotong);
    }

    public function test_export_pdf_respects_max_rows_config(): void
    {
        config(['arsip.pdf_max_rows' => 2]);
        $subKegiatan = SubKegiatan::first();

        for ($i = 1; $i <= 3; $i++) {
            Arsip::create([
                'id_user' => $this->admin->id_user,
                'id_kategori' => $this->kategori->id_kategori,
                'sub_kegiatan_id' => $subKegiatan->id,
                'kode_arsip' => "ARS-LIMIT-0{$i}",
                'judul_arsip' => "Arsip Uji Limit {$i}",
                'google_drive_url' => "https://drive.google.com/file/d/test{$i}/view",
                'tgl_upload' => '2026-05-01',
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('arsip.exportPdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_pdf_view_renders_columns_and_handles_null_logo(): void
    {
        $subKegiatan = SubKegiatan::with('kegiatan.program')->first();

        $arsip = Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'kode_arsip' => 'ARS-RELASI-01',
            'judul_arsip' => 'Arsip Berelasi Lengkap',
            'google_drive_url' => 'https://drive.google.com/file/d/test-relasi/view',
            'tgl_upload' => '2026-05-01',
        ]);

        $arsipLoaded = Arsip::with(['kategori', 'subKegiatan.kegiatan.program'])->find($arsip->id_arsip);

        // 1. Render view dengan relasi lengkap
        $viewContent = view('arsip.export-pdf', [
            'arsips' => collect([$arsipLoaded]),
            'total' => 1,
            'batas' => 2000,
            'terpotong' => false,
            'filters' => [],
            'printDate' => '02 November 2026',
            'currentUser' => $this->admin,
            'logoBase64' => 'data:image/png;base64,samplebase64',
        ])->render();

        $this->assertStringContainsString($this->kategori->nama_kategori, $viewContent);
        $this->assertStringContainsString($subKegiatan->kegiatan->program->nama_program, $viewContent);
        $this->assertStringContainsString($subKegiatan->kegiatan->nama_kegiatan, $viewContent);
        $this->assertStringContainsString($subKegiatan->nama_sub_kegiatan, $viewContent);

        // 2. Render view dengan logoBase64 = null (tidak boleh error)
        $viewNullLogo = view('arsip.export-pdf', [
            'arsips' => collect([$arsipLoaded]),
            'total' => 1,
            'batas' => 2000,
            'terpotong' => false,
            'filters' => [],
            'printDate' => '02 November 2026',
            'currentUser' => $this->admin,
            'logoBase64' => null,
        ])->render();

        $this->assertNotEmpty($viewNullLogo);
        $this->assertStringNotContainsString('data:image/png;base64', $viewNullLogo);
    }

    public function test_export_pdf_view_renders_all_twelve_archives_without_loss(): void
    {
        $subKegiatan = SubKegiatan::first();
        $createdCodes = [];

        for ($i = 1; $i <= 12; $i++) {
            $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $kode = "ARS-MULTI-{$num}";
            $createdCodes[] = $kode;

            Arsip::create([
                'id_user' => $this->admin->id_user,
                'id_kategori' => $this->kategori->id_kategori,
                'sub_kegiatan_id' => $subKegiatan->id,
                'kode_arsip' => $kode,
                'judul_arsip' => "Arsip Uji Multi Baris {$num}",
                'google_drive_url' => "https://drive.google.com/file/d/multi-{$num}/view",
                'tgl_upload' => '2026-05-01',
            ]);
        }

        $arsips = Arsip::with(['kategori', 'user', 'subKegiatan.kegiatan.program', 'subJenisSpj', 'tahunArsip'])
            ->whereIn('kode_arsip', $createdCodes)
            ->get();

        $this->assertCount(12, $arsips);

        $html = view('arsip.export-pdf', [
            'arsips' => $arsips,
            'total' => 12,
            'batas' => 2000,
            'terpotong' => false,
            'filters' => [],
            'printDate' => '02 November 2026',
            'currentUser' => $this->admin,
            'logoBase64' => null,
        ])->render();

        foreach ($createdCodes as $kode) {
            $this->assertStringContainsString($kode, $html, "Kode arsip {$kode} tidak ditemukan di dalam HTML PDF.");
        }
    }

    public function test_export_pdf_route_with_twelve_archives_generates_valid_pdf_pages(): void
    {
        $subKegiatan = SubKegiatan::first();

        for ($i = 1; $i <= 12; $i++) {
            $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            Arsip::create([
                'id_user' => $this->admin->id_user,
                'id_kategori' => $this->kategori->id_kategori,
                'sub_kegiatan_id' => $subKegiatan->id,
                'kode_arsip' => "ARS-PAGE-{$num}",
                'judul_arsip' => "Arsip Uji Halaman PDF {$num}",
                'google_drive_url' => "https://drive.google.com/file/d/page-{$num}/view",
                'tgl_upload' => '2026-05-01',
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('arsip.exportPdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        $content = $response->getContent();
        $pageCount = preg_match_all('/\/Type\s*\/Page[^s]/', $content, $matches);

        $this->assertGreaterThan(0, $pageCount, 'Jumlah halaman PDF harus lebih dari 0.');
    }

    public function test_export_pdf_view_unit_pengolah_consistency_and_no_fallback_to_sumber(): void
    {
        $arsipTanpaUnit = new Arsip([
            'kode_arsip' => 'ARS-UNIT-01',
            'judul_arsip' => 'Arsip Uji Unit Pengolah Kosong',
            'unit_pengolah' => null,
            'sumber' => 'SUMBER-UJI-XYZ',
            'status_berkas' => 'Fisik',
        ]);

        $html1 = view('arsip.export-pdf', [
            'arsips' => collect([$arsipTanpaUnit]),
            'total' => 1,
            'batas' => 2000,
            'terpotong' => false,
            'filters' => [],
            'printDate' => '02 November 2026',
            'currentUser' => $this->admin,
            'logoBase64' => null,
        ])->render();

        $this->assertStringNotContainsString('SUMBER-UJI-XYZ', $html1);
        $this->assertStringContainsString('<td>-</td>', $html1);

        $arsipDenganUnit = new Arsip([
            'kode_arsip' => 'ARS-UNIT-02',
            'judul_arsip' => 'Arsip Uji Unit Pengolah Terisi',
            'unit_pengolah' => 'BIDANG UJI',
            'sumber' => 'SUMBER-LAIN',
            'status_berkas' => 'Fisik',
        ]);

        $html2 = view('arsip.export-pdf', [
            'arsips' => collect([$arsipDenganUnit]),
            'total' => 1,
            'batas' => 2000,
            'terpotong' => false,
            'filters' => [],
            'printDate' => '02 November 2026',
            'currentUser' => $this->admin,
            'logoBase64' => null,
        ])->render();

        $this->assertStringContainsString('BIDANG UJI', $html2);
    }

    public function test_export_excel_format_matches_diskominfo_standard(): void
    {
        $subKegiatan = SubKegiatan::first();

        $arsip = Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'kode_arsip' => 'ARS-DISKOMINFO-01',
            'no_item_arsip' => '001/ITEM/2026',
            'nomor_berkas' => 'BERKAS-10',
            'nomor_surat' => '900/123/DISKOMINFO/2026',
            'tanggal_surat' => '2026-11-24',
            'jumlah_surat' => 5,
            'kode_klasifikasi' => 'KU.01.02',
            'skkad' => 'Terbuka',
            'judul_arsip' => 'Pengadaan Komputer Kantor',
            'hal' => 'Surat Pesanan Komputer',
            'google_drive_url' => 'https://drive.google.com/file/d/test-excel/view',
            'tgl_upload' => '2026-11-24',
        ]);

        $response = $this->actingAs($this->admin)->get(route('arsip.downloadExcel', $arsip));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $tempFile = tempnam(sys_get_temp_dir(), 'excel_test');
        file_put_contents($tempFile, $response->streamedContent());

        $reader = new Xlsx;
        $spreadsheet = $reader->load($tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Validasi 9 Header Diskominfo (Tanpa kolom 'No')
        $expectedHeaders = [
            'A1' => 'Nomor Item Arsip',
            'B1' => 'Nomor Berkas',
            'C1' => 'Kode Klasifikasi',
            'D1' => 'Uraian Informasi Berkas',
            'E1' => 'Uraian Informasi Arsip',
            'F1' => 'Tanggal Surat',
            'G1' => 'Jumlah Surat',
            'H1' => 'Keterangan',
            'I1' => 'SKKAAD',
        ];

        foreach ($expectedHeaders as $cell => $expectedTitle) {
            $this->assertEquals($expectedTitle, $sheet->getCell($cell)->getValue(), "Header pada {$cell} harus {$expectedTitle}");
        }

        // 2. Validasi Isi Data Baris 2 (9 Kolom)
        $this->assertEquals('001/ITEM/2026', $sheet->getCell('A2')->getValue());
        $this->assertEquals('BERKAS-10', $sheet->getCell('B2')->getValue());
        $this->assertEquals('KU.01.02', $sheet->getCell('C2')->getValue());
        $this->assertEquals($subKegiatan->nama_sub_kegiatan, $sheet->getCell('D2')->getValue());
        $this->assertEquals('900/123/DISKOMINFO/2026 - Pengadaan Komputer Kantor - Surat Pesanan Komputer', $sheet->getCell('E2')->getValue());
        $this->assertEquals('24/11/2026', $sheet->getCell('F2')->getValue());
        $this->assertEquals('5 lembar', $sheet->getCell('G2')->getValue());
        $this->assertEquals('Soft File', $sheet->getCell('H2')->getValue());
        $this->assertEquals('Terbuka', $sheet->getCell('I2')->getValue());

        @unlink($tempFile);
    }

    public function test_export_excel_keterangan_arsip_kertas_and_no_nomor_surat(): void
    {
        $arsipKertas = Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $this->kategori->id_kategori,
            'kode_arsip' => 'ARS-DISKOMINFO-02',
            'no_item_arsip' => '002/ITEM/2026',
            'nomor_berkas' => 'BERKAS-11',
            'nomor_surat' => null,
            'tanggal_surat' => null,
            'jumlah_surat' => null,
            'kode_klasifikasi' => 'OT.00.01',
            'skkad' => 'Rahasia',
            'judul_arsip' => 'Berkas Fisik Rahasia Tanpa Digital',
            'file_path' => null,
            'google_drive_url' => null,
            'tgl_upload' => '2026-11-24',
        ]);

        $response = $this->actingAs($this->admin)->get(route('arsip.downloadExcel', $arsipKertas));
        $response->assertStatus(200);

        $tempFile = tempnam(sys_get_temp_dir(), 'excel_test');
        file_put_contents($tempFile, $response->streamedContent());

        $reader = new Xlsx;
        $spreadsheet = $reader->load($tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertEquals('Berkas Fisik Rahasia Tanpa Digital', $sheet->getCell('E2')->getValue());
        $this->assertEquals('1 lembar', $sheet->getCell('G2')->getValue());
        $this->assertEquals('Arsip Kertas', $sheet->getCell('H2')->getValue());
        $this->assertEquals('Rahasia', $sheet->getCell('I2')->getValue());

        @unlink($tempFile);
    }

    public function test_skkad_validation_accepts_only_three_valid_options(): void
    {
        $subKegiatan = SubKegiatan::first();

        // 1. Opsi lama 'Biasa' atau 'Terbatas' harus gagal validasi
        $invalidRes = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Uji SKKAAD Invalid',
            'skkad' => 'Biasa',
            'tgl_upload' => '2026-11-25',
            'google_drive_url' => 'https://drive.google.com/file/d/test-skkad/view',
        ]);
        $invalidRes->assertSessionHasErrors('skkad');

        // 2. Opsi valid 'Tertutup' harus berhasil
        $validRes = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Uji SKKAAD Valid Tertutup',
            'skkad' => 'Tertutup',
            'tgl_upload' => '2026-11-25',
            'google_drive_url' => 'https://drive.google.com/file/d/test-skkad2/view',
        ]);
        $validRes->assertRedirect(route('arsip.index'));
        $this->assertDatabaseHas('arsip', [
            'judul_arsip' => 'Arsip Uji SKKAAD Valid Tertutup',
            'skkad' => 'Tertutup',
        ]);
    }

    public function test_arsip_crud_with_jumlah_surat(): void
    {
        $subKegiatan = SubKegiatan::first();

        // 1. Simpan Arsip baru dengan jumlah_surat
        $response = $this->actingAs($this->operator)->post('/arsip', [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Uji Jumlah Surat',
            'jumlah_surat' => 12,
            'google_drive_url' => 'https://drive.google.com/file/d/test-jumlah/view',
            'tgl_upload' => '2026-11-24',
        ]);

        $response->assertRedirect(route('arsip.index'));
        $arsip = Arsip::where('judul_arsip', 'Arsip Uji Jumlah Surat')->first();
        $this->assertNotNull($arsip);
        $this->assertEquals(12, $arsip->jumlah_surat);

        // 2. Update jumlah_surat
        $updateResponse = $this->actingAs($this->operator)->put("/arsip/{$arsip->id_arsip}", [
            'id_kategori' => $this->kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Arsip Uji Jumlah Surat Updated',
            'jumlah_surat' => 25,
            'google_drive_url' => 'https://drive.google.com/file/d/test-jumlah/view',
            'tgl_upload' => '2026-11-24',
        ]);

        $updateResponse->assertRedirect(route('arsip.index'));
        $arsip->refresh();
        $this->assertEquals(25, $arsip->jumlah_surat);
        $this->assertEquals('Arsip Uji Jumlah Surat Updated', $arsip->judul_arsip);
    }
}
