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
        Schema::create('vpay_transactions', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignUuid('wallet_id');
            $table->decimal('amount', 15, 2);
            $table->enum('transaction_type', ['cashin', 'cashout']);
            $table->text('notes')->nullable();
            $table->decimal('balance_before_transaction', 15, 2);
            $table->decimal('current_balance_after_transaction', 15, 2);
            $table->foreignUuid('sender_id')->nullable();
            $table->foreignUuid('receiver_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vpay_transactions');
    }
};
