<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Products extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->float('price')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->integer('qty')->nullable();
            $table->boolean('isHighlighted')->nullable();
            $table->boolean('isTrending')->nullable();
            $table->boolean('isActive')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
