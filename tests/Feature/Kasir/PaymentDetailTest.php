<?php

namespace Tests\Feature\Kasir;

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

class PaymentDetailTest extends TestCase
{
    use RefreshDatabase;

    protected $kasirUser;
    protected $payment;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kasirUser = User::factory()->create(['role' => UserRole::KASIR->value]);
        
        $poli = Poli::create(['name' => 'Poli Test']);
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value, 'name' => 'Dr. Tony']);
        $doctor = Doctor::create(['user_id' => $doctorUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '1']);

        $patientUser = User::factory()->create(['role' => UserRole::PASIEN->value, 'name' => 'Baby Smith']);
        $patient = Patient::create(['user_id' => $patientUser->id, 'nik' => '1', 'date_of_birth' => '1990-01-01', 'phone_number' => '1', 'address' => 'Test']);

        $schedule = Schedule::create(['doctor_id' => $doctor->id, 'day_of_week' => 'Senin', 'start_time' => '08:00', 'end_time' => '12:00']);

        $booking = Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'completed',
        ]);

        $this->payment = Payment::create([
            'booking_id' => $booking->id,
            'consultation_fee' => 50000,
            'medicine_fee' => 20000,
            'total_amount' => 70000,
            'payment_method' => 'cash',
            'paid_at' => now(),
            'cashier_id' => $this->kasirUser->id,
        ]);
    }

    public function test_kasir_can_view_payment_print_view(): void
    {
        $response = $this->actingAs($this->kasirUser)->get(route('kasir.payment.show', $this->payment));

        $response->assertStatus(200);
        $response->assertSee('Baby Smith');
        $response->assertSee('Dr. Tony');
        $response->assertSee('70.000');
    }

    public function test_kasir_cannot_store_duplicate_payment_for_same_booking(): void
    {
        $booking = $this->payment->booking;
        
        $response = $this->actingAs($this->kasirUser)->get(route('kasir.payment.create', $booking));

        $response->assertRedirect(route('kasir.dashboard'));
        $response->assertSessionHas('error', 'Pasien ini sudah melakukan pembayaran.');
    }
}
