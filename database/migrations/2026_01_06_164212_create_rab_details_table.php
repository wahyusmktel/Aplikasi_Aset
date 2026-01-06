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
        Schema::create('rab_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rab_id')->constrained()->onDelete('cascade');
            $table->foreignId('rkas_id')->constrained('rkas')->onDelete('cascade');
            $table->string('alias_name');
            $table->decimal('quantity', 15, 2);
            $table->string('unit');
            $table->decimal('price', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_details');
    }
};
