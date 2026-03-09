<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackagePriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->package->name,
            'description' => $this->package->description,
            'price' => $this->price,
            'minimum_order' => $this->package->minimum_order,
            'image_url' => $this->package->image_url,
        ];
    }
}
