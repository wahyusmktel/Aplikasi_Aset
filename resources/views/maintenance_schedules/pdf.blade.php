<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pemeliharaan Aset</title>
    <style>
        body {
            font-family: sans-serif;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        thead {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Laporan Pemeliharaan Aset</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Aset</th>
                <th>Judul</th>
                <th>Tipe</th>
                <th>Tgl. Jadwal</th>
                <th>Tgl. Selesai</th>
                <th>Status</th>
                <th>Teknisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->id }}</td>
                    <td>{{ $schedule->asset->name ?? 'N/A' }}</td>
                    <td>{{ $schedule->title }}</td>
                    <td>{{ ucfirst($schedule->maintenance_type) }}</td>
                    <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d-m-Y') }}</td>
                    <td>{{ $schedule->completed_at ? \Carbon\Carbon::parse($schedule->completed_at)->format('d-m-Y') : '-' }}
                    </td>
                    <td>{{ ucfirst($schedule->status) }}</td>
                    <td>{{ $schedule->assignedTo->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
