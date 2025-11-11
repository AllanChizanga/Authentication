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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('body_type');
            $table->integer('age');
            $table->string('color');
            $table->string('vehicle_reg_certificate');
            $table->string('authorization_letter')->nullable();
            $table->string('vehicle_photo')->nullable();
            $table->string('make');
            $table->string('model');
            $table->integer('capacity');
            $table->string('plate_number')->unique();
            $table->string('insurance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
