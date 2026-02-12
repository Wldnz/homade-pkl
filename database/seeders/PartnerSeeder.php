<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 3 partner dlu aja
        $partners = [
            [
                "name" => "CocoWork",
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/cocowork.jpg"
            ],
            [
                "name" => "Goers",
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/goers.jpg",
            ],
            [
                "name" => "Rumah Zakat",
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/rumahzakat.jpg"
            ],
            [
                "name" => "Kenobi",
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/kenobispace.jpg"
            ],
            [
                "name" => "Bakrie Amanah",
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/bakrieamanah.jpg",
            ],
            [
                "name" => "Offices",
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/vroffices.jpg"
            ],
            [
                "name" => "Tech In Asia",
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/techinasia.jpg"
            ],
            [
                "name" => "Conclave",
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/conclave.jpg",
            ]
            ];

        foreach($partners as $partner){
            Partner::create([
                "name" => $partner['name'],
                "image_url" => $partner['image_url'],
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
    }
}
