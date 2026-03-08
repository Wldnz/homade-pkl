<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SummaryMenuResource extends JsonResource
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
            'theme' => $this->theme->name,
            'name' => $this->name,
            'description' => $this->description,
            'addon' => [
                'vegetable' => $this->vegetable,
                'side_dish' => $this->side_dish,
                'sauce' => $this->chili_sauce,
            ],
            'image_url' => $this->image_url,
            // 'category' => $this->category, perlu?
            'packages' => $this->prices->map(function ($price) {
                return [
                    'id' => $price->id,
                    'name' => $price->package->name,
                    'description' => $price->package->description,
                    'price' => $price->price,
                    'minimum_order' => $price->package->minimum_order,
                    'image_url' => $price->package->image_url,
                    'quantity' => $price->quantity,
                    'note' => $price->note ?? '',
                ];
            }),
        ];
    }
}
