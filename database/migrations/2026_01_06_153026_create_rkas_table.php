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
        Schema::create('rkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('kode_lokasi')->nullable();
            $table->string('struktur_pp')->nullable();
            $table->string('kode_pp')->nullable();
            $table->string('nama_pp')->nullable();
            $table->string('kode_rkm')->nullable();
            $table->string('kode_drk')->nullable();
            $table->string('nama_drk')->nullable();
            $table->string('mta')->nullable();
            $table->string('nama_akun')->nullable();
            $table->text('rincian_kegiatan')->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('tarif', 15, 2)->default(0);
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('bulan', 20)->nullable();
            $table->string('sumber_anggaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rkas');
    }
};
