<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Antrian Tanda Tangan Dokumen</h2>
            <a href="{{ route('tanda-tangan.index') }}"
               class="flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Tanda Tangan
            </a>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3 rounded-xl text-sm font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            {{-- Banner info --}}
            <div class="flex items-start gap-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-bold text-amber-800">Dokumen Menunggu Tanda Tangan Manual</p>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Dokumen-dokumen ini dibuat saat mode tanda tangan otomatis Anda dinonaktifkan.
                        Klik <strong>Tanda Tangani</strong> dan masukkan PIN untuk mengesahkan setiap dokumen.
                        QR code akan muncul di PDF setelah Anda menandatangani.
                    </p>
                </div>
            </div>

            {{-- Tabel Antrian --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-gray-800">Dokumen Menunggu Tanda Tangan</h4>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $pendingDocs->total() }} dokumen dalam antrian</p>
                    </div>
                </div>

                @if($pendingDocs->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Dokumen</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Masuk Antrian</th>
                                    <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($pendingDocs as $doc)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-800 text-sm">{{ $doc->document_title }}</p>
                                            @if($doc->reference_id)
                                                <p class="text-xs text-gray-400 mt-0.5 font-mono">Ref: {{ $doc->reference_id }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-xs font-semibold px-2 py-1 bg-indigo-50 text-indigo-600 rounded-md">
                                                {{ $doc->document_type }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-sm text-gray-500">
                                                {{ $doc->created_at->translatedFormat('d M Y') }}
                                            </span>
                                            <p class="text-xs text-gray-400">{{ $doc->created_at->diffForHumans() }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button onclick="openSignModal({{ $doc->id }}, '{{ addslashes($doc->document_title) }}')"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                                Tanda Tangani
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $pendingDocs->links() }}
                    </div>
                @else
                    <div class="py-20 text-center">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="font-bold text-gray-400 text-base">Tidak ada dokumen dalam antrian</p>
                        <p class="text-sm text-gray-400 mt-1">Semua dokumen sudah ditandatangani atau mode otomatis aktif.</p>
                        <a href="{{ route('tanda-tangan.index') }}"
                           class="mt-4 inline-block text-sm font-semibold text-indigo-600 hover:underline">
                            Kembali ke halaman tanda tangan
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Modal Tanda Tangan --}}
    <div id="signModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-5 bg-gradient-to-r from-indigo-600 to-violet-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-base">Konfirmasi Tanda Tangan</h3>
                        <p class="text-indigo-200 text-xs mt-0.5">Masukkan PIN untuk mengesahkan dokumen</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div id="signDocTitle" class="mb-4 p-3 bg-gray-50 rounded-xl text-sm font-semibold text-gray-700 border border-gray-200 leading-relaxed"></div>
                <form id="signForm" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                            PIN Keamanan
                        </label>
                        <input type="password" name="pin" id="signPin" inputmode="numeric" maxlength="8" required
                            placeholder="Masukkan PIN Anda"
                            class="w-full rounded-xl border-gray-200 text-sm font-mono tracking-widest text-center text-xl py-3 focus:ring-indigo-500 focus:border-indigo-500"
                            autocomplete="off">
                    </div>
                    <div class="flex gap-3 pt-1">
                        <button type="button" onclick="closeSignModal()"
                            class="flex-1 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl text-sm hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-sm">
                            Tanda Tangani Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openSignModal(docId, docTitle) {
            document.getElementById('signDocTitle').textContent = docTitle;
            document.getElementById('signForm').action = '/tanda-tangan/antrian/' + docId + '/sign';
            document.getElementById('signPin').value = '';
            const m = document.getElementById('signModal');
            m.classList.remove('hidden');
            m.classList.add('flex');
            setTimeout(() => document.getElementById('signPin').focus(), 100);
        }
        function closeSignModal() {
            const m = document.getElementById('signModal');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        document.getElementById('signModal').addEventListener('click', e => {
            if (e.target === e.currentTarget) closeSignModal();
        });
    </script>
    @endpush
</x-app-layout>
