<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Poli;
use App\Models\Schedule;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
    }

    public function test_admin_can_view_revenue_report(): void
    {
        // Setup data for payment
        $kasir = User::factory()->create(['role' => UserRole::KASIR->value]);
        $poli = Poli::create(['name' => 'Poli Test']);
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $doctor = Doctor::create(['user_id' => $doctorUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '081']);
        
        $patientUser = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $patient = Patient::create(['user_id' => $patientUser->id, 'nik' => '1', 'date_of_birth' => '1990-01-01', 'phone_number' => '1', 'address' => '1']);
        
        $schedule = Schedule::create(['doctor_id' => $doctor->id, 'day_of_week' => 'Senin', 'start_time' => '08:00', 'end_time' => '12:00']);
        
        $booking = Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'completed'
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'consultation_fee' => 50000,
            'medicine_fee' => 10000,
            'total_amount' => 60000,
            'payment_method' => 'cash',
            'paid_at' => now(),
            'cashier_id' => $kasir->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.revenue'));

        $response->assertStatus(200);
        $response->assertSee('60.000');
    }

    public function test_admin_can_view_patient_report(): void
    {
        // Setup data for booking
        $poli = Poli::create(['name' => 'Poli Test']);
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $doctor = Doctor::create(['user_id' => $doctorUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '081']);
        
        $patientUser = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $patient = Patient::create(['user_id' => $patientUser->id, 'nik' => '1', 'date_of_birth' => '1990-01-01', 'phone_number' => '1', 'address' => '1']);
        
        $schedule = Schedule::create(['doctor_id' => $doctor->id, 'day_of_week' => 'Senin', 'start_time' => '08:00', 'end_time' => '12:00']);
        
        Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.patients'));

        $response->assertStatus(200);
        $response->assertSee($patientUser->name);
    }
}
