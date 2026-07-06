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
        Schema::create('radiology_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ordered_by')->constrained('users')->cascadeOnDelete();
            $table->string('procedure_name');
            $table->text('clinical_indication')->nullable();
            $table->string('status')->default('ordered'); // ordered, completed, reported, cancelled
            $table->timestamp('order_date');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('radiology_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('radiology_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('radiologist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('findings');
            $table->text('impression');
            $table->string('attachment_path')->nullable();
            $table->timestamp('reported_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiology_reports');
        Schema::dropIfExists('radiology_orders');
    }
};
