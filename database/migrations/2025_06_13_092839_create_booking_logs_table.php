<?php

use App\Models\BookingLog;
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
        Schema::create('booking_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_reference')->unique();
            $table->string('hotel_code');
            $table->date('arrival_date');
            $table->date('departure_date');
            $table->integer('rooms_count');
            $table->enum('status', array_keys(BookingLog::STATUSES));
            $table->integer('successful_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->json('request_data');
            $table->json('response_data')->nullable();
            $table->timestamps();

            $table->index(['hotel_code', 'arrival_date']);
            $table->index('booking_reference');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_logs');
    }
};
