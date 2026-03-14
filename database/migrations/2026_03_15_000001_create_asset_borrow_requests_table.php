<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_borrow_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');

            // Pemohon (dari Aplikasi-Izin)
            $table->string('requester_user_id');        // ID user di Aplikasi-Izin
            $table->string('requester_name');            // Nama pemohon
            $table->string('requester_role')->nullable(); // Role aktif pemohon
            $table->string('requester_app')->default('aplikasi-izin'); // Identifier app

            // Detail peminjaman
            $table->text('purpose');                     // Tujuan peminjaman
            $table->date('start_date');                  // Rencana mulai
            $table->date('end_date')->nullable();        // Rencana selesai
            $table->text('notes')->nullable();           // Catatan tambahan

            // Status & approval
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned'])
                  ->default('pending');
            $table->string('approved_by')->nullable();   // Nama admin yang menyetujui
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Pengembalian
            $table->timestamp('returned_at')->nullable();
            $table->text('return_notes')->nullable();    // Kondisi saat dikembalikan

            $table->timestamps();

            // Index untuk query performa
            $table->index(['requester_user_id', 'requester_app']);
            $table->index(['asset_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_borrow_requests');
    }
};
