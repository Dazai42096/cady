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
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->index();
            $table->foreignId('created_by')->nullable()->index();
            $table->string('ref_number')->unique()->index();
            $table->string('type')->default('sale')->index(); // sale, rental, maintenance_contract, spare_parts, other
            $table->string('status')->default('draft')->index(); // draft, sent, accepted, rejected, expired
            $table->date('quotation_date');
            $table->date('valid_until')->nullable();
            $table->string('project')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00); // percentage, e.g. 16.00 for 16%
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
            $table->string('currency')->default('JOD');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
