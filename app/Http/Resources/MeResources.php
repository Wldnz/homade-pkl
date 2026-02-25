<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeResources extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'address' => $this->address->map(function($address){
                return [
                    'id' => $address->id,
                    'received_name' => $address->received_name,
                    'phone' => $address->phone,
                    'label' => $address->label,
                    'address' => $address->address,
                    'note' => $address->note,
                    'longitude' => $address->longitude,
                    'latitude' => $address->latitude,
                ];
            }),
            'orders' => TransactionResource::collection($this->orders)
        ];
    }
}
