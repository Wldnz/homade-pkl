<?php

namespace Database\Seeders;

use App\Models\MenuPrice;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\TransactionAddress;
use App\Models\User;
use App\Models\UserAddress;
use App\StatusTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $transactions = [
            [
                "total_menu" => 2,
                "note" => "Bang, Terima Kasih Banyak ya!",
                "status" => StatusTransaction::SUCCESS,
            ],
            [
                "total_menu" => 1,
                "note" => "Gak Pedes ya bang",
                "status" => StatusTransaction::PENDING,
            ]
        ];

        foreach ($transactions as $transaction) {

            $totalPrice = 0;

            $orders = [];

            for ($i = 0; $i < $transaction['total_menu']; $i++) {
                $menuPrice = MenuPrice::inRandomOrder()->first();
                $totalMenu = random_int(1, 10);
                $isAlreadyIncluded = array_search(['id_menu_price' => $menuPrice->id], $orders);
                if ($isAlreadyIncluded) {
                    array_push($orders, [
                        "id_menu_price" => $menuPrice->id,
                        "total_price" => $menuPrice->price * $totalMenu,
                        "quantity" => $totalMenu,
                        "note" => $transaction['note']
                    ]);
                    $totalPrice += $menuPrice->price * $totalMenu;
                }
            }

            $shippingCost = 5000;
            $userId = User::inRandomOrder()->where("role", "=", "customer")->value("id");
            $userAddress = UserAddress::inRandomOrder()->where('id_user', $userId)->first();
        
            $newTransaction = Transaction::create([
                "id_user" => $userId,
                "subtotal" => $totalPrice,
                "shipping_cost" => $shippingCost,
                "total_price" => $totalPrice + $shippingCost,
                "status" => $transaction['status'],
                "delivery_at" => now()->addDays(1),
                "created_at" => now(),
                "updated_at" => now()
            ]);



            foreach ($orders as $order) {
                Order::create([
                    "id_transaction" => $newTransaction->id,
                    "id_menu_price" => $order['id_menu_price'],
                    "total_price" => $order['total_price'],
                    "quantity" => $order['quantity'],
                    "note" => $order['note'],
                    "created_at" => now(),
                    "updated_at" => now()
                ]);
            }

            TransactionAddress::create([
                'id_transaction' => $newTransaction->id,
                'received_name' => $userAddress->received_name,
                'phone' => $userAddress->phone,
                'label' => $userAddress->label,
                'address' => $userAddress->address,
                'note' => $userAddress->note,
                'longitude' => $userAddress->longitude,
                'latitude' => $userAddress->latitude,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }

    }
}
