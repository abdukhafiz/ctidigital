<?php

use App\Models\OraclePromoCode;
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
        Schema::create('oracle_promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('promo_code', 200)->unique();
            $table->string('name', 255);
            $table->enum('discount_type', array_keys(OraclePromoCode::DISCOUNT_TYPES));
            $table->date('valid_from');
            $table->date('valid_to');
            $table->timestamps();

            $table->index('promo_code', 'idx_promo_code');
            $table->index('discount_type', 'idx_discount_type');
            $table->index(['valid_from','valid_to'], 'idx_valid_range');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_promo_codes');
    }
};
