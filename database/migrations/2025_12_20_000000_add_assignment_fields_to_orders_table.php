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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('user_id')->constrained('users')->onDelete('set null')->comment('Nhân viên bán hàng được phân công');
            $table->foreignId('assigned_shipper')->nullable()->after('assigned_to')->constrained('users')->onDelete('set null')->comment('Nhân viên giao hàng được phân công');
            $table->foreignId('assigned_packer')->nullable()->after('assigned_shipper')->constrained('users')->onDelete('set null')->comment('Nhân viên đóng hàng được phân công');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['assigned_shipper']);
            $table->dropForeign(['assigned_packer']);
            $table->dropColumn(['assigned_to', 'assigned_shipper', 'assigned_packer']);
        });
    }
};

