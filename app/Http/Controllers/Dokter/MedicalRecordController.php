<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalRecordController extends Controller
{
    // Menampilkan form untuk membuat rekam medis
    public function create(Booking $booking)
    {
        // Pastikan dokter hanya bisa mengisi rekam medis untuk pasiennya sendiri
        if ($booking->doctor_id !== auth()->user()->doctor->id) {
            abort(403, 'AKSES DITOLAK');
        }

        $medicines = Medicine::where('stock', '>', 0)->get();

        return view('dokter.medical_records.create', compact('booking', 'medicines'));
    }

    // Menyimpan rekam medis baru
    public function store(Request $request, Booking $booking)
    {
        // Pastikan dokter hanya bisa mengisi rekam medis untuk pasiennya sendiri
        if ($booking->doctor_id !== auth()->user()->doctor->id) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'complaint' => 'required|string',
            'diagnosis' => 'required|string',
            'prescription' => 'nullable|string',
            'medicines' => 'nullable|array',
            'medicines.*.id' => 'required|exists:medicines,id',
            'medicines.*.quantity' => 'required|integer|min:1',
            'medicines.*.instructions' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Buat rekam medis
            $medicalRecord = $booking->medicalRecord()->create([
                'complaint' => $request->complaint,
                'diagnosis' => $request->diagnosis,
                'prescription' => $request->prescription ?? '-',
            ]);

            // Simpan detail obat jika ada
            if ($request->has('medicines')) {
                foreach ($request->medicines as $med) {
                    $medicine = Medicine::find($med['id']);
                    
                    // Cek stok (opsional tapi disarankan)
                    if ($medicine->stock < $med['quantity']) {
                        throw new \Exception("Stok obat {$medicine->name} tidak mencukupi.");
                    }

                    // Kurangi stok
                    $medicine->decrement('stock', $med['quantity']);

                    // Hubungkan ke medical record
                    $medicalRecord->medicines()->attach($medicine->id, [
                        'quantity' => $med['quantity'],
                        'instructions' => $med['instructions'],
                        'price_at_time' => $medicine->price,
                    ]);
                }
            }

            // Update status booking menjadi 'completed' (selesai)
            $booking->update(['status' => 'completed']);

            DB::commit();
            return redirect()->route('dokter.dashboard')
                             ->with('success', 'Rekam medis dan resep berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}