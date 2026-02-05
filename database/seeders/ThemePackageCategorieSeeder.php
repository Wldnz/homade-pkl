<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Package;
use App\Models\Theme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemePackageCategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('themes')->delete();
        DB::table( 'categories')->delete();
        DB::table('packages')->delete();

        $themes = [
            [
                "name" => "Japanese",
                "description" => "Temukan kenikmataan dan keunikan dari makanan khas jepang."
            ],
            [
                "name" => "Javanese",
                "description" => "Makanan yang unik dan tradisional, cocok unutk dinikmati bersama keluarga atau pasangan kalian!.",
            ],
        ];

        $categories = [
            [
                "name" => "Nasi"
            ],
            [
                "name" => "Ayam"
            ],
            [
                "name" => "Seafood"
            ],
            [
                "name" => "Sapi"
            ],
            [
                "name" => "Kambing"
            ]
        ];

        $packages = [
            [
                "name" => "Bento Mealbox",
                "description" => "Paket normal terdiri dari lauk utama dan lauk pendamping lengkap.",
                "minimum_order" => 5,
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/kemasan-bentomealbox2.png",
            ],
            [
                "name" => "Valuebox",
                "description" => "Paket hemat terdiri dari lauk utama dan lauk pendamping terbatas (optional).",
                "minimum_order" => 5,
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/kemasan-valuebox2.png",
            ],
            [
                "name" => "Family Pack",
                "description" => "Paket keluarga terdiri dari lauk utama dan sayuran pendamping (tanpa nasi), porsi untuk 4 orang.",
                "minimum_order" => 1,
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/kemasan-familypack.png",
            ]
        ];

        foreach($themes as $theme){
            Theme::create($theme);
        }

        foreach($categories as $category){
            Category::create($category);
        }
        
        foreach($packages as $package){
            Package::create($package);
        }
    }
}
