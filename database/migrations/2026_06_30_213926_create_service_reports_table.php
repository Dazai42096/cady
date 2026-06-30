<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('report_number')->unique();

            $table->uuid('customer_id')->nullable()->index();
            $table->uuid('generator_id')->nullable()->index();
            $table->uuid('rental_id')->nullable()->index();
            $table->uuid('maintenance_contract_id')->nullable()->index();
            $table->uuid('created_by')->nullable()->index();

            $table->string('report_type')->default('maintenance')->index();
            $table->string('status')->default('draft')->index();

            $table->date('service_date');
            $table->string('technician_name')->nullable();

            $table->text('fault_description')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('mechanical_work')->nullable();
            $table->text('electrical_work')->nullable();
            $table->text('spare_parts')->nullable();
            $table->text('technician_notes')->nullable();
            $table->text('recommended_follow_up')->nullable();

            $table->boolean('customer_visible')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('generator_id')->references('id')->on('generators')->onDelete('set null');
            $table->foreign('rental_id')->references('id')->on('rentals')->onDelete('set null');
            $table->foreign('maintenance_contract_id')->references('id')->on('maintenance_contracts')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_reports');
    }
};