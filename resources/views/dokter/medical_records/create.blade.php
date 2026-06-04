<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Input Rekam Medis Pasien
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Data Pasien</h3>
                        <div class="mt-2 text-sm text-gray-600">
                            <p><strong>Nama:</strong> {{ $booking->patient->user->name }}</p>
                            <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($booking->patient->date_of_birth)->isoFormat('D MMMM Y') }}</p>
                            <p><strong>No. Antrian:</strong> {{ $booking->queue_number }}</p>
                        </div>
                    </div>
                    
                    <!-- Alert Error -->
                    @if (session('error'))
                        <div class="flex items-center p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded-lg" role="alert">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <hr class="my-6">
                    
                    <form action="{{ route('dokter.medical-record.store', $booking->id) }}" method="POST">
                        @csrf
                        
                        <div class="mt-4">
                            <label for="complaint" class="block font-medium text-sm text-gray-700">Keluhan Pasien (Anamnesis)</label>
                            <textarea name="complaint" id="complaint" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>{{ old('complaint') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label for="diagnosis" class="block font-medium text-sm text-gray-700">Diagnosa Dokter</label>
                            <textarea name="diagnosis" id="diagnosis" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>{{ old('diagnosis') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label for="prescription" class="block font-medium text-sm text-gray-700">Resep Tambahan / Tindakan (Manual)</label>
                            <textarea name="prescription" id="prescription" rows="2" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('prescription') }}</textarea>
                        </div>

                        <!-- Inventaris Obat -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Resep Obat dari Inventaris</h3>
                            
                            <div id="medicine-list" class="space-y-4">
                                <!-- Row template (hidden) -->
                                <div id="medicine-row-template" class="hidden medicine-row flex gap-4 items-end">
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-500">Pilih Obat</label>
                                        <select name="medicines[INDEX][id]" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm" disabled>
                                            <option value="">-- Pilih Obat --</option>
                                            @foreach($medicines as $medicine)
                                                <option value="{{ $medicine->id }}">
                                                    {{ $medicine->name }} (Stok: {{ $medicine->stock }} {{ $medicine->unit }}) - Rp {{ number_format($medicine->price, 0, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-24">
                                        <label class="block text-xs font-medium text-gray-500">Jumlah</label>
                                        <input type="number" name="medicines[INDEX][quantity]" min="1" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm" disabled>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-500">Instruksi (Contoh: 3x1 hari)</label>
                                        <input type="text" name="medicines[INDEX][instructions]" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm" disabled>
                                    </div>
                                    <button type="button" class="remove-medicine-btn p-2 text-red-600 hover:bg-red-50 rounded-md">
                                        🗑
                                    </button>
                                </div>
                            </div>

                            <button type="button" id="add-medicine-btn" class="mt-4 inline-flex items-center px-3 py-2 border border-emerald-500 text-emerald-600 text-sm font-medium rounded-md hover:bg-emerald-50">
                                ➕ Tambah Obat
                            </button>
                        </div>

                        <div class="flex items-center justify-end mt-10 border-t pt-6">
                            <a href="{{ route('dokter.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-gray-800 to-gray-700 text-white rounded-lg font-bold text-sm uppercase shadow-lg hover:from-gray-900 transition-all">
                                Simpan dan Selesaikan Pemeriksaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const list = document.getElementById('medicine-list');
            const btnAdd = document.getElementById('add-medicine-btn');
            const template = document.getElementById('medicine-row-template');
            let index = 0;

            btnAdd.addEventListener('click', function () {
                const newRow = template.cloneNode(true);
                newRow.id = '';
                newRow.classList.remove('hidden');
                
                // Update names with current index and enable them
                const inputs = newRow.querySelectorAll('select, input');
                inputs.forEach(input => {
                    input.name = input.name.replace('INDEX', index);
                    input.disabled = false;
                    input.required = true;
                });

                newRow.querySelector('.remove-medicine-btn').addEventListener('click', function() {
                    newRow.remove();
                });

                list.appendChild(newRow);
                index++;
            });
        });
    </script>
</x-app-layout>