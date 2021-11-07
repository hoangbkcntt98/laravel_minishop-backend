<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_product_variations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('display_id')->nullable()->unique();
            $table->string('custom_id')->nullable()->unique();
            $table->integer('color')->nullable();
            $table->integer('size')->nullable();
            $table->string('images')->nullable();
            $table->unsignedBigInteger('web_product_id');
            $table->foreign('web_product_id')
            ->references('display_id')
            ->on('web_products')
            ->onUpdate('cascade')
            ->onDelete('cascade');
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
        Schema::dropIfExists('web_product_variations');
    }
}
