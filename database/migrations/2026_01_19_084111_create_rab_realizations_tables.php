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
        Schema::create('rab_realizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rab_id')->constrained()->onDelete('cascade');
            $table->decimal('total_penerimaan', 15, 2)->default(0);
            $table->decimal('total_pengeluaran', 15, 2)->default(0);
            $table->decimal('final_balance', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('rab_realization_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rab_realization_id')->constrained('rab_realizations')->onDelete('cascade');
            $table->string('tgl')->nullable();
            $table->string('uraian');
            $table->decimal('penerimaan', 15, 2)->default(0);
            $table->decimal('pengeluaran', 15, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_realization_details');
        Schema::dropIfExists('rab_realizations');
    }
};
