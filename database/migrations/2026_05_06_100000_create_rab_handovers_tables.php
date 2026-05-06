<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rab_handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rab_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('document_number');
            $table->date('handover_date');
            $table->string('handed_by');
            $table->string('received_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('rab_handover_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rab_handover_id')->constrained('rab_handovers')->onDelete('cascade');
            $table->string('uraian');
            $table->decimal('qty', 10, 2)->nullable();
            $table->string('spesifikasi')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rab_handover_items');
        Schema::dropIfExists('rab_handovers');
    }
};
