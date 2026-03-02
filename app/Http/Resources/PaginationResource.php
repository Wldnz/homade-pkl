<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'current_page' => $this->currentPage(),
            'first_page' => $this->from(),
            'next_page' => $this->to(),
            'last_page' => $this->lastPage(),
            'per_page' => $this->perPage(),
            'url' => [
                // 'first_page' =>  $this->firstPageUrl,
                'last_page' =>  $this->lastPageUrl(),
                'next_page' =>  $this->nextPageUrl(),
                'previus_page' => $this->previousPageUrl(),
            ],
            'total' => $this->total(),
        ];
    }
}
