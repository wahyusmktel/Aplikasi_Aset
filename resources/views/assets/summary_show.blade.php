<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $title }} — {{ $yr }} (Detail)
        </h2>
    </x-slot>

    @if (session('success'))
        <div
            class="mb-3 px-3 py-2 rounded-md text-sm bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-3 px-3 py-2 rounded-md text-sm bg-rose-50 text-rose-800 dark:bg-rose-900/20 dark:text-rose-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- BULK ACTION TOOLBAR --}}
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-2">
                        {{-- Form Bulk Move --}}
                        <form id="bulk-move-form" method="POST" action="{{ route('assets.bulk-move') }}"
                            class="flex flex-col gap-2 md:flex-row md:items-end">
                            @csrf
                            <input type="hidden" name="ids" id="bulk-move-ids">

                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Gedung</label>
                                <select name="building_id"
                                    class="select2 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                    <option value="">— Tidak Diubah —</option>
                                    @foreach (\App\Models\Building::orderBy('name')->get() as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Ruangan</label>
                                <select name="room_id"
                                    class="select2 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                    <option value="">— Tidak Diubah —</option>
                                    @foreach (\App\Models\Room::orderBy('name')->get() as $r)
                                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">PIC</label>
                                <select name="person_in_charge_id"
                                    class="select2 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                    <option value="">— Tidak Diubah —</option>
                                    @foreach (\App\Models\PersonInCharge::orderBy('name')->get() as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                class="inline-flex items-center px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md">
                                🚚 Pindahkan/Assign
                            </button>
                        </form>

                        {{-- Form Bulk Status --}}
                        <form id="bulk-status-form" method="POST" action="{{ route('assets.bulk-status') }}"
                            class="flex flex-col gap-2 md:flex-row md:items-end">
                            @csrf
                            <input type="hidden" name="ids" id="bulk-status-ids">

                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status Baru</label>
                                <select name="status"
                                    class="select2 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                    required>
                                    <option value="">— Pilih Status —</option>
                                    @foreach (['Aktif', 'Dipinjam', 'Maintenance', 'Rusak', 'Disposed'] as $st)
                                        <option value="{{ $st }}">{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-md">
                                ✅ Update Status
                            </button>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="px-4 py-3">
                                        <input type="checkbox" id="select-all"
                                            class="rounded border-gray-300 dark:border-gray-600">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">#
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Kode
                                        Aset YPT</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tahun
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Lokasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">PIC
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($items as $i => $a)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                        <td class="px-4 py-3">
                                            <input type="checkbox"
                                                class="row-check rounded border-gray-300 dark:border-gray-600"
                                                value="{{ $a->id }}">
                                        </td>
                                        <td class="px-4 py-3">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 font-mono text-sm">{{ $a->asset_code_ypt ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $a->name }}</td>
                                        <td class="px-4 py-3">{{ $a->purchase_year ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            {{ optional($a->room)->name ?? (optional($a->building)->name ?? '-') }}
                                        </td>
                                        <td class="px-4 py-3">{{ optional($a->personInCharge)->name ?? '-' }}</td>
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
                                                class="px-2 py-0.5 rounded text-xs font-medium {{ $badge($a->status) }}">
                                                {{ $a->status ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                        @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const selectAll = document.getElementById('select-all');
                                    const checks = Array.from(document.querySelectorAll('.row-check'));
                                    const moveForm = document.getElementById('bulk-move-form');
                                    const statusForm = document.getElementById('bulk-status-form');
                                    const moveIds = document.getElementById('bulk-move-ids');
                                    const statusIds = document.getElementById('bulk-status-ids');

                                    if (selectAll) {
                                        selectAll.addEventListener('change', () => {
                                            checks.forEach(c => {
                                                c.checked = selectAll.checked;
                                            });
                                        });
                                    }

                                    function collectIds() {
                                        return checks.filter(c => c.checked).map(c => c.value).join(',');
                                    }

                                    if (moveForm) {
                                        moveForm.addEventListener('submit', (e) => {
                                            const ids = collectIds();
                                            if (!ids) {
                                                e.preventDefault();
                                                alert('Pilih minimal satu aset terlebih dahulu.');
                                                return;
                                            }
                                            moveIds.value = ids;
                                        });
                                    }

                                    if (statusForm) {
                                        statusForm.addEventListener('submit', (e) => {
                                            const ids = collectIds();
                                            if (!ids) {
                                                e.preventDefault();
                                                alert('Pilih minimal satu aset terlebih dahulu.');
                                                return;
                                            }
                                            statusIds.value = ids;
                                        });
                                    }
                                });
                            </script>
                        @endpush
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('assets.summary') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-600 text-white text-sm font-medium hover:bg-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Ringkasan
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
