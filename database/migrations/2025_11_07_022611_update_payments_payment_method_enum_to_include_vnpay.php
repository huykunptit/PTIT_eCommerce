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
        // Thêm 'vnpay' và 'cod' vào ENUM payment_method
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('credit_card', 'paypal', 'cash_on_delivery', 'vnpay', 'cod') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Trả lại ENUM ban đầu
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('credit_card', 'paypal', 'cash_on_delivery') NOT NULL");
    }
};
