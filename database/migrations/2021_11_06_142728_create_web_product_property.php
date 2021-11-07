<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebProductProperty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_product_property', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('web_product_property_id');
            $table->foreign('web_product_property_id')
            ->references('id')
            ->on('web_product_properties')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->unsignedBigInteger('web_product_id');
            $table->foreign('web_product_id')
            ->references('id')
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
        Schema::dropIfExists('web_product_property');
    }
}
