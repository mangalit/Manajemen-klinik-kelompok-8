<x-app-layout title="Dashboard Admin">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 tracking-wide">
                {{ __('Dashboard Admin') }}
            </h2>
            <span class="text-sm text-gray-500">Selamat datang kembali, {{ Auth::user()->name }}</span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-gradient-to-r from-emerald-400 to-teal-500 text-white shadow-lg rounded-2xl p-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium">Pendapatan Hari Ini</h3>
                        <p class="mt-2 text-3xl font-bold">
                            Rp {{ number_format($todaysRevenue, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3v7h6v-7c0-1.657-1.343-3-3-3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 8h12M6 16h12" />
                        </svg>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-gradient-to-r from-sky-400 to-blue-500 text-white shadow-lg rounded-2xl p-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium">Pasien Hari Ini</h3>
                        <p class="mt-2 text-3xl font-bold">
                            {{ $todaysPatients }} Orang
                        </p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 11a4 4 0 10-8 0 4 4 0 008 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-gradient-to-r from-amber-400 to-orange-500 text-white shadow-lg rounded-2xl p-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium">Booking Pending</h3>
                        <p class="mt-2 text-3xl font-bold">
                            {{ $pendingBookings }} Booking
                        </p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tabel Booking -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Booking Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-300">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3">Pasien</th>
                                <th class="px-6 py-3">Dokter</th>
                                <th class="px-6 py-3">Tgl Booking</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($recentBookings as $booking)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4 font-medium">{{ $booking->patient->user->name }}</td>
                                    <td class="px-6 py-4">{{ $booking->doctor->user->name }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($booking->booking_date)->isoFormat('D MMM Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                            @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($booking->status == 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($booking->status == 'completed') bg-green-100 text-green-800
                                            @elseif($booking->status == 'cancelled') bg-gray-200 text-gray-800
                                            @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada data booking.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
