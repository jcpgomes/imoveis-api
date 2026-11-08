<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => ['value' => $this->status->value, 'label' => $this->status->label()],
            'source' => ['value' => $this->source->value, 'label' => $this->source->label()],
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'address' => $this->address,
            'property_type' => PropertyTypeResource::make($this->whenLoaded('propertyType')),
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'garage_spaces' => $this->garage_spaces,
            'area_m2' => $this->area_m2,
            'city' => $this->city,
            'state' => $this->state,
            'neighborhood' => $this->neighborhood,
            'external_code' => $this->external_code,
        ];
    }
}
