<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $title }} — {{ $yr }} (Detail)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">#</th>
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
                                        <td class="px-4 py-3">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 font-mono text-sm">{{ $a->asset_code_ypt ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $a->name }}</td>
                                        <td class="px-4 py-3">{{ $a->purchase_year ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            {{ optional($a->room)->name ?? (optional($a->building)->name ?? '-') }}</td>
                                        <td class="px-4 py-3">{{ optional($a->personInCharge)->name ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $a->status ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
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
