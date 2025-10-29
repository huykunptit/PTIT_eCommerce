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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->foreignId('seller_id')->constrained('users');
            $table->foreignId('category_id')->constrained('categories');  // Khóa ngoại tham chiếu đến bảng categories
            $table->string('image_url')->nullable();
            $table->enum('status', ['active', 'out_of_stock'])->default('active');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('products');
    }

};
