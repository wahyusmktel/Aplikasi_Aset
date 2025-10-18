<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->date('inspection_date'); // Tanggal pemeriksaan
            $table->string('condition'); // Kondisi (Baik, Perlu Perbaikan, Rusak Ringan, Rusak Berat)
            $table->text('notes')->nullable(); // Catatan hasil pemeriksaan
            $table->foreignId('inspector_id')->nullable()->constrained('users')->onDelete('set null'); // ID user yang melakukan inspeksi (opsional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_inspections');
    }
};
