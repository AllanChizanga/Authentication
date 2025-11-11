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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fullname');
            $table->string('national_id')->nullable();
            $table->string('phone');
            $table->string('country');
            $table->string('city');
            $table->string('email')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('id_photo')->nullable();
            $table->string('work_location')->nullable();
            $table->string('home_location')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('payment_preference', ['cash', 'vpay', 'ecocash', 'bank','innbucks','omari','zipit']);
            $table->boolean('is_activated');
            $table->enum('badge', ['red', 'green'])->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
