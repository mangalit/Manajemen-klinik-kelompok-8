<?php

namespace Tests\Feature\Admin;

use App\Models\Doctor;
use App\Models\Poli;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorManagementTest extends TestCase
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

    public function test_admin_can_view_doctor_list(): void
    {
        $poli = Poli::create(['name' => 'Poli Umum']);
        $user = User::factory()->create(['role' => UserRole::DOKTER->value]);
        Doctor::create([
            'user_id' => $user->id,
            'poli_id' => $poli->id,
            'specialization' => 'Dokter Umum',
            'phone_number' => '08123456789',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.doctors.index'));

        $response->assertStatus(200);
        $response->assertSee('Dokter Umum');
        $response->assertSee($user->name);
    }

    public function test_admin_can_create_doctor(): void
    {
        $poli = Poli::create(['name' => 'Poli Umum']);
        $user = User::factory()->create(['role' => UserRole::DOKTER->value]);
        
        $doctorData = [
            'user_id' => $user->id,
            'poli_id' => $poli->id,
            'specialization' => 'Spesialis Jantung',
            'phone_number' => '08987654321',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.doctors.store'), $doctorData);

        $response->assertRedirect(route('admin.doctors.index'));
        $this->assertDatabaseHas('doctors', [
            'user_id' => $user->id,
            'specialization' => 'Spesialis Jantung',
        ]);
    }

    public function test_admin_can_update_doctor(): void
    {
        $poli = Poli::create(['name' => 'Poli Umum']);
        $user = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'poli_id' => $poli->id,
            'specialization' => 'Old Specialization',
            'phone_number' => '08123',
        ]);

        $updateData = [
            'poli_id' => $poli->id,
            'specialization' => 'New Specialization',
            'phone_number' => '08456',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.doctors.update', $doctor), $updateData);

        $response->assertRedirect(route('admin.doctors.index'));
        $this->assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'specialization' => 'New Specialization',
        ]);
    }

    public function test_admin_can_delete_doctor(): void
    {
        $poli = Poli::create(['name' => 'Poli Umum']);
        $user = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'poli_id' => $poli->id,
            'specialization' => 'To Be Deleted',
            'phone_number' => '08123',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.doctors.destroy', $doctor));

        $response->assertRedirect(route('admin.doctors.index'));
        $this->assertDatabaseMissing('doctors', ['id' => $doctor->id]);
    }
}
