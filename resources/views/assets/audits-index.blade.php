<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Riwayat Perubahan Aset
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (($ids ?? collect())->isNotEmpty())
                        <div class="mb-3 text-sm text-gray-500 dark:text-gray-400">
                            Filter aset: {{ $ids->implode(', ') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Waktu</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Aset</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Aksi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Perubahan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($logs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                        <td class="px-4 py-3 text-sm">{{ $log->created_at }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="font-mono">{{ $log->asset->asset_code_ypt ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->asset->name ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $log->action }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $log->actor_name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-xs">
                                            @php
                                                $before = $log->before ?? [];
                                                $after = $log->after ?? [];
                                                $keys = array_unique(
                                                    array_merge(array_keys($before), array_keys($after)),
                                                );
                                            @endphp
                                            @foreach ($keys as $k)
                                                <div><span class="font-medium">{{ $k }}</span>:
                                                    {{ $before[$k] ?? '-' }} → {{ $after[$k] ?? '-' }}</div>
                                            @endforeach
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada
                                            log.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $logs->links() }}</div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
