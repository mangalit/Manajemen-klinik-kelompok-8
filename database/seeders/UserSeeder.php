<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Patient; // <-- Tambahkan import model Patient
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat Admin
        User::create([
            'name' => 'Admin Klinik',
            'email' => 'admin@klinik.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        // Membuat Kasir
        User::create([
            'name' => 'Kasir Klinik',
            'email' => 'kasir@klinik.com',
            'password' => Hash::make('password'),
            'role' => UserRole::KASIR,
        ]);

        // Membuat Dokter (contoh)
        User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'dr.budi@klinik.com',
            'password' => Hash::make('password'),
            'role' => UserRole::DOKTER,
        ]);

        // ===============================================================
        // BAGIAN YANG DIPERBARUI
        // ===============================================================

        // 1. Buat dulu user pasien
        //loler@pasien.com
        //password
        $pasienUser = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'andi@pasien.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PASIEN,
        ]);

        // 2. Buat data pasien yang terhubung dengan user di atas
        Patient::create([
            'user_id' => $pasienUser->id, // <-- Hubungkan dengan ID user pasien
            'nik' => '3270010020030004',   // Contoh NIK
            'date_of_birth' => '2000-05-15', // Contoh tanggal lahir
            'phone_number' => '081234567890', // Contoh nomor HP
            'address' => 'Jl. Merdeka No. 10, Jakarta', // Contoh alamat
        ]);
    }
}
