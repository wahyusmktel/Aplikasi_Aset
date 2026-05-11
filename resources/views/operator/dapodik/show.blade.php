<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-3xl text-gray-800 leading-tight tracking-tight">Detail Pengajuan</h2>
                <p class="text-sm text-gray-400 mt-1">Review perubahan data Dapodik yang diajukan pegawai.</p>
            </div>
            <a href="{{ route('operator.dapodik.index') }}"
               class="flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
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

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- Info Card --}}
                <div class="xl:col-span-1 space-y-4">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 space-y-4">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pegawai</p>
                            <p class="font-bold text-gray-800 text-base">{{ $changeRequest->employee->name }}</p>
                            <p class="text-sm text-gray-500">{{ $changeRequest->employee->position }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">NIP: {{ $changeRequest->employee->nip ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Diajukan oleh</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $changeRequest->requestedBy->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $changeRequest->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                            @if($changeRequest->status === 'pending')
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Validasi
                                </span>
                            @elseif($changeRequest->status === 'approved')
                                <span class="text-xs font-bold px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full">Disetujui</span>
                                <p class="text-xs text-gray-400 mt-1">
                                    oleh {{ $changeRequest->reviewedBy->name ?? '—' }}
                                    — {{ $changeRequest->reviewed_at?->translatedFormat('d M Y, H:i') }}
                                </p>
                            @else
                                <span class="text-xs font-bold px-2.5 py-1 bg-red-100 text-red-700 rounded-full">Ditolak</span>
                                <p class="text-xs text-gray-400 mt-1">
                                    oleh {{ $changeRequest->reviewedBy->name ?? '—' }}
                                    — {{ $changeRequest->reviewed_at?->translatedFormat('d M Y, H:i') }}
                                </p>
                                @if($changeRequest->rejection_reason)
                                    <p class="text-xs text-red-700 mt-2 p-2 bg-red-50 rounded-lg">
                                        <strong>Alasan:</strong> {{ $changeRequest->rejection_reason }}
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Action buttons (only for pending) --}}
                    @if($changeRequest->isPending())
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 space-y-3">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tindakan</p>

                            <form method="POST" action="{{ route('operator.dapodik.approve', $changeRequest) }}"
                                onsubmit="return confirm('Setujui pengajuan ini? Data pegawai akan langsung diperbarui.')">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Setujui & Perbarui Data
                                </button>
                            </form>

                            <button onclick="document.getElementById('rejectPanel').classList.toggle('hidden')"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-red-200 text-red-600 hover:bg-red-50 font-bold rounded-xl text-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak dengan Alasan
                            </button>

                            <div id="rejectPanel" class="hidden">
                                <form method="POST" action="{{ route('operator.dapodik.reject', $changeRequest) }}" class="space-y-3">
                                    @csrf
                                    <textarea name="rejection_reason" rows="3" required
                                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500"
                                        placeholder="Tuliskan alasan penolakan yang jelas agar pegawai dapat memperbaiki pengajuannya..."></textarea>
                                    <button type="submit"
                                        class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition shadow-sm">
                                        Kirim Penolakan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Changes comparison --}}
                <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h4 class="font-bold text-gray-800">Perbandingan Perubahan Data</h4>
                        <p class="text-xs text-gray-400 mt-0.5">Kolom kiri: data sebelumnya. Kolom kanan: data yang diajukan.</p>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($changeRequest->proposed_changes as $field => $newVal)
                            @php
                                $oldVal = $changeRequest->current_values[$field] ?? null;
                                $label  = \App\Http\Controllers\UserDapodikController::fieldLabel($field);
                            @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                                <div class="px-6 py-4 bg-red-50/40 border-r border-dashed border-gray-200">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ $label }} (Lama)</p>
                                    <p class="text-sm text-red-700 font-semibold">{{ $oldVal ?: '— (kosong)' }}</p>
                                </div>
                                <div class="px-6 py-4 bg-emerald-50/40">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ $label }} (Baru)</p>
                                    <p class="text-sm text-emerald-700 font-semibold">{{ $newVal ?: '— (kosong)' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
