@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Tambah Aset ke Lab: {{ $lab->name }}</h1>
    <form action="{{ route('labs.assets.store', $lab) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block font-medium" for="asset_name">Nama Aset</label>
            <input type="text" name="asset_name" id="asset_name" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label class="block font-medium" for="quantity">Jumlah</label>
            <input type="number" name="quantity" id="quantity" class="w-full border rounded p-2" min="1" required>
        </div>
        <div>
            <label class="block font-medium" for="specifications">Spesifikasi (JSON)</label>
            <textarea name="specifications" id="specifications" rows="6" class="w-full border rounded p-2" placeholder='{"Processor":"i5-10400F","RAM":"16 GB","OS":"Windows 11 Pro","VGA":"GTX 1650"}' required></textarea>
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Simpan Aset</button>
        <a href="{{ route('labs.show', $lab) }}" class="ml-4 text-gray-600 hover:underline">Batal</a>
    </form>
</div>
@endsection
