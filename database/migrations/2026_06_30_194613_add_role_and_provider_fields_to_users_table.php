<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('clinical_staff')->after('email');
            $table->string('npi_number')->nullable()->after('role');
            $table->string('dea_number')->nullable()->after('npi_number');
            $table->string('phone')->nullable()->after('dea_number');
            $table->string('timezone')->default('America/New_York')->after('phone');
            $table->boolean('is_active')->default(true)->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'npi_number', 'dea_number', 'phone', 'timezone', 'is_active']);
        });
    }
};
