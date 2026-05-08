<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen Digital — SMK Telkom Lampung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }

        .drop-zone {
            border: 2.5px dashed #c7d2fe;
            transition: border-color .2s, background .2s;
        }
        .drop-zone.drag-over {
            border-color: #6366f1;
            background-color: #eef2ff;
        }

        @keyframes spin-slow { to { transform: rotate(360deg); } }
        .spin-slow { animation: spin-slow 2s linear infinite; }

        @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fade-in .35s ease both; }

        .result-card { animation: fade-in .3s ease both; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white">

    {{-- Header --}}
    <header class="border-b border-white/10 bg-white/5 backdrop-blur-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-white text-sm leading-tight">Verifikasi Dokumen Digital</p>
                    <p class="text-xs text-indigo-300">SMK Telkom Lampung — Sistem SARPRA</p>
                </div>
            </div>
            <a href="/" class="text-xs text-indigo-300 hover:text-white transition font-medium">← Kembali</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 space-y-8">

        {{-- Hero --}}
        <div class="text-center space-y-3">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold mb-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span>
                </span>
                Layanan Verifikasi Aktif
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                Verifikasi Keaslian Dokumen
            </h1>
            <p class="text-indigo-200 text-base max-w-xl mx-auto leading-relaxed">
                Unggah dokumen PDF yang ingin diverifikasi. Sistem akan mendeteksi QR code tanda tangan digital
                dan memvalidasi keaslian setiap penandatangan secara otomatis.
            </p>
        </div>

        {{-- Upload Zone --}}
        <div id="uploadSection" class="bg-white/5 border border-white/10 rounded-3xl p-6 sm:p-8 backdrop-blur-sm">
            <div id="dropZone" class="drop-zone rounded-2xl p-8 sm:p-12 text-center cursor-pointer bg-white/3"
                 onclick="document.getElementById('fileInput').click()"
                 ondragover="handleDragOver(event)"
                 ondragleave="handleDragLeave(event)"
                 ondrop="handleDrop(event)">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-white text-lg">Seret & letakkan file PDF di sini</p>
                        <p class="text-indigo-300 text-sm mt-1">atau klik untuk memilih file</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-indigo-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                        Format: PDF · Maks. 20 MB
                    </div>
                </div>
            </div>
            <input type="file" id="fileInput" accept=".pdf" class="hidden" onchange="handleFileSelect(event)">
        </div>

        {{-- Progress --}}
        <div id="progressSection" class="hidden bg-white/5 border border-white/10 rounded-3xl p-6 sm:p-8 backdrop-blur-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center flex-shrink-0">
                    <svg id="progressIcon" class="w-5 h-5 text-indigo-300 spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-white text-sm" id="progressTitle">Memuat dokumen...</p>
                    <p class="text-indigo-300 text-xs mt-0.5" id="progressDetail">Mohon tunggu</p>
                </div>
            </div>
            <div class="w-full bg-white/10 rounded-full h-2">
                <div id="progressBar" class="bg-indigo-400 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        {{-- Results --}}
        <div id="resultsSection" class="hidden space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-white text-xl" id="resultsTitle">Hasil Verifikasi</h2>
                <button onclick="resetVerifier()"
                    class="text-xs font-semibold text-indigo-300 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Verifikasi Dokumen Lain
                </button>
            </div>

            {{-- Summary --}}
            <div id="resultsSummary" class="grid grid-cols-3 gap-3"></div>

            {{-- Detail cards --}}
            <div id="resultsCards" class="space-y-3"></div>

            {{-- No QR found --}}
            <div id="noQrFound" class="hidden bg-amber-500/10 border border-amber-400/30 rounded-2xl p-6 text-center">
                <svg class="w-12 h-12 text-amber-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="font-bold text-amber-300 text-base">Tidak ditemukan QR code tanda tangan digital</p>
                <p class="text-amber-400/80 text-sm mt-1">
                    Dokumen ini tidak memiliki QR code tanda tangan dari sistem SARPRA,
                    atau QR code terlalu kecil untuk terdeteksi.
                </p>
            </div>
        </div>

        {{-- Cara Kerja --}}
        <div class="bg-white/5 border border-white/10 rounded-3xl p-6 sm:p-8 backdrop-blur-sm">
            <h3 class="font-bold text-white text-base mb-5">Cara Kerja Verifikasi</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach([
                    ['01', 'Unggah Dokumen', 'Pilih atau seret file PDF dokumen BAST yang ingin diverifikasi', 'text-blue-300', 'bg-blue-500/20 border-blue-400/30'],
                    ['02', 'Deteksi QR Code', 'Sistem memindai setiap halaman PDF untuk menemukan QR code tanda tangan digital', 'text-violet-300', 'bg-violet-500/20 border-violet-400/30'],
                    ['03', 'Validasi Keaslian', 'Setiap QR diverifikasi ke database — nama penandatangan, waktu, dan integritas hash kriptografi ditampilkan', 'text-emerald-300', 'bg-emerald-500/20 border-emerald-400/30'],
                ] as [$num, $title, $desc, $textColor, $bgBorder])
                <div class="flex items-start gap-3 p-4 rounded-2xl {{ $bgBorder }} border">
                    <span class="text-2xl font-black {{ $textColor }} leading-none mt-0.5">{{ $num }}</span>
                    <div>
                        <p class="font-bold text-white text-sm">{{ $title }}</p>
                        <p class="text-xs text-white/60 mt-1 leading-relaxed">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </main>

    <footer class="border-t border-white/10 py-6 text-center">
        <p class="text-xs text-white/30">
            Sistem Verifikasi Tanda Tangan Digital · SMK Telkom Lampung · SARPRA v2
        </p>
    </footer>

    {{-- pdf.js + jsQR via CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <script>
        const BASE_URL = '{{ url('') }}';
        const VERIFY_URL = BASE_URL + '/verify/signature/';

        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // ── Drag & Drop ──────────────────────────────────────────────────────────
        function handleDragOver(e) {
            e.preventDefault();
            document.getElementById('dropZone').classList.add('drag-over');
        }
        function handleDragLeave(e) {
            document.getElementById('dropZone').classList.remove('drag-over');
        }
        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('dropZone').classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file && file.type === 'application/pdf') processFile(file);
            else alert('Hanya file PDF yang diterima.');
        }
        function handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) processFile(file);
        }

        // ── Reset ────────────────────────────────────────────────────────────────
        function resetVerifier() {
            document.getElementById('uploadSection').classList.remove('hidden');
            document.getElementById('progressSection').classList.add('hidden');
            document.getElementById('resultsSection').classList.add('hidden');
            document.getElementById('fileInput').value = '';
        }

        // ── Progress helpers ─────────────────────────────────────────────────────
        function setProgress(title, detail, pct) {
            document.getElementById('progressTitle').textContent  = title;
            document.getElementById('progressDetail').textContent = detail;
            document.getElementById('progressBar').style.width    = pct + '%';
        }

        // ── Scan canvas for ALL QR codes (mask-and-rescan) ───────────────────────
        function scanAllQR(canvas) {
            const ctx     = canvas.getContext('2d');
            const results = [];
            const MAX     = 8;

            for (let i = 0; i < MAX; i++) {
                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const qr = jsQR(imgData.data, imgData.width, imgData.height, { inversionAttempts: 'dontInvert' });
                if (!qr) break;

                results.push(qr.data);

                // Mask the found QR region with white to expose the next one
                const pts  = [qr.location.topLeftCorner, qr.location.topRightCorner,
                               qr.location.bottomRightCorner, qr.location.bottomLeftCorner];
                const xs   = pts.map(p => p.x);
                const ys   = pts.map(p => p.y);
                const pad  = 15;
                const x    = Math.max(0, Math.min(...xs) - pad);
                const y    = Math.max(0, Math.min(...ys) - pad);
                const w    = Math.min(canvas.width  - x, Math.max(...xs) - Math.min(...xs) + pad * 2);
                const h    = Math.min(canvas.height - y, Math.max(...ys) - Math.min(...ys) + pad * 2);
                ctx.fillStyle = 'white';
                ctx.fillRect(x, y, w, h);
            }
            return results;
        }

        // ── Main process ─────────────────────────────────────────────────────────
        async function processFile(file) {
            document.getElementById('uploadSection').classList.add('hidden');
            document.getElementById('progressSection').classList.remove('hidden');
            document.getElementById('resultsSection').classList.add('hidden');

            setProgress('Memuat dokumen PDF...', 'Membaca file...', 5);

            let pdf;
            try {
                const arrayBuffer = await file.arrayBuffer();
                pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            } catch (err) {
                alert('Gagal membaca PDF: ' + err.message);
                resetVerifier();
                return;
            }

            const totalPages = pdf.numPages;
            const foundTokens = new Set();
            const signaturePattern = /\/verify\/signature\/([0-9a-f-]{36})/i;

            for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                const pct = Math.round(5 + (pageNum / totalPages) * 60);
                setProgress(`Memindai halaman ${pageNum} dari ${totalPages}...`, 'Mencari QR code tanda tangan...', pct);

                const page     = await pdf.getPage(pageNum);
                const viewport = page.getViewport({ scale: 3.0 }); // scale tinggi untuk deteksi lebih akurat

                const canvas    = document.createElement('canvas');
                canvas.width    = viewport.width;
                canvas.height   = viewport.height;
                const ctx       = canvas.getContext('2d');

                await page.render({ canvasContext: ctx, viewport }).promise;

                const qrTexts = scanAllQR(canvas);
                for (const text of qrTexts) {
                    const m = text.match(signaturePattern);
                    if (m) foundTokens.add(m[1]);
                }
            }

            setProgress('Memverifikasi tanda tangan...', `Ditemukan ${foundTokens.size} QR kode valid`, 70);

            // Verify each token against server
            const verifiedResults = [];
            let idx = 0;
            for (const token of foundTokens) {
                idx++;
                setProgress('Memverifikasi tanda tangan...', `Memvalidasi token ${idx} dari ${foundTokens.size}`, 70 + (idx / foundTokens.size) * 28);
                try {
                    const res = await fetch(VERIFY_URL + token, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    verifiedResults.push({ token, ...data });
                } catch {
                    verifiedResults.push({ token, found: false, valid: false, message: 'Gagal terhubung ke server.' });
                }
            }

            setProgress('Selesai!', 'Verifikasi selesai.', 100);
            setTimeout(() => showResults(verifiedResults, file.name), 400);
        }

        // ── Render Results ───────────────────────────────────────────────────────
        function showResults(results, filename) {
            document.getElementById('progressSection').classList.add('hidden');
            document.getElementById('resultsSection').classList.remove('hidden');
            document.getElementById('resultsTitle').textContent =
                `Hasil Verifikasi — ${filename}`;

            const summaryEl = document.getElementById('resultsSummary');
            const cardsEl   = document.getElementById('resultsCards');
            const noQrEl    = document.getElementById('noQrFound');

            summaryEl.innerHTML = '';
            cardsEl.innerHTML   = '';
            noQrEl.classList.add('hidden');

            if (results.length === 0) {
                noQrEl.classList.remove('hidden');
                buildSummary(summaryEl, 0, 0, 0);
                return;
            }

            const totalFound  = results.length;
            const validCount  = results.filter(r => r.valid).length;
            const revokedCount= results.filter(r => r.found && !r.valid && r.revoked_at).length;
            const invalidCount= totalFound - validCount - revokedCount;

            buildSummary(summaryEl, totalFound, validCount, revokedCount + invalidCount);

            results.forEach(r => {
                cardsEl.appendChild(buildCard(r));
            });
        }

        function buildSummary(el, total, valid, invalid) {
            el.innerHTML = `
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
                    <p class="text-3xl font-black text-white">${total}</p>
                    <p class="text-xs text-white/50 mt-1 font-medium">QR Ditemukan</p>
                </div>
                <div class="bg-emerald-500/10 border border-emerald-400/30 rounded-2xl p-4 text-center">
                    <p class="text-3xl font-black text-emerald-300">${valid}</p>
                    <p class="text-xs text-emerald-400/70 mt-1 font-medium">Tanda Tangan Sah</p>
                </div>
                <div class="bg-red-500/10 border border-red-400/30 rounded-2xl p-4 text-center">
                    <p class="text-3xl font-black text-red-300">${invalid}</p>
                    <p class="text-xs text-red-400/70 mt-1 font-medium">Tidak Sah / Dicabut</p>
                </div>
            `;
        }

        function buildCard(r) {
            const div = document.createElement('div');
            div.className = 'result-card';

            if (!r.found) {
                div.innerHTML = `
                <div class="bg-red-500/10 border border-red-400/30 rounded-2xl p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-red-500/20 border border-red-400/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-red-300 text-sm">Token Tidak Ditemukan</p>
                            <p class="text-xs text-red-400/70 mt-0.5 font-mono break-all">${r.token}</p>
                            <p class="text-xs text-red-400/60 mt-1">${r.message || 'Token ini tidak terdaftar dalam sistem.'}</p>
                        </div>
                    </div>
                </div>`;
                return div;
            }

            if (r.valid) {
                div.innerHTML = `
                <div class="bg-emerald-500/10 border border-emerald-400/30 rounded-2xl p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-bold text-emerald-300 text-sm">✓ Tanda Tangan Sah & Valid</p>
                                <span class="text-[10px] font-black px-2 py-0.5 bg-emerald-400/20 text-emerald-300 rounded-full uppercase tracking-widest">VERIFIED</span>
                            </div>
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div class="text-xs">
                                    <p class="text-white/40 font-medium uppercase tracking-wider text-[10px]">Penandatangan</p>
                                    <p class="text-white font-bold mt-0.5">${r.signer_name || '-'}</p>
                                </div>
                                <div class="text-xs">
                                    <p class="text-white/40 font-medium uppercase tracking-wider text-[10px]">Jabatan</p>
                                    <p class="text-white font-semibold mt-0.5">${r.signer_role || '-'}</p>
                                </div>
                                ${r.signer_nip ? `
                                <div class="text-xs">
                                    <p class="text-white/40 font-medium uppercase tracking-wider text-[10px]">NIP</p>
                                    <p class="text-white font-semibold mt-0.5 font-mono">${r.signer_nip}</p>
                                </div>` : ''}
                                <div class="text-xs">
                                    <p class="text-white/40 font-medium uppercase tracking-wider text-[10px]">Waktu Tanda Tangan</p>
                                    <p class="text-white font-semibold mt-0.5">${r.signed_at || '-'}</p>
                                </div>
                                <div class="text-xs sm:col-span-2">
                                    <p class="text-white/40 font-medium uppercase tracking-wider text-[10px]">Dokumen</p>
                                    <p class="text-white/80 font-medium mt-0.5">${r.document_title || '-'}</p>
                                </div>
                            </div>
                            <p class="text-[10px] text-emerald-400/50 mt-3 font-mono break-all">Token: ${r.token}</p>
                        </div>
                    </div>
                </div>`;
            } else {
                const isRevoked = r.revoked_at;
                div.innerHTML = `
                <div class="bg-red-500/10 border border-red-400/30 rounded-2xl p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-red-500/20 border border-red-400/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-bold text-red-300 text-sm">✗ Tanda Tangan ${isRevoked ? 'Dicabut' : 'Tidak Valid'}</p>
                                <span class="text-[10px] font-black px-2 py-0.5 bg-red-400/20 text-red-300 rounded-full uppercase tracking-widest">${isRevoked ? 'REVOKED' : 'INVALID'}</span>
                            </div>
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div class="text-xs">
                                    <p class="text-white/40 font-medium uppercase tracking-wider text-[10px]">Penandatangan</p>
                                    <p class="text-red-300 font-bold mt-0.5">${r.signer_name || '-'}</p>
                                </div>
                                ${isRevoked ? `
                                <div class="text-xs">
                                    <p class="text-white/40 font-medium uppercase tracking-wider text-[10px]">Dicabut Pada</p>
                                    <p class="text-red-300 font-semibold mt-0.5">${r.revoked_at}</p>
                                </div>
                                <div class="text-xs sm:col-span-2">
                                    <p class="text-white/40 font-medium uppercase tracking-wider text-[10px]">Alasan Pencabutan</p>
                                    <p class="text-red-300/80 font-medium mt-0.5">${r.revoke_reason || '-'}</p>
                                </div>` : ''}
                            </div>
                            <p class="text-[10px] text-red-400/50 mt-3 font-mono break-all">Token: ${r.token}</p>
                        </div>
                    </div>
                </div>`;
            }

            return div;
        }
    </script>
</body>
</html>
