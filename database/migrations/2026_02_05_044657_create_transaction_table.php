<?php

use App\StatusDelivery;
use App\StatusTransaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("id_user");
            $table->decimal("total_price",11, 2);
            $table->smallInteger("total_menu");
            $table->enum("status",StatusTransaction::cases())->default(StatusTransaction::PENDING);
            $table->enum("status_delivery",StatusDelivery::cases())->default(StatusDelivery::WAIT_FOR_CONFIRMATION);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("id_transaction");
            $table->foreignUuid("id_menu_price");
            $table->decimal("total_price",11, 2);
            $table->integer("quantity");
            $table->text("note");
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('transactions');
    }
};
