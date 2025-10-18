<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->date('maintenance_date'); // Tanggal perbaikan/perawatan
            $table->string('type'); // Jenis (Perbaikan, Perawatan Rutin, Upgrade)
            $table->text('description'); // Deskripsi pekerjaan yang dilakukan
            $table->decimal('cost', 15, 2)->nullable(); // Biaya (opsional)
            $table->string('technician')->nullable(); // Nama teknisi/vendor (opsional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
