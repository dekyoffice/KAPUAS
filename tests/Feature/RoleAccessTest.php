<?php

namespace Tests\Feature;

use App\Models\Arsip;
use App\Models\Kategori;
use App\Models\SubKegiatan;
use App\Models\User;
use Tests\TestCase;

class RoleAccessTest extends TestCase
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

    public function test_admin_has_full_access(): void
    {
        $this->actingAs($this->admin)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->admin)->get('/arsip')->assertStatus(200);
        $this->actingAs($this->admin)->get('/arsip/create')->assertStatus(200);
        $this->actingAs($this->admin)->get('/kategori')->assertStatus(200);
        $this->actingAs($this->admin)->get('/users')->assertStatus(200);
        $this->actingAs($this->admin)->get('/users/create')->assertStatus(200);
        $this->actingAs($this->admin)->get(route('master-data.program.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('master-data.sub-jenis-spj.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('master-data.tahun-arsip.index'))->assertStatus(200);
    }

    public function test_operator_can_manage_arsip_but_forbidden_from_user_management(): void
    {
        $this->actingAs($this->operator)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->operator)->get('/arsip')->assertStatus(200);
        $this->actingAs($this->operator)->get('/arsip/create')->assertStatus(200);
        $this->actingAs($this->operator)->get('/kategori')->assertStatus(200);

        // Akses user management dan master data harus 403
        $this->actingAs($this->operator)->get('/users')->assertStatus(403);
        $this->actingAs($this->operator)->get('/users/create')->assertStatus(403);
        $this->actingAs($this->operator)->get(route('master-data.program.index'))->assertStatus(403);
        $this->actingAs($this->operator)->get(route('master-data.sub-jenis-spj.index'))->assertStatus(403);
        $this->actingAs($this->operator)->get(route('master-data.tahun-arsip.index'))->assertStatus(403);
    }

    public function test_viewer_has_read_only_access_and_forbidden_from_create(): void
    {
        $this->actingAs($this->viewer)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->viewer)->get('/arsip')->assertStatus(200);

        // Create arsip & kelola kategori harus 403
        $this->actingAs($this->viewer)->get('/arsip/create')->assertStatus(403);
        $this->actingAs($this->viewer)->get('/kategori')->assertStatus(403);

        // Akses user management dan master data harus 403
        $this->actingAs($this->viewer)->get('/users')->assertStatus(403);
        $this->actingAs($this->viewer)->get(route('master-data.program.index'))->assertStatus(403);
        $this->actingAs($this->viewer)->get(route('master-data.sub-jenis-spj.index'))->assertStatus(403);
        $this->actingAs($this->viewer)->get(route('master-data.tahun-arsip.index'))->assertStatus(403);
    }

    public function test_viewer_is_strictly_blocked_from_accessing_rahasia_archives(): void
    {
        $kategori = Kategori::first();
        $subKegiatan = SubKegiatan::first();

        // Buat arsip berstatus Rahasia
        $arsipRahasia = Arsip::create([
            'id_user' => $this->admin->id_user,
            'id_kategori' => $kategori->id_kategori,
            'sub_kegiatan_id' => $subKegiatan->id,
            'kode_arsip' => 'ARS-SECRET-01',
            'judul_arsip' => 'Dokumen Sangat Rahasia Negara',
            'skkad' => 'Rahasia',
            'google_drive_url' => 'https://drive.google.com/file/d/secret/view',
            'tgl_upload' => '2026-11-25',
        ]);

        // 1. Viewer tidak boleh melihat di daftar arsip (/arsip)
        $responseList = $this->actingAs($this->viewer)->get('/arsip');
        $responseList->assertStatus(200);
        $responseList->assertDontSee('Dokumen Sangat Rahasia Negara');

        // 2. Viewer diblokir 403 saat melihat berkas (/arsip/{id}/view)
        $this->actingAs($this->viewer)->get("/arsip/{$arsipRahasia->id_arsip}/view")->assertStatus(403);

        // 3. Viewer diblokir 403 saat unduh berkas (/arsip/{id}/download)
        $this->actingAs($this->viewer)->get("/arsip/{$arsipRahasia->id_arsip}/download")->assertStatus(403);

        // 4. Viewer diblokir 403 saat unduh Excel tunggal (/arsip/{id}/download-excel)
        $this->actingAs($this->viewer)->get("/arsip/{$arsipRahasia->id_arsip}/download-excel")->assertStatus(403);

        // 5. Admin & Operator tetap bisa mengakses
        $this->actingAs($this->admin)->get("/arsip/{$arsipRahasia->id_arsip}/view")->assertRedirect();
        $this->actingAs($this->operator)->get("/arsip/{$arsipRahasia->id_arsip}/view")->assertRedirect();
    }
}
