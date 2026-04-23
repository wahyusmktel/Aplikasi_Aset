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
            $table->string('status')->default('pengajuan')->after('notes');
            $table->timestamp('waka_approved_at')->nullable()->after('status');
            $table->timestamp('kepsek_approved_at')->nullable()->after('waka_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_logs', function (Blueprint $table) {
            $table->dropColumn(['status', 'waka_approved_at', 'kepsek_approved_at']);
        });
    }
};
