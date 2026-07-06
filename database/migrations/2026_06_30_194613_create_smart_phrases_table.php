<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_phrases', function (Blueprint $table) {
            $table->id();
            $table->string('trigger');
            $table->text('expansion');
            $table->string('category')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users');
            $table->boolean('is_global')->default(false);
            $table->boolean('is_ai_suggested')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_phrases');
    }
};
