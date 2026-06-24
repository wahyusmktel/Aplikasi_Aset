@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Detail Lab: {{ $lab->name }}</h1>
    <div class="mb-4">
        <p><strong>Ruangan:</strong> {{ $lab->room->name ?? '-' }}</p>
        <p><strong>Prodi / Unit:</strong> {{ $lab->department->name ?? '-' }}</p>
        <p><strong>Penanggung Jawab:</strong> {{ $lab->personInCharge->name ?? '-' }}</p>
    </div>
    <div class="flex space-x-4 mb-4">
        <a href="{{ route('labs.assets.create', $lab) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Tambah Aset</a>
        <a href="{{ route('labs.exportPdf', $lab) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">Cetak PDF Aset</a>
    </div>
    <h2 class="text-xl font-semibold mb-2">Daftar Aset Lab</h2>
    @if($lab->labAssets->isEmpty())
        <p>Belum ada aset yang terdaftar.</p>
    @else
        <table class="min-w-full bg-white shadow rounded">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">Nama Aset</th>
                    <th class="px-4 py-2 border">Spesifikasi</th>
                    <th class="px-4 py-2 border">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lab->labAssets as $labAsset)
                    <tr>
                        <td class="px-4 py-2 border">{{ $labAsset->asset->name }}</td>
                        <td class="px-4 py-2 border">
                            <pre class="whitespace-pre-wrap">{{ json_encode($labAsset->specifications, JSON_PRETTY_PRINT) }}</pre>
                        </td>
                        <td class="px-4 py-2 border text-center">{{ $labAsset->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <div class="mt-6">
        <a href="{{ route('labs.inventory') }}" class="text-gray-600 hover:underline">← Kembali ke Inventaris Lab</a>
    </div>
</div>
@endsection
