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
        Schema::create('custom_room_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')
                ->constrained('oracle_hotels')
                ->cascadeOnDelete();
            $table->foreignId('room_type_id')
                ->constrained('oracle_roomtypes')
                ->cascadeOnDelete();
            $table->integer('max_adults')->nullable();
            $table->integer('max_children')->nullable();
            $table->boolean('children_allowed')->default(true);
            $table->integer('max_guests')->nullable();
            $table->date('closure_start_date')->nullable();
            $table->date('closure_end_date')->nullable();
            $table->timestamps();

            $table->index(['closure_start_date','closure_end_date'],'idx_closure_date_range');
            $table->index('children_allowed','idx_children_allowed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_room_restrictions');
    }
};
