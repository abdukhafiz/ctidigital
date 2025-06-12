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
        Schema::create('oracle_promo_code_rates', function (Blueprint $table) {
            $table->foreignId('promo_code_id')
                ->constrained('oracle_promo_codes')
                ->cascadeOnDelete();
            $table->foreignId('rate_id')
                ->constrained('oracle_rates')
                ->cascadeOnDelete();
            $table->foreignId('hotel_id')
                ->constrained('oracle_hotels')
                ->cascadeOnDelete();

            $table->primary(['promo_code_id','rate_id','hotel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_promo_code_rates');
    }
};
