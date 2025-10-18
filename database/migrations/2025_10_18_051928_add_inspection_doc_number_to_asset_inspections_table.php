<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_inspections', function (Blueprint $table) {
            $table->string('inspection_doc_number')->nullable()->unique()->after('id'); // Nomor surat BAPK
        });
    }

    public function down(): void
    {
        Schema::table('asset_inspections', function (Blueprint $table) {
            $table->dropColumn('inspection_doc_number');
        });
    }
};
