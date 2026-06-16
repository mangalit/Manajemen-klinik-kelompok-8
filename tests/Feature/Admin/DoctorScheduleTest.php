<?php

namespace Tests\Feature\Admin;

use App\Models\Doctor;
use App\Models\Poli;
use App\Models\Schedule;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        
        $poli = Poli::create(['name' => 'Poli Test']);
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $this->doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'poli_id' => $poli->id,
            'specialization' => 'Test',
            'phone_number' => '08123',
        ]);
    }

    public function test_admin_can_view_doctor_schedule_index(): void
    {
        Schedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.doctors.schedules.index', $this->doctor));

        $response->assertStatus(200);
        $response->assertSee('Senin');
        $response->assertSee('08:00');
    }

    public function test_admin_can_create_doctor_schedule(): void
    {
        $scheduleData = [
            'day_of_week' => 'Selasa',
            'start_time' => '13:00',
            'end_time' => '17:00',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.doctors.schedules.store', $this->doctor), $scheduleData);

        $response->assertRedirect(route('admin.doctors.schedules.index', $this->doctor));
        $this->assertDatabaseHas('schedules', [
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 'Selasa',
            'start_time' => '13:00',
        ]);
    }

    public function test_admin_cannot_create_schedule_with_invalid_times(): void
    {
        $scheduleData = [
            'day_of_week' => 'Selasa',
            'start_time' => '13:00',
            'end_time' => '10:00', // End time before start time
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.doctors.schedules.store', $this->doctor), $scheduleData);

        $response->assertSessionHasErrors(['end_time']);
    }

    public function test_admin_can_update_doctor_schedule(): void
    {
        $schedule = Schedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => true,
        ]);

        $updateData = [
            'day_of_week' => 'Rabu',
            'start_time' => '09:00',
            'end_time' => '13:00',
            'is_active' => false,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.schedules.update', $schedule), $updateData);

        $response->assertRedirect(route('admin.doctors.schedules.index', $this->doctor->id));
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'day_of_week' => 'Rabu',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_doctor_schedule(): void
    {
        $schedule = Schedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.schedules.destroy', $schedule));

        $response->assertRedirect(route('admin.doctors.schedules.index', $this->doctor->id));
        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }
}
