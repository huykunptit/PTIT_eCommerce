<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'status')) {
            // Set existing null/empty statuses to 'active'
            DB::statement("UPDATE users SET status = 'active' WHERE status IS NULL OR status = ''");
            // Ensure column has NOT NULL with default 'active'
            DB::statement("ALTER TABLE users MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'active'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'status')) {
            // Remove default while keeping NOT NULL (original state from add_status migration)
            DB::statement("ALTER TABLE users MODIFY COLUMN status VARCHAR(255) NOT NULL");
        }
    }
};
