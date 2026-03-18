<?php

namespace Database\Seeders;

use App\EnumDay;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            "app_name" => "Homade",
            "address" => "Jl. Tebet Timur Dalam VI No.3, RT.008 RW.011, Kec. Tebet Timur, Jakarta Selatan, 12820.",
            "operating_days_info" => "Senin - Jumat",
            "email" => 'support@homade.id',
            "customer_care_phone" => "085711801336",
            "open_hours_at" => "09:00:00",
            "close_hours_at" => "17:00:00",
            "instagram_url" => "https://www.instagram.com/homade.indonesia/?hl=id",
            "is_ordering_active" => true,
            "longitude" => 106.8574, // Contoh koordinat Tebet
            "latitude" => -6.2305,
            "shipping_fee_per_km" => 5000,
            "created_at" => now(),
            "updated_at" => now(),
        ]);
    }
}
