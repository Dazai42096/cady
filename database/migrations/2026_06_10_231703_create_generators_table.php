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
        Schema::create('generators', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->nullable()->index();
            $table->string('serial_number')->unique()->nullable();
            $table->string('model')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('capacity_kva', 10, 2)->default(0.00);
            $table->string('fuel_type')->nullable(); // diesel, petrol, gas, etc.
            $table->string('location')->nullable();
            $table->string('status')->default('available')->index(); // available, rented, maintenance, inactive
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generators');
    }
};
