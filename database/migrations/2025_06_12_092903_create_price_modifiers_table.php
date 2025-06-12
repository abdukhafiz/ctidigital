<?php

use App\Models\PriceModifier;
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
        Schema::create('price_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')
                ->constrained('oracle_hotels')
                ->cascadeOnDelete();
            $table->foreignId('rate_id')
                ->constrained('oracle_rates')
                ->cascadeOnDelete();
            $table->date('date');
            $table->enum('modifier_type', array_keys(PriceModifier::MODIFIER_TYPES));
            $table->enum('operation', array_keys(PriceModifier::OPERATIONS));
            $table->decimal('price',10,2);
            $table->timestamps();

            $table->index('date','idx_modifier_date');
            $table->index('modifier_type','idx_modifier_type');
            $table->index('operation','idx_operation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_modifiers');
    }
};
