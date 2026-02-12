<?php

namespace App\Service;

use App\Models\Setting;

class ContactService
{

    public function information(
        array $columns = [
            'email',
            'customer_care_phone',
            'start_day',
            'end_day',
            'open_hours_at',
            'close_hours_at',
            'address',
            'facebook_url',
            'instagram_url',
            'tiktok_url',
            'youtube_url',
            'x_url',
        ]
    ) {
        return Setting::first($columns);
    }

}