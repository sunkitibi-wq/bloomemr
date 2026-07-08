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
        Schema::create('webhook_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained();
            $table->string('name');
            $table->string('endpoint_url');
            $table->string('secret')->nullable();
            $table->json('events');
            $table->string('status')->default('active');
            $table->timestamp('last_sent_at')->nullable();
            $table->unsignedInteger('failure_count')->default(0);
            $table->timestamp('paused_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_subscriptions');
    }
};
