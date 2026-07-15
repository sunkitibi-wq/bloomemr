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
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status')->default('unsubmitted');
            $table->json('kyc_data')->nullable();
            $table->text('kyc_rejection_reason')->nullable();
            $table->timestamp('subscribed_until')->nullable();
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->boolean('is_enterprise')->default(false);
            $table->timestamp('enterprise_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kyc_status', 'kyc_data', 'kyc_rejection_reason', 'subscribed_until']);
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn(['is_enterprise', 'enterprise_expires_at']);
        });
    }
};
