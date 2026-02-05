<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\MenuPrice;
use App\Models\MenuSchedule;
use App\Models\Package;
use App\Models\Theme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{

    private $currentMenu = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                "uuid" => fake()->uuid(),
                "name" => "Nasi Kuning Ayam Goreng Lengkuas",
                "description" => "ini makanannya enak banget...",
                "vegetable" => "Lalapan",
                "side_dish" => "Tempe orek Kering, Telur Dadar Iris",
                "chili_sauce" => "Sambal Goreng",
            ],
            [
                "uuid" => fake()->uuid(),
                "name" => "Nasi Kuning Tongkol Balado Suwir",
                "description" => "ini makanannya enak banget...",
                "vegetable" => "Lalapan",
                "side_dish" => "Tempe orek Kering, Telur Dadar Iris",
                "chili_sauce" => "Sambal Goreng",
            ],
            [
                "uuid" => fake()->uuid(),
                "name" => "Mie Goreng Ayam (Mie Pengganti Nasi)",
                "description" => "ini makanannya enak banget...",
                "vegetable" => "Acar",
                "side_dish" => "Telor Ceplok",
                "chili_sauce" => "Saus Sachet",
            ],
            [
                "uuid" => fake()->uuid(),
                "name" => "Ayam Cabe Garam",
                "description" => "ini makanannya enak banget...",
                "vegetable" => "Salad Jepang",
                "side_dish" => "Scrambled Egg",
                "chili_sauce" => "Chili Oil",
            ]
        ];

        foreach ($menus as $menu) {
            $packageID = Package::inRandomOrder()->value('uuid');
            $categoryID = Category::inRandomOrder()->value('uuid');
            $themeID = Theme::inRandomOrder()->value('uuid');

            Menu::create([
                "id_theme" => $themeID,
                "name" => $menu['name'],
                "description" => $menu['description'],
                "vegetable" => $menu['vegetable'],
                "side_dish" => $menu['side_dish'],
                "chili_sauce" => $menu['chili_sauce'],
                "image_url" => "https://homade.id/wp-content/uploads/2020/05/menu-ayam-panggang-klaten.jpg",
                "created_at" => now(),
                "updated_at" => now()
            ]);

            MenuCategory::create([
                "id_category" => $categoryID,
                "id_menu" => $menu['uuid'],
                "created_at" => now(),
                "updated_at" => now()
            ]);

            MenuPrice::create([
                "id_menu" => $menu["uuid"],
                "id_package" => $packageID,
                "price" => 230000,
                "created_at" => now(),
                "updated_at" => now(),
            ]);

            if (count($this->currentMenu) < 2) {
                array_push($this->currentMenu, []);
                MenuSchedule::create([
                    "id_menu" => $menu["uuid"],
                    "date_at" => now(),
                    "created_at" => now(),
                    "updated_at" => now(),
                ]);
            }
        }
    }
}
