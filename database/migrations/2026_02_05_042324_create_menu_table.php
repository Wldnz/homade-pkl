<?php

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
        Schema::create('menus', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("id_theme");
            $table->string("name", 120);
            $table->text("description");
            $table->string("vegetable", 120);
            $table->string("side_dish", 120);
            $table->string("chili_sauce", 120);
            $table->string("image_url", 265);
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });

        Schema::create('menu_categories', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("id_category");
            $table->foreignUuid("id_menu");
            $table->timestamps();
        });

        Schema::create('menu_prices', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("id_menu");
            $table->foreignUuid("id_package");
            $table->decimal("price", 10,2);
            $table->timestamps();
        });

        Schema::create('menu_schedules', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("id_menu");
            $table->timestamp("date_at");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_categories');
        Schema::dropIfExists('menu_price');
        Schema::dropIfExists('menu_schedules');
        Schema::dropIfExists('menus');
    }
};
