<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'app_name',
        'address',
        'email',
        'customer_care_phone',
        'tiktok_url',
        'youtube_url',
        'facebook_url',
        'instagram_url',
        'x_url',
        'operating_days_info',
        'open_hours_at',
        'close_hours_at',
        'is_ordering_active',
        'longitude',
        'latitude',
        'shipping_fee_per_km',
    ];
}
