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
        Schema::table('assets', function (Blueprint $table) {
            $table->date('disposal_date')->nullable()->after('current_status'); // Tanggal disposal
            $table->string('disposal_method')->nullable()->after('disposal_date'); // Cara (Dijual, Dihapusbukukan, Hilang, Dihibahkan)
            $table->text('disposal_reason')->nullable()->after('disposal_method'); // Alasan disposal
            $table->decimal('disposal_value', 15, 2)->nullable()->after('disposal_reason'); // Nilai jual (jika dijual)
            $table->string('disposal_doc_number')->nullable()->unique()->after('disposal_value'); // Nomor BAPh
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'disposal_date',
                'disposal_method',
                'disposal_reason',
                'disposal_value',
                'disposal_doc_number',
            ]);
        });
    }
};
