<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use App\UserRole;
use Config;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Log;
use Psr\Log\LogLevel;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       $password_admin = Config::get('app.user_management_password.admin');     
       $password_owner = Config::get('app.user_management_password.owner');    

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
                'role' => UserRole::ADMIN,
                "password" => Hash::make($password_admin)
            ],
            [
                "first_name" => "Owner",
                "last_name" => "Homade",
                "email" => "owmer@homade.id",
                'role' => UserRole::OWNER,
                "password" => Hash::make($password_owner)
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
           if($user['role'] == UserRole::ADMIN){
             Log::log(LogLevel::DEBUG, 'password for admin is : '. $password_admin);
           }
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
