<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Proses Pembayaran Pasien
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Detail Kunjungan</h3>
                        <div class="mt-2 text-sm text-gray-600 space-y-1">
                            <p><strong>Nama Pasien:</strong> {{ $booking->patient->user->name }}</p>
                            <p><strong>Dokter:</strong> {{ $booking->doctor->user->name }}</p>
                            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->isoFormat('D MMMM Y') }}</p>
                        </div>
                    </div>

                    @if($booking->medicalRecord && $booking->medicalRecord->medicines->count() > 0)
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-bold text-gray-700 mb-2 uppercase">Detail Obat</h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            @foreach($booking->medicalRecord->medicines as $medicine)
                                <li class="flex justify-between border-b border-gray-200 pb-1">
                                    <span>{{ $medicine->name }} ({{ $medicine->pivot->quantity }} x {{ number_format($medicine->pivot->price_at_time, 0, ',', '.') }})</span>
                                    <span class="font-medium">Rp {{ number_format($medicine->pivot->quantity * $medicine->pivot->price_at_time, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                            <li class="flex justify-between pt-2 font-bold text-gray-800">
                                <span>Total Biaya Obat</span>
                                <span>Rp {{ number_format($medicineFee, 0, ',', '.') }}</span>
                            </li>
                        </ul>
                    </div>
                    @endif
                    
                    <hr class="my-4">
                    
                    <form action="{{ route('kasir.payment.store', $booking->id) }}" method="POST">
                        @csrf
                        
                        <div>
                            <label for="consultation_fee" class="block font-medium text-sm text-gray-700">Biaya Konsultasi (Rp)</label>
                            <input type="number" name="consultation_fee" id="consultation_fee" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('consultation_fee', 50000) }}" required>
                        </div>

                        <div class="mt-4">
                            <label for="medicine_fee" class="block font-medium text-sm text-gray-700">Biaya Obat/Tindakan (Rp)</label>
                            <input type="number" name="medicine_fee" id="medicine_fee" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('medicine_fee', $medicineFee) }}" required>
                        </div>
                        
                        <div class="mt-4">
                            <label for="payment_method" class="block font-medium text-sm text-gray-700">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                <option value="cash" selected>Cash</option>
                                <option value="debit">Debit</option>
                                <option value="credit_card">Kartu Kredit</option>
                                <option value="insurance">Asuransi</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('kasir.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md font-semibold text-xs uppercase">
                                Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>