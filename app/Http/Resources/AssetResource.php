<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ref_id' => $this->ref_id,
            'category_type' => $this->category_type,
            'category' => $this->category,
            'sub_category' => $this->sub_category,
            'brand' => $this->brand,
            'model' => $this->model,
            'status' => $this->status,
            'condition' => $this->condition,
            'acquisition_date' => $this->acquisition_date,
            'item_cost' => $this->item_cost,
            'depreciated_value' => $this->depreciated_value,
        ];
    }
}