<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_logs', function (Blueprint $table) {
            // Peminjam (bisa dari user akun, bukan hanya employee)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('employee_id');
            $table->string('borrower_name')->nullable()->after('user_id');
            $table->string('borrower_nip')->nullable()->after('borrower_name');

            // Status pengemudi
            $table->string('driver_type')->default('self')->after('destination'); // 'self' atau 'school_driver'
            $table->foreignId('driver_employee_id')->nullable()->constrained('employees')->nullOnDelete()->after('driver_type');

            // Estimasi kembali
            $table->dateTime('estimated_return_time')->nullable()->after('departure_time');

            // Kondisi BBM
            $table->string('fuel_level_start')->nullable()->after('start_odometer');  // Full, 3/4, 1/2, 1/4, Hampir Habis
            $table->string('fuel_level_end')->nullable()->after('end_odometer');

            // Foto pengembalian (JSON array path)
            $table->json('return_photos')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['driver_employee_id']);
            $table->dropColumn([
                'user_id', 'borrower_name', 'borrower_nip',
                'driver_type', 'driver_employee_id',
                'estimated_return_time',
                'fuel_level_start', 'fuel_level_end',
                'return_photos',
            ]);
        });
    }
};
