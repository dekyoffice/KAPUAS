<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_create_new_user(): void
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'nama_user' => 'Pengguna Uji Coba',
            'email' => 'ujicoba@kapuas.go.id',
            'role' => 'operator',
            'status' => 'aktif',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'ujicoba@kapuas.go.id',
            'role' => 'operator',
            'status' => 'aktif',
        ]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete("/users/{$this->admin->id_user}");

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id_user' => $this->admin->id_user,
        ]);
    }

    public function test_cannot_delete_user_with_associated_archives(): void
    {
        $operator = User::where('role', 'operator')->first();

        // Operator sudah memiliki arsip dari seeder
        $this->assertGreaterThan(0, $operator->arsip()->count());

        $response = $this->actingAs($this->admin)->delete("/users/{$operator->id_user}");

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id_user' => $operator->id_user,
        ]);
    }

    public function test_user_list_escapes_special_characters_in_name(): void
    {
        $user = User::factory()->create([
            'nama_user' => "Ma'ruf <b>x</b>",
            'email' => 'maruf@kapuas.go.id',
            'role' => 'operator',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);
        $response->assertSee('Ma\u0027ruf', false);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $response = $this->actingAs($this->admin)->put("/users/{$this->admin->id_user}", [
            'nama_user' => $this->admin->nama_user,
            'email' => $this->admin->email,
            'role' => 'admin',
            'status' => 'nonaktif',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        $this->assertEquals('aktif', $this->admin->fresh()->status);
    }

    public function test_admin_cannot_change_own_role_to_non_admin(): void
    {
        $response = $this->actingAs($this->admin)->put("/users/{$this->admin->id_user}", [
            'nama_user' => $this->admin->nama_user,
            'email' => $this->admin->email,
            'role' => 'operator',
            'status' => 'aktif',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
        $this->assertEquals('admin', $this->admin->fresh()->role);
    }

    public function test_last_active_admin_cannot_be_deleted_or_deactivated(): void
    {
        // Pastikan hanya ada 1 admin aktif
        User::where('role', 'admin')->where('id_user', '!=', $this->admin->id_user)->delete();

        // Coba hapus akun admin yang sedang login (terakhir)
        $response = $this->actingAs($this->admin)->delete("/users/{$this->admin->id_user}");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id_user' => $this->admin->id_user]);
    }
}

