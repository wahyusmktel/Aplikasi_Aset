@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@csrf <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="title" :value="__('Judul Pekerjaan *')" />
        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $schedule->title ?? '')" required
            autofocus />
    </div>

    <div>
        <x-input-label for="asset_id" :value="__('Aset yang Dipelihara *')" />
        <select id="asset_id" name="asset_id"
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            required>
            <option value="">-- Pilih Aset --</option>
            @foreach ($assets as $asset)
                <option value="{{ $asset->id }}"
                    {{ old('asset_id', $schedule->asset_id ?? '') == $asset->id ? 'selected' : '' }}>
                    {{ $asset->name }} ({{ $asset->asset_code_ypt }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <x-input-label for="schedule_date" :value="__('Tanggal Dijadwalkan *')" />
        <x-text-input id="schedule_date" class="block mt-1 w-full" type="date" name="schedule_date" :value="old('schedule_date', $schedule->schedule_date ?? '')"
            required />
    </div>

    <div>
        <x-input-label for="maintenance_type" :value="__('Tipe Pemeliharaan *')" />
        <select id="maintenance_type" name="maintenance_type"
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            required>
            <option value="">-- Pilih Tipe --</option>
            <option value="preventive"
                {{ old('maintenance_type', $schedule->maintenance_type ?? '') == 'preventive' ? 'selected' : '' }}>
                Preventive (Pencegahan)</option>
            <option value="corrective"
                {{ old('maintenance_type', $schedule->maintenance_type ?? '') == 'corrective' ? 'selected' : '' }}>
                Corrective (Perbaikan)</option>
            <option value="inspection"
                {{ old('maintenance_type', $schedule->maintenance_type ?? '') == 'inspection' ? 'selected' : '' }}>
                Inspection (Inspeksi)</option>
        </select>
    </div>

    <div>
        <x-input-label for="assigned_to_user_id" :value="__('Ditugaskan Kepada (Teknisi)')" />
        <select id="assigned_to_user_id" name="assigned_to_user_id"
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
            <option value="">-- Belum Ditugaskan --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}"
                    {{ old('assigned_to_user_id', $schedule->assigned_to_user_id ?? '') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <x-input-label for="description" :value="__('Deskripsi Pekerjaan')" />
        <textarea id="description" name="description" rows="4"
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $schedule->description ?? '') }}</textarea>
    </div>
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('maintenance-schedules.index') }}"
        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
        Batal
    </a>
    <x-primary-button class="ms-4">
        {{ $submitButtonText ?? 'Simpan' }}
    </x-primary-button>
</div>
