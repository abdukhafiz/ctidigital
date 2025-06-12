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
        Schema::create('oracle_hotels', function (Blueprint $table) {
            $table->id();
            $table->string('hotel_code', 200)->unique();
            $table->string('name', 255);
            $table->timestamps();

            $table->index('hotel_code', 'idx_hotel_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_hotels');
    }
};
