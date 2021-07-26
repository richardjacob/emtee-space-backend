<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpaceRulesLangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('space_rules_lang');
        Schema::create('space_rules_lang', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('space_rule_id')->unsigned();
            $table->foreign('space_rule_id')->references('id')->on('space_rules');
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
       Schema::drop('space_rules_lang');
    }
}
