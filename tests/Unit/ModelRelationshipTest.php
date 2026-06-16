<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\User;
use App\Models\MedicalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relationships(): void
    {
        $user = User::factory()->create();
        
        $patient = Patient::create([
            'user_id' => $user->id,
            'nik' => '123',
            'date_of_birth' => '1990-01-01',
            'phone_number' => '123',
            'address' => 'Test',
        ]);

        $this->assertInstanceOf(Patient::class, $user->fresh()->patient);
        $this->assertEquals($user->id, $patient->user->id);
    }

    public function test_doctor_poli_relationship(): void
    {
        $poli = Poli::create(['name' => 'Poli Gigi']);
        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'poli_id' => $poli->id,
            'specialization' => 'Gigi',
            'phone_number' => '123',
        ]);

        $this->assertEquals($poli->id, $doctor->poli->id);
        $this->assertTrue($poli->doctors->contains($doctor));
    }

    public function test_booking_medical_record_relationship(): void
    {
        $poli = Poli::create(['name' => 'Test']);
        $docUser = User::factory()->create();
        $doctor = Doctor::create(['user_id' => $docUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '1']);
        $patientUser = User::factory()->create();
        $patient = Patient::create(['user_id' => $patientUser->id, 'nik' => '1', 'date_of_birth' => '1990-01-01', 'phone_number' => '1', 'address' => '1']);
        
        $schedule = \App\Models\Schedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '12:00',
        ]);

        $booking = Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
        ]);

        $mr = MedicalRecord::create([
            'booking_id' => $booking->id,
            'complaint' => 'Test',
            'diagnosis' => 'Test',
            'prescription' => 'Test',
        ]);

        $this->assertEquals($mr->id, $booking->fresh()->medicalRecord->id);
        $this->assertEquals($booking->id, $mr->booking->id);
    }
}
