<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('mrn')->unique()->comment('Medical Record Number');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('gender_identity')->nullable();
            $table->string('pronouns')->nullable();
            $table->string('race_ethnicity')->nullable();
            $table->string('preferred_language')->default('en');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('primary_insurance')->nullable();
            $table->string('secondary_insurance')->nullable();
            $table->text('allergies')->nullable();
            $table->text('problem_list')->nullable();
            $table->string('photo_path')->nullable();
            $table->foreignId('primary_provider_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
