<?php

use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->boolean('is_anonymous_sender')->default(false);
            $table->boolean('is_recipient_current_user');
            $table->boolean('previously_call_to_recipient')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone', 11)->nullable();
            $table->string('delivery_address');
            $table->string('entrance')->nullable();
            $table->string('floor')->nullable();
            $table->string('apartment_number')->nullable();
            $table->string('comment_for_courier')->nullable();
            $table->timestamp('delivery_date_time')->nullable();
            $table->string('client_wishes')->nullable();
            $table->integer('order_price');
            $table->integer('delivery_price');
            $table->integer('total_price');
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
