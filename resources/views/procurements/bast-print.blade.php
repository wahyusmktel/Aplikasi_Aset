<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Serah Terima - {{ $handover->document_number }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #333; line-height: 1.6; }
        .container { max-width: 800px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .content { margin-bottom: 40px; }
        .content p { text-align: justify; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 10px; text-align: left; font-size: 14px; }
        .signatures { margin-top: 50px; display: flex; justify-content: space-between; }
        .sign-box { text-align: center; width: 250px; }
        .sign-space { height: 80px; }
        @media print {
            .no-print { display: none; }
            .container { border: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #f4f4f4; padding: 10px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">Cetak Dokumen</button>
    </div>

    <div class="container">
        <div class="header">
            <h1>Berita Acara Serah Terima (BAST)</h1>
            <div style="font-size: 14px;">Nomor: {{ $handover->document_number }}</div>
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
                        <th>No</th>
                        <th>Nama Barang / Deskripsi</th>
                        <th>Jumlah</th>
                        <th>Spesifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($procurement->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->quantity }} Unit</td>
                            <td>{{ $item->specs ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Demikian Berita Acara ini dibuat dalam rangkap 2 (dua) untuk dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="signatures">
            <div class="sign-box">
                <div>PIHAK PERTAMA,</div>
                <div class="sign-space"></div>
                <div style="font-weight: bold; text-decoration: underline;">
                    {{ $type == 'vendor_to_school' ? $handover->from_name : Auth::user()->name }}
                </div>
            </div>
            <div class="sign-box">
                <div>PIHAK KEDUA,</div>
                <div class="sign-space"></div>
                <div style="font-weight: bold; text-decoration: underline;">
                    {{ $type == 'vendor_to_school' ? Auth::user()->name : $handover->to_name }}
                </div>
            </div>
        </div>

        <div style="margin-top: 50px; text-align: center;">
            <div>Mengetahui,</div>
            <div class="sign-space"></div>
            <div style="font-weight: bold; text-decoration: underline;">Kepala Sekolah</div>
        </div>
    </div>
</body>
</html>
