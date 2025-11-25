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
        Schema::table('lab_usage_logs', function (Blueprint $table) {
            $table->string('checkin_doc_number')->nullable()->after('id');
            $table->string('checkout_doc_number')->nullable()->after('checkin_doc_number');
        });
    }

    public function down(): void
    {
        Schema::table('lab_usage_logs', function (Blueprint $table) {
            $table->dropColumn(['checkin_doc_number', 'checkout_doc_number']);
        });
    }
};
