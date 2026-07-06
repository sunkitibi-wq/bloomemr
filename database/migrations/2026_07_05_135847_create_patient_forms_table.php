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
        Schema::create('form_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type'); // intake, consent
            $table->json('fields'); // Form configuration / fields schema
            $table->integer('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('patient_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('practice_id')->constrained('practices')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('form_templates')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, completed
            $table->json('form_data')->nullable();
            $table->string('signature_name')->nullable();
            $table->string('signature_ip')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_forms');
        Schema::dropIfExists('form_templates');
    }
};
