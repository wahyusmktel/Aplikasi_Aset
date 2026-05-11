<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-3xl text-gray-800 leading-tight tracking-tight">Pengajuan Perubahan Dapodik</h2>
                <p class="text-sm text-gray-400 mt-1">Review dan validasi pengajuan perubahan data Dapodik pegawai.</p>
            </div>
            @if($pendingCount > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $pendingCount }} Menunggu
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-xl text-sm font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-xl text-sm font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filter tabs --}}
            <div class="flex gap-2 flex-wrap">
                @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
                    <a href="{{ route('operator.dapodik.index', ['status' => $key]) }}"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition
                            {{ $status === $key
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <p class="text-xs text-gray-400">{{ $requests->total() }} pengajuan ditemukan</p>
                </div>

                @if($requests->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Pegawai</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Perubahan</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Diajukan</th>
                                    <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($requests as $req)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-800">{{ $req->employee->name ?? '—' }}</p>
                                            <p class="text-xs text-gray-400">{{ $req->employee->position ?? '' }}</p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_keys($req->proposed_changes) as $field)
                                                    <span class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-md font-medium">
                                                        {{ \App\Http\Controllers\UserDapodikController::fieldLabel($field) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-sm text-gray-600">{{ $req->created_at->translatedFormat('d M Y') }}</span>
                                            <p class="text-xs text-gray-400">{{ $req->created_at->diffForHumans() }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @if($req->status === 'pending')
                                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    Menunggu
                                                </span>
                                            @elseif($req->status === 'approved')
                                                <span class="text-xs font-bold px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full">Disetujui</span>
                                            @else
                                                <span class="text-xs font-bold px-2.5 py-1 bg-red-100 text-red-700 rounded-full">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <a href="{{ route('operator.dapodik.show', $req) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $requests->links() }}
                    </div>
                @else
                    <div class="py-20 text-center">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="font-bold text-gray-400 text-base">Tidak ada pengajuan ditemukan</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
