<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pemetaan Kategori Aset (AI)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div
                        class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 dark:bg-yellow-800/20 dark:text-yellow-300">
                        <p class="font-bold">Perhatian!</p>
                        <p class="text-sm">Halaman ini menampilkan 10 aset yang kategorinya 'Belum Ditentukan'. AI akan
                            merekomendasikan kategori yang paling sesuai.</p>
                    </div>

                    <form action="{{ route('asset-mapping.store') }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="py-3 px-6">Nama Aset</th>
                                        <th class="py-3 px-6">Rekomendasi AI</th>
                                        <th class="py-3 px-6">Pilih Kategori (Manual)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($assetsToMap as $asset)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="py-4 px-6 font-semibold">{{ $asset->name }}</td>
                                            <td class="py-4 px-6">
                                                @if ($asset->ai_suggestion_type == 'existing')
                                                    <span
                                                        class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100 text-xs">
                                                        {{ $asset->ai_suggestion }}
                                                    </span>
                                                @elseif($asset->ai_suggestion_type == 'new')
                                                    <span
                                                        class="px-2 py-1 font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full dark:bg-blue-700 dark:text-blue-100 text-xs"
                                                        title="Kategori ini belum ada di database">
                                                        {{ $asset->ai_suggestion }} (Baru)
                                                    </span>
                                                @else
                                                    <span
                                                        class="text-red-500 text-xs">{{ $asset->ai_suggestion }}</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6">
                                                <input type="hidden" name="asset_category[{{ $asset->id }}]"
                                                    value=""> {{-- Default value null --}}
                                                <select name="asset_category[{{ $asset->id }}]"
                                                    class="select2-mapping mt-1 block w-full text-sm">
                                                    <option value="">Pilih Kategori...</option>
                                                    @foreach ($allCategories as $category)
                                                        <option value="{{ $category->id }}" {{-- Coba pilih otomatis jika sugesti AI cocok --}}
                                                            @if ($asset->ai_suggestion_type == 'existing' && $asset->ai_suggestion == $category->name) selected @endif>
                                                            {{ $category->name }} ({{ $category->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">Luar biasa! Tidak ada aset yang
                                                perlu di-mapping.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($assetsToMap->count() > 0)
                            <div class="flex justify-end mt-6">
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Simpan Perubahan Kategori
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Inisialisasi Select2 untuk dropdown mapping
                $('.select2-mapping').select2({
                    theme: "classic",
                    width: '100%'
                });
            });
        </script>
    @endpush
</x-app-layout>
