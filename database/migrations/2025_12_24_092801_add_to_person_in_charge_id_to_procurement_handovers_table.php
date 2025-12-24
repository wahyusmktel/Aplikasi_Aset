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
        Schema::table('procurement_handovers', function (Blueprint $table) {
            $table->foreignId('to_person_in_charge_id')->nullable()->after('to_department_id')->constrained('persons_in_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procurement_handovers', function (Blueprint $table) {
            $table->dropForeign(['to_person_in_charge_id']);
            $table->dropColumn('to_person_in_charge_id');
        });
    }
};
