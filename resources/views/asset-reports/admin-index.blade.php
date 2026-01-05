<x-app-layout>
    <div class="space-y-8 pb-12">
        <!-- Header Section -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-700 to-slate-900 p-8 shadow-2xl animate-fadeIn">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold text-white mb-2">{{ __('Kelola Laporan Kerusakan') }}</h2>
                <p class="text-slate-300 opacity-90">Pantau dan tindak lanjuti laporan kerusakan aset yang dikirim oleh warga sekolah.</p>
            </div>
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-6 py-4 rounded-2xl animate-fadeIn">
                {{ session('success') }}
            </div>
        @endif

        <!-- Reports Table -->
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden animate-slideUp">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aset & Pelapor</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kondisi & Deskripsi</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-all duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col mb-2">
                                        <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $report->asset->name }}</span>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $report->asset->asset_code_ypt }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-500 mr-2">
                                            {{ substr($report->user->name, 0, 1) }}
                                        </div>
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $report->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-600 mb-1 inline-block uppercase">
                                        {{ $report->reported_condition }}
                                    </span>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 max-w-sm">
                                        {{ $report->description }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-2 italic">Dilaporkan: {{ $report->created_at->isoFormat('D MMM YYYY, HH:mm') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($report->image_path)
                                        <a href="{{ asset('storage/' . $report->image_path) }}" target="_blank" class="block w-12 h-12 rounded-lg overflow-hidden border border-gray-100 dark:border-gray-800">
                                            <img src="{{ asset('storage/' . $report->image_path) }}" class="w-full h-full object-cover">
                                        </a>
                                    @else
                                        <span class="text-[10px] text-gray-400">Tidak ada foto</span>
                                    @endif
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
                                <td class="px-6 py-4 text-center">
                                    <button @click="openStatusModal({{ json_encode($report) }})" 
                                        class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all">
                                        Update Status
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada laporan kerusakan yang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($reports->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-800">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Status Modal -->
    <div x-data="{ 
        isOpen: false, 
        report: {},
        status: '',
        note: ''
    }" 
    @open-status-modal.window="isOpen = true; report = $event.detail; status = $event.detail.status; note = $event.detail.admin_note || ''"
    x-show="isOpen" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
    x-cloak>
        <div class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden p-8 animate-slideUp">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Update Status Laporan</h3>
                <button @click="isOpen = false" class="text-gray-400 hover:text-rose-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form :action="`/admin/asset-reports/${report.id}/status`" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Status Baru</label>
                    <select name="status" x-model="status" class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-slate-500 transition-all dark:text-gray-200">
                        <option value="pending">Menunggu</option>
                        <option value="verifying">Diverifikasi</option>
                        <option value="processed">Diproses</option>
                        <option value="fixed">Selesai / Diperbaiki</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Catatan Admin</label>
                    <textarea name="admin_note" x-model="note" rows="3" placeholder="Berikan catatan terkait tindak lanjut laporan ini..."
                        class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-slate-500 transition-all dark:text-gray-200"></textarea>
                </div>

                <div class="flex gap-4">
                    <button type="button" @click="isOpen = false" class="flex-1 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold rounded-2xl">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-slate-800 text-white font-bold rounded-2xl hover:bg-slate-900 transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openStatusModal(report) {
            window.dispatchEvent(new CustomEvent('open-status-modal', { detail: report }));
        }
    </script>
    @endpush
</x-app-layout>
