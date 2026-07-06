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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('category'); // vaccine, supply, drug, equipment
            $table->integer('stock_quantity')->default(0);
            $table->integer('reorder_level')->default(5);
            $table->string('status')->default('active'); // active, low_stock, out_of_stock
            $table->timestamps();
        });

        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->string('asset_name');
            $table->string('serial_number')->nullable();
            $table->string('status')->default('operational'); // operational, maintenance_required, under_repair
            $table->timestamp('last_calibrated_at')->nullable();
            $table->timestamp('next_calibration_due')->nullable();
            $table->timestamps();
        });

        Schema::create('patient_cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('patient_cohort_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('patient_cohorts')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at');
            $table->timestamps();
        });

        Schema::create('care_gaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('gap_type'); // vaccine_due, annual_wellness, medication_review, lab_followup
            $table->text('description')->nullable();
            $table->string('status')->default('open'); // open, closed
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('care_gaps');
        Schema::dropIfExists('patient_cohort_members');
        Schema::dropIfExists('patient_cohorts');
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('inventory_items');
    }
};
