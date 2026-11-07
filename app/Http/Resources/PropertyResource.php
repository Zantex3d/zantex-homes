<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'code' => $this->property_code,
            'name' => $this->name,
            'estate' => $this->estate->only(['id', 'name', 'location']),
            'type' => $this->propertyType->name ?? null,
            'units_count' => $this->units_count ?? $this->units()->count(),
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
