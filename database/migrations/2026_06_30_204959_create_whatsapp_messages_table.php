<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('customer_id')->nullable()->index();
            $table->uuid('quotation_id')->nullable()->index();
            $table->uuid('rental_id')->nullable()->index();
            $table->uuid('maintenance_contract_id')->nullable()->index();

            $table->string('phone', 30);
            $table->string('message_type')->default('general')->index();
            $table->string('status')->default('draft')->index();

            $table->text('message_body');
            $table->text('whatsapp_url')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('set null');
            $table->foreign('rental_id')->references('id')->on('rentals')->onDelete('set null');
            $table->foreign('maintenance_contract_id')->references('id')->on('maintenance_contracts')->onDelete('set null');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};