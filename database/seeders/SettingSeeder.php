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
            "address" => "Jl.Tebet Timur Dalam VI No.3, RT.008 RW.011 Kel. Tebet Timur, Kec. Jakarta Selatan, Jakarta 12820 Telp. 0857-1180-1336.",
            "start_day" => EnumDay::MONDAY,
            "end_day" => EnumDay::FRIDAY,
            "email" => 'support@homade.id',
            "customer_care_phone" => "081234567891",
            "open_hours_at" => "09:00:00",
            "close_hours_at" => "17:00:00",
            // "youtube_url" => ,
            "instagram_url" => "https://www.instagram.com/homade.indonesia/?hl=id",
            // "facebook_url" => ,
            // "tiktok_url" => ,
            // "x_url" => ,
            "created_at" => now(),
            "updated_at" => now(),
        ]);
    }
}
