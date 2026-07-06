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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->json('cpt_codes'); // e.g. [{"code": "90791", "description": "Psychiatric Evaluation", "fee": 150.00}]
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('draft'); // draft, pending, paid, void
            $table->string('insurance_claim_status')->default('unsubmitted'); // unsubmitted, submitted, accepted, rejected
            $table->date('due_date');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
