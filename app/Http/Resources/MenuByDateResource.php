<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuByDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->menu->id,
            'theme' => $this->menu->theme->name,
            'name' => $this->menu->name,
            'description' => $this->menu->description,
            'image_url' => $this->menu->image_url,
            'addon' => [
                'vegetable' => $this->menu->vegetable,
                'side_dish' => $this->menu->side_dish,
                'sauce' => $this->menu->chili_sauce,
            ],
            'categories' => $this->menu->menu_categories->map(function ($category) {
                return $category->categories->name;
            }),
            'packages' => $this->menu->prices->map(function ($price) {
                return [
                    'id' => $price->id,
                    'name' => $price->package->name,
                    'description' => $price->package->description,
                    'price' => $price->price,
                    'minimum_order' => $price->package->minimum_order,
                    'image_url' => $price->package->image_url,
                ];
            })
        ];
    }
}
