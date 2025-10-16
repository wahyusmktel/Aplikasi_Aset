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
        Schema::create('assets', function (Blueprint $table) {
            $table->id(); // Sesuai kolom "No"
            $table->string('name'); // NamaBarang
            $table->year('purchase_year'); // TahunPembelian(YYYY)
            $table->string('asset_code_ypt')->nullable()->unique(); // KodeAsetYPT
            $table->string('sequence_number', 4); // NoUrutBarang(4)

            // Foreign Keys untuk relasi ke tabel master
            $table->foreignId('institution_id')->constrained('institutions');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('building_id')->constrained('buildings');
            $table->foreignId('room_id')->constrained('rooms');
            $table->foreignId('faculty_id')->constrained('faculties');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('person_in_charge_id')->constrained('persons_in_charge');
            $table->foreignId('asset_function_id')->constrained('asset_functions');
            $table->foreignId('funding_source_id')->constrained('funding_sources');

            $table->string('status')->nullable(); // Status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
