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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->foreignId('order_id')->constrained('orders')->restrictOnDelete();
            $table->enum('status', ['PENDING', 'SUCCEEDED', 'CANCELED']);
            $table->string('payment_method')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('rrn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
