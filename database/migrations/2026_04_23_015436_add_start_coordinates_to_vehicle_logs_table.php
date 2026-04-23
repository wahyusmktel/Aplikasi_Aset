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
        Schema::table('vehicle_logs', function (Blueprint $table) {
            $table->decimal('start_latitude', 10, 8)->nullable()->after('start_odometer');
            $table->decimal('start_longitude', 11, 8)->nullable()->after('start_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_logs', function (Blueprint $table) {
            $table->dropColumn(['start_latitude', 'start_longitude']);
        });
    }
};
