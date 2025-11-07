<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Jadwal Pemeliharaan Massal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('maintenance-schedules.storeBulk') }}">
                        @csrf

                        @if ($errors->any())
                            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                                role="alert">
                                <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <h3 class="text-lg font-semibold mb-4">Detail Pekerjaan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="title" :value="__('Judul Pekerjaan *')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                                    :value="old('title')" required />
                            </div>

                            <div>
                                <x-input-label for="schedule_date" :value="__('Tanggal Dijadwalkan *')" />
                                <x-text-input id="schedule_date" class="block mt-1 w-full" type="date"
                                    name="schedule_date" :value="old('schedule_date')" required />
                            </div>

                            <div>
                                <x-input-label for="maintenance_type" :value="__('Tipe Pemeliharaan *')" />
                                <select id="maintenance_type" name="maintenance_type"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="preventive"
                                        {{ old('maintenance_type') == 'preventive' ? 'selected' : '' }}>Preventive
                                        (Pencegahan)</option>
                                    <option value="corrective"
                                        {{ old('maintenance_type') == 'corrective' ? 'selected' : '' }}>Corrective
                                        (Perbaikan)</option>
                                    <option value="inspection"
                                        {{ old('maintenance_type') == 'inspection' ? 'selected' : '' }}>Inspection
                                        (Inspeksi)</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="assigned_to_user_id" :value="__('Ditugaskan Kepada (Teknisi)')" />
                                <select id="assigned_to_user_id" name="assigned_to_user_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">-- Belum Ditugaskan --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('assigned_to_user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Deskripsi Pekerjaan')" />
                                <textarea id="description" name="description" rows="3"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <hr class="dark:border-gray-700 my-6">
                        <h3 class="text-lg font-semibold mb-4">Pilih Aset (Minimal 1)</h3>

                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg border dark:border-gray-700">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 w-12">
                                            <input type="checkbox" id="select-all-assets"
                                                class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                        </th>
                                        <th scope="col" class="py-3 px-6">Nama Aset</th>
                                        <th scope="col" class="py-3 px-6">Kode Aset</th>
                                        <th scope="col" class="py-3 px-6">Kategori</th>
                                        <th scope="col" class="py-3 px-6">Ruang / Gedung</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($assets as $asset)
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="py-4 px-4">
                                                <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}"
                                                    class="asset-checkbox rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                            </td>
                                            <td class="py-4 px-6 font-medium text-gray-900 dark:text-white">
                                                {{ $asset->name }}</td>
                                            <td class="py-4 px-6">{{ $asset->asset_code_ypt }}</td>
                                            <td class="py-4 px-6">{{ $asset->category->name ?? 'N/A' }}</td>
                                            <td class="py-4 px-6">
                                                {{ $asset->room->name ?? ($asset->building->name ?? 'N/A') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 px-6 text-center">Tidak ada data aset.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('maintenance-schedules.index') }}"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Batal
                            </a>
                            <x-primary-button class="ms-4">
                                Simpan Jadwal Massal
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('select-all-assets').addEventListener('click', function(event) {
                let checkboxes = document.querySelectorAll('.asset-checkbox');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = event.target.checked;
                });
            });
        </script>
    @endpush

</x-app-layout>
