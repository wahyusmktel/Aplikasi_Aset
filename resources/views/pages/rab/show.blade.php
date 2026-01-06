<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('rab.index') }}" class="p-3 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-900 transition-all flex items-center justify-center text-gray-400 hover:text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">
                        {{ $rab->name }}
                    </h2>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-widest font-black">RAB Preview • <span class="text-red-600">{{ $rab->academicYear->year }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('rab.exportPdf', $rab->id) }}"
                    class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-black rounded-2xl transition-all shadow-xl shadow-red-500/30 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 pb-24">
            {{-- Info Content --}}
            <div class="bg-white dark:bg-gray-950 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate-fadeIn p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Mata Anggaran (MTA)</span>
                        <span class="text-lg font-black text-red-600">{{ $rab->mta }}</span>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl lg:col-span-2">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Akun</span>
                        <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $rab->nama_akun }}</span>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Waktu Pelaksanaan</span>
                        <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $rab->kebutuhan_waktu }}</span>
                    </div>
                </div>

                {{-- Table Details --}}
                <div class="overflow-x-auto rounded-3xl border border-gray-100 dark:border-gray-800 mb-8">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">No</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Uraian Kegiatan</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Vol & Satuan</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Harga Satuan</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                            @foreach($rab->details as $index => $detail)
                                <tr>
                                    <td class="p-6 text-sm font-bold text-gray-400">{{ $index + 1 }}</td>
                                    <td class="p-6">
                                        <div class="flex flex-col">
                                            <span class="text-base font-bold text-gray-800 dark:text-white">{{ $detail->alias_name }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $detail->rkas->rincian_kegiatan }}</span>
                                        </div>
                                    </td>
                                    <td class="p-6 text-right">
                                        <span class="text-sm font-bold text-gray-800 dark:text-white">{{ number_format($detail->quantity, 0, ',', '.') }} {{ $detail->unit }}</span>
                                    </td>
                                    <td class="p-6 text-right">
                                        <span class="text-sm font-bold text-gray-400">Rp {{ number_format($detail->price, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="p-6 text-right">
                                        <span class="text-base font-black text-red-600">Rp {{ number_format($detail->amount, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <td colspan="4" class="p-8 text-right text-xs font-black text-gray-400 uppercase tracking-widest">Total Keseluruhan Anggaran:</td>
                                <td class="p-8 text-right">
                                    <span class="text-2xl font-black text-red-600">Rp {{ number_format($rab->total_amount, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Signatures --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mt-12 pt-12 border-t border-gray-50 dark:border-gray-900 text-center">
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-12">Dibuat Oleh</span>
                        <span class="block text-sm font-black text-gray-800 dark:text-white underline">{{ $rab->creator->name }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-12">Diperiksa Oleh</span>
                        <span class="block text-sm font-black text-gray-800 dark:text-white underline">{{ $rab->checker->name }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-12">Disetujui Oleh</span>
                        <span class="block text-sm font-black text-gray-800 dark:text-white underline">{{ $rab->approver->name }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-12">Kepala Sekolah</span>
                        <span class="block text-sm font-black text-gray-800 dark:text-white underline">{{ $rab->headmaster->name }}</span>
                    </div>
                </div>

                @if($rab->notes)
                    <div class="mt-12 p-8 bg-gray-50 dark:bg-gray-900 rounded-3xl border-l-4 border-red-600">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Catatan Khusus:</span>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $rab->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
