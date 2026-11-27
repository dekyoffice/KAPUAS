<?php

namespace Tests\Feature;

use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Program;
use App\Models\SubJenisSpj;
use App\Models\SubKegiatan;
use App\Models\TahunArsip;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    protected User $admin;
    protected User $operator;
    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first();
        $this->operator = User::where('role', 'operator')->first();
        $this->viewer = User::where('role', 'viewer')->first();
    }

    public function test_admin_can_access_all_master_data_pages(): void
    {
        $this->actingAs($this->admin)->get(route('master-data.program.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('master-data.sub-jenis-spj.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('master-data.tahun-arsip.index'))->assertStatus(200);
    }

    public function test_operator_and_viewer_are_forbidden_from_master_data_management(): void
    {
        $this->actingAs($this->operator)->get(route('master-data.program.index'))->assertStatus(403);
        $this->actingAs($this->operator)->get(route('master-data.sub-jenis-spj.index'))->assertStatus(403);
        $this->actingAs($this->operator)->get(route('master-data.tahun-arsip.index'))->assertStatus(403);

        $this->actingAs($this->viewer)->get(route('master-data.program.index'))->assertStatus(403);
        $this->actingAs($this->viewer)->get(route('master-data.sub-jenis-spj.index'))->assertStatus(403);
        $this->actingAs($this->viewer)->get(route('master-data.tahun-arsip.index'))->assertStatus(403);
    }

    public function test_admin_and_operator_can_access_internal_master_api(): void
    {
        $this->actingAs($this->operator)
            ->getJson(route('api.master.tahun-program'))
            ->assertStatus(200)
            ->assertJsonIsArray();

        $progRes = $this->actingAs($this->admin)
            ->getJson(route('api.master.programs', ['tahun' => 2026]))
            ->assertStatus(200)
            ->assertJsonIsArray();

        $program = Program::first();
        if ($program) {
            $this->actingAs($this->operator)
                ->getJson(route('api.master.kegiatans', ['program_id' => $program->id]))
                ->assertStatus(200)
                ->assertJsonIsArray();
        }

        $kegiatan = Kegiatan::first();
        if ($kegiatan) {
            $this->actingAs($this->operator)
                ->getJson(route('api.master.sub-kegiatans', ['kegiatan_id' => $kegiatan->id]))
                ->assertStatus(200)
                ->assertJsonIsArray();
        }
    }

    public function test_admin_can_crud_hierarki_program(): void
    {
        // 1. Create Program
        $response = $this->actingAs($this->admin)->post(route('master-data.program.store'), [
            'tahun_penetapan' => 2030,
            'nama_program' => 'Program Uji Otomatisasi',
        ]);
        $response->assertRedirect();
        $program = Program::where('nama_program', 'Program Uji Otomatisasi')->first();
        $this->assertNotNull($program);

        // 2. Create Kegiatan
        $responseKeg = $this->actingAs($this->admin)->post(route('master-data.kegiatan.store'), [
            'program_id' => $program->id,
            'nama_kegiatan' => 'Kegiatan Uji Otomatisasi',
        ]);
        $responseKeg->assertRedirect();
        $kegiatan = Kegiatan::where('nama_kegiatan', 'Kegiatan Uji Otomatisasi')->first();
        $this->assertNotNull($kegiatan);

        // 3. Create Sub Kegiatan
        $responseSub = $this->actingAs($this->admin)->post(route('master-data.sub-kegiatan.store'), [
            'kegiatan_id' => $kegiatan->id,
            'nama_sub_kegiatan' => 'Sub Kegiatan Uji Otomatisasi',
        ]);
        $responseSub->assertRedirect();
        $subKegiatan = SubKegiatan::where('nama_sub_kegiatan', 'Sub Kegiatan Uji Otomatisasi')->first();
        $this->assertNotNull($subKegiatan);

        // Test full_path accessor
        $this->assertStringContainsString('Program Uji Otomatisasi', $subKegiatan->full_path);
        $this->assertStringContainsString('Kegiatan Uji Otomatisasi', $subKegiatan->full_path);
        $this->assertStringContainsString('Sub Kegiatan Uji Otomatisasi', $subKegiatan->full_path);

        // 4. Update Sub Kegiatan
        $this->actingAs($this->admin)->put(route('master-data.sub-kegiatan.update', $subKegiatan), [
            'kegiatan_id' => $kegiatan->id,
            'nama_sub_kegiatan' => 'Sub Kegiatan Uji Diperbarui',
        ])->assertRedirect();
        $this->assertDatabaseHas('sub_kegiatans', ['nama_sub_kegiatan' => 'Sub Kegiatan Uji Diperbarui']);

        // 5. Delete Sub Kegiatan
        $this->actingAs($this->admin)->delete(route('master-data.sub-kegiatan.destroy', $subKegiatan))->assertRedirect();
        $this->assertDatabaseMissing('sub_kegiatans', ['id' => $subKegiatan->id]);

        // Clean up kegiatan & program
        $this->actingAs($this->admin)->delete(route('master-data.kegiatan.destroy', $kegiatan))->assertRedirect();
        $this->actingAs($this->admin)->delete(route('master-data.program.destroy', $program))->assertRedirect();
    }

    public function test_cannot_delete_sub_kegiatan_if_referenced_by_arsip(): void
    {
        // Cari sub kegiatan yang sudah memiliki arsip
        $subKegiatanWithArsip = SubKegiatan::has('arsip')->first();
        $this->assertNotNull($subKegiatanWithArsip);

        $response = $this->actingAs($this->admin)->delete(route('master-data.sub-kegiatan.destroy', $subKegiatanWithArsip));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('sub_kegiatans', ['id' => $subKegiatanWithArsip->id]);
    }

    public function test_cannot_delete_sub_jenis_spj_if_referenced_by_arsip(): void
    {
        $spjWithArsip = SubJenisSpj::has('arsip')->first();
        if ($spjWithArsip) {
            $response = $this->actingAs($this->admin)->delete(route('master-data.sub-jenis-spj.destroy', $spjWithArsip));
            $response->assertSessionHas('error');
            $this->assertDatabaseHas('sub_jenis_spjs', ['id' => $spjWithArsip->id]);
        }
    }

    public function test_cannot_delete_tahun_arsip_if_referenced_by_arsip(): void
    {
        $tahunWithArsip = TahunArsip::has('arsip')->first();
        if ($tahunWithArsip) {
            $response = $this->actingAs($this->admin)->delete(route('master-data.tahun-arsip.destroy', $tahunWithArsip));
            $response->assertSessionHas('error');
            $this->assertDatabaseHas('tahun_arsips', ['id' => $tahunWithArsip->id]);
        }
    }

    public function test_arsip_store_syncs_hierarki_text_from_master_data(): void
    {
        Storage::fake('local');

        $subKegiatan = SubKegiatan::with('kegiatan.program')->first();
        $this->assertNotNull($subKegiatan);

        $file = UploadedFile::fake()->create('dokumen_test.pdf', 500, 'application/pdf');

        $kategori = \App\Models\Kategori::first();
        $this->assertNotNull($kategori);

        $response = $this->actingAs($this->operator)->post(route('arsip.store'), [
            'id_kategori' => $kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'judul_arsip' => 'Berkas Uji Integrasi Master Data',
            'tgl_upload' => '2026-03-01',
            'file' => $file,
        ]);

        $response->assertRedirect(route('arsip.index'));

        $arsip = Arsip::where('judul_arsip', 'Berkas Uji Integrasi Master Data')->first();
        $this->assertNotNull($arsip);
        $this->assertEquals($subKegiatan->id, $arsip->sub_kegiatan_id);
        $this->assertEquals($subKegiatan->nama_sub_kegiatan, $arsip->sub_kegiatan);
        $this->assertEquals($subKegiatan->kegiatan->nama_kegiatan, $arsip->kegiatan);
        $this->assertEquals($subKegiatan->kegiatan->program->nama_program, $arsip->program);
    }

    public function test_duplicate_kegiatan_and_sub_kegiatan_rejected(): void
    {
        $program = Program::first();
        $this->assertNotNull($program);

        // 1. Duplikasi kegiatan pada program yang sama harus ditolak
        $this->actingAs($this->admin)->post(route('master-data.kegiatan.store'), [
            'program_id' => $program->id,
            'nama_kegiatan' => 'Kegiatan Unik Bersama',
        ])->assertRedirect();

        $dupKegiatan = $this->actingAs($this->admin)->post(route('master-data.kegiatan.store'), [
            'program_id' => $program->id,
            'nama_kegiatan' => 'Kegiatan Unik Bersama',
        ]);
        $dupKegiatan->assertSessionHasErrors('nama_kegiatan');

        $kegiatan = Kegiatan::where('nama_kegiatan', 'Kegiatan Unik Bersama')->first();

        // 2. Duplikasi sub kegiatan pada kegiatan yang sama harus ditolak
        $this->actingAs($this->admin)->post(route('master-data.sub-kegiatan.store'), [
            'kegiatan_id' => $kegiatan->id,
            'nama_sub_kegiatan' => 'Sub Kegiatan Unik Bersama',
        ])->assertRedirect();

        $dupSubKegiatan = $this->actingAs($this->admin)->post(route('master-data.sub-kegiatan.store'), [
            'kegiatan_id' => $kegiatan->id,
            'nama_sub_kegiatan' => 'Sub Kegiatan Unik Bersama',
        ]);
        $dupSubKegiatan->assertSessionHasErrors('nama_sub_kegiatan');
    }

    public function test_rename_master_sub_kegiatan_updates_related_archives(): void
    {
        Storage::fake('local');
        $subKegiatan = SubKegiatan::first();
        $kategori = \App\Models\Kategori::first();

        $arsip = Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'kode_arsip' => 'ARS-2026-8888',
            'judul_arsip' => 'Arsip Uji Sinkronisasi Hierarki',
            'sub_kegiatan' => $subKegiatan->nama_sub_kegiatan,
            'tgl_upload' => '2026-03-01',
        ]);

        $namaBaru = 'Nama Sub Kegiatan Hasil Sinkronisasi';

        $this->actingAs($this->admin)->put(route('master-data.sub-kegiatan.update', $subKegiatan), [
            'kegiatan_id' => $subKegiatan->kegiatan_id,
            'kode_sub_kegiatan' => $subKegiatan->kode_sub_kegiatan,
            'nama_sub_kegiatan' => $namaBaru,
        ])->assertRedirect();

        $arsip->refresh();
        $this->assertEquals($namaBaru, $arsip->sub_kegiatan);
    }
}

