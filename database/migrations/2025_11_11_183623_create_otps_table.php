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
        Schema::create('otps', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignUuid('user_id');
            $table->string('otp_code');
            $table->string('phone_number');
            $table->text('session_token')->nullable();
            $table->dateTime('expires_at');
            $table->dateTime('verified_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->string('purpose');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp');
    }
};
