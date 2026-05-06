<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan Digital — SARPRA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">

        {{-- Header --}}
        <div class="text-center mb-8">
            @php $logo = \App\Models\Setting::get('app_logo'); @endphp
            @if($logo)
                <img src="{{ asset('storage/' . $logo) }}" class="h-14 mx-auto mb-3" alt="Logo">
            @endif
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">SMK Telkom Lampung</p>
            <h1 class="text-xl font-extrabold text-gray-800 mt-1">Verifikasi Tanda Tangan Digital</h1>
            <p class="text-sm text-gray-500 mt-1">Sistem Manajemen Aset Sarana Prasarana</p>
        </div>

        @if($doc)
            @if($doc->is_valid && $doc->verifyHmac())
                {{-- VALID --}}
                <div class="bg-white rounded-2xl shadow-lg border border-green-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-green-100 text-xs font-bold uppercase tracking-widest">Dokumen Sah</p>
                                <h2 class="text-white text-xl font-extrabold">Tanda Tangan Valid</h2>
                                <p class="text-green-100 text-sm mt-0.5">Dokumen ini telah diverifikasi keasliannya</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Judul Dokumen</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $doc->document_title }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jenis Dokumen</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $doc->document_type }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Ditandatangani Oleh</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $doc->signer_name }}</p>
                                @if($doc->signer_nip)
                                    <p class="text-xs text-gray-500">NIP: {{ $doc->signer_nip }}</p>
                                @endif
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jabatan</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $doc->signer_role ?? '-' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 col-span-2">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Waktu Penandatanganan</p>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($doc->signed_at)->translatedFormat('l, d F Y \p\u\k\u\l H:i') }} WIB
                                </p>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Hash Dokumen (SHA-256)</p>
                            <p class="text-xs font-mono text-gray-600 bg-gray-50 rounded-lg p-3 break-all">{{ $doc->document_hash }}</p>
                        </div>

                        <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <p class="text-xs text-green-700 font-semibold">Integritas dokumen terverifikasi — HMAC-SHA256 cocok</p>
                        </div>
                    </div>
                </div>

            @elseif(!$doc->is_valid)
                {{-- REVOKED --}}
                <div class="bg-white rounded-2xl shadow-lg border border-red-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-500 to-rose-600 px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-red-100 text-xs font-bold uppercase tracking-widest">Tanda Tangan Tidak Aktif</p>
                                <h2 class="text-white text-xl font-extrabold">Tanda Tangan Telah Dicabut</h2>
                                <p class="text-red-100 text-sm mt-0.5">Dokumen ini tidak lagi dianggap sah</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                            <p class="text-sm font-bold text-red-700">Alasan Pencabutan:</p>
                            <p class="text-sm text-red-600 mt-1">{{ $doc->revoke_reason ?? 'Tidak disebutkan' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Dicabut Pada</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $doc->revoked_at ? \Carbon\Carbon::parse($doc->revoked_at)->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

            @else
                {{-- TAMPERED --}}
                <div class="bg-white rounded-2xl shadow-lg border border-orange-200 p-6 text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-extrabold text-gray-800 mb-2">Integritas Dokumen Diragukan</h2>
                    <p class="text-sm text-gray-500">HMAC tidak cocok — kemungkinan dokumen telah dimodifikasi.</p>
                </div>
            @endif

        @else
            {{-- NOT FOUND --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-extrabold text-gray-800 mb-2">Dokumen Tidak Ditemukan</h2>
                <p class="text-sm text-gray-500 mb-4">Token <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $token }}</span> tidak ditemukan dalam sistem.</p>
                <p class="text-xs text-gray-400">Pastikan QR code / token yang Anda scan berasal dari dokumen BAST resmi SARPRA.</p>
            </div>
        @endif

        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; {{ date('Y') }} SMK Telkom Lampung · Sistem Manajemen Aset
        </p>
    </div>
</body>
</html>
