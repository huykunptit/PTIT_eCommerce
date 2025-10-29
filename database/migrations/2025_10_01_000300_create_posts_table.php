<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('quote')->nullable();
            $table->text('summary');
            $table->longText('description')->nullable();
            $table->foreignId('post_cat_id')->nullable();
            $table->string('tags')->nullable();
            $table->foreignId('added_by')->constrained('users');
            $table->string('photo')->nullable();
            $table->enum('status', ['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');
    }
};


