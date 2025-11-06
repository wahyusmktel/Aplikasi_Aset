<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel 'assets'
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');

            // Relasi ke 'users' (siapa yang ditugaskan, opsional tapi bagus)
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('title'); // Judul pekerjaan, misal: "Servis AC Bulanan"
            $table->text('description')->nullable(); // Detail apa yang harus dilakukan
            $table->string('maintenance_type'); // Tipe: 'preventive', 'corrective', 'inspection'

            $table->date('schedule_date'); // Tanggal dijadwalkan
            $table->date('completed_at')->nullable(); // Tanggal selesai (diisi saat status = completed)

            $table->string('status')->default('scheduled'); // Status: 'scheduled', 'in_progress', 'completed', 'cancelled'
            $table->text('notes')->nullable(); // Catatan dari teknisi saat pengerjaan

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
