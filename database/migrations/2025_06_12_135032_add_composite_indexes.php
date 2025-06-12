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
        Schema::table('oracle_room_counts', function (Blueprint $table) {
            $table->index(['hotel_id', 'room_type_id', 'availability_date'], 'idx_hotel_room_date');
        });

        Schema::table('oracle_rate_prices', function (Blueprint $table) {
            $table->index(['hotel_id', 'room_type_id', 'rate_id', 'price_date'], 'idx_hotel_room_rate_date');
        });

        Schema::table('oracle_hotel_room_restrictions', function (Blueprint $table) {
            $table->index(['hotel_id', 'room_type_id', 'restriction_id', 'date'], 'idx_hotel_room_restriction_date');
        });

        Schema::table('custom_room_restrictions', function (Blueprint $table) {
            $table->index(['hotel_id', 'room_type_id'], 'idx_hotel_room');
        });

        Schema::table('price_modifiers', function (Blueprint $table) {
            $table->index(['hotel_id', 'rate_id', 'date'], 'idx_hotel_rate_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oracle_room_counts', function (Blueprint $table) {
            $table->dropIndex('idx_hotel_room_date');
        });

        Schema::table('oracle_rate_prices', function (Blueprint $table) {
            $table->dropIndex('idx_hotel_room_rate_date');
        });

        Schema::table('oracle_hotel_room_restrictions', function (Blueprint $table) {
            $table->dropIndex('idx_hotel_room_restriction_date');
        });

        Schema::table('custom_room_restrictions', function (Blueprint $table) {
            $table->dropIndex('idx_hotel_room');
        });

        Schema::table('price_modifiers', function (Blueprint $table) {
            $table->dropIndex('idx_hotel_rate_date');
        });
    }
};
