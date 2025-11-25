<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Jadwal Lab (Planning)
        Schema::create('lab_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade'); // Ruangan Lab
            $table->foreignId('teacher_id')->constrained('employees'); // Guru Pengampu
            $table->string('subject'); // Mata Pelajaran
            $table->string('class_group'); // Kelas (misal: XII RPL 1)
            $table->enum('day_of_week', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('semester')->nullable(); // Ganjil/Genap
            $table->string('academic_year')->nullable(); // 2024/2025
            $table->timestamps();
        });

        // 2. Tabel Log Penggunaan Lab (Realisasi/Jurnal)
        Schema::create('lab_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('employees'); // Guru/Penanggung Jawab saat itu
            $table->string('class_group')->nullable(); // Kelas
            $table->date('usage_date');
            $table->dateTime('check_in_time');
            $table->dateTime('check_out_time')->nullable();
            $table->text('activity_description'); // Materi/Kegiatan
            $table->string('condition_before')->default('Baik');
            $table->string('condition_after')->nullable();
            $table->text('notes')->nullable(); // Catatan kejadian (misal: mouse rusak 1)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_usage_logs');
        Schema::dropIfExists('lab_schedules');
    }
};
