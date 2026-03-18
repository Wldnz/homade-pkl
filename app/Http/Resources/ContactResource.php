<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->app_name, 
            'email' => $this->email, 
            'customer_care_phone' => $this->customer_care_phone, 
            'information_operational_day' => $this->operating_days_info, 
            'open_hours_at' => $this->open_hours_at, 
            'close_hours_at' => $this->close_hours_at, 
            'address' => $this->address, 
            'facebook_url' => $this->facebook_url, 
            'instagram_url' => $this->instagram_url, 
            'tiktok_url' => $this->tiktok_url, 
            'youtube_url' => $this->youtube_url, 
            'x_url' => $this->x_url, 
            'longitude' => $this->longitude, 
            'latitude' => $this->latitude
        ];
    }
}
