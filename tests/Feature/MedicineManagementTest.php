<?php

namespace Tests\Feature;

use App\Models\Medicine;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicineManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Buat user dengan role admin untuk akses ke controller
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_view_medicine_list(): void
    {
        $medicine = Medicine::create([
            'name' => 'Paracetamol',
            'code' => 'PCT01',
            'stock' => 100,
            'unit' => 'tablet',
            'price' => 5000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.medicines.index'));

        $response->assertStatus(200);
        $response->assertSee('Paracetamol');
        $response->assertSee('PCT01');
    }

    public function test_admin_can_create_medicine(): void
    {
        $medicineData = [
            'name' => 'Amoxicillin',
            'code' => 'AMX01',
            'description' => 'Antibiotik',
            'stock' => 50,
            'unit' => 'kaplet',
            'price' => 12000,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.medicines.store'), $medicineData);

        $response->assertRedirect(route('admin.medicines.index'));
        $this->assertDatabaseHas('medicines', [
            'name' => 'Amoxicillin',
            'code' => 'AMX01',
        ]);
    }

    public function test_admin_can_update_medicine(): void
    {
        $medicine = Medicine::create([
            'name' => 'Old Name',
            'code' => 'OLD01',
            'stock' => 10,
            'unit' => 'botol',
            'price' => 15000,
        ]);

        $updateData = [
            'name' => 'New Name',
            'code' => 'NEW01',
            'stock' => 20,
            'unit' => 'botol',
            'price' => 16000,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.medicines.update', $medicine), $updateData);

        $response->assertRedirect(route('admin.medicines.index'));
        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'name' => 'New Name',
            'code' => 'NEW01',
        ]);
    }

    public function test_admin_can_delete_medicine(): void
    {
        $medicine = Medicine::create([
            'name' => 'To Be Deleted',
            'code' => 'DEL01',
            'stock' => 5,
            'unit' => 'strip',
            'price' => 2000,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.medicines.destroy', $medicine));

        $response->assertRedirect(route('admin.medicines.index'));
        $this->assertDatabaseMissing('medicines', [
            'id' => $medicine->id,
        ]);
    }
}
