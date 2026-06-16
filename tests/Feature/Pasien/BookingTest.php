<?php

namespace Tests\Feature\Pasien;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Schedule;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $patient;
    protected $poli;
    protected $doctor;
    protected $schedule;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['role' => UserRole::PASIEN->value]);
        $this->patient = Patient::create([
            'user_id' => $this->user->id,
            'nik' => '1234567890123456',
            'date_of_birth' => '1990-01-01',
            'phone_number' => '0812345678',
            'address' => 'Test Address',
        ]);

        $this->poli = Poli::create(['name' => 'Poli Test']);
        
        $doctorUser = User::factory()->create(['role' => UserRole::DOKTER->value]);
        $this->doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'poli_id' => $this->poli->id,
            'specialization' => 'Test Specialization',
            'phone_number' => '08123',
        ]);

        $this->schedule = Schedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => true,
        ]);
    }

    public function test_pasien_can_view_poli_selection(): void
    {
        $response = $this->actingAs($this->user)->get(route('pasien.booking.step-one'));
        $response->assertStatus(200);
        $response->assertSee('Poli Test');
    }

    public function test_pasien_can_view_doctor_selection(): void
    {
        $response = $this->actingAs($this->user)->get(route('pasien.booking.step-two', $this->poli));
        $response->assertStatus(200);
        $response->assertSee($this->doctor->user->name);
    }

    public function test_pasien_can_store_booking(): void
    {
        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => now()->next('Monday')->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)->post(route('pasien.booking.store'), $bookingData);

        $response->assertRedirect(route('pasien.dashboard'));
        $this->assertDatabaseHas('bookings', [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'pending',
        ]);
    }

    public function test_pasien_cannot_book_twice_on_same_date(): void
    {
        $date = now()->next('Monday')->format('Y-m-d');
        
        Booking::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => $date,
            'queue_number' => 1,
            'status' => 'pending',
        ]);

        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => $date,
        ];

        $response = $this->actingAs($this->user)->post(route('pasien.booking.store'), $bookingData);

        $response->assertSessionHas('error');
        $this->assertEquals(1, Booking::where('patient_id', $this->patient->id)->where('booking_date', $date)->count());
    }
}
