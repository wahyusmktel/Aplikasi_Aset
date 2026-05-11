<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('jenis_kelamin')->nullable()->after('position');       // L / P
            $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama')->nullable()->after('tanggal_lahir');
            $table->string('status_perkawinan')->nullable()->after('agama');     // Belum Kawin, Kawin, Cerai
            $table->string('nuptk')->nullable()->unique()->after('status_perkawinan');
            $table->string('status_kepegawaian')->nullable()->after('nuptk');    // PNS, PPPK, GTY, GTT, Honorer
            $table->string('golongan_pangkat')->nullable()->after('status_kepegawaian');
            $table->date('tmt_pengangkatan')->nullable()->after('golongan_pangkat');
            $table->string('pendidikan_terakhir')->nullable()->after('tmt_pengangkatan'); // S1, S2, D3, dll
            $table->string('bidang_studi')->nullable()->after('pendidikan_terakhir');
            $table->string('lembaga_pendidikan')->nullable()->after('bidang_studi');
            $table->text('alamat')->nullable()->after('lembaga_pendidikan');
            $table->string('no_hp', 20)->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama',
                'status_perkawinan', 'nuptk', 'status_kepegawaian',
                'golongan_pangkat', 'tmt_pengangkatan', 'pendidikan_terakhir',
                'bidang_studi', 'lembaga_pendidikan', 'alamat', 'no_hp',
            ]);
        });
    }
};
