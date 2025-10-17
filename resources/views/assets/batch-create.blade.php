<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Aset Massal (Batch)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('assets.batchStore') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium">Nama Barang</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                </div>

                                {{-- Input Jumlah (Quantity) --}}
                                <div class="mb-4">
                                    <label for="quantity" class="block text-sm font-medium">Jumlah (Quantity)</label>
                                    <input type="number" name="quantity" id="quantity"
                                        value="{{ old('quantity', 1) }}"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required min="1">
                                </div>

                                {{-- Input Nomor Urut Mulai --}}
                                <div class="mb-4">
                                    <label for="start_sequence_number" class="block text-sm font-medium">Nomor Urut
                                        Mulai (4 Digit)</label>
                                    <input type="text" name="start_sequence_number" id="start_sequence_number"
                                        value="{{ old('start_sequence_number') }}"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required maxlength="4"
                                        placeholder="0001">
                                </div>

                                <div class="mb-4">
                                    <label for="purchase_year" class="block text-sm font-medium">Tahun Pembelian
                                        (YYYY)</label>
                                    <input type="number" name="purchase_year" id="purchase_year"
                                        value="{{ old('purchase_year', date('Y')) }}"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                </div>

                                <div class="mb-4">
                                    <label for="institution_id" class="block text-sm font-medium">Lembaga</label>
                                    <select name="institution_id" id="institution_id"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        <option value="">Pilih Lembaga</option>
                                        @foreach ($institutions as $item)
                                            <option value="{{ $item->id }}" @selected(old('institution_id') == $item->id)>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="category_id" class="block text-sm font-medium">Kategori Barang</label>
                                    <select name="category_id" id="category_id"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $item)
                                            <option value="{{ $item->id }}" @selected(old('category_id') == $item->id)>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="building_id" class="block text-sm font-medium">Gedung</label>
                                    <select name="building_id" id="building_id"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        <option value="">Pilih Gedung</option>
                                        @foreach ($buildings as $item)
                                            <option value="{{ $item->id }}" @selected(old('building_id') == $item->id)>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div>
                                <div class="mb-4">
                                    <label for="room_id" class="block text-sm font-medium">Ruangan</label>
                                    <select name="room_id" id="room_id"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        <option value="">Pilih Ruangan</option>
                                        @foreach ($rooms as $item)
                                            <option value="{{ $item->id }}" @selected(old('room_id') == $item->id)>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="faculty_id" class="block text-sm font-medium">Fakultas /
                                        Direktorat</label>
                                    <select name="faculty_id" id="faculty_id"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        <option value="">Pilih Fakultas/Direktorat</option>
                                        @foreach ($faculties as $item)
                                            <option value="{{ $item->id }}" @selected(old('faculty_id') == $item->id)>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="department_id" class="block text-sm font-medium">Prodi / Unit</label>
                                    <select name="department_id" id="department_id"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        <option value="">Pilih Prodi/Unit</option>
                                        @foreach ($departments as $item)
                                            <option value="{{ $item->id }}" @selected(old('department_id') == $item->id)>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="person_in_charge_id" class="block text-sm font-medium">Penanggung
                                        Jawab</label>
                                    <select name="person_in_charge_id" id="person_in_charge_id"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        <option value="">Pilih Penanggung Jawab</option>
                                        @foreach ($personsInCharge as $item)
                                            <option value="{{ $item->id }}" @selected(old('person_in_charge_id') == $item->id)>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="asset_function_id" class="block text-sm font-medium">Fungsi
                                        Barang</label>
                                    <select name="asset_function_id" id="asset_function_id"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        <option value="">Pilih Fungsi Barang</option>
                                        @foreach ($assetFunctions as $item)
                                            <option value="{{ $item->id }}" @selected(old('asset_function_id') == $item->id)>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="funding_source_id" class="block text-sm font-medium">Jenis
                                        Pendanaan</label>
                                    <select name="funding_source_id" id="funding_source_id"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        <option value="">Pilih Jenis Pendanaan</option>
                                        @foreach ($fundingSources as $item)
                                            <option value="{{ $item->id }}" @selected(old('funding_source_id') == $item->id)>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('assets.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Batch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
