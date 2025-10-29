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
        Schema::table('users', function (Blueprint $table) {
            // Rollback về enum values cũ
            $table->enum('role', ['buyer', 'seller'])->default('buyer')->change();
        });
    }
}; 