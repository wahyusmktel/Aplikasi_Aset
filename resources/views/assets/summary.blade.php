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

                    {{-- Bar Preset --}}
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        {{-- Load Preset --}}
                        <form method="GET" action="{{ route('assets.summary') }}" class="flex items-center gap-2">
                            <select name="preset_id" class="select2 min-w-64">
                                <option value="">— Pilih Preset —</option>
                                @foreach ($presets ?? [] as $p)
                                    <option value="{{ $p->id }}" @selected(request('preset_id') == $p->id)>{{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button
                                class="px-3 py-1.5 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-700">Terapkan</button>
                        </form>

                        {{-- Save Preset (ambil dari filter aktif) --}}
                        <form method="POST" action="{{ route('assets.summary.preset.save') }}"
                            class="flex items-center gap-2">
                            @csrf
                            {{-- kirim filter aktif sebagai input form (disamakan dengan name) --}}
                            <input type="hidden" name="q" value="{{ request('q') }}">
                            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                            <input type="hidden" name="year" value="{{ request('year') }}">
                            @foreach ((array) request('status', []) as $st)
                                <input type="hidden" name="status[]" value="{{ $st }}">
                            @endforeach

                            <input type="text" name="name"
                                class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                placeholder="Nama preset (mis. Buku 2024 Aktif)" required>
                            <button
                                class="px-3 py-1.5 rounded-md bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                                Simpan Preset
                            </button>
                        </form>

                        {{-- Delete Preset --}}
                        <form id="delPresetForm" method="POST" action=""
                            onsubmit="return confirm('Hapus preset ini?')">
                            @csrf
                            @method('DELETE')
                            <button id="delPresetBtn"
                                class="px-3 py-1.5 rounded-md text-sm text-white transition
             bg-rose-400 cursor-not-allowed"
                                disabled>
                                Hapus Preset
                            </button>
                        </form>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const select = document.querySelector('select[name="preset_id"]');
                                const form = document.getElementById('delPresetForm');
                                const btn = document.getElementById('delPresetBtn');

                                if (!select) return;

                                const update = () => {
                                    const val = select.value;
                                    if (val) {
                                        form.action = "{{ route('assets.summary.preset.delete', ['preset' => '___ID___']) }}"
                                            .replace('___ID___', val);
                                        btn.disabled = false;
                                        btn.classList.remove('cursor-not-allowed', 'bg-rose-400');
                                        btn.classList.add('bg-rose-600', 'hover:bg-rose-700');
                                    } else {
                                        form.action = '';
                                        btn.disabled = true;
                                        btn.classList.add('cursor-not-allowed', 'bg-rose-400');
                                        btn.classList.remove('bg-rose-600', 'hover:bg-rose-700');
                                    }
                                };

                                update();
                                select.addEventListener('change', update);
                            });
                        </script>
                    </div>

                    <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
                        {{-- Cari --}}
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Cari nama/kode/desc..."
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900" />

                        {{-- Kategori --}}
                        <select name="category_id"
                            class="select2 w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            data-placeholder="Semua Kategori">
                            <option value="">Semua Kategori</option>
                            @foreach (\App\Models\Category::orderBy('name')->get() as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Tahun --}}
                        <select name="year"
                            class="select2 w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            data-placeholder="Semua Tahun">
                            <option value="">Semua Tahun</option>
                            @foreach ($years ?? [] as $y)
                                <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Status (multi) --}}
                        <select name="status[]"
                            class="select2 w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            multiple data-placeholder="Semua Status">
                            @foreach ($allStatuses ?? [] as $st)
                                <option value="{{ $st }}" @selected(collect(request('status', []))->contains($st))>{{ $st }}
                                </option>
                            @endforeach
                        </select>

                        <button
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700">
                            Filter
                        </button>
                    </form>

                    <div class="flex items-center gap-2 mb-3">
                        <a href="{{ route('assets.summary.export-excel', request()->query()) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md shadow-sm transition">
                            ⬇️ Export Excel
                        </a>
                        <a href="{{ route('assets.summary.export-pdf', request()->query()) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-md shadow-sm transition">
                            ⬇️ Export PDF
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">#
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Rentang Kode Aset YPT</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama
                                        Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tahun
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Total
                                        Nilai (Rp)</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">QTY
                                    </th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                    $fmtRange = function ($min, $max) {
                                        if (!$min && !$max) {
                                            return '-';
                                        }
                                        if (!$max) {
                                            return $min;
                                        }
                                        if ($min === $max) {
                                            return $min;
                                        }

                                        $len = min(strlen($min), strlen($max));
                                        $i = 0;
                                        for (; $i < $len && $min[$i] === $max[$i]; $i++);

                                        $prefix = substr($min, 0, $i);
                                        $sufMin = substr($min, $i);
                                        $sufMax = substr($max, $i);

                                        // Rapikan jika prefix berakhir dengan titik (.)
                                        // tampil: PREFIX[suffixMin – suffixMax]
                                        return $prefix . '[' . $sufMin . ' – ' . $sufMax . ']';
                                    };
                                @endphp

                                @php
                                    $fmtRange = function ($min, $max) {
                                        if (!$min && !$max) {
                                            return '-';
                                        }
                                        if (!$max) {
                                            return $min;
                                        }
                                        if ($min === $max) {
                                            return $min;
                                        }
                                        $len = min(strlen($min), strlen($max));
                                        $i = 0;
                                        for (; $i < $len && $min[$i] === $max[$i]; $i++) {
                                        }
                                        $prefix = substr($min, 0, $i);
                                        $sufMin = substr($min, $i);
                                        $sufMax = substr($max, $i);
                                        return $prefix . '[' . $sufMin . ' – ' . $sufMax . ']';
                                    };
                                    $idr = fn($n) => number_format((float) $n, 0, ',', '.');
                                @endphp

                                @forelse($groups as $i => $g)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                        <td class="px-4 py-3">{{ $groups->firstItem() + $i }}</td>
                                        <td class="px-4 py-3 font-mono text-sm">
                                            {{ $fmtRange($g->min_code, $g->max_code) }}</td>
                                        <td class="px-4 py-3">{{ $g->name }}</td>
                                        <td class="px-4 py-3">{{ $g->yr }}</td>
                                        @php
                                            $badge = fn($s) => match ($s) {
                                                'Aktif'
                                                    => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200',
                                                'Dipinjam'
                                                    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-200',
                                                'Maintenance'
                                                    => 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-200',
                                                'Rusak'
                                                    => 'bg-rose-100 text-rose-800 dark:bg-rose-900/20 dark:text-rose-200',
                                                'Disposed'
                                                    => 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                                default
                                                    => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200',
                                            };
                                        @endphp

                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-0.5 rounded text-xs font-medium {{ $badge($g->status_label) }}">
                                                {{ $g->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-semibold">Rp {{ $idr($g->total_cost) }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ $g->qty }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('assets.summary.show', $g->group_key) }}"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                Lihat Detail
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
