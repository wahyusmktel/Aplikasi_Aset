<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">
                    Permintaan Peminjaman Aset
                </h2>
                <p class="text-sm text-gray-400 mt-1">Kelola permintaan peminjaman dari Aplikasi SISFO terintegrasi.</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{ rejectModalOpen: false, rejectId: null, returnModalOpen: false, returnId: null }">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 flex items-center gap-3 px-5 py-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-red-50 border border-red-200 rounded-2xl text-red-800">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @php
                $statItems = [
                    ['label' => 'Menunggu',     'key' => 'pending',  'color' => 'amber',   'link' => '?tab=pending'],
                    ['label' => 'Disetujui',    'key' => 'approved', 'color' => 'emerald', 'link' => '?tab=approved'],
                    ['label' => 'Ditolak',      'key' => 'rejected', 'color' => 'red',     'link' => '?tab=rejected'],
                    ['label' => 'Dikembalikan', 'key' => 'returned', 'color' => 'gray',    'link' => '?tab=returned'],
                ];
                $colorMap = [
                    'amber'   => ['bg' => 'bg-amber-50',   'icon' => 'text-amber-600',   'num' => 'text-amber-700'],
                    'emerald' => ['bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'num' => 'text-emerald-700'],
                    'red'     => ['bg' => 'bg-red-50',     'icon' => 'text-red-600',     'num' => 'text-red-700'],
                    'gray'    => ['bg' => 'bg-gray-50',    'icon' => 'text-gray-500',    'num' => 'text-gray-600'],
                ];
            @endphp
            @foreach($statItems as $s)
            @php $c = $colorMap[$s['color']]; @endphp
            <a href="{{ $s['link'] }}" class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-all group {{ $tab == $s['key'] ? 'ring-2 ring-'.$s['color'].'-400' : '' }}">
                <div class="w-12 h-12 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $s['label'] }}</p>
                    <p class="text-2xl font-black {{ $c['num'] }}">{{ $counts[$s['key']] }}</p>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Tabs --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-2 overflow-x-auto">
                @foreach(['pending' => 'Menunggu Persetujuan', 'approved' => 'Dipinjam', 'rejected' => 'Ditolak', 'returned' => 'Dikembalikan'] as $key => $label)
                <a href="?tab={{ $key }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap
                        {{ $tab == $key ? 'bg-red-600 text-white shadow-md shadow-red-500/20' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                    @if($counts[$key] > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full {{ $tab == $key ? 'bg-white/20 text-white' : 'bg-gray-300 text-gray-700' }} text-[10px] font-black">
                        {{ $counts[$key] > 99 ? '99+' : $counts[$key] }}
                    </span>
                    @endif
                </a>
                @endforeach
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pemohon</th>
                            <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Aset</th>
                            <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hidden md:table-cell">Tujuan & Tanggal</th>
                            <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($requests as $req)
                        @php
                            $statusConfig = [
                                'pending'  => ['bg' => 'bg-amber-100',   'text' => 'text-amber-700',   'dot' => 'bg-amber-500',   'label' => 'Menunggu'],
                                'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Disetujui'],
                                'rejected' => ['bg' => 'bg-red-100',     'text' => 'text-red-700',     'dot' => 'bg-red-500',     'label' => 'Ditolak'],
                                'returned' => ['bg' => 'bg-gray-100',    'text' => 'text-gray-600',    'dot' => 'bg-gray-400',    'label' => 'Dikembalikan'],
                            ];
                            $sc = $statusConfig[$req->status] ?? $statusConfig['pending'];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $req->requester_name }}</p>
                                    @if($req->requester_role)
                                    <span class="text-[10px] text-gray-400 font-medium">{{ $req->requester_role }}</span>
                                    @endif
                                    <div class="mt-0.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 bg-orange-100 text-orange-600 text-[9px] font-black rounded uppercase tracking-widest">via {{ $req->requester_app }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div>
                                    @if($req->asset)
                                    <p class="text-[10px] font-black text-red-600">{{ $req->asset->asset_code_ypt }}</p>
                                    <p class="text-sm font-semibold text-gray-700">{{ $req->asset->name }}</p>
                                    @else
                                    <span class="text-xs text-gray-400">Aset tidak ditemukan</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 hidden md:table-cell">
                                <p class="text-xs text-gray-600 line-clamp-2 max-w-48">{{ $req->purpose }}</p>
                                <div class="mt-1 text-[10px] text-gray-400 font-medium">
                                    <span>{{ $req->start_date?->format('d M Y') }}</span>
                                    @if($req->end_date)
                                    <span> — {{ $req->end_date->format('d M Y') }}</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Diajukan: {{ $req->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $sc['bg'] }} {{ $sc['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                                    {{ $sc['label'] }}
                                </span>
                                @if($req->status === 'rejected' && $req->rejection_reason)
                                <p class="text-[10px] text-red-500 mt-1 max-w-24 mx-auto line-clamp-2">{{ $req->rejection_reason }}</p>
                                @endif
                                @if($req->status === 'approved' && $req->approved_by)
                                <p class="text-[10px] text-gray-400 mt-1">oleh {{ $req->approved_by }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($req->status === 'pending')
                                    {{-- Setujui --}}
                                    <form action="{{ route('borrow-requests.approve', $req) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Setujui permintaan peminjaman dari {{ $req->requester_name }}?')"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white text-xs font-bold rounded-xl transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Setujui
                                        </button>
                                    </form>
                                    {{-- Tolak --}}
                                    <button @click="rejectModalOpen = true; rejectId = {{ $req->id }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold rounded-xl transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Tolak
                                    </button>
                                    @elseif($req->status === 'approved')
                                    {{-- Tandai Dikembalikan --}}
                                    <button @click="returnModalOpen = true; returnId = {{ $req->id }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white text-xs font-bold rounded-xl transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        Dikembalikan
                                    </button>
                                    @else
                                    <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-500">Tidak ada permintaan peminjaman</p>
                                    <p class="text-xs text-gray-400 mt-1">untuk tab ini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
                {{ $requests->links() }}
            </div>
            @endif
        </div>

        {{-- Modal: Tolak --}}
        <div x-show="rejectModalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6" @click.stop>
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-black text-gray-800">Tolak Permintaan</h3>
                </div>
                <form :action="'/borrow-requests/' + rejectId + '/reject'" method="POST" id="reject-form">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea name="rejection_reason" rows="3" required
                            placeholder="Tuliskan alasan penolakan..."
                            class="w-full rounded-xl border border-gray-200 p-3 text-sm focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all resize-none"></textarea>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="rejectModalOpen = false"
                            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-200 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition-all shadow-md shadow-red-500/20">
                            Tolak Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Tandai Dikembalikan --}}
        <div x-show="returnModalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6" @click.stop>
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-black text-gray-800">Tandai Aset Dikembalikan</h3>
                </div>
                <form :action="'/borrow-requests/' + returnId + '/returned'" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Catatan Pengembalian</label>
                        <textarea name="return_notes" rows="3"
                            placeholder="Kondisi aset saat dikembalikan (opsional)..."
                            class="w-full rounded-xl border border-gray-200 p-3 text-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all resize-none"></textarea>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="returnModalOpen = false"
                            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-200 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-500/20">
                            Konfirmasi Dikembalikan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
