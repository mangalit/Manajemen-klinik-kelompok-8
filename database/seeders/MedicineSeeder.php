<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            [
                'name' => 'Paracetamol 500mg',
                'code' => 'PCT500',
                'description' => 'Meredakan demam dan nyeri ringan.',
                'stock' => 100,
                'unit' => 'Tablet',
                'price' => 500,
            ],
            [
                'name' => 'Amoxicillin 500mg',
                'code' => 'AMX500',
                'description' => 'Antibiotik untuk infeksi bakteri.',
                'stock' => 50,
                'unit' => 'Tablet',
                'price' => 2000,
            ],
            [
                'name' => 'OBH Combi Plus 60ml',
                'code' => 'OBH60',
                'description' => 'Sirup obat batuk dan flu.',
                'stock' => 20,
                'unit' => 'Botol',
                'price' => 15000,
            ],
            [
                'name' => 'Amlodipine 5mg',
                'code' => 'AML5',
                'description' => 'Obat penurun tekanan darah tinggi.',
                'stock' => 80,
                'unit' => 'Tablet',
                'price' => 1000,
            ],
            [
                'name' => 'Antasida Doen',
                'code' => 'ANTSD',
                'description' => 'Obat untuk meredakan gejala sakit maag.',
                'stock' => 150,
                'unit' => 'Tablet',
                'price' => 200,
            ],
            [
                'name' => 'Cetirizine 10mg',
                'code' => 'CTZ10',
                'description' => 'Antihistamin untuk meredakan gejala alergi.',
                'stock' => 60,
                'unit' => 'Tablet',
                'price' => 1200,
            ],
            [
                'name' => 'Betadine 15ml',
                'code' => 'BTD15',
                'description' => 'Antiseptik untuk luka luar.',
                'stock' => 45,
                'unit' => 'Botol',
                'price' => 12000,
            ],
            [
                'name' => 'Vitamin C 500mg',
                'code' => 'VITC500',
                'description' => 'Suplemen untuk menjaga daya tahan tubuh.',
                'stock' => 200,
                'unit' => 'Tablet',
                'price' => 800,
            ],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}
