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
        Schema::create('delivery_price_settings', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('fix_price_distance_km');
            $table->integer('fix_price');
            $table->integer('price_per_one_km');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_price_settings');
    }
};
