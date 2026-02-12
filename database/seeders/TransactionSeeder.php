<?php

namespace Database\Seeders;

use App\Models\MenuPrice;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
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

            $newTransaction = Transaction::create([
                "id_user" => User::inRandomOrder()->where("role", "=", "customer")->value("id"),
                "total_price" => $totalPrice,
                "total_menu" => count($orders),
                "status" => $transaction['status'],
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

        }

    }
}
