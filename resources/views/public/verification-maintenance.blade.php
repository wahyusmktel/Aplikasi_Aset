<x-guest-layout>
    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100 text-center">
                    <div class="flex justify-center mb-4">
                        <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold">Dokumen Maintenance Terverifikasi</h2>
                    <p class="text-gray-500 mt-1">Catatan perbaikan/perawatan ini sah dan tercatat.</p>

                    <div class="mt-6 text-left border-t pt-6 dark:border-gray-700">
                        <h3 class="font-bold text-lg mb-2">Detail Pekerjaan</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Nomor Dokumen</dt>
                                <dd class="w-2/3 font-mono">{{ $maintenance->doc_number }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Nama Aset</dt>
                                <dd class="w-2/3">{{ $maintenance->asset->name }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Tanggal</dt>
                                <dd class="w-2/3">
                                    {{ \Carbon\Carbon::parse($maintenance->maintenance_date)->isoFormat('D MMMM YYYY') }}
                                </dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Jenis</dt>
                                <dd class="w-2/3">{{ $maintenance->type }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Deskripsi</dt>
                                <dd class="w-2/3">{{ $maintenance->description }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Teknisi</dt>
                                <dd class="w-2/3">{{ $maintenance->technician ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
