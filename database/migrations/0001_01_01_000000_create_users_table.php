<?php

use App\UserRole;
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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string('first_name', 120);
            $table->string('last_name', 120);
            $table->string('email')->unique();
            $table->enum("role", UserRole::cases())->default(UserRole::CUSTOMER);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('user_address', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("id_user");
            $table->string('received_name', 120);
            $table->string("phone", 15)->nullable();
            $table->string('label', 60);
            $table->string('address');
            $table->text('note')->nullable();
            $table->decimal('longitude', 11, 8);
            $table->decimal('latitude', 10, 8);
            $table->timestamps();
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('user_address');
        Schema::dropIfExists('users');
    }
};
