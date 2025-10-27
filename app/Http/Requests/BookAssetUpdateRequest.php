<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookAssetUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => 'sometimes|required|string|max:255',
            'purchase_year'      => 'sometimes|required|digits:4|integer|min:1900',
            'institution_id'     => 'sometimes|required|exists:institutions,id',
            'category_id'        => 'prohibited', // tidak boleh diubah via API ini
            'building_id'        => 'nullable|exists:buildings,id',
            'room_id'            => 'nullable|exists:rooms,id',
            'faculty_id'         => 'nullable|exists:faculties,id',
            'department_id'      => 'nullable|exists:departments,id',
            'person_in_charge_id' => 'nullable|exists:persons_in_charge,id',
            'asset_function_id'  => 'nullable|exists:asset_functions,id',
            'funding_source_id'  => 'nullable|exists:funding_sources,id',
            'purchase_cost'      => 'nullable|numeric|min:0',
            'useful_life'        => 'nullable|integer|min:1',
            'salvage_value'      => 'nullable|numeric|min:0|lte:purchase_cost',
            'status'             => 'nullable|in:Aktif,Dipinjam,Maintenance,Rusak,Disposed',
        ];
    }
}
