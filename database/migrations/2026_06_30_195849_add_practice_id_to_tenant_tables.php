<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('practice_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('practice_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('encounters', function (Blueprint $table) {
            $table->foreignId('practice_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('clinical_notes', function (Blueprint $table) {
            $table->foreignId('practice_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('practice_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('smart_phrases', function (Blueprint $table) {
            $table->foreignId('practice_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('smart_phrases', fn (Blueprint $t) => $t->dropConstrainedForeignId('practice_id'));
        Schema::table('documents', fn (Blueprint $t) => $t->dropConstrainedForeignId('practice_id'));
        Schema::table('clinical_notes', fn (Blueprint $t) => $t->dropConstrainedForeignId('practice_id'));
        Schema::table('encounters', fn (Blueprint $t) => $t->dropConstrainedForeignId('practice_id'));
        Schema::table('patients', fn (Blueprint $t) => $t->dropConstrainedForeignId('practice_id'));
        Schema::table('users', fn (Blueprint $t) => $t->dropConstrainedForeignId('practice_id'));
    }
};
