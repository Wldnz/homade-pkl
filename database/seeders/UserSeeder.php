<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $users = [
            [
                "first_name" => fake()->firstName(),
                "last_name" => fake()->lastName(),
                "email" => "customer@homade.id",
                "phone" => '812345678',
                "password" => Hash::make("customermade14#")
            ],
            [
                "first_name" => "Admin",
                "last_name" => "Homade",
                "email" => "admin@homade.id",
                "phone" => '812345678',
                "password" => Hash::make("adminmodemad12#")
            ],
            [
                "first_name" => "Owner",
                "last_name" => "Homade",
                "email" => "owmer@homade.id",
                "phone" => '812345678',
                "password" => Hash::make("ownermakesmile2#")
            ]
        ];

        foreach($users as $user){
            User::create($user);
        }

    }
}
