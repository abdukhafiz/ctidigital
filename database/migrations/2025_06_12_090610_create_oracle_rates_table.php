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
        Schema::create('oracle_rates', function (Blueprint $table) {
            $table->id();
            $table->string('rate_code', 50)->unique();
            $table->string('name', 255);
            $table->timestamps();

            $table->index('rate_code', 'idx_rate_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_rates');
    }
};
