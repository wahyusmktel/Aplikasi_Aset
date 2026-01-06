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
        Schema::create('rabs', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('mta');
            $table->string('nama_akun');
            $table->string('drk');
            $table->string('kebutuhan_waktu');
            $table->decimal('total_amount', 15, 2);
            $table->unsignedBigInteger('created_by_id');
            $table->unsignedBigInteger('checked_by_id');
            $table->unsignedBigInteger('approved_by_id');
            $table->unsignedBigInteger('headmaster_id');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('created_by_id')->references('id')->on('employees');
            $table->foreign('checked_by_id')->references('id')->on('employees');
            $table->foreign('approved_by_id')->references('id')->on('employees');
            $table->foreign('headmaster_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rabs');
    }
};
