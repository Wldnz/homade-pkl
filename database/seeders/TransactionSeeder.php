<?php

namespace Database\Seeders;

use App\Models\MenuPrice;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\TransactionAddress;
use App\Models\TransactionPaymentProof;
use App\Models\TransactionProof;
use App\Models\User;
use App\Models\UserAddress;
use App\RefundStatus;
use App\StatusDelivery;
use App\StatusTransaction;
use App\TransactionCategory;
use App\TransactionPaymentProofStatus;
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
                'status' => StatusTransaction::PAID,
                'status_delivery' => StatusDelivery::PROCESS,
                'shipping_cost' => 10000,
                'delivery_at' => now()->addDays(2),
                'notes' => [
                    'Bang ini jangan pake nasi ya!',
                    'tolong sambelnya yang sasetan aja ya bang',
                    'ini makanannya jangan di kasih sayuran ya bang!'
                ],
                'payment' => [
                    'status' => TransactionPaymentProofStatus::ACCEPTED,
                ]
            ],
            [
                'status' => StatusTransaction::PENDING,
                'status_delivery' => StatusDelivery::WAIT_FOR_CONFIRMATION,
                'delivery_at' => now()->addDays(1),
                'notes' => [
                    'Bang ini jangan pake nasi ya!',
                ],
                'payment' => [
                    'status' => TransactionPaymentProofStatus::WAIT_FOR_CONFIRMATION,
                ]
            ],
            [
                'status' => StatusTransaction::SUCCESS,
                'status_delivery' => StatusDelivery::DELIVERED,
                'delivery_at' => now()->addDays(1),
                'notes' => [
                    'Bang ini jangan pake ikan ya!',
                ],
                'payment' => [
                    'status' => TransactionPaymentProofStatus::ACCEPTED,
                ]
            ],
            [
                'status' => StatusTransaction::CANCELLED_BY_ADMIN,
                'status_delivery' => StatusDelivery::WAIT_FOR_CONFIRMATION,
                'refund_status' => RefundStatus::SUCCESS,
                'category' => TransactionCategory::PRE_ORDER,
                'refund_notes' => 'maaf jarak tempuhnya jauh dan kondisi tidak memungkinkan 🙏',
                'delivery_at' => now()->addDays(1),
                'notes' => [
                    'Bang ini jangan pake sambel ya!',
                ],
            ],
            [
                'status' => StatusTransaction::CANCELLED_BY_CUSTOMER,
                'status_delivery' => StatusDelivery::WAIT_FOR_CONFIRMATION,
                'refund_status' => RefundStatus::PENDING,
                'shipping_cost' => 15000,
                'refund_notes' => 'maaf saya tidak setuju dengan ongkir yang saya mahal....',
                'delivery_at' => now()->addDays(1),
                'notes' => [
                    'Bang ini jangan pake nasi ya!',
                ]
            ]
        ];

        $users = User::where('role', 'customer')->get();
        $shippingCost = 5000;

        foreach ($users as $user) {
            foreach ($transactions as $transaction) {
                $subtotal = 0;
                $orders = [];
                foreach ($transaction['notes'] as $note) {
                    $menuPrice = MenuPrice::inRandomOrder()->first();
                    $quantity = random_int(5, 120);
                    array_push($orders, [
                        'id_menu_price' => $menuPrice->id,
                        'total_price' => $menuPrice->price * $quantity,
                        'quantity' => $quantity,
                        'note' => $note
                    ]);
                    $subtotal += $menuPrice->price * $quantity;
                }

                $final_shipping_cost = $transaction['shipping_cost'] ?? $shippingCost;

                $createdTransaction = Transaction::create([
                    'id_user' => $user->id,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $final_shipping_cost,
                    'total_price' => $final_shipping_cost + $subtotal,
                    'category' => $transaction['category'] ?? TransactionCategory::ORDER,
                    'status' => $transaction['status'],
                    'status_delivery' => $transaction['status_delivery'],
                    'refund_status' => $transaction['refund_status'] ?? RefundStatus::NONE,
                    'refund_reason' => $transaction['refund_reason'] ?? null,
                    'delivery_at' => $transaction['delivery_at'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($orders as $order) {
                    Order::create([
                        'id_transaction' => $createdTransaction->id,
                        'id_menu_price' => $order['id_menu_price'],
                        'total_price' => $order['total_price'],
                        'quantity' => $order['quantity'],
                        'note' => $order['note'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $address = UserAddress::where('id_user', $user->id)->inRandomOrder()->first();

                TransactionAddress::create([
                    'id_transaction' => $createdTransaction->id,
                    'received_name' => $address->received_name,
                    'phone' => $address->phone,
                    'label' => $address->label,
                    'address' => $address->address,
                    'note' => $address->note,
                    'longitude' => $address->longitude,
                    'latitude' => $address->latitude,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (isset($transaction['payment'])) {
                    TransactionPaymentProof::create([
                        'id_transaction' => $createdTransaction->id,
                        'url' => fake()->imageUrl(),
                        'reason' => fake()->text(50),
                        'status' => $transaction['payment']['status'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

            }

        }
    }
}
