<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Aset Terpasang / Inventaris Pegawai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-end items-center mb-6">
                        <form action="{{ route('assignedAssets.index') }}" method="GET">
                            <input type="text" name="search" placeholder="Cari Aset / Pegawai..."
                                class="form-input rounded-md dark:bg-gray-700 w-64" value="{{ request('search') }}">
                            <button type="submit"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md ml-2">Cari</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="py-3 px-6">Nama Aset</th>
                                    <th class="py-3 px-6">Kode Aset</th>
                                    <th class="py-3 px-6">Pegawai Pengguna</th>
                                    <th class="py-3 px-6">Tgl Serah Terima</th>
                                    <th class="py-3 px-6">Kondisi Saat Serah Terima</th>
                                    <th class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assignedAssets as $assignment)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-4 px-6 font-semibold">{{ $assignment->asset->name ?? '-' }}</td>
                                        <td class="py-4 px-6 font-mono text-xs">
                                            {{ $assignment->asset->asset_code_ypt ?? '-' }}</td>
                                        <td class="py-4 px-6">{{ $assignment->employee->name ?? '-' }}</td>
                                        <td class="py-4 px-6">
                                            {{ \Carbon\Carbon::parse($assignment->assigned_date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="py-4 px-6">{{ $assignment->condition_on_assign }}</td>
                                        <td class="py-4 px-6">
                                            {{-- Tombol ini akan mengarah ke halaman detail aset, tempat form pengembalian berada --}}
                                            <a href="{{ route('assets.show', $assignment->asset_id) }}"
                                                class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                Proses Pengembalian
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Tidak ada aset yang sedang
                                            ditugaskan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $assignedAssets->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
