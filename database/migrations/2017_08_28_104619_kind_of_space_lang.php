<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KindOfSpaceLang extends Migration
{
    /**new
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('kind_of_space_lang');
        Schema::create('kind_of_space_lang', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kind_of_space_id')->unsigned();
            $table->foreign('kind_of_space_id')->references('id')->on('kind_of_space');
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
       Schema::drop('kind_of_space_lang');
    }
}
