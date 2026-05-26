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
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dateTime('date')->change();
        });
        Schema::table('stock_outs', function (Blueprint $table) {
            $table->dateTime('date')->change();
        });
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dateTime('date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->date('date')->change();
        });
        Schema::table('stock_outs', function (Blueprint $table) {
            $table->date('date')->change();
        });
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->date('date')->change();
        });
    }
};
