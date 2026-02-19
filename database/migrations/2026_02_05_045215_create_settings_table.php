<?php

use App\EnumDay;
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
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->text("address");
            $table->enum("start_day", EnumDay::cases());
            $table->enum("end_day", EnumDay::cases());
            $table->string("email", 255);
            $table->string("customer_care_phone", 15);
            $table->string('tiktok_url', 265)->nullable();
            $table->string('youtube_url', 265)->nullable();
            $table->string('facebook_url', 265)->nullable();
            $table->string('instagram_url', 265)->nullable();
            $table->string('x_url', 265)->nullable();
            $table->time("open_hours_at");
            $table->time("close_hours_at");
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
