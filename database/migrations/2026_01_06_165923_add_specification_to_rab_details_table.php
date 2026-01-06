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
        Schema::table('rab_details', function (Blueprint $table) {
            $table->string('specification')->nullable()->after('alias_name');
        });
    }

    public function down(): void
    {
        Schema::table('rab_details', function (Blueprint $table) {
            $table->dropColumn('specification');
        });
    }
};
