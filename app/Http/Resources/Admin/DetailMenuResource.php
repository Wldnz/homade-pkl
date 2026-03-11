<?php

namespace App\Http\Resources\Admin;

use App\Models\MenuPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class DetailMenuResource extends JsonResource
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
            'theme_id' => $this->theme->id,
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'addon' => [
                'side_dish' => $this->side_dish,
                'vegetable' => $this->vegetable,
                'sauce' => $this->chili_sauce,
                'fruit' => $this->fruit,
            ],
            'categories' => $this->menu_categories->map(function($category){
                return $category->categories->id;
            }),
            'packages' => $this->prices->map(function($price){
                return [
                    'id' => $price->id,
                    'package_id' => $price->package->id,
                    'name' => $price->package->name,
                    'description' => $price->package->description,
                    'price' => $price->price,
                    'minimum_order' => $price->package->minimum_order,
                    'image_url' => $price->package->image_url,
                ];
            }),
        ];
    }


}
