<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_audits', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('asset_id')->index();
            $t->string('action', 100); // e.g. bulk_move, bulk_status, create, update
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('actor_name', 150)->nullable();
            $t->string('ip_address', 64)->nullable();
            $t->json('before')->nullable();
            $t->json('after')->nullable();
            $t->timestamps();

            $t->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $t->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_audits');
    }
};
