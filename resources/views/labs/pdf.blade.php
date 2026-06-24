@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Daftar Aset Lab: {{ $lab->name }}</h1>
    <table class="min-w-full bg-white shadow rounded">
        <thead>
            <tr>
                <th class="px-4 py-2 border">Nama Aset</th>
                <th class="px-4 py-2 border">Spesifikasi</th>
                <th class="px-4 py-2 border">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($labAssets as $labAsset)
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
</div>
@endsection
