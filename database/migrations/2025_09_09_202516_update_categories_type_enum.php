<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, change the column to allow 'income' type
        DB::statement("ALTER TABLE categories MODIFY COLUMN type ENUM('income', 'needs', 'wants', 'savings') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE categories MODIFY COLUMN type ENUM('needs', 'wants', 'savings') NOT NULL");
    }
};
