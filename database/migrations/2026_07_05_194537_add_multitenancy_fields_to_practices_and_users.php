<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('slug');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_system_admin')->default(false)->after('practice_id');
            $table->unsignedBigInteger('practice_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('practices', fn (Blueprint $t) => $t->dropColumn('is_active'));
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn('is_system_admin');
        });
    }
};
