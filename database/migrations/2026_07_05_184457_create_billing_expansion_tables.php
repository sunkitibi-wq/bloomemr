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
        Schema::create('eligibility_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('status'); // eligible, ineligible, error
            $table->decimal('copay_amount', 8, 2)->default(0.00);
            $table->decimal('deductible_amount', 8, 2)->default(0.00);
            $table->string('payer_name');
            $table->timestamp('checked_at');
            $table->timestamps();
        });

        Schema::create('claim_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->longText('edi_request');
            $table->longText('edi_response');
            $table->string('status'); // accepted, rejected, processing
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->string('payment_method'); // credit_card
            $table->string('transaction_reference');
            $table->timestamp('paid_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('claim_submissions');
        Schema::dropIfExists('eligibility_checks');
    }
};
