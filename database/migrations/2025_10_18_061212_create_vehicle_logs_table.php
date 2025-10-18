<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->comment('FK ke tabel assets (hanya KBM Dinas)'); // Link ke Aset Kendaraan
            $table->foreignId('employee_id')->constrained('employees'); // Link ke Pegawai yang menggunakan
            $table->dateTime('departure_time'); // Waktu Berangkat
            $table->dateTime('return_time')->nullable(); // Waktu Kembali (null jika belum kembali)
            $table->string('destination'); // Tujuan Perjalanan
            $table->text('purpose'); // Keperluan Perjalanan
            $table->integer('start_odometer'); // Kilometer Awal
            $table->integer('end_odometer')->nullable(); // Kilometer Akhir
            $table->string('condition_on_checkout'); // Kondisi saat ambil
            $table->string('condition_on_checkin')->nullable(); // Kondisi saat kembali
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->string('checkout_doc_number')->nullable()->unique(); // Nomor BAST Ambil
            $table->string('checkin_doc_number')->nullable()->unique(); // Nomor BAP Kembali
            $table->timestamps();
        });

        // Update status di tabel assets jika belum ada
        // Pastikan kolom ini sudah ada dari migrasi AssetAssignment
        if (!Schema::hasColumn('assets', 'current_status')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('current_status')->after('status')->default('Tersedia');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_logs');
        // Jangan hapus current_status karena mungkin masih dipakai Assignment
    }
};
