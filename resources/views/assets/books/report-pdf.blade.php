<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            /* Ukuran font diperkecil agar muat banyak data */
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 2px 0 0 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            /* Garis tabel lebih tegas */
            padding: 5px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            /* Memastikan teks panjang tidak keluar tabel */
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .no-wrap {
            white-space: nowrap;
            /* Mencegah teks terpotong di kolom tertentu */
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        {{-- Mengambil nama institusi dari data pertama yang ada --}}
        <p>{{ $books->first()->institution->name ?? 'Institusi Aset' }}</p>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no-wrap">No</th>
                <th>Kode Aset</th>
                <th>Nama Barang</th>
                <th class="no-wrap">Tahun Beli</th>
                <th>No Urut</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Penanggung Jawab</th>
                <th>Fungsi</th>
                <th>Sumber Dana</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($books as $index => $book)
                <tr>
                    <td class="no-wrap">{{ $index + 1 }}</td>
                    <td>{{ $book->asset_code_ypt ?? '-' }}</td>
                    <td>{{ $book->name ?? '-' }}</td>
                    <td class="no-wrap">{{ $book->purchase_year ?? '-' }}</td>
                    <td>{{ $book->sequence_number ?? '-' }}</td>
                    <td>{{ $book->category->name ?? '-' }}</td>
                    <td>{{ $book->building->name ?? '' }} / {{ $book->room->name ?? '' }}</td>
                    <td>{{ $book->personInCharge->name ?? '-' }}</td>
                    <td>{{ $book->assetFunction->name ?? '-' }}</td>
                    <td>{{ $book->fundingSource->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
