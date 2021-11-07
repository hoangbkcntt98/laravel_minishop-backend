<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('price'); 
            $table->string('custom_id')->nullable()->unique();
            $table->unsignedBigInteger('display_id')->unique();
            $table->boolean('warning_by_variation')->default(false)->nullable();
            // $table->string('web_brand_id');
            // $table->foreign('web_brand_id')
            // ->references('id')
            // ->on('web_brands')
            // ->onUpdate('cascade')
            // ->onDelete('cascade');
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
        Schema::dropIfExists('web_products');
    }
}
