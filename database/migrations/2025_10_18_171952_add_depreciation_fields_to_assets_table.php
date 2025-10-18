<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('purchase_cost', 15, 2)->default(0)->after('purchase_year'); // Harga Beli
            $table->integer('useful_life')->nullable()->after('purchase_cost'); // Masa Manfaat (tahun)
            $table->decimal('salvage_value', 15, 2)->default(0)->after('useful_life'); // Nilai Sisa/Residu
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['purchase_cost', 'useful_life', 'salvage_value']);
        });
    }
};
