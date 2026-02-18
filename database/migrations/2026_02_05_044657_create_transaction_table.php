<?php

use App\RefundStatus;
use App\StatusDelivery;
use App\StatusTransaction;
use App\TransactionCategory;
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
            $table->decimal("subtotal",11, 2);
            $table->decimal("shipping_cost",10, 2);
            $table->decimal("total_price",11, 2);
            $table->enum('category', TransactionCategory::cases())->default(TransactionCategory::ORDER);
            $table->enum("status",StatusTransaction::cases())->default(StatusTransaction::PENDING);
            $table->enum("status_delivery",StatusDelivery::cases())->default(StatusDelivery::WAIT_FOR_CONFIRMATION);
            $table->enum('refund_status', RefundStatus::cases())->default(RefundStatus::NONE);
            $table->text('refund_reason')->nullable();
            $table->timestamp('delivery_at');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("id_transaction");
            $table->foreignUuid("id_menu_price");
            $table->decimal("total_price",11, 2);
            $table->integer("quantity");
            $table->text("note")->nullable();
            $table->timestamps();
        });

        Schema::create('transaction_address', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("id_transaction");
            $table->string('received_name', 120);
            $table->string("phone", 15)->nullable();
            $table->string('label', 60);
            $table->string('address');
            $table->text('note')->nullable();
            $table->decimal('longitude', 11, 8);
            $table->decimal('latitude', 10, 8);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_address');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('transactions');
    }
};
