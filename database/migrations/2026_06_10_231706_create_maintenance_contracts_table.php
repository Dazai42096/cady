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
        Schema::create('maintenance_contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->index();
            $table->uuid('generator_id')->nullable()->index();
            $table->foreignId('created_by')->nullable()->index();
            $table->string('ref_number')->unique()->index();
            $table->string('to_name');
            $table->string('project')->nullable();
            $table->string('status')->default('draft')->index(); // draft, sent, active, expired, terminated
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->integer('visit_count')->default(0);
            $table->string('payment_method')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_value', 15, 2)->default(0.00);
            $table->string('currency')->default('JOD');
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('generator_id')->references('id')->on('generators')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_contracts');
    }
};
