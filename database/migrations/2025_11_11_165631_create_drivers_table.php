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
        Schema::create('drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id');
            $table->string('license_url')->nullable();
            $table->string('proof_of_residence_url')->nullable();
            $table->string('police_clearance_letter_url')->nullable();
            $table->integer('number_of_completed_rides')->default(0);
            $table->boolean('is_activated');
            $table->enum('badge', ['red', 'green']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
