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
        Schema::create('maintenance_visits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('maintenance_contract_id')->index();
            $table->integer('visit_number');
            $table->date('planned_date')->nullable();
            $table->date('confirmed_date')->nullable();
            $table->date('actual_date')->nullable();
            $table->string('status')->default('scheduled')->index(); // scheduled, confirmed, in_progress, completed, cancelled
            $table->foreignId('assigned_to')->nullable()->index(); // references technician (support user)
            $table->text('technician_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign keys
            $table->foreign('maintenance_contract_id')->references('id')->on('maintenance_contracts')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_visits');
    }
};
