<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saved_filters', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->string('scope', 64);        // contoh: 'assets_summary'
            $t->string('name', 100);        // nama preset
            $t->json('payload');            // query string/array filter
            $t->timestamps();

            $t->index(['user_id', 'scope']);
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('saved_filters');
    }
};
