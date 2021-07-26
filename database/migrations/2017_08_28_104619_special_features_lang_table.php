<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpecialFeaturesLangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('special_features_lang');
        Schema::create('special_features_lang', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('special_feature_id')->unsigned();
            $table->foreign('special_feature_id')->references('id')->on('special_features');
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
       Schema::drop('special_features_lang');
    }
}
