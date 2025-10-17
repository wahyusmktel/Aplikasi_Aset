<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->timestamp('assigned_date'); // Tanggal aset diserahkan
            $table->timestamp('returned_date')->nullable(); // Tanggal aset dikembalikan (diisi saat check-in)
            $table->string('condition_on_assign'); // Kondisi saat diserahkan (misal: Baik, Ada Goresan)
            $table->string('condition_on_return')->nullable(); // Kondisi saat dikembalikan
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
        });

        // Tambahkan kolom status ke tabel assets
        Schema::table('assets', function (Blueprint $table) {
            $table->string('current_status')->after('status')->default('Tersedia'); // Misal: Tersedia, Dipinjam, Rusak
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('current_status');
        });
    }
};
