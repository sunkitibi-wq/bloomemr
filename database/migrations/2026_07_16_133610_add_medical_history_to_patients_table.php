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
        Schema::table('patients', function (Blueprint $table) {
            $table->text('past_medical_history')->nullable()->after('problem_list');
            $table->text('surgical_history')->nullable()->after('past_medical_history');
            $table->text('family_history')->nullable()->after('surgical_history');
            $table->text('social_history')->nullable()->after('family_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'past_medical_history',
                'surgical_history',
                'family_history',
                'social_history',
            ]);
        });
    }
};
