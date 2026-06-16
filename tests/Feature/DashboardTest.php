<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Poli;
use App\Models\Schedule;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_correct_stats(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        
        $kasir = User::factory()->create(['role' => UserRole::KASIR->value]);
        $poli = Poli::create(['name' => 'Poli Test']);
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $doctor = Doctor::create(['user_id' => $doctorUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '1']);
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
            'medicine_fee' => 0,
            'total_amount' => 50000,
            'payment_method' => 'cash',
            'paid_at' => now(),
            'cashier_id' => $kasir->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('50.000'); // Todays revenue
        $response->assertSee('1 Orang'); // Todays patients
    }

    public function test_dokter_dashboard_shows_correct_stats(): void
    {
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $poli = Poli::create(['name' => 'Poli Test']);
        $doctor = Doctor::create(['user_id' => $doctorUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '1']);
        
        $patientUser = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $patient = Patient::create(['user_id' => $patientUser->id, 'nik' => '1', 'date_of_birth' => '1990-01-01', 'phone_number' => '1', 'address' => '1']);
        $schedule = Schedule::create(['doctor_id' => $doctor->id, 'day_of_week' => 'Senin', 'start_time' => '08:00', 'end_time' => '12:00']);

        Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($doctorUser)->get(route('dokter.dashboard'));

        $response->assertStatus(200);
        $response->assertSee($patientUser->name);
    }

    public function test_kasir_dashboard_shows_correct_stats(): void
    {
        $kasir = User::factory()->create(['role' => UserRole::KASIR->value]);
        
        $poli = Poli::create(['name' => 'Poli Test']);
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $doctor = Doctor::create(['user_id' => $doctorUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '1']);
        $patientUser = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $patient = Patient::create(['user_id' => $patientUser->id, 'nik' => '1', 'date_of_birth' => '1990-01-01', 'phone_number' => '1', 'address' => '1']);
        $schedule = Schedule::create(['doctor_id' => $doctor->id, 'day_of_week' => 'Senin', 'start_time' => '08:00', 'end_time' => '12:00']);

        Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($kasir)->get(route('kasir.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('1 Orang'); // Matches 'Menunggu Konfirmasi' card
    }

    public function test_pasien_dashboard_shows_bookings(): void
    {
        $patientUser = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $patient = Patient::create(['user_id' => $patientUser->id, 'nik' => '1', 'date_of_birth' => '1990-01-01', 'phone_number' => '1', 'address' => '1']);
        
        $poli = Poli::create(['name' => 'Poli Test']);
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $doctor = Doctor::create(['user_id' => $doctorUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '1']);
        $schedule = Schedule::create(['doctor_id' => $doctor->id, 'day_of_week' => 'Senin', 'start_time' => '08:00', 'end_time' => '12:00']);

        Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($patientUser)->get(route('pasien.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Poli Test');
    }

    public function test_pasien_can_view_own_medical_record(): void
    {
        $patientUser = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $patient = Patient::create(['user_id' => $patientUser->id, 'nik' => '1', 'date_of_birth' => '1990-01-01', 'phone_number' => '1', 'address' => '1']);
        
        $poli = Poli::create(['name' => 'Poli Test']);
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $doctor = Doctor::create(['user_id' => $doctorUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '1']);
        $schedule = Schedule::create(['doctor_id' => $doctor->id, 'day_of_week' => 'Senin', 'start_time' => '08:00', 'end_time' => '12:00']);

        $booking = Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'completed'
        ]);

        MedicalRecord::create([
            'booking_id' => $booking->id,
            'complaint' => 'Pusing',
            'diagnosis' => 'Vertigo',
            'prescription' => 'Obat',
        ]);

        $response = $this->actingAs($patientUser)->get(route('pasien.riwayat.show', $booking));

        $response->assertStatus(200);
        $response->assertSee('Vertigo');
    }

    public function test_pasien_cannot_view_others_medical_record(): void
    {
        $patientUser1 = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $patient1 = Patient::create(['user_id' => $patientUser1->id, 'nik' => '1', 'date_of_birth' => '1990-01-01', 'phone_number' => '1', 'address' => '1']);
        
        $patientUser2 = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $patient2 = Patient::create(['user_id' => $patientUser2->id, 'nik' => '2', 'date_of_birth' => '1990-01-01', 'phone_number' => '2', 'address' => '2']);

        $poli = Poli::create(['name' => 'Poli Test']);
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $doctor = Doctor::create(['user_id' => $doctorUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '1']);
        $schedule = Schedule::create(['doctor_id' => $doctor->id, 'day_of_week' => 'Senin', 'start_time' => '08:00', 'end_time' => '12:00']);

        $booking = Booking::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'completed'
        ]);

        MedicalRecord::create([
            'booking_id' => $booking->id,
            'complaint' => 'Pusing',
            'diagnosis' => 'Vertigo',
            'prescription' => 'Obat',
        ]);

        $response = $this->actingAs($patientUser2)->get(route('pasien.riwayat.show', $booking));

        $response->assertStatus(403);
    }
}
