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
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('phone', 50)->nullable()->after('username');
            $table->text('address')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('branch_id');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->text('description')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone', 'address', 'is_active']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_active']);
        });
    }
};
