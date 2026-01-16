<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Aset</title>
    @vite(['resources/css/app.css'])
    @php
        $style = request('style', 'small');
        $codeType = request('code_type', 'qr');
        $template = request('template', 'classic'); // classic, modern, minimal
    @endphp
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --label-border: #e2e8f0;
            --label-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --accent: #3b82f6;
            --accent-dark: #2563eb;
        }

        /* Langkah 1: Set ukuran kertas */
        @page {
            @if($style == 'a4')
                size: 210mm 297mm;
            @else
                size: 190mm 134mm;
            @endif
            margin: 0;
        }

        /* Langkah 2: Print helpers */
        @media print {
            body {
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .sheet {
                box-shadow: none !important;
                margin: 0 !important;
                border: none !important;
            }
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            background-color: #f8fafc;
        }

        /* Langkah 3: Wrapper lembar */
        .sheet {
            display: grid;
            background: white;
            page-break-after: always;
            margin: 20px auto;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            border: 1px solid #e5e7eb;
            box-sizing: border-box;
            @if($style == 'a4')
                width: 210mm;
                height: 297mm;
                padding: 10mm 10mm;
                grid-template-columns: repeat(2, 95mm);
                grid-auto-rows: 46mm;
                column-gap: 0mm;
                row-gap: 0mm;
            @else
                width: 190mm;
                height: 134mm;
                padding: 4mm 2mm;
                grid-template-columns: repeat(3, 60mm);
                grid-auto-rows: 30mm;
                column-gap: 3mm;
                row-gap: 2mm;
            @endif
        }

        /* Langkah 4: Kotak label */
        .label-container {
            box-sizing: border-box;
            border: 1px solid var(--label-border);
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: var(--label-bg);
            @if($style == 'a4')
                width: 95mm;
                height: 46mm;
                padding: 4mm 6mm;
            @else
                width: 60mm;
                height: 30mm;
                padding: 2mm;
            @endif
        }

        /* --- TEMPLATE: CLASSIC --- */
        .template-classic::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, #ef4444, #dc2626, #991b1b);
        }

        /* --- TEMPLATE: MODERN --- */
        .template-modern {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .template-modern .label-header {
            background: #f8fafc;
            margin: -6mm -6mm 4mm -6mm;
            padding: 4mm 6mm;
            border-bottom: 1px solid #e2e8f0;
            @if($style != 'a4')
                margin: -2mm -2mm 2mm -2mm;
                padding: 1.5mm 2mm;
            @endif
        }
        .template-modern .asset-name {
            font-family: 'Outfit', sans-serif;
            color: #0f172a;
            @if($style == 'a4') font-size: 11pt; @endif
        }

        /* --- TEMPLATE: MINIMAL --- */
        .template-minimal {
            border: 1px solid #f1f5f9;
        }
        .template-minimal .label-header {
            border-bottom: none;
            margin-bottom: 2mm;
        }
        .template-minimal .code-zone {
            background: transparent;
            border: none;
            padding: 0;
        }
        .template-minimal .label-footer {
            border-top: 1px dashed #e2e8f0;
        }

        /* Common Elements */
        .label-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 2mm;
            margin-bottom: 3mm;
        }

        .inst-info {
            flex: 1;
        }

        .inst-label {
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: var(--text-muted);
            margin: 0;
            @if($style == 'a4') font-size: 7.5pt; @else font-size: 5pt; @endif
        }

        .inst-name {
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            line-height: 1.2;
            @if($style == 'a4') font-size: 11pt; @else font-size: 8pt; @endif
        }

        .label-body {
            display: flex;
            gap: 4mm;
            flex: 1;
            align-items: center;
        }

        .code-zone {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            padding: 2mm;
            border-radius: 4px;
            border: 1px solid #f1f5f9;
        }

        .asset-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .asset-name {
            font-weight: 700;
            color: var(--accent);
            margin: 0 0 1mm 0;
            line-height: 1.1;
            @if($style == 'a4') font-size: 11pt; @else font-size: 8pt; @endif
        }

        .detail-row {
            display: flex;
            margin-bottom: 0.5mm;
            @if($style == 'a4') font-size: 8.5pt; @else font-size: 6pt; @endif
        }

        .detail-label {
            color: var(--text-muted);
            width: 2.2cm;
            flex-shrink: 0;
            @if($style != 'a4') width: 1.5cm; @endif
        }

        .detail-val {
            font-weight: 500;
            color: var(--text-main);
        }

        .label-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 2mm;
            border-top: 1px solid #f1f5f9;
        }

        .asset-code-text {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            background: #f1f5f9;
            padding: 1mm 2mm;
            border-radius: 3px;
            color: var(--text-main);
            @if($style == 'a4') font-size: 10pt; @else font-size: 6pt; @endif
        }

        .warning-text {
            font-weight: 700;
            text-transform: uppercase;
            color: #ef4444;
            letter-spacing: 0.02em;
            @if($style == 'a4') font-size: 7pt; @else font-size: 4.5pt; @endif
        }

        .barcode-wrapper svg {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body class="bg-slate-50">

    <!-- Toolbar non-cetak -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 no-print">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-1">
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Konfigurasi Label Aset</h1>
                        <p class="text-sm text-slate-500 font-medium">Kustomisasi desain dan format label sebelum mencetak.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-blue-500/30 transition-all inline-flex items-center transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"></path></svg>
                            Cetak Label
                        </button>
                        <a href="{{ route('assets.index') }}"
                            class="bg-white hover:bg-slate-50 text-slate-600 font-bold py-3 px-6 rounded-xl border border-slate-200 transition-all shadow-sm">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Pilihan Ukuran -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Ukuran Kertas</label>
                        <div class="flex p-1.5 bg-slate-100 rounded-2xl w-full">
                            <a href="{{ request()->fullUrlWithQuery(['style' => 'small']) }}" 
                               class="flex-1 text-center py-2.5 rounded-xl text-xs font-black transition-all {{ $style == 'small' ? 'bg-white text-blue-600 shadow-md' : 'text-slate-500 hover:text-slate-700' }}">
                                CUSTOM (19x13.4cm)
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['style' => 'a4']) }}" 
                               class="flex-1 text-center py-2.5 rounded-xl text-xs font-black transition-all {{ $style == 'a4' ? 'bg-white text-blue-600 shadow-md' : 'text-slate-500 hover:text-slate-700' }}">
                                STANDAR A4 (12 Label)
                            </a>
                        </div>
                    </div>

                    <!-- Pilihan Desain -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Pilihan Desain</label>
                        <div class="flex p-1.5 bg-slate-100 rounded-2xl w-full">
                            <a href="{{ request()->fullUrlWithQuery(['template' => 'classic']) }}" 
                               class="flex-1 text-center py-2.5 rounded-xl text-xs font-black transition-all {{ $template == 'classic' ? 'bg-white text-blue-600 shadow-md' : 'text-slate-500 hover:text-slate-700' }}">
                                CLASSIC
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['template' => 'modern']) }}" 
                               class="flex-1 text-center py-2.5 rounded-xl text-xs font-black transition-all {{ $template == 'modern' ? 'bg-white text-blue-600 shadow-md' : 'text-slate-500 hover:text-slate-700' }}">
                                MODERN
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['template' => 'minimal']) }}" 
                               class="flex-1 text-center py-2.5 rounded-xl text-xs font-black transition-all {{ $template == 'minimal' ? 'bg-white text-blue-600 shadow-md' : 'text-slate-500 hover:text-slate-700' }}">
                                MINIMAL
                            </a>
                        </div>
                    </div>

                    <!-- Pilihan Kode -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Format Kode</label>
                        <div class="flex p-1.5 bg-slate-100 rounded-2xl w-full">
                            <a href="{{ request()->fullUrlWithQuery(['code_type' => 'qr']) }}" 
                               class="flex-1 text-center py-2.5 rounded-xl text-xs font-black transition-all {{ $codeType == 'qr' ? 'bg-white text-blue-600 shadow-md' : 'text-slate-500 hover:text-slate-700' }}">
                                QR CODE
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['code_type' => 'barcode']) }}" 
                               class="flex-1 text-center py-2.5 rounded-xl text-xs font-black transition-all {{ $codeType == 'barcode' ? 'bg-white text-blue-600 shadow-md' : 'text-slate-500 hover:text-slate-700' }}">
                                BARCODE
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="text-sm">
                            <p class="font-black text-blue-900 uppercase tracking-wider mb-1">Panduan Cetak Presisi</p>
                            <p class="text-blue-700/80 font-medium leading-relaxed">Gunakan browser Chrome/Edge, setel <strong>Scale: 100%</strong> dan <strong>Margins: None</strong>. Pastikan ukuran kertas di dialog cetak sesuai dengan pilihan Anda (A4 atau Custom).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $perPage = ($style == 'a4') ? 12 : 12;
        $generator = new Picqer\Barcode\BarcodeGeneratorSVG();
        $logo = \App\Models\Setting::where('key', 'logo')->first()?->value;
    @endphp

    @foreach ($assets->chunk($perPage) as $chunk)
        <div class="sheet">
            @foreach ($chunk as $asset)
                <div class="label-container template-{{ $template }}">
                    <div class="label-header">
                        <div class="flex items-center gap-3">
                            @if($logo)
                                <img src="{{ asset('storage/' . $logo) }}" class="h-8 md:h-10 w-auto object-contain">
                            @endif
                            <div class="inst-info">
                                <p class="inst-label">Property Of</p>
                                <p class="inst-name">{{ $asset->institution->name }}</p>
                            </div>
                        </div>
                        <div class="warning-text">Security Sealed</div>
                    </div>

                    <div class="label-body">
                        <div class="code-zone">
                            @if ($codeType == 'qr')
                                @php
                                    $publicDomain = 'https://sarpra.smktelkom-lpg.id';
                                    $relativePath = route('public.assets.show', $asset->asset_code_ypt, false);
                                    $fullPublicUrl = $publicDomain . $relativePath;
                                @endphp
                                {!! QrCode::size($style == 'a4' ? 85 : 55)->generate($fullPublicUrl) !!}
                            @else
                                <div class="barcode-wrapper">
                                    {!! $generator->getBarcode($asset->asset_code_ypt, $generator::TYPE_CODE_128, $style == 'a4' ? 1.5 : 1, $style == 'a4' ? 50 : 30) !!}
                                </div>
                            @endif
                        </div>
                        
                        <div class="asset-details">
                            <h2 class="asset-name">{{ Str::limit($asset->name, 40) }}</h2>
                            <div class="detail-row">
                                <span class="detail-label">Tahun Reg.</span>
                                <span class="detail-val">{{ $asset->purchase_year }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Sumber Dana</span>
                                <span class="detail-val">{{ Str::limit($asset->fundingSource->name ?? '-', 15) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="label-footer">
                        <div class="asset-code-text">{{ $asset->asset_code_ypt }}</div>
                        <div class="warning-text">Don't Remove</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

</body>

</html>
