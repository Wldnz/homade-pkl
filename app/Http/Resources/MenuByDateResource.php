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
            
            // 'packages' => $price
        ];
    }
}
