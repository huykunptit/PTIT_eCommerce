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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('status', 50)->nullable()->after('amount');
            $table->string('transaction_no')->nullable()->after('status');
            $table->text('transaction_data')->nullable()->after('transaction_no');
            $table->string('bank_code', 50)->nullable()->after('transaction_data');
            $table->dateTime('pay_date')->nullable()->after('bank_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'transaction_no',
                'transaction_data',
                'bank_code',
                'pay_date',
            ]);
        });
    }
};
