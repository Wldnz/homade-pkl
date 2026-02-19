<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
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
                "password" => Hash::make("customermade14#"),
                'role' => 'customer',
                "address" => [
                    [
                        "phone" => '812345678',
                        'label' => 'Rumah',
                        'address' => fake()->address(),
                        'note' => 'itu di depan rumahnya si first_name',
                        'longitude' => fake()->longitude(),
                        'latitude' => fake()->latitude(),
                    ]
                ]
            ],
            [
                "first_name" => "Admin",
                "last_name" => "Homade",
                "email" => "admin@homade.id",
                'role' => 'admin',
                "password" => Hash::make("adminmodemad12#")
            ],
            [
                "first_name" => "Owner",
                "last_name" => "Homade",
                "email" => "owmer@homade.id",
                'role' => 'admin',
                "password" => Hash::make("ownermakesmile2#")
            ]
        ];

        foreach ($users as $user) {
            $createdUser = User::create([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'password' => $user['password'],
            ]);
            if ($createdUser->role === 'customer') {
                foreach ($user['address'] as $address) {
                    UserAddress::create([
                        'id_user' => $createdUser->id,
                        'received_name' => $createdUser->first_name . " " . $createdUser->last_name,
                        'phone' => $address['phone'],
                        'label' => $address['label'],
                        'address' => $address['address'],
                        'note' => $address['note'],
                        'longitude' => $address['longitude'],
                        'latitude' => $address['latitude'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

    }
}
