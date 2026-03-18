<?php

use App\EnumDay;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("app_name", 100)->default('Homade');

            // Kontak & Support
            $table->text("address");
            $table->string("email", 255);
            $table->string("customer_care_phone", 15);

            // Sosmed (Biar gampang ganti link di footer)
            $table->string('tiktok_url', 265)->nullable();
            $table->string('youtube_url', 265)->nullable();
            $table->string('facebook_url', 265)->nullable();
            $table->string('instagram_url', 265)->nullable();
            $table->string('x_url', 265)->nullable();

            // Operasional (Simple Info)
            $table->string("operating_days_info")->default('Senin - Sabtu');
            $table->time("open_hours_at");
            $table->time("close_hours_at");

            // Fitur Masa Depan (Ready tapi dormant)
            $table->boolean("is_ordering_active")->default(true);
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('shipping_fee_per_km', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
