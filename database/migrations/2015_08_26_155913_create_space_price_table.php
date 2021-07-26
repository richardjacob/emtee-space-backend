<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpacePriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('space_price', function (Blueprint $table) {
            $table->integer('space_id')->unique()->unsigned();
            $table->foreign('space_id')->references('id')->on('space');
            $table->integer('security');
            $table->string('currency_code',10);
            $table->foreign('currency_code')->references('code')->on('currency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('space_price');
    }
}
