<?php

namespace Tests\Feature\Admin;

use App\Models\Poli;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoliManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);
    }

    public function test_admin_can_view_poli_list(): void
    {
        Poli::create([
            'name' => 'Poli Umum',
            'description' => 'Layanan kesehatan umum',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.polis.index'));

        $response->assertStatus(200);
        $response->assertSee('Poli Umum');
    }

    public function test_admin_can_create_poli(): void
    {
        $poliData = [
            'name' => 'Poli Gigi',
            'description' => 'Layanan kesehatan gigi',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.polis.store'), $poliData);

        $response->assertRedirect(route('admin.polis.index'));
        $this->assertDatabaseHas('polis', ['name' => 'Poli Gigi']);
    }

    public function test_admin_can_update_poli(): void
    {
        $poli = Poli::create([
            'name' => 'Old Poli',
            'description' => 'Old Description',
        ]);

        $updateData = [
            'name' => 'Updated Poli',
            'description' => 'Updated Description',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.polis.update', $poli), $updateData);

        $response->assertRedirect(route('admin.polis.index'));
        $this->assertDatabaseHas('polis', [
            'id' => $poli->id,
            'name' => 'Updated Poli',
        ]);
    }

    public function test_admin_can_delete_poli(): void
    {
        $poli = Poli::create([
            'name' => 'To Be Deleted',
            'description' => 'Will be deleted',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.polis.destroy', $poli));

        $response->assertRedirect(route('admin.polis.index'));
        $this->assertDatabaseMissing('polis', ['id' => $poli->id]);
    }

    public function test_non_admin_cannot_access_poli_management(): void
    {
        $pasien = User::factory()->create(['role' => UserRole::PASIEN->value]);

        $response = $this->actingAs($pasien)->get(route('admin.polis.index'));

        $response->assertStatus(403); // Middleware role should deny access
    }
}
