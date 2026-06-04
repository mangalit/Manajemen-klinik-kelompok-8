<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // Menampilkan form untuk memproses pembayaran
    public function create(Booking $booking)
    {
        // Cek apakah booking sudah punya data pembayaran
        if ($booking->payment) {
            return redirect()->route('kasir.dashboard')->with('error', 'Pasien ini sudah melakukan pembayaran.');
        }

        // Hitung biaya obat otomatis dari rekam medis
        $medicineFee = 0;
        if ($booking->medicalRecord) {
            foreach ($booking->medicalRecord->medicines as $medicine) {
                $medicineFee += $medicine->pivot->quantity * $medicine->pivot->price_at_time;
            }
        }

        return view('kasir.payments.create', compact('booking', 'medicineFee'));
    }

    // Menyimpan data pembayaran
    public function store(Request $request, Booking $booking)
    {
        $request->validate([
            'consultation_fee' => 'required|numeric|min:0',
            'medicine_fee' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $total = $request->consultation_fee + $request->medicine_fee;

        Payment::create([
            'booking_id' => $booking->id,
            'consultation_fee' => $request->consultation_fee,
            'medicine_fee' => $request->medicine_fee,
            'total_amount' => $total,
            'payment_method' => $request->payment_method,
            'paid_at' => now(),
            'cashier_id' => Auth::id(), // ID user kasir yang login
        ]);

        return redirect()->route('kasir.dashboard')
                         ->with('success', 'Pembayaran berhasil disimpan.');
    }
     public function show(Payment $payment)
    {
        // Eager load relasi yang dibutuhkan agar tidak ada query N+1 di view
        $payment->load(['booking.patient.user', 'booking.doctor.user', 'cashier']);

        // Mengembalikan view khusus untuk kwitansi
        return view('kasir.payments.show', compact('payment'));
    }
}