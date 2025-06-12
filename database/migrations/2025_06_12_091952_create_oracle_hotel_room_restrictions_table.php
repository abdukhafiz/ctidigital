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
        Schema::create('oracle_hotel_room_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')
                ->constrained('oracle_hotels')
                ->cascadeOnDelete();
            $table->foreignId('room_type_id')
                ->constrained('oracle_roomtypes')
                ->cascadeOnDelete();
            $table->foreignId('restriction_id')
                ->constrained('oracle_restrictions')
                ->cascadeOnDelete();
            $table->date('date');
            $table->timestamps();

            $table->index('date', 'idx_restriction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_hotel_room_restrictions');
    }
};
