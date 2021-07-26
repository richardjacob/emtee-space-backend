<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StylesLangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('styles_lang');
        Schema::create('styles_lang', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('style_id')->unsigned();
            $table->foreign('style_id')->references('id')->on('styles');
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
       Schema::drop('styles_lang');
    }
}
