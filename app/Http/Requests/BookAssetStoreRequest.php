<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookAssetStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => 'required|string|max:255',
            'purchase_year'       => 'required|digits:4|integer|min:1900',
            'institution_id'      => 'required|exists:institutions,id',
            'category_id'         => 'nullable|exists:categories,id', // akan di-force kategori buku
            'building_id'         => 'required|exists:buildings,id',
            'room_id'             => 'required|exists:rooms,id',
            'faculty_id'          => 'required|exists:faculties,id',
            'department_id'       => 'required|exists:departments,id',
            'person_in_charge_id' => 'required|exists:persons_in_charge,id',
            'asset_function_id'   => 'required|exists:asset_functions,id',
            'funding_source_id'   => 'required|exists:funding_sources,id',
            'purchase_cost'       => 'required|numeric|min:0',
            'useful_life'         => 'nullable|integer|min:1',
            'salvage_value'       => 'nullable|numeric|min:0|lte:purchase_cost',
            'status'              => 'nullable|in:Aktif,Dipinjam,Maintenance,Rusak,Disposed',
        ];
    }
}
