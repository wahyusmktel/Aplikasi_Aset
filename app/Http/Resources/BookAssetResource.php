<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookAssetResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'asset_code_ypt'  => $this->asset_code_ypt,
            'name'            => $this->name,
            'purchase_year'   => $this->purchase_year,
            'status'          => $this->status,
            'category_id'     => $this->category_id,
            'building'        => optional($this->building)->name,
            'room'            => optional($this->room)->name,
            'person_in_charge' => optional($this->personInCharge)->name,
            'purchase_cost'   => $this->purchase_cost,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
