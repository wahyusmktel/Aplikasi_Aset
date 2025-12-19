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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('npwp')->nullable();
            $table->timestamps();
        });

        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('procurement_date');
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->string('status')->default('pending'); 
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('procurements')->onDelete('cascade');
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->onDelete('set null');
            $table->integer('quantity');
            $table->integer('received_quantity')->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->text('specs')->nullable();
            $table->boolean('is_converted_to_asset')->default(false);
            $table->timestamps();
        });

        Schema::create('procurement_handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('procurements')->onDelete('cascade');
            $table->string('type'); 
            $table->string('document_number')->unique();
            $table->date('handover_date');
            $table->string('from_name')->nullable();
            $table->foreignId('from_user_id')->nullable()->constrained('users');
            $table->string('to_name')->nullable();
            $table->foreignId('to_user_id')->nullable()->constrained('users');
            $table->foreignId('to_department_id')->nullable()->constrained('departments');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_handovers');
        Schema::dropIfExists('procurement_items');
        Schema::dropIfExists('procurements');
        Schema::dropIfExists('vendors');
    }
};
