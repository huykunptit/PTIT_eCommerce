<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Skip on sqlite (test env) to avoid DBAL requirement and unsupported enum alteration
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        Schema::table('users', function (Blueprint $table) {
            // Thay đổi enum values của cột role từ ['buyer', 'seller'] thành ['user', 'admin']
            $table->enum('role', ['user', 'admin'])->default('user')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        Schema::table('users', function (Blueprint $table) {
            // Rollback về enum values cũ
            $table->enum('role', ['buyer', 'seller'])->default('buyer')->change();
        });
    }
}; 