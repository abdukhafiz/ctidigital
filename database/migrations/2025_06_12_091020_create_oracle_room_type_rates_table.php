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
        Schema::create('oracle_room_type_rates', function (Blueprint $table) {
            $table->foreignId('room_type_id')
                ->constrained('oracle_roomtypes')
                ->cascadeOnDelete();
            $table->foreignId('rate_id')
                ->constrained('oracle_rates')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['room_type_id', 'rate_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_room_type_rates');
    }
};
