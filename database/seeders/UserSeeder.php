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
                'phone' => '812345678',
                "address" => [
                    [
                        "phone" => '812345678',
                        'label' => 'Rumah',
                        'address' => fake()->address(),
                        'note' => 'itu di depan rumahnya si first_name',
                        'longitude' => fake()->longitude(),
                        'is_main_address' => true,
                        'latitude' => fake()->latitude(),
                    ]
                ]
            ],
            [
                "first_name" => "Ahmad",
                "last_name" => "Driver",
                "email" => "driver.ahmad@homade.id",
                "phone" => '8128419182',
                "role" => 'driver',
                "password" => Hash::make('ahmaddrivernihbossenggoldong23!')
            ],
            [
                "first_name" => "Admin",
                "last_name" => "Homade",
                "email" => "admin@homade.id",
                'role' => 'admin',
                "password" => Hash::make(env('ADMIN_PASSWORD'))
            ],
            [
                "first_name" => "Owner",
                "last_name" => "Homade",
                "email" => "owmer@homade.id",
                'role' => 'admin',
                "password" => Hash::make(env('OWNER_PASSWORD'))
            ]
        ];

        foreach ($users as $user) {
            $createdUser = User::create([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'phone' => $user['phone'] ?? null,
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
                        'is_main_address' => $address['is_main_address'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

    }
}
