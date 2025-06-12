<?php

use App\Models\OracleRestriction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oracle_restrictions', function (Blueprint $table) {
            $table->id();
            $table->string('restriction_code', 200)->unique();
            $table->string('name', 255);
            $table->enum('restriction_type', array_keys(OracleRestriction::RESTRICTION_TYPE));
            $table->timestamps();

            $table->index('restriction_code', 'idx_restriction_code');
            $table->index('restriction_type', 'idx_restriction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_restrictions');
    }
};
