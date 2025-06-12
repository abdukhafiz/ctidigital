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
        Schema::create('oracle_hotel_rates', function (Blueprint $table) {
            $table->foreignId('hotel_id')
                ->constrained('oracle_hotels')
                ->cascadeOnDelete();
            $table->foreignId('rate_id')
                ->constrained('oracle_rates')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['hotel_id', 'rate_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_hotel_rates');
    }
};
