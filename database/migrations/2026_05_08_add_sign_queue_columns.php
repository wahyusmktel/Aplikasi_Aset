<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_digital_signatures', function (Blueprint $table) {
            $table->boolean('auto_sign_bast')->default(true)->after('is_active');
        });

        Schema::table('digital_documents', function (Blueprint $table) {
            // 'signed' = already signed, 'pending' = waiting manual sign, 'revoked' = revoked
            $table->string('status')->default('signed')->after('is_valid');
            $table->index(['signed_by', 'status'], 'dd_signed_by_status');
        });
    }

    public function down(): void
    {
        Schema::table('user_digital_signatures', function (Blueprint $table) {
            $table->dropColumn('auto_sign_bast');
        });

        Schema::table('digital_documents', function (Blueprint $table) {
            $table->dropIndex('dd_signed_by_status');
            $table->dropColumn('status');
        });
    }
};
