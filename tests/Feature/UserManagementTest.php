<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/settings/users');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_settings(): void
    {
        $user = User::where('role', 'user')->first() ?? User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->get('/settings/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/settings/users');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Akun Pengguna');
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->post('/settings/users', [
            'name' => 'Operator Baru',
            'email' => 'operator@sscworks.com',
            'role' => 'user',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/settings/users');
        $this->assertDatabaseHas('users', ['email' => 'operator@sscworks.com']);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->delete("/settings/users/{$admin->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
