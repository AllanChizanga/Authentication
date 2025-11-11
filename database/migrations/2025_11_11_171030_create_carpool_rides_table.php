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
        Schema::create('carpool_rides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('driver_vehicle_id');
            $table->string('origin_name');
            $table->string('destination_name');
            $table->timestamp('departure_time');
            $table->date('date_of_departure');
            $table->integer('available_seats');
            $table->string('origin_coordinations')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'full', 'completed'])->default('open');
            $table->decimal('contribution_per_seat', 10, 2);
            $table->integer('total_bookings')->default(0);
            $table->decimal('origin_lat', 10, 7)->nullable();
            $table->decimal('origin_long', 10, 7)->nullable();
            $table->decimal('destination_lat', 10, 7)->nullable();
            $table->decimal('destination_long', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carpool_rides');
    }
};
