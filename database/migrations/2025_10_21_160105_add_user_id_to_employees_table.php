<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // user_id bisa null (jika pegawai belum punya akun), tapi harus unik jika diisi
            // onDelete('set null') artinya jika user dihapus, kolom user_id di employee jadi null
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Hati-hati saat drop foreign key di MySQL
            // Cek nama constraint jika default tidak bekerja: $table->dropForeign(['user_id']);
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
