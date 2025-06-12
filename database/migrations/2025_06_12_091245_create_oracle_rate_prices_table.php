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
        Schema::create('oracle_rate_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')
                ->constrained('oracle_hotels')
                ->cascadeOnDelete();
            $table->foreignId('room_type_id')
                ->constrained('oracle_roomtypes')
                ->cascadeOnDelete();
            $table->foreignId('rate_id')
                ->constrained('oracle_rates')
                ->cascadeOnDelete();
            $table->date('price_date');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3);
            $table->timestamps();

            $table->index('price_date', 'idx_price_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_rate_prices');
    }
};
