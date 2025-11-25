<x-guest-layout>
    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100 text-center">
                    {{-- Icon Centang --}}
                    <div class="flex justify-center mb-4">
                        <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold">Dokumen Terverifikasi</h2>
                    <p class="text-gray-500 mt-1">Dokumen ini sah dan tercatat dalam sistem.</p>

                    <div class="mt-6 text-left border-t pt-6 dark:border-gray-700">
                        <h3 class="font-bold text-lg mb-2">{{ $documentType }}</h3>
                        <dl class="space-y-2 text-sm">

                            {{-- Nomor Surat --}}
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Nomor Surat</dt>
                                <dd class="w-2/3 font-mono break-all">{{ $docNumber }}</dd>
                            </div>

                            {{-- Nama Aset / Ruangan --}}
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">
                                    @if (isset($labLog))
                                        Ruangan
                                    @else
                                        Nama Aset
                                    @endif
                                </dt>
                                <dd class="w-2/3">{{ $assetName }}</dd>
                            </div>

                            {{-- Pihak Terlibat --}}
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">
                                    @if (isset($inspection))
                                        Pemeriksa
                                    @elseif(isset($vehicleLog))
                                        Pengguna
                                    @elseif(isset($labLog))
                                        Guru / PJ
                                    @elseif(isset($assignment))
                                        Pegawai
                                    @elseif(isset($asset))
                                        Penanggung Jawab
                                    @else
                                        Pihak Terlibat
                                    @endif
                                </dt>
                                <dd class="w-2/3">{{ $employeeName }}</dd>
                            </div>

                            {{-- Tanggal --}}
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Tanggal</dt>
                                <dd class="w-2/3">
                                    {{ \Carbon\Carbon::parse($transactionDate)->isoFormat('D MMMM YYYY') }}</dd>
                            </div>

                            {{-- DETAIL KHUSUS: LAB --}}
                            @if (isset($labLog))
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">Kelas</dt>
                                    <dd class="w-2/3">{{ $labLog->class_group }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">Kegiatan</dt>
                                    <dd class="w-2/3">{{ $labLog->activity_description }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">Waktu
                                        {{ $isReturn ? 'Selesai' : 'Masuk' }}</dt>
                                    <dd class="w-2/3">
                                        {{ $isReturn ? $labLog->check_out_time->format('H:i') : $labLog->check_in_time->format('H:i') }}
                                        WIB
                                    </dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">Kondisi
                                        {{ $isReturn ? 'Akhir' : 'Awal' }}</dt>
                                    <dd class="w-2/3">
                                        {{ $isReturn ? $labLog->condition_after : $labLog->condition_before }}</dd>
                                </div>

                                {{-- DETAIL KHUSUS: KENDARAAN --}}
                            @elseif(isset($vehicleLog))
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">Tujuan</dt>
                                    <dd class="w-2/3">{{ $vehicleLog->destination }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">Keperluan</dt>
                                    <dd class="w-2/3">{{ $vehicleLog->purpose }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">KM {{ $isReturn ? 'Akhir' : 'Awal' }}
                                    </dt>
                                    <dd class="w-2/3">
                                        {{ number_format($isReturn ? $vehicleLog->end_odometer : $vehicleLog->start_odometer) }}
                                        KM</dd>
                                </div>

                                {{-- DETAIL KHUSUS: INSPEKSI --}}
                            @elseif(isset($inspection))
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">Kondisi Tercatat</dt>
                                    <dd class="w-2/3">{{ $inspection->condition }}</dd>
                                </div>

                                {{-- DETAIL KHUSUS: PEMINJAMAN ASET BIASA --}}
                            @elseif(isset($assignment))
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">Kondisi
                                        {{ $isReturn ? 'Kembali' : 'Pinjam' }}</dt>
                                    <dd class="w-2/3">
                                        {{ $isReturn ? $assignment->condition_on_return : $assignment->condition_on_assign }}
                                    </dd>
                                </div>

                                {{-- DETAIL KHUSUS: DISPOSAL --}}
                            @elseif(isset($asset))
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">Metode Disposal</dt>
                                    <dd class="w-2/3">{{ $asset->disposal_method }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 font-semibold text-gray-500">Alasan Disposal</dt>
                                    <dd class="w-2/3">{{ $asset->disposal_reason }}</dd>
                                </div>
                                @if ($asset->disposal_method == 'Dijual')
                                    <div class="flex">
                                        <dt class="w-1/3 font-semibold text-gray-500">Nilai Jual</dt>
                                        <dd class="w-2/3">Rp
                                            {{ number_format($asset->disposal_value ?? 0, 0, ',', '.') }}</dd>
                                    </div>
                                @endif
                                {{-- === AKHIR TAMBAHAN === --}}
                            @endif

                        </dl>
                    </div>
                </div>
            </div>
            <div class="text-center mt-6 text-sm text-gray-500">
                Powered by {{ config('app.name', 'Laravel') }}
            </div>
        </div>
    </div>
</x-guest-layout>
