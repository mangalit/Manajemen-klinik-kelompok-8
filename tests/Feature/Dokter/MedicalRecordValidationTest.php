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

class MedicalRecordValidationTest extends TestCase
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
            'nik' => '1',
            'date_of_birth' => '1990-01-01',
            'phone_number' => '1',
            'address' => 'Test',
        ]);

        $schedule = Schedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '12:00',
        ]);

        $this->booking = Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $this->doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'confirmed',
        ]);

        $this->medicine = Medicine::create([
            'name' => 'Amoxicillin',
            'code' => 'AMX01',
            'stock' => 5,
            'unit' => 'tablet',
            'price' => 2000,
        ]);
    }

    public function test_doctor_cannot_store_medical_record_if_stock_insufficient(): void
    {
        $data = [
            'complaint' => 'Infeksi',
            'diagnosis' => 'Sakit',
            'medicines' => [
                [
                    'id' => $this->medicine->id,
                    'quantity' => 10, // More than stock (5)
                    'instructions' => '3x1',
                ]
            ],
        ];

        $response = $this->actingAs($this->doctorUser)->post(route('dokter.medical-record.store', $this->booking), $data);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('Stok obat Amoxicillin tidak mencukupi', session('error'));
        $this->assertEquals(5, $this->medicine->fresh()->stock); // Stock remains unchanged
    }

    public function test_doctor_only_sees_medicines_with_stock_greater_than_zero(): void
    {
        $outOfStockMedicine = Medicine::create([
            'name' => 'Habis',
            'code' => 'HBS01',
            'stock' => 0,
            'unit' => 'tablet',
            'price' => 1000,
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('dokter.medical-record.create', $this->booking));

        $response->assertStatus(200);
        $response->assertSee('Amoxicillin');
        $response->assertDontSee('Habis');
    }
}
