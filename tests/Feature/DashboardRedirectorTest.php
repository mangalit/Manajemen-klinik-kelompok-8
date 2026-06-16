<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRedirectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_dokter_is_redirected_to_dokter_dashboard(): void
    {
        $user = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect(route('dokter.dashboard'));
    }

    public function test_kasir_is_redirected_to_kasir_dashboard(): void
    {
        $user = User::factory()->create(['role' => UserRole::KASIR->value]);
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect(route('kasir.dashboard'));
    }

    public function test_pasien_is_redirected_to_pasien_dashboard(): void
    {
        $user = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect(route('pasien.dashboard'));
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect('/login');
    }
}
