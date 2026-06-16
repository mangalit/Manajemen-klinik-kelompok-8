<?php

namespace Tests\Feature\Kasir;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Schedule;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $kasirUser;
    protected $booking;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kasirUser = User::factory()->create(['role' => UserRole::KASIR->value]);
        
        $poli = Poli::create(['name' => 'Poli Test']);
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
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
            'doctor_id' => $doctor->id,
            'day_of_week' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => true,
        ]);

        $this->booking = Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'completed',
        ]);

        MedicalRecord::create([
            'booking_id' => $this->booking->id,
            'complaint' => 'Test',
            'diagnosis' => 'Test',
            'prescription' => 'Test',
        ]);
    }

    public function test_kasir_can_view_payment_form(): void
    {
        $response = $this->actingAs($this->kasirUser)->get(route('kasir.payment.create', $this->booking));
        $response->assertStatus(200);
    }

    public function test_kasir_can_store_payment(): void
    {
        $data = [
            'consultation_fee' => 50000,
            'medicine_fee' => 20000,
            'payment_method' => 'Cash',
        ];

        $response = $this->actingAs($this->kasirUser)->post(route('kasir.payment.store', $this->booking), $data);

        $response->assertRedirect(route('kasir.dashboard'));
        $this->assertDatabaseHas('payments', [
            'booking_id' => $this->booking->id,
            'total_amount' => 70000,
            'cashier_id' => $this->kasirUser->id,
        ]);
    }
}
