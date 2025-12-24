<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(
            "ALTER TABLE orders MODIFY COLUMN shipping_status " .
            "ENUM('pending_confirmation','pending_pickup','in_transit','delivered','cancelled','returned') " .
            "NOT NULL DEFAULT 'pending_confirmation'"
        );

        // Backfill: existing NULL/empty values (if any) -> pending_pickup to preserve current behavior
        DB::statement("UPDATE orders SET shipping_status = 'pending_pickup' WHERE shipping_status IS NULL OR shipping_status = ''");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(
            "ALTER TABLE orders MODIFY COLUMN shipping_status " .
            "ENUM('pending_pickup','in_transit','delivered','cancelled','returned') " .
            "NOT NULL DEFAULT 'pending_pickup'"
        );
    }
};
