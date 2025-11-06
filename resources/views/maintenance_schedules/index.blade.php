<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Jadwal Pemeliharaan Aset') }}
            </h2>
            <a href="{{ route('maintenance-schedules.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Buat Jadwal Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">Aset</th>
                                    <th scope="col" class="py-3 px-6">Judul</th>
                                    <th scope="col" class="py-3 px-6">Tgl. Jadwal</th>
                                    <th scope="col" class="py-3 px-6">Status</th>
                                    <th scope="col" class="py-3 px-6">Teknisi</th>
                                    <th scope="col" class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schedules as $schedule)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6 font-medium text-gray-900 dark:text-white">
                                            {{ $schedule->asset->name ?? 'Aset Dihapus' }}
                                        </td>
                                        <td class="py-4 px-6">{{ $schedule->title }}</td>
                                        <td class="py-4 px-6">
                                            {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</td>
                                        <td class="py-4 px-6">
                                            <span @class([
                                                'px-2 py-1 font-semibold leading-tight text-xs rounded-full',
                                                'text-blue-700 bg-blue-100 dark:bg-blue-700 dark:text-blue-100' =>
                                                    $schedule->status == 'scheduled',
                                                'text-yellow-700 bg-yellow-100 dark:bg-yellow-700 dark:text-yellow-100' =>
                                                    $schedule->status == 'in_progress',
                                                'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' =>
                                                    $schedule->status == 'completed',
                                                'text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-100' =>
                                                    $schedule->status == 'cancelled',
                                            ])>
                                                {{ ucfirst($schedule->status) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">{{ $schedule->assignedTo->name ?? 'Belum Ditugaskan' }}
                                        </td>
                                        <td class="py-4 px-6 flex gap-2">
                                            <a href="{{ route('maintenance-schedules.show', $schedule->id) }}"
                                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-2 rounded text-xs">Detail</a>
                                            <a href="{{ route('maintenance-schedules.edit', $schedule->id) }}"
                                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded text-xs">Edit</a>

                                            <form action="{{ route('maintenance-schedules.destroy', $schedule->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Anda yakin ingin menghapus jadwal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 px-6 text-center">Belum ada jadwal pemeliharaan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $schedules->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
