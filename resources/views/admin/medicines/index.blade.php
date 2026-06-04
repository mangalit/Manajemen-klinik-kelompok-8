<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200">
                {{ __('Inventaris Obat') }}
            </h2>
            <a href="{{ route('admin.medicines.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold text-sm rounded-lg shadow-md hover:from-emerald-600 hover:to-teal-700 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Obat
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Alert -->
                    @if (session('success'))
                        <div class="flex items-center p-4 mb-4 text-sm text-green-700 bg-green-100 border border-green-300 rounded-lg" role="alert">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-7v2h2v-2h-2zm0-4h2v2h-2V7z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-300">
                            <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-3">No</th>
                                    <th class="px-6 py-3">Kode</th>
                                    <th class="px-6 py-3">Nama Obat</th>
                                    <th class="px-6 py-3">Stok</th>
                                    <th class="px-6 py-3">Satuan</th>
                                    <th class="px-6 py-3">Harga</th>
                                    <th class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($medicines as $medicine)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-6 py-4">{{ ($medicines->currentPage() - 1) * $medicines->perPage() + $loop->iteration }}</td>
                                        <td class="px-6 py-4">{{ $medicine->code ?? '-' }}</td>
                                        <td class="px-6 py-4 font-semibold">{{ $medicine->name }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-bold rounded {{ $medicine->stock <= 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                                {{ $medicine->stock }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $medicine->unit }}</td>
                                        <td class="px-6 py-4 font-medium text-emerald-600">Rp {{ number_format($medicine->price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.medicines.edit', $medicine->id) }}" 
                                               class="inline-flex items-center px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-100 rounded hover:bg-indigo-200 transition">
                                                ✏️ Edit
                                            </a>
                                            <form action="{{ route('admin.medicines.destroy', $medicine->id) }}" method="POST" class="inline-block ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-red-600 bg-red-100 rounded hover:bg-red-200 transition">
                                                    🗑 Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                            Data obat tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $medicines->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
