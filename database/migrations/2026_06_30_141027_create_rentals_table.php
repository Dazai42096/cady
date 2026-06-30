<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('ref_number')->unique();

            $table->uuid('customer_id')->index();
            $table->uuid('generator_id')->index();

            $table->date('start_date');
            $table->date('end_date');

            $table->decimal('monthly_rate', 15, 3);
            $table->string('currency', 3)->default('JOD');

            $table->string('status')->default('draft')->index();

            $table->unsignedInteger('initial_hour_meter')->default(0);
            $table->unsignedInteger('final_hour_meter')->nullable();

            $table->unsignedInteger('calculated_days')->default(0);
            $table->decimal('total_amount', 15, 3)->default(0);
            $table->json('calculation_breakdown')->nullable();

            $table->timestamp('activated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('generator_id')->references('id')->on('generators')->onDelete('cascade');

            $table->index(['generator_id', 'start_date', 'end_date']);
            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};