<x-app-layout>
    <div class="space-y-8 pb-12">
        <!-- Header Section -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-500 to-rose-600 p-8 shadow-2xl animate-fadeIn">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2">{{ __('Laporan Kerusakan Aset') }}</h2>
                    <p class="text-amber-100 opacity-90">Bantu kami menjaga aset sekolah dengan melaporkan kerusakan atau kendala teknis.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('asset-reports.scan') }}" 
                        class="inline-flex items-center px-6 py-3 bg-white text-rose-600 hover:bg-amber-50 font-bold rounded-2xl shadow-lg transition-all duration-300 hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 11v1m5-14H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2z" /></svg>
                        Scan QR Label
                    </a>
                    <a href="{{ route('asset-reports.create') }}" 
                        class="inline-flex items-center px-6 py-3 bg-rose-700 text-white hover:bg-rose-800 font-bold rounded-2xl shadow-lg transition-all duration-300 hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Lapor Manual
                    </a>
                </div>
            </div>
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-rose-400/20 rounded-full blur-3xl"></div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-6 py-4 rounded-2xl animate-fadeIn">
                {{ session('success') }}
            </div>
        @endif

        <!-- My Reports Content -->
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden animate-slideUp">
            <div class="p-8 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Riwayat Laporan Saya</h3>
                <div class="p-2 bg-rose-50 dark:bg-rose-900/20 rounded-xl">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Laporan</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aset</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi Masalah</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-all duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs font-bold font-mono text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                                        {{ $report->report_doc_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $report->asset->name }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $report->asset->asset_code_ypt }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-1 truncate max-w-xs" title="{{ $report->description }}">
                                        {{ $report->description }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'verifying' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'processed' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                            'fixed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'rejected' => 'bg-rose-50 text-rose-600 border-rose-100',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Menunggu',
                                            'verifying' => 'Diverifikasi',
                                            'processed' => 'Diproses',
                                            'fixed' => 'Selesai',
                                            'rejected' => 'Ditolak',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 {{ $statusClasses[$report->status] ?? 'bg-gray-50 text-gray-600' }} text-[10px] font-bold uppercase rounded-lg border">
                                        {{ $statusLabels[$report->status] ?? $report->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 italic">
                                    {{ $report->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Anda belum pernah mengirimkan laporan kerusakan.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-50 dark:border-gray-800">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
