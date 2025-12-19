<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Pengadaan Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Header Action & Search -->
            <div class="bg-white dark:bg-gray-950 overflow-hidden shadow-sm rounded-3xl border border-gray-100 dark:border-gray-800">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                        <div>
                            <a href="{{ route('procurements.create') }}" 
                                class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary-500/30 group">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                {{ __('Buat Pengadaan Baru') }}
                            </a>
                        </div>
                        <form action="{{ route('procurements.index') }}" method="GET" class="w-full md:w-auto">
                            <div class="relative group">
                                <input type="text" name="search" placeholder="Cari nomor ref/vendor..."
                                    class="w-full md:w-80 pl-12 pr-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 transition-all shadow-sm"
                                    value="{{ request('search') }}">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- List Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fadeIn">
                @forelse ($procurements as $procurement)
                    <div class="bg-white dark:bg-gray-950 rounded-3xl border border-gray-100 dark:border-gray-800 p-6 hover:shadow-xl transition-all group overflow-hidden relative">
                        <!-- Status Badge -->
                        <div class="absolute top-0 right-0">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
                                    'received' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                                    'unit_delivered' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
                                ];
                                $statusLabels = [
                                    'pending' => 'Sedang Diproses',
                                    'received' => 'Diterima Sekolah',
                                    'unit_delivered' => 'Diserahterimakan ke Unit',
                                ];
                            @endphp
                            <span class="px-4 py-2 rounded-bl-2xl text-xs font-bold {{ $statusClasses[$procurement->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusLabels[$procurement->status] ?? $procurement->status }}
                            </span>
                        </div>

                        <div class="mb-4 pt-2">
                            <div class="text-xs font-bold text-primary-600 uppercase tracking-widest mb-1">{{ $procurement->reference_number }}</div>
                            <h3 class="text-lg font-black text-gray-800 dark:text-white line-clamp-1 group-hover:text-primary-600 transition-colors">
                                {{ $procurement->vendor->name }}
                            </h3>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                {{ $procurement->procurement_date->format('d M Y') }}
                            </div>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                {{ $procurement->items->count() }} Jenis Barang
                            </div>
                            <div class="text-xl font-black text-primary-700 dark:text-primary-400">
                                Rp {{ number_format($procurement->total_cost, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-50 dark:border-gray-800">
                            <a href="{{ route('procurements.show', $procurement) }}" class="text-sm font-bold text-gray-400 hover:text-primary-600 transition-colors flex items-center group/btn">
                                Detail Pengadaan
                                <svg class="w-4 h-4 ml-1 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>
                            
                            <form action="{{ route('procurements.destroy', $procurement) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data pengadaan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m3 4h.01" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-3 bg-white dark:bg-gray-950 rounded-3xl border border-gray-100 dark:border-gray-800 p-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-20 h-20 text-gray-100 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                            <p class="text-lg font-medium">Belum ada data pengadaan aset.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($procurements->hasPages())
                <div class="mt-8">
                    {{ $procurements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
