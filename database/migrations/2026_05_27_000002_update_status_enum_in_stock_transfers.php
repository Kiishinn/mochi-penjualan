<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE stock_transfers MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'received') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Reverting this might cause data loss if there are 'received' records, 
        // but we'll try to revert back to the old enum for completeness
        DB::statement("ALTER TABLE stock_transfers MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
