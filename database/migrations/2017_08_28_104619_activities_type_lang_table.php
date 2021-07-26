<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActivitiesTypeLangTable extends Migration
{
    /**new
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('activities_type_lang');
        Schema::create('activities_type_lang', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_type_id')->unsigned();
            $table->foreign('activity_type_id')->references('id')->on('activities_type');
            $table->string('name', 35);
            $table->string('lang_code',5);
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
       Schema::drop('activities_type_lang');
    }
}
