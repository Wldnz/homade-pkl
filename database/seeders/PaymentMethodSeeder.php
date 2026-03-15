<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Str;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $methods = [
            [
                'id' => Str::uuid(),
                'bank_name' => 'BCA',
                'account_number' => '1234567890', // Ganti dengan nomor rekening asli nanti
                'account_owner' => 'PT Homade Katering', // Ganti dengan nama pemilik asli
                'image_url' => 'https://res.cloudinary.com/ddiulakke/image/upload/v1773559009/bca-logo_cvcoru.png',
                'image_public_id' => 'bca-logo_cvcoru',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'bank_name' => 'Mandiri',
                'account_number' => '0987654321', // Ganti dengan nomor rekening asli nanti
                'account_owner' => 'PT Homade Katering', // Ganti dengan nama pemilik asli
                'image_url' => 'https://res.cloudinary.com/ddiulakke/image/upload/v1773559007/bank-mandiri-logo-png_seeklogo-16290_gidd7n.png',
                'image_public_id' => 'bank-mandiri-logo-png_seeklogo-16290_gidd7n',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        DB::table('payment_methods')->insert($methods);
    }
}
