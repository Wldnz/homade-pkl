<?php

namespace Database\Seeders;

use App\Models\MenuPrice;
use App\Models\Transaction;
use App\Models\TransactionAddress;
use App\Models\TransactionDriver;
use App\Models\TransactionOrder;
use App\Models\TransactionPaymentProof;
use App\Models\TransactionProof;
use App\Models\User;
use App\Models\UserAddress;
use App\RefundStatus;
use App\StatusDelivery;
use App\StatusTransaction;
use App\TransactionCategory;
use App\TransactionPaymentProofStatus;
use App\UserRole;
use App\Utils\CloudinaryClient;
use Exception;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $cloudinary = new CloudinaryClient();
        $sample_url = 'https://res.cloudinary.com/ddiulakke/image/upload/v1773030579/contoh-payment-success_dcpgei.png';
        $images_assets = $cloudinary->getAllPaymentProofs(2);
        $transactions = [
            [
                'status' => StatusTransaction::WAITING_FOR_INVOICE,
                'status_delivery' => StatusDelivery::WAIT_FOR_CONFIRMATION,
                'delivery_at' => now()->addDays(5),
                'notes' => [
                    'Bang gw pesan buat besok ya, tolong buatkan invoicenya',
                ],
            ],
            [
                'shipping_cost' => 5000,
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
                ],
                // '' blm ada driver
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
                ],
                'driver' => [
                    'status' => StatusDelivery::DELIVERED,
                    'picked_up_at' => now()->addDays(1),
                    'delivered_at' => now()->addDays(1)
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

        $users = User::where('role', UserRole::CUSTOMER)->get();
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
                        'id_menu' => $menuPrice->id_menu,
                        'price_at_purchase' => $menuPrice->price,
                        'quantity' => $quantity,
                        'total_price' => $menuPrice->price * $quantity,
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
                    'total_items' => count($orders),
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
                    TransactionOrder::create([
                        'id_transaction' => $createdTransaction->id,
                        'id_menu_price' => $order['id_menu_price'],
                        'id_menu' => $order['id_menu'],
                        'price_at_purchase' => $order['price_at_purchase'],
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
                    $image = null;
                    if (count($images_assets) > 0) {
                        $image = $images_assets[0];
                        unset($images_assets[0]);
                        $images_assets = array_values($images_assets);
                    } else {
                        $image = $cloudinary->uploudPaymentProof($sample_url);
                        if (!$image){
                            return;
                        }
                    }
                    TransactionPaymentProof::create([
                        'id_transaction' => $createdTransaction->id,
                        'public_id' => $image['public_id'],
                        'url' => $image['secure_url'],
                        'reason' => fake()->text(50),
                        'status' => $transaction['payment']['status'],
                        'created_at' => now(),
                    ]);
                }

                if (isset($transaction['driver'])) {
                    TransactionDriver::create([
                        'id_transaction' => $createdTransaction->id,
                        'id_driver' => User::where('role', 'driver')->inRandomOrder()->first()->value('id'),
                        'status' => $transaction['driver']['status'],
                        'picked_up_at' => $transaction['driver']['picked_up_at'],
                        'delivered_at' => $transaction['driver']['delivered_at'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

        }
    }
}
