<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .container { padding: 10px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .doc-number { font-size: 12px; margin-top: 5px; }
        .content p { text-align: justify; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table, th, td { border: 1px solid #000; }
        th { background-color: #f2f2f2; padding: 8px; text-align: left; font-weight: bold; }
        td { padding: 8px; vertical-align: top; }
        .signatures { margin-top: 30px; width: 100%; }
        .sign-table { width: 100%; border: none; }
        .sign-table td { border: none; text-align: center; width: 50%; padding-top: 20px; }
        .sign-space { height: 60px; }
        .footer { margin-top: 40px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Berita Acara Serah Terima (BAST)</h1>
            <div class="doc-number">Nomor: {{ $handover->document_number }}</div>
        </div>

        <div class="content">
            <p>Pada hari ini <strong>{{ $handover->handover_date->isoFormat('dddd') }}</strong>, tanggal <strong>{{ $handover->handover_date->isoFormat('D MMMM Y') }}</strong>, kami yang bertanda tangan di bawah ini:</p>
            
            <div style="margin-left: 20px;">
                @if($type == 'vendor_to_school')
                    <p><strong>I. Nama: {{ $handover->from_name }}</strong><br>
                    Jabatan: Perwakilan Rekanan ({{ $procurement->vendor->name }})<br>
                    Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>

                    <p><strong>II. Nama: {{ Auth::user()->name }}</strong><br>
                    Jabatan: Petugas Inventaris Sekolah<br>
                    Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</p>
                @else
                    <p><strong>I. Nama: {{ Auth::user()->name }}</strong><br>
                    Jabatan: Waka Sarana Prasarana / Petugas Sekolah<br>
                    Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>

                    <p><strong>II. Nama: {{ $handover->to_name }}</strong><br>
                    Jabatan: Perwakilan Unit / {{ $handover->toDepartment->name ?? 'Unit Terkait' }}<br>
                    Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</p>
                @endif
            </div>

            <p>PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menyatakan telah menerima dari PIHAK PERTAMA barang-barang dengan rincian sebagai berikut:</p>

            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Nama Barang / Deskripsi</th>
                        <th style="width: 80px;">Jumlah</th>
                        <th>Spesifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($procurement->items as $index => $item)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td style="text-align: center;">{{ $item->quantity }} Unit</td>
                            <td>{{ $item->specs ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Demikian Berita Acara ini dibuat dalam rangkap 2 (dua) untuk dipergunakan sebagaimana mestinya.</p>
        </div>

        <table class="sign-table">
            <tr>
                <td>
                    <div>PIHAK PERTAMA,</div>
                    <div class="sign-space"></div>
                    <div style="font-weight: bold; text-decoration: underline;">
                        {{ $type == 'vendor_to_school' ? $handover->from_name : Auth::user()->name }}
                    </div>
                </td>
                <td>
                    <div>PIHAK KEDUA,</div>
                    <div class="sign-space"></div>
                    <div style="font-weight: bold; text-decoration: underline;">
                        {{ $type == 'vendor_to_school' ? Auth::user()->name : $handover->to_name }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            <div>Mengetahui,</div>
            <div class="sign-space"></div>
            <div style="font-weight: bold; text-decoration: underline;">Kepala Sekolah</div>
        </div>
    </div>
</body>
</html>
