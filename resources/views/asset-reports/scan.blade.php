<x-app-layout>
    <div class="space-y-8 pb-12">
        <!-- Header Section -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-500 to-teal-700 p-8 shadow-2xl animate-fadeIn">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold text-white mb-2">{{ __('Scan QR Label Aset') }}</h2>
                <p class="text-emerald-100 opacity-90">Arahkan kamera ke QR Code yang tertempel pada label aset.</p>
            </div>
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-8 animate-slideUp">
                <div id="reader" class="rounded-2xl overflow-hidden border-4 border-gray-100 dark:border-gray-800 mb-6"></div>
                
                <div class="flex flex-col items-center space-y-4">
                    <div id="result" class="hidden w-full p-4 bg-emerald-50 text-emerald-600 rounded-2xl text-center font-bold animate-pulse">
                        Aset Ditemukan! Mengalihkan...
                    </div>
                    
                    <a href="{{ route('asset-reports.create') }}" class="text-gray-500 hover:text-emerald-600 font-medium transition-colors">
                        Gunakan Input Manual Jika Kamera Bermasalah
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Kita asumsikan QR Code berisi URL publik aset, misalnya http://yoursite.com/aset/ASET-123
            // Atau bisa juga langsung berisi kode asetnya ASET-123
            
            let assetCode = "";
            
            if (decodedText.includes('/aset/')) {
                // Ekstrak kode dari URL
                assetCode = decodedText.split('/aset/').pop();
            } else {
                // Gunakan teks langsung jika bukan URL
                assetCode = decodedText;
            }

            document.getElementById('result').classList.remove('hidden');
            
            // Redirect ke halaman buat laporan dengan parameter asset_code
            setTimeout(() => {
                window.location.href = `{{ route('asset-reports.create') }}?asset_code=${assetCode}`;
            }, 1000);
            
            html5QrcodeScanner.clear();
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: {width: 250, height: 250} }, /* verbose= */ false);
        html5QrcodeScanner.render(onScanSuccess);
    </script>
    @endpush
</x-app-layout>
