<?php

namespace Tests\Feature;

use App\Models\Arsip;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sistem Informasi KAPUAS');
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@kapuas.go.id',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@kapuas.go.id',
            'password' => '123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::where('email', 'admin@kapuas.go.id')->first();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_active_user_session_terminated_when_deactivated(): void
    {
        $user = User::factory()->create([
            'status' => 'aktif',
            'role' => 'operator',
        ]);

        $this->actingAs($user);

        // Akses halaman saat masih aktif
        $this->get('/dashboard')->assertStatus(200);

        // Pengguna dinonaktifkan
        $user->update(['status' => 'nonaktif']);

        // Request berikutnya dari sesi aktif harus ditendang ke login dan menjadi guest
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        $this->assertGuest();
    }

    public function test_login_rate_limiter_throttles_after_five_failed_attempts(): void
    {
        $throttleEmail = 'bruteforce@kapuas.go.id';

        // 5x percobaan gagal diperbolehkan (hanya error kredensial biasa)
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->from('/login')->post('/login', [
                'email' => $throttleEmail,
                'password' => 'wrong-pass',
            ]);
            $response->assertRedirect('/login');
            $response->assertSessionHasErrors('email');
            $errors = session('errors')->get('email');
            $this->assertStringContainsString('Email atau kata sandi yang Anda masukkan tidak sesuai', $errors[0]);
        }

        // Percobaan ke-6 harus di-throttle rate limiter
        $response6 = $this->from('/login')->post('/login', [
            'email' => $throttleEmail,
            'password' => 'wrong-pass',
        ]);
        $response6->assertRedirect('/login');
        $response6->assertSessionHasErrors('email');
        $errors6 = session('errors')->get('email');
        $this->assertStringContainsString('Terlalu banyak percobaan login', $errors6[0]);
    }

    public function test_protected_pages_have_no_store_cache_headers(): void
    {
        $admin = User::where('role', 'admin')->first();
        $viewer = User::where('role', 'viewer')->first();

        // 1. GET /dashboard sebagai admin: header Cache-Control memuat no-store
        $resDashboard = $this->actingAs($admin)->get('/dashboard');
        $resDashboard->assertStatus(200);
        $this->assertStringContainsString('no-store', (string) $resDashboard->headers->get('Cache-Control'));

        // 2. GET /arsip sebagai viewer: header memuat no-store
        $resArsip = $this->actingAs($viewer)->get('/arsip');
        $resArsip->assertStatus(200);
        $this->assertStringContainsString('no-store', (string) $resArsip->headers->get('Cache-Control'));
    }

    public function test_file_download_is_not_forced_no_store(): void
    {
        Storage::fake('local');
        $fakePath = 'arsip/2026/sample_dokumen.pdf';
        Storage::disk('local')->put($fakePath, 'Sample content');

        $admin = User::where('role', 'admin')->first();
        $kategori = Kategori::first();

        $arsip = Arsip::create([
            'id_user' => $admin->id_user,
            'id_kategori' => $kategori->id_kategori,
            'kode_arsip' => 'ARS-CACHE-01',
            'judul_arsip' => 'Test Unduh No Store Bypass',
            'file_path' => $fakePath,
            'tgl_upload' => '2026-05-01',
        ]);

        $response = $this->actingAs($admin)->get("/arsip/{$arsip->id_arsip}/download");
        $response->assertStatus(200);
    }

    public function test_after_logout_dashboard_redirects_to_login(): void
    {
        $user = User::where('role', 'admin')->first();

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));

        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }
}
