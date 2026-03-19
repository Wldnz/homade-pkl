<?php

namespace App\Service;

use App\Models\Setting;

class ContactService
{

    public function information()
    {
        return Setting::first();
    }

    public function socialMedia()
    {
        return Setting::first([
            'youtube_url',
            'instagram_url',
            'facebook_url',
            'tiktok_url',
            'x_url'
        ]);
    }

    public function address()
    {
        return Setting::first(
            [
                'address',
                'longitude',
                'latitude'
            ]
        );
    }

    public function operational()
    {
        return Setting::first([
            'start_day',
            'end_day',
            'open_hours_at',
            'close_hours_at'
        ]);
    }

    public function contact()
    {
        return Setting::first([
            'email',
            'customer_care_phone'
        ]);
    }

    public function shipping_fee_per_km(){
        return Setting::first([
            'shipping_fee_per_km'
        ])->value('shipping_fee_per_km');
    }

    // Tambahkan di dalam class ContactService
    public function editHandler(array $data)
    {
        try {
            $setting = Setting::first();
            if (!$setting) {
                return Setting::create($data);
            }
            $setting->update($data);
            return $setting;
        } catch (\Exception $e) {
            \Log::error("Gagal update setting: " . $e->getMessage());
            throw $e;
        }
    }

}