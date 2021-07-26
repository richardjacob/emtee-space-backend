<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::create('space_location', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('space_id')->unique()->unsigned();
            $table->foreign('space_id')->references('id')->on('space');
            $table->string('address_line_1');
            $table->string('address_line_2');
            $table->string('city',30);
            $table->string('state',30);
            $table->string('country',5);
            $table->foreign('country')->references('short_name')->on('country');
            $table->string('postal_code',10);
            $table->string('latitude',20);
            $table->string('longitude',20);
            $table->string('guidance',200);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('space_location');
    }
}
