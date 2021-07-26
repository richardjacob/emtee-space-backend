<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceDescriptionLangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('space_description_lang', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('space_id')->unsigned();
            $table->foreign('space_id')->references('id')->on('space');
            $table->string('lang_code', 5);
            $table->string('name', 35);
            $table->string('summary', 500);
            $table->text('space');
            $table->text('access');
            $table->text('interaction');
            $table->text('notes');
            $table->text('house_rules');
            $table->text('neighborhood_overview');
            $table->text('transit');
        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('space_description_lang');
    }
}
