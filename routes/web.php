<?php

use Illuminate\Support\Facades\Route;

// ======================================================================
// PENTING: BAGIAN IMPORT CONTROLLER DENGAN ALIAS
// ======================================================================
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardRedirectorController;
use App\Http\Controllers\Admin\PoliController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\DoctorScheduleController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\Pasien\BookingController;
use App\Http\Controllers\Dokter\DashboardController;
use App\Http\Controllers\Dokter\MedicalRecordController;
use App\Http\Controllers\Kasir\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Kasir\BookingConfirmationController;


// Di sini kita memberikan alias agar tidak terjadi konflik nama
use App\Http\Controllers\Pasien\DashboardController as PasienDashboardController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;



// Rute Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

// Rute yang akan mengarahkan user setelah login berdasarkan rolenya
Route::get('/dashboard', DashboardRedirectorController::class)
    ->middleware(['auth'])
    ->name('dashboard');

// Rute untuk Halaman Profil (bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// === GRUP RUTE UNTUK SETIAP ROLE ===

// Rute untuk Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // diubah untuk menunjuk ke controller new DashboardController
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('polis', PoliController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('medicines', MedicineController::class);
    Route::resource('doctors.schedules', DoctorScheduleController::class)->shallow();
    // RUTE BARU UNTUK LAPORAN
    Route::get('/reports/revenue', [ReportController::class, 'revenueReport'])->name('reports.revenue');
    Route::get('/reports/patients', [ReportController::class, 'patientReport'])->name('reports.patients');
});

// Rute untuk Dokter
Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->name('dokter.')->group(function () {
    // Rute dashboard utama dokter
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute untuk mengelola rekam medis
    Route::get('/bookings/{booking}/medical-record/create', [MedicalRecordController::class, 'create'])->name('medical-record.create');
    Route::post('/bookings/{booking}/medical-record', [MedicalRecordController::class, 'store'])->name('medical-record.store');
});

// Rute untuk Kasir
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    // Pastikan baris ini memiliki ->name('dashboard')
    Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('dashboard');
    Route::patch('/bookings/{booking}/confirm', BookingConfirmationController::class)->name('booking.confirm');
    Route::get('/bookings/{booking}/payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('/payments/{payment}/print', [PaymentController::class, 'show'])->name('payment.show');
});

// Rute untuk Pasien
Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->name('pasien.')->group(function () {
    // PENTING: Gunakan alias yang sudah kita buat
    Route::get('/dashboard', [PasienDashboardController::class, 'index'])->name('dashboard');
    Route::get('/booking/pilih-poli', [BookingController::class, 'stepOne'])->name('booking.step-one');
    Route::get('/booking/pilih-dokter/{poli}', [BookingController::class, 'stepTwo'])->name('booking.step-two');
    Route::get('/booking/pilih-jadwal/{doctor}', [BookingController::class, 'stepThree'])->name('booking.step-three');
    Route::post('/booking/simpan', [BookingController::class, 'store'])->name('booking.store');
    // PENTING: RUTE BARU UNTUK MELIHAT RIWAYAT
    Route::get('/riwayat/{booking}', [PasienDashboardController::class, 'showMedicalRecord'])->name('riwayat.show');
});


// Meng-include file rute autentikasi bawaan Breeze
require __DIR__.'/auth.php';

// Route sementara untuk menjalankan migrasi dan seeder di Railway
Route::get('/init-db', function () {
    set_time_limit(0);
    try {
        // Menjalankan php artisan migrate:fresh --seed --force secara otomatis
        Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true // Wajib digunakan di mode production
        ]);

        return 'Tebas tuntas! Database dan data dummy berhasil dimasukkan.';
    } catch (\Exception $e) {
        return 'Aduh gagal: ' . $e->getMessage();
    }
});
