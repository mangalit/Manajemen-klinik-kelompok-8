<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal untuk dokter tertentu.
     */
    public function index(Doctor $doctor)
    {
        // Ambil semua jadwal yang dimiliki oleh dokter ini
        $schedules = $doctor->schedules()->orderBy('day_of_week')->get();
        return view('admin.schedules.index', compact('doctor', 'schedules'));
    }

    /**
     * Menampilkan form untuk membuat jadwal baru untuk dokter tertentu.
     */
    public function create(Doctor $doctor)
    {
        return view('admin.schedules.create', compact('doctor'));
    }

    /**
     * Menyimpan jadwal baru ke database.
     */
    public function store(Request $request, Doctor $doctor)
    {
        $request->validate([
            'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Buat jadwal baru yang langsung terhubung dengan dokter ini
        $doctor->schedules()->create($request->all());

        return redirect()->route('admin.doctors.schedules.index', $doctor->id)
                         ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit jadwal.
     */
    public function edit(Doctor $doctor, Schedule $schedule)
    {
        return view('admin.schedules.edit', compact('doctor', 'schedule'));
    }

    /**
     * Memperbarui data jadwal di database.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'required|boolean',
        ]);

        $schedule->update($request->all());

        return redirect()->route('admin.doctors.schedules.index', $schedule->doctor_id)
                         ->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Menghapus jadwal dari database.
     */
    public function destroy(Schedule $schedule)
    {
        $doctorId = $schedule->doctor_id;
        $schedule->delete();

        return redirect()->route('admin.doctors.schedules.index', $doctorId)
                         ->with('success', 'Jadwal berhasil dihapus.');
    }
}