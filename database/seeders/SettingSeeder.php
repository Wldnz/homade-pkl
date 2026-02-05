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
            "address" => "-",
            "start_day" => EnumDay::MONDAY,
            "end_day" => EnumDay::FRIDAY,
            "customer_care_phone" => "081234567891",
            // "open_hours_at" => "time()",
            // "close_hours_at" => time(),
            "created_at" => now(),
            "updated_at" => now(),
        ]);
    }
}
