@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Lab</h1>
    <form action="{{ route('labs.update', $lab) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block font-medium" for="name">Nama Lab</label>
            <input type="text" name="name" id="name" class="w-full border rounded p-2" value="{{ $lab->name }}" required>
        </div>
        <div>
            <label class="block font-medium" for="room_id">Ruangan</label>
            <select name="room_id" id="room_id" class="w-full border rounded p-2" required>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ $lab->room_id == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-medium" for="department_id">Prodi / Unit</label>
            <select name="department_id" id="department_id" class="w-full border rounded p-2" required>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ $lab->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-medium" for="person_in_charge_id">Penanggung Jawab</label>
            <select name="person_in_charge_id" id="person_in_charge_id" class="w-full border rounded p-2" required>
                @foreach($persons as $person)
                    <option value="{{ $person->id }}" {{ $lab->person_in_charge_id == $person->id ? 'selected' : '' }}>{{ $person->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Perbarui Lab</button>
        <a href="{{ route('labs.inventory') }}" class="ml-4 text-gray-600 hover:underline">Batal</a>
    </form>
</div>
@endsection
