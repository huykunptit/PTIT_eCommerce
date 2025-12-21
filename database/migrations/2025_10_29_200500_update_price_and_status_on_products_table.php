<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        Schema::table('products', function (Blueprint $table) {
            // Increase price precision to avoid out-of-range errors
            $table->decimal('price', 15, 2)->change();
        });

        // Adjust ENUM values for status to match the form (active/inactive)
        // Note: changing ENUM requires raw SQL in MySQL
        if (Schema::hasColumn('products', 'status')) {
            DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
        }
    }

    public function down()
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
        });

        if (Schema::hasColumn('products', 'status')) {
            DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('active','out_of_stock') NOT NULL DEFAULT 'active'");
        }
    }
};


