<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                "name" => "Homade Launching",
                "description" => "Homade Launching pada 4 Juni 2017. CEO sekaligus founder Homade bersama tim, membangun Homade resmi menjadi startup katering online untuk area Jakarta.",
                "date_at" => 1497030032,
            ],
            [
                "name" => "3rd Winner – Startup Istanbul, Turki",
                "description" => "Homade berhasil membuat bangga Indonesia yaitu Juara 3 kompetisi startup bergengsi di dunia Startup Istanbul 2017, Turki. Dengan berbagai tahapan seleksi ketat yang dilewati, Homade berhasil memperkenalkan diri ke dunia.",
                "date_at" => 1507570832000,
            ],
            [
                "name" => "1st Winner – Get In The Ring, Jakarta",
                "description" => "Homade juga berhasil menjuarai kompetisi Get In The Ring Jakarta sebagai Juara 1. Homade berhasil mewakili Indonesia di Portugal, untuk ajang lebih besar yaitu Get In The Ring Global.",
                "date_at" => 1518198032000,
            ],
            [
                "name" => "Participant – Get In The Ring Global, Portugal",
                "description" => "Kesempatan dan partisipasi Homade di Get In The Ring Global Portugal, berhasil menghubungkan Homade kepada jejaring bisnis dunia.",
            ],
        ];

        foreach($achievements as $achievement){
            Achievement::create([
                "name" => $achievement['name'],
                "description" => $achievement['description'],
                "date_at" => $achievement['date_at'],
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

    }
}
