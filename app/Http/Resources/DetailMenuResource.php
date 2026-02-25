<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailMenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $menu = $this['menu'];
        return [
            'id' => $menu->id,
            'theme' => $menu->theme->name,
            'name' => $menu->name,
            'description' => $menu->description,
            'image_url' => $menu->image_url,
            'addon' => [
                'side_dish' => $menu->side_dish,
                'vegetable' => $menu->vegetable,
                'sauce' => $menu->chili_sauce
            ],
            'categories' => $menu->menu_categories->map(function($category){
                return $category->categories->name;
            }),
            'packages' => $menu->prices->map(function($price){
                return [
                    'id' => $price->id,
                    'name' => $price->package->name,
                    'description' => $price->package->description,
                    'price' => $price->price,
                    'minimum_order' => $price->package->minimum_order,
                    'image_url' => $price->package->image_url,
                ];
            }),
            'relevants' => $this['relevants'] ? MenuResource::collection($this['relevants']) : []
        ];
    }
}
