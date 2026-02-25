<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "theme" => $this->theme->name,
            "name" => $this->name,
            "description" => $this->description,
            "addon" => [
                "vegetable" => $this->vegetable,
                "side_dish" => $this->side_dish,
                "sauce" => $this->chili_sauce,
            ],
            "image_url" => $this->image_url,
            "categories" => $this->menu_categories->map(function ($category) {
                return $category->categories->name;
            }),
        ];
    }
}
