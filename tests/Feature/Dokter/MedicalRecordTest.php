<?php

namespace Tests\Feature\Dokter;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Schedule;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalRecordTest extends TestCase
{
    use RefreshDatabase;

    protected $doctorUser;
    protected $doctor;
    protected $booking;
    protected $medicine;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $poli = Poli::create(['name' => 'Poli Test']);
        $this->doctor = Doctor::create([
            'user_id' => $this->doctorUser->id,
            'poli_id' => $poli->id,
            'specialization' => 'Test',
            'phone_number' => '08123',
        ]);

        $patientUser = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'nik' => '1234567890123456',
            'date_of_birth' => '1990-01-01',
            'phone_number' => '0812345678',
            'address' => 'Test',
        ]);

        $schedule = Schedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => true,
        ]);

        $this->booking = Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $this->doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'pending',
        ]);

        $this->medicine = Medicine::create([
            'name' => 'Paracetamol',
            'code' => 'PCT01',
            'stock' => 100,
            'unit' => 'tablet',
            'price' => 5000,
        ]);
    }

    public function test_doctor_can_view_create_medical_record_form(): void
    {
        $response = $this->actingAs($this->doctorUser)->get(route('dokter.medical-record.create', $this->booking));
        $response->assertStatus(200);
        $response->assertSee('Paracetamol');
    }

    public function test_doctor_can_store_medical_record(): void
    {
        $data = [
            'complaint' => 'Sakit kepala',
            'diagnosis' => 'Migrain',
            'prescription' => 'Istirahat cukup',
            'medicines' => [
                [
                    'id' => $this->medicine->id,
                    'quantity' => 2,
                    'instructions' => '2x sehari',
                ]
            ],
        ];

        $response = $this->actingAs($this->doctorUser)->post(route('dokter.medical-record.store', $this->booking), $data);

        $response->assertRedirect(route('dokter.dashboard'));
        $this->assertDatabaseHas('medical_records', [
            'booking_id' => $this->booking->id,
            'diagnosis' => 'Migrain',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'completed',
        ]);

        $this->assertEquals(98, $this->medicine->fresh()->stock);
    }

    public function test_doctor_cannot_fill_medical_record_for_other_doctors_patient(): void
    {
        $otherDoctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $poli = Poli::create(['name' => 'Other Poli']);
        Doctor::create([
            'user_id' => $otherDoctorUser->id,
            'poli_id' => $poli->id,
            'specialization' => 'Other',
            'phone_number' => '08123',
        ]);

        $response = $this->actingAs($otherDoctorUser)->get(route('dokter.medical-record.create', $this->booking));
        $response->assertStatus(403);
    }
}
