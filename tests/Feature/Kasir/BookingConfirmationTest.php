<?php

namespace Tests\Feature\Kasir;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Schedule;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingConfirmationTest extends TestCase
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
        $doctor = Doctor::create(['user_id' => $doctorUser->id, 'poli_id' => $poli->id, 'specialization' => 'Test', 'phone_number' => '08123']);

        $patientUser = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $patient = Patient::create(['user_id' => $patientUser->id, 'nik' => '1', 'date_of_birth' => '1990-01-01', 'phone_number' => '1', 'address' => '1']);

        $schedule = Schedule::create(['doctor_id' => $doctor->id, 'day_of_week' => 'Senin', 'start_time' => '08:00', 'end_time' => '12:00']);

        $this->booking = Booking::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'queue_number' => 1,
            'status' => 'pending',
        ]);
    }

    public function test_kasir_can_confirm_booking_for_today(): void
    {
        $response = $this->actingAs($this->kasirUser)->patch(route('kasir.booking.confirm', $this->booking));

        $response->assertRedirect(route('kasir.dashboard'));
        $this->assertEquals('confirmed', $this->booking->fresh()->status);
    }

    public function test_kasir_cannot_confirm_booking_for_different_date(): void
    {
        $this->booking->update(['booking_date' => date('Y-m-d', strtotime('+1 day'))]);

        $response = $this->actingAs($this->kasirUser)->patch(route('kasir.booking.confirm', $this->booking));

        $response->assertSessionHas('error');
        $this->assertEquals('pending', $this->booking->fresh()->status);
    }
}
