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
        Schema::table('rabs', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_id')->nullable()->change();
            $table->unsignedBigInteger('checked_by_id')->nullable()->change();
            $table->unsignedBigInteger('approved_by_id')->nullable()->change();
            $table->unsignedBigInteger('headmaster_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rabs', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_id')->nullable(false)->change();
            $table->unsignedBigInteger('checked_by_id')->nullable(false)->change();
            $table->unsignedBigInteger('approved_by_id')->nullable(false)->change();
            $table->unsignedBigInteger('headmaster_id')->nullable(false)->change();
        });
    }
};
