<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Pekerjaan: ') }} {{ $maintenanceSchedule->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="md:col-span-2">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                        <h3 class="text-lg font-bold">Detail Pekerjaan</h3>

                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Aset</dt>
                            <dd class="text-lg">{{ $maintenanceSchedule->asset->name }}
                                ({{ $maintenanceSchedule->asset->asset_code_ypt }})</dd>
                        </div>

                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Jadwal</dt>
                            <dd class="text-lg">
                                {{ \Carbon\Carbon::parse($maintenanceSchedule->schedule_date)->format('l, d F Y') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Tipe</dt>
                            <dd class="text-lg">{{ ucfirst($maintenanceSchedule->maintenance_type) }}</dd>
                        </div>

                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Deskripsi</dt>
                            <dd class="prose dark:prose-invert max-w-none">
                                {!! nl2br(e($maintenanceSchedule->description)) !!}
                            </dd>
                        </div>

                        <hr class="dark:border-gray-700">

                        <h3 class="text-lg font-bold">Riwayat Pengerjaan</h3>

                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Teknisi</dt>
                            <dd class="text-lg">{{ $maintenanceSchedule->assignedTo->name ?? 'Belum Ditugaskan' }}</dd>
                        </div>

                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Tanggal Selesai</dt>
                            <dd class="text-lg">
                                {{ $maintenanceSchedule->completed_at ? \Carbon\Carbon::parse($maintenanceSchedule->completed_at)->format('l, d F Y H:i') : 'Belum Selesai' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Catatan Teknisi</dt>
                            <dd class="prose dark:prose-invert max-w-none">
                                {!! nl2br(e($maintenanceSchedule->notes)) ?? '<em>Tidak ada catatan.</em>' !!}
                            </dd>
                        </div>

                    </div>
                </div>
            </div>

            <div class="md:col-span-1">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">Update Status & Catatan</h3>

                        <form method="POST"
                            action="{{ route('maintenance-schedules.update', $maintenanceSchedule->id) }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="title" value="{{ $maintenanceSchedule->title }}">
                            <input type="hidden" name="asset_id" value="{{ $maintenanceSchedule->asset_id }}">
                            <input type="hidden" name="schedule_date"
                                value="{{ $maintenanceSchedule->schedule_date }}">
                            <input type="hidden" name="maintenance_type"
                                value="{{ $maintenanceSchedule->maintenance_type }}">

                            <div class="mb-4">
                                <x-input-label for="status" :value="__('Ubah Status *')" />
                                <select id="status" name="status"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="scheduled"
                                        {{ old('status', $maintenanceSchedule->status) == 'scheduled' ? 'selected' : '' }}>
                                        Scheduled (Dijadwalkan)</option>
                                    <option value="in_progress"
                                        {{ old('status', $maintenanceSchedule->status) == 'in_progress' ? 'selected' : '' }}>
                                        In Progress (Dikerjakan)</option>
                                    <option value="completed"
                                        {{ old('status', $maintenanceSchedule->status) == 'completed' ? 'selected' : '' }}>
                                        Completed (Selesai)</option>
                                    <option value="cancelled"
                                        {{ old('status', $maintenanceSchedule->status) == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled (Dibatalkan)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="notes" :value="__('Catatan Pengerjaan')" />
                                <textarea id="notes" name="notes" rows="6"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('notes', $maintenanceSchedule->notes ?? '') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Isi catatan sebelum
                                    menyelesaikan pekerjaan.</p>
                            </div>

                            <x-primary-button class="w-full justify-center">
                                Update Pekerjaan
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
