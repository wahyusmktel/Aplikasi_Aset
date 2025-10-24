<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ringkasan Aset (Grouped)') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                        {{-- Pencarian teks --}}
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Cari nama/kode/desc..."
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900" />

                        {{-- Filter Kategori (Select2) --}}
                        <select name="category_id"
                            class="js-select2 w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            data-placeholder="Semua Kategori">
                            <option value="">Semua Kategori</option>
                            @foreach (\App\Models\Category::orderBy('name')->get() as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Filter Tahun (Select2) --}}
                        <select name="year"
                            class="js-select2 w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            data-placeholder="Semua Tahun">
                            <option value="">Semua Tahun</option>
                            @foreach ($years ?? [] as $y)
                                <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}
                                </option>
                            @endforeach
                        </select>

                        <button
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700">
                            Filter
                        </button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">#
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Kode
                                        Aset YPT</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama
                                        Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tahun
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">QTY
                                    </th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($groups as $i => $g)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                        <td class="px-4 py-3">{{ $groups->firstItem() + $i }}</td>
                                        <td class="px-4 py-3 font-mono text-sm">{{ $g->sample_code ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $g->name }}</td>
                                        <td class="px-4 py-3">{{ $g->yr }}</td>
                                        <td class="px-4 py-3">{{ $g->status_label }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ $g->qty }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('assets.summary.show', $g->group_key) }}"
                                                class="text-indigo-600 hover:underline">
                                                Lihat detail →
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada
                                            data.</td>
                                    </tr>
                                @endforelse
                            </tbody>


                        </table>
                    </div>

                    <div class="mt-4">{{ $groups->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.js-select2').select2({
            width: '100%',
            placeholder: 'Pilih...',
            allowClear: true
        });
    });
</script> --}}
