<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->string('checkout_doc_number')->nullable()->unique()->after('id'); // Nomor surat saat serah terima
            $table->string('return_doc_number')->nullable()->unique()->after('checkout_doc_number'); // Nomor surat saat pengembalian
        });
    }

    public function down(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropColumn(['checkout_doc_number', 'return_doc_number']);
        });
    }
};
