@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Inventaris Lab</h1>
    <a href="{{ route('labs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        Tambah Lab Baru
    </a>
    <table class="min-w-full bg-white mt-4 shadow rounded">
        <thead>
            <tr>
                <th class="px-4 py-2 border">Nama Lab</th>
                <th class="px-4 py-2 border">Ruangan</th>
                <th class="px-4 py-2 border">Prodi / Unit</th>
                <th class="px-4 py-2 border">Penanggung Jawab</th>
                <th class="px-4 py-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($labs as $lab)
            <tr>
                <td class="px-4 py-2 border">{{ $lab->name }}</td>
                <td class="px-4 py-2 border">{{ $lab->room->name ?? '-' }}</td>
                <td class="px-4 py-2 border">{{ $lab->department->name ?? '-' }}</td>
                <td class="px-4 py-2 border">{{ $lab->personInCharge->name ?? '-' }}</td>
                <td class="px-4 py-2 border space-x-2">
                    <a href="{{ route('labs.show', $lab) }}" class="text-indigo-600 hover:underline">Detail</a>
                    <a href="{{ route('labs.edit', $lab) }}" class="text-green-600 hover:underline">Edit</a>
                    <form action="{{ route('labs.destroy', $lab) }}" method="POST" class="inline" onsubmit="return confirm('Hapus lab ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-2 text-center">Tidak ada lab.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
