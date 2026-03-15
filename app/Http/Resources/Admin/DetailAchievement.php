<?php

namespace App\Http\Resources\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailAchievement extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $achievement = parent::toArray($request);
        $achievement['date_at'] = Carbon::parse($achievement['date_at'])->format('d-m-Y');
        return $achievement;
    }
}
