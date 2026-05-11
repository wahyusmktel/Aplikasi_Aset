<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-3xl text-gray-800 leading-tight tracking-tight">Data Dapodik Saya</h2>
            <p class="text-sm text-gray-400 mt-1">Lihat dan ajukan perubahan data Dapodik Anda.</p>
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

            {{-- Status: Pending request --}}
            @if($pendingRequest)
                <div class="flex items-start gap-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                    <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-amber-800">Pengajuan Perubahan Sedang Diproses</p>
                        <p class="text-xs text-amber-700 mt-0.5">
                            Anda memiliki pengajuan perubahan data yang sedang menunggu validasi operator.
                            Dikirim {{ $pendingRequest->created_at->diffForHumans() }}.
                            Anda tidak dapat mengajukan perubahan baru hingga pengajuan ini diproses.
                        </p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($pendingRequest->proposed_changes as $field => $val)
                                <span class="text-xs px-2 py-0.5 bg-amber-100 text-amber-800 rounded-md font-medium">
                                    {{ \App\Http\Controllers\UserDapodikController::fieldLabel($field) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Status: Rejected request --}}
            @if($rejectedRequest)
                <div class="flex items-start gap-4 p-4 bg-red-50 border border-red-200 rounded-2xl">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-red-800">Pengajuan Terakhir Ditolak</p>
                        <p class="text-xs text-red-700 mt-0.5">
                            <strong>Alasan:</strong> {{ $rejectedRequest->rejection_reason }}
                        </p>
                        <p class="text-xs text-red-500 mt-1">Silahkan perbaiki data Anda dan ajukan ulang di bawah.</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- Current Data Card --}}
                <div class="xl:col-span-1 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h4 class="font-bold text-gray-800">Data Aktif Sekarang</h4>
                        <p class="text-xs text-gray-400 mt-0.5">Data yang tersimpan di sistem</p>
                    </div>
                    <div class="p-5 space-y-3">
                        @php
                            $dataFields = [
                                'name'               => ['label' => 'Nama', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                                'nip'                => ['label' => 'NIP', 'icon' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2'],
                                'position'           => ['label' => 'Jabatan', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                                'nuptk'              => ['label' => 'NUPTK', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                'jenis_kelamin'      => ['label' => 'Jenis Kelamin', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                                'tempat_lahir'       => ['label' => 'Tempat Lahir', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                                'tanggal_lahir'      => ['label' => 'Tanggal Lahir', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                'agama'              => ['label' => 'Agama', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                                'status_perkawinan'  => ['label' => 'Status Perkawinan', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                                'status_kepegawaian' => ['label' => 'Status Kepegawaian', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                                'golongan_pangkat'   => ['label' => 'Golongan/Pangkat', 'icon' => 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z'],
                                'tmt_pengangkatan'   => ['label' => 'TMT Pengangkatan', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                'pendidikan_terakhir'=> ['label' => 'Pendidikan Terakhir', 'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
                                'bidang_studi'       => ['label' => 'Bidang Studi', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                                'lembaga_pendidikan' => ['label' => 'Lembaga Pendidikan', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                                'alamat'             => ['label' => 'Alamat', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                                'no_hp'              => ['label' => 'No. HP', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 7V5z'],
                            ];
                        @endphp
                        @foreach($dataFields as $field => $meta)
                            @php
                                $val = $field === 'tanggal_lahir' || $field === 'tmt_pengangkatan'
                                    ? ($employee->$field ? $employee->$field->translatedFormat('d M Y') : null)
                                    : $employee->$field;
                            @endphp
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $meta['icon'] }}"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-400 font-medium">{{ $meta['label'] }}</p>
                                    <p class="text-sm font-semibold text-gray-700 break-words">
                                        {{ $val ?: '—' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Change Request Form --}}
                <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-gray-800">Ajukan Perubahan Data</h4>
                            <p class="text-xs text-gray-400 mt-0.5">Ubah hanya bidang yang perlu diperbarui, lalu kirim untuk divalidasi operator.</p>
                        </div>
                        @if($pendingRequest)
                            <span class="text-xs font-bold px-3 py-1 bg-amber-100 text-amber-700 rounded-full">Pengajuan Aktif</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('user.dapodik.submit') }}" class="p-6">
                        @csrf

                        @if($pendingRequest)
                            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                                Form dinonaktifkan sementara karena ada pengajuan yang sedang diproses operator.
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Jenis Kelamin --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                                <select name="jenis_kelamin" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}">
                                    <option value="">— Pilih —</option>
                                    @foreach(['L' => 'Laki-laki', 'P' => 'Perempuan'] as $val => $label)
                                        <option value="{{ $val }}" {{ $employee->jenis_kelamin === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Agama --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Agama</label>
                                <select name="agama" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}">
                                    <option value="">— Pilih —</option>
                                    @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ $employee->agama === $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status Perkawinan --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Status Perkawinan</label>
                                <select name="status_perkawinan" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}">
                                    <option value="">— Pilih —</option>
                                    @foreach(['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'] as $sp)
                                        <option value="{{ $sp }}" {{ $employee->status_perkawinan === $sp ? 'selected' : '' }}>{{ $sp }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status Kepegawaian --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Status Kepegawaian</label>
                                <select name="status_kepegawaian" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}">
                                    <option value="">— Pilih —</option>
                                    @foreach(['PNS','PPPK','GTY','GTT','Honorer'] as $sk)
                                        <option value="{{ $sk }}" {{ $employee->status_kepegawaian === $sk ? 'selected' : '' }}>{{ $sk }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tempat Lahir --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" value="{{ $employee->tempat_lahir }}" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}"
                                    placeholder="Kota/Kabupaten">
                            </div>

                            {{-- Tanggal Lahir --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir"
                                    value="{{ $employee->tanggal_lahir ? $employee->tanggal_lahir->format('Y-m-d') : '' }}"
                                    {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}">
                            </div>

                            {{-- NUPTK --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">NUPTK</label>
                                <input type="text" name="nuptk" value="{{ $employee->nuptk }}" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}"
                                    placeholder="16 digit NUPTK" maxlength="30">
                            </div>

                            {{-- Golongan/Pangkat --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Golongan / Pangkat</label>
                                <input type="text" name="golongan_pangkat" value="{{ $employee->golongan_pangkat }}" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}"
                                    placeholder="cth. III/a, IV/b">
                            </div>

                            {{-- TMT Pengangkatan --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">TMT Pengangkatan</label>
                                <input type="date" name="tmt_pengangkatan"
                                    value="{{ $employee->tmt_pengangkatan ? $employee->tmt_pengangkatan->format('Y-m-d') : '' }}"
                                    {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}">
                            </div>

                            {{-- Pendidikan Terakhir --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}">
                                    <option value="">— Pilih —</option>
                                    @foreach(['SD','SMP','SMA/SMK','D1','D2','D3','D4','S1','S2','S3'] as $pt)
                                        <option value="{{ $pt }}" {{ $employee->pendidikan_terakhir === $pt ? 'selected' : '' }}>{{ $pt }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Bidang Studi --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Bidang Studi</label>
                                <input type="text" name="bidang_studi" value="{{ $employee->bidang_studi }}" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}"
                                    placeholder="cth. Teknik Informatika">
                            </div>

                            {{-- Lembaga Pendidikan --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Lembaga Pendidikan</label>
                                <input type="text" name="lembaga_pendidikan" value="{{ $employee->lembaga_pendidikan }}" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}"
                                    placeholder="Nama universitas/sekolah tinggi">
                            </div>

                            {{-- No HP --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">No. HP</label>
                                <input type="text" name="no_hp" value="{{ $employee->no_hp }}" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}"
                                    placeholder="08xxxxxxxxxx" maxlength="20">
                            </div>

                            {{-- Alamat --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Alamat</label>
                                <textarea name="alamat" rows="3" {{ $pendingRequest ? 'disabled' : '' }}
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $pendingRequest ? 'bg-gray-50 text-gray-400' : '' }}"
                                    placeholder="Alamat lengkap">{{ $employee->alamat }}</textarea>
                            </div>

                        </div>

                        @unless($pendingRequest)
                            <div class="mt-6 flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Kirim Pengajuan Perubahan
                                </button>
                            </div>
                        @endunless
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
