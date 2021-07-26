<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities_price', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('space_id')->unsigned();
            $table->foreign('space_id')->references('id')->on('space');
            $table->integer('activity_id')->unsigned();
            $table->foreign('activity_id')->references('id')->on('space_activities');
            $table->string('currency_code',10)->nullable();
            $table->foreign('currency_code')->references('code')->on('currency');
            $table->tinyInteger('min_hours')->default(1);
            $table->integer('hourly')->nullable();
            $table->integer('full_day')->nullable();
            $table->integer('weekly')->nullable();
            $table->integer('monthly')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities_price');
    }
}
