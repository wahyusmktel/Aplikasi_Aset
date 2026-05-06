<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rab_handovers', function (Blueprint $table) {
            $table->string('handed_by_jabatan')->nullable()->after('handed_by');
            $table->string('received_by_jabatan')->nullable()->after('received_by');
        });
    }

    public function down(): void
    {
        Schema::table('rab_handovers', function (Blueprint $table) {
            $table->dropColumn(['handed_by_jabatan', 'received_by_jabatan']);
        });
    }
};
