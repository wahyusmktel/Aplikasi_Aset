<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Detail Aset - {{ $asset->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.4;
            padding: 40px 50px;
        }

        /* ===== KOP SURAT ===== */
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 16px; }
        .kop img { width: 100%; max-height: 100px; object-fit: contain; }
        .kop h1 { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .kop h2 { font-size: 13px; font-weight: normal; }
        .kop p  { font-size: 11px; }

        /* ===== JUDUL DOKUMEN ===== */
        .doc-title {
            text-align: center;
            margin-bottom: 16px;
        }
        .doc-title h3 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 1px;
        }
        .doc-title p { font-size: 11px; margin-top: 4px; color: #555; }

        /* ===== SECTION ===== */
        .section { margin-bottom: 20px; }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #222;
            color: #fff;
            padding: 5px 10px;
            margin-bottom: 0;
            letter-spacing: 0.5px;
        }

        /* ===== TABLE ===== */
        table.detail {
            width: 100%;
            border-collapse: collapse;
        }
        table.detail td {
            border: 1px solid #999;
            padding: 5px 8px;
            vertical-align: top;
        }
        table.detail td.label {
            width: 35%;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        table.detail td.value { width: 65%; }

        /* ===== STATUS BADGE ===== */
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-green  { background:#dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-blue   { background:#dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-red    { background:#fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-gray   { background:#f3f4f6; color: #374151; border: 1px solid #d1d5db; }
        .badge-purple { background:#f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }

        /* ===== FOOTER / TANDA TANGAN ===== */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .footer-table td {
            border: none;
            text-align: center;
            vertical-align: top;
            padding: 0 8px;
        }
        .footer-table td.qr-col {
            width: 25%;
            text-align: center;
            vertical-align: middle;
        }
        .footer-table td.sign-col {
            width: 37.5%;
            text-align: center;
        }
        .sign-placeholder {
            height: 60px;
        }
        .sign-name {
            border-top: 1px solid #333;
            padding-top: 4px;
            font-weight: bold;
            font-size: 11px;
        }

        /* ===== PRINT META ===== */
        .print-meta {
            font-size: 9px; color: #aaa;
            text-align: right;
            margin-top: 16px;
            border-top: 1px solid #eee;
            padding-top: 6px;
        }

        /* ===== RIWAYAT TABLE ===== */
        table.history {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.history th {
            background-color: #374151;
            color: #fff;
            padding: 5px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.history td {
            border: 1px solid #d1d5db;
            padding: 5px 8px;
            vertical-align: top;
        }
        table.history tr:nth-child(even) td { background-color: #f9fafb; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="kop">
        @php $kop = \App\Models\Setting::get('app_kop_surat'); @endphp
        @if($kop)
            <img src="{{ public_path('storage/' . $kop) }}" alt="Kop Surat">
        @else
            <h1>SMK Telkom Lampung</h1>
            <h2>Sistem Manajemen Aset</h2>
            <p>Jl. HOS Cokroaminoto No.72, Bandar Lampung</p>
        @endif
    </div>

    {{-- JUDUL --}}
    <div class="doc-title">
        <h3>Profil Detail Aset</h3>
        <p>Dokumen ini dicetak secara resmi dari Sistem Manajemen Aset StellaLog's</p>
    </div>

    {{-- SEKSI 1: IDENTITAS ASET --}}
    <div class="section">
        <div class="section-title">1. Identitas Aset</div>
        <table class="detail">
            <tr>
                <td class="label">Nama Aset</td>
                <td class="value"><strong>{{ $asset->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">Kode Aset YPT</td>
                <td class="value"><strong>{{ $asset->asset_code_ypt }}</strong></td>
            </tr>
            <tr>
                <td class="label">Kategori</td>
                <td class="value">{{ $asset->category->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tahun Perolehan</td>
                <td class="value">{{ $asset->purchase_year }}</td>
            </tr>
            <tr>
                <td class="label">Status Aset</td>
                <td class="value">
                    @php
                        $statusMap = [
                            'Tersedia'   => 'badge-green',
                            'Dipinjam'   => 'badge-blue',
                            'Rusak'      => 'badge-red',
                            'Maintenance'=> 'badge-purple',
                        ];
                        $cls = $statusMap[$asset->current_status] ?? 'badge-gray';
                    @endphp
                    <span class="badge {{ $cls }}">{{ $asset->current_status ?? $asset->status ?? '-' }}</span>
                    @if($asset->disposal_date)
                        <span class="badge badge-purple" style="margin-left:6px;">DISPOSE: {{ \Carbon\Carbon::parse($asset->disposal_date)->isoFormat('D MMM YYYY') }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Fungsi Barang</td>
                <td class="value">{{ $asset->assetFunction->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Sumber Dana</td>
                <td class="value">{{ $asset->fundingSource->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- SEKSI 2: LOKASI & PENEMPATAN --}}
    <div class="section">
        <div class="section-title">2. Lokasi & Penempatan</div>
        <table class="detail">
            <tr>
                <td class="label">Lembaga</td>
                <td class="value">{{ $asset->institution->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Gedung</td>
                <td class="value">{{ $asset->building->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Ruangan</td>
                <td class="value">{{ $asset->room->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Fakultas / Unit</td>
                <td class="value">{{ $asset->faculty->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Prodi / Departemen</td>
                <td class="value">{{ $asset->department->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Penanggung Jawab</td>
                <td class="value">{{ $asset->personInCharge->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- SEKSI 3: INFORMASI FINANSIAL --}}
    <div class="section">
        <div class="section-title">3. Informasi Finansial</div>
        <table class="detail">
            <tr>
                <td class="label">Harga Perolehan</td>
                <td class="value">Rp {{ number_format($asset->purchase_cost, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Masa Manfaat</td>
                <td class="value">{{ $asset->useful_life ?? '-' }} Tahun</td>
            </tr>
            <tr>
                <td class="label">Nilai Sisa (Salvage)</td>
                <td class="value">Rp {{ number_format($asset->salvage_value ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Nilai Buku Saat Ini</td>
                <td class="value"><strong>Rp {{ number_format($asset->book_value ?? 0, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- SEKSI 4: RIWAYAT PENUGASAN --}}
    @if($assignments->isNotEmpty())
    <div class="section">
        <div class="section-title">4. Riwayat Serah Terima (5 Terakhir)</div>
        <table class="history">
            <thead>
                <tr>
                    <th>Pegawai</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Kondisi</th>
                    <th>No. Dokumen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $a)
                <tr>
                    <td>{{ $a->employee->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($a->assigned_date)->isoFormat('D MMM YYYY') }}</td>
                    <td>{{ $a->returned_date ? \Carbon\Carbon::parse($a->returned_date)->isoFormat('D MMM YYYY') : 'Aktif' }}</td>
                    <td>{{ $a->condition_on_assign }}</td>
                    <td style="font-size:9px;">{{ $a->checkout_doc_number ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- SEKSI 5: RIWAYAT MAINTENANCE --}}
    @if($maintenances->isNotEmpty())
    <div class="section">
        <div class="section-title">5. Riwayat Maintenance (5 Terakhir)</div>
        <table class="history">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Deskripsi</th>
                    <th>Biaya</th>
                    <th>Teknisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maintenances as $m)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($m->maintenance_date)->isoFormat('D MMM YYYY') }}</td>
                    <td>{{ $m->type }}</td>
                    <td>{{ $m->description }}</td>
                    <td>Rp {{ number_format($m->cost ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $m->technician ?? 'Staf Internal' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- SEKSI 6: RIWAYAT INSPEKSI --}}
    @if($inspections->isNotEmpty())
    <div class="section">
        <div class="section-title">6. Riwayat Inspeksi Kondisi (5 Terakhir)</div>
        <table class="history">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kondisi</th>
                    <th>Catatan</th>
                    <th>No. Dokumen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inspections as $i)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($i->inspection_date)->isoFormat('D MMM YYYY') }}</td>
                    <td>{{ $i->condition }}</td>
                    <td>{{ $i->notes ?? '-' }}</td>
                    <td style="font-size:9px;">{{ $i->inspection_doc_number ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- TANDA TANGAN (menggunakan table agar DomPDF mendukung horizontal layout) --}}
    <table class="footer-table">
        <tr>
            <td class="qr-col">
                <img src="{{ $qrCode }}" width="80" height="80"><br>
                <span style="font-size:9px;color:#888;">Scan QR untuk verifikasi</span><br>
                <span style="font-size:8px;color:#aaa;">{{ $asset->asset_code_ypt }}</span>
            </td>
            <td class="sign-col">
                <p style="margin-bottom:4px;">Bandar Lampung, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</p>
                <p style="margin-bottom:4px;">Mengetahui,<br><strong>Kepala Sekolah</strong></p>
                <div class="sign-placeholder"></div>
                <div class="sign-name">{{ $headmaster->name ?? '(Kepala Sekolah)' }}</div>
            </td>
            <td class="sign-col">
                <p style="margin-bottom:4px;">&nbsp;</p>
                <p style="margin-bottom:4px;">Dicetak oleh,<br><strong>Kaur. Sarana &amp; Prasarana</strong></p>
                <div class="sign-placeholder"></div>
                <div class="sign-name">{{ $kaur->name ?? '(Kaur Sarpras)' }}</div>
            </td>
        </tr>
    </table>

    <div class="print-meta">
        Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB
        &nbsp;|&nbsp; Sistem Manajemen Aset StellaLog's &nbsp;|&nbsp; SMK Telkom Lampung
    </div>

</body>
</html>
