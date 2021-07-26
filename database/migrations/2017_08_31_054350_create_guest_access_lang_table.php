<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestAccessLangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_access_lang', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guest_access_id')->unsigned();
            $table->foreign('guest_access_id')->references('id')->on('guest_access');
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
        Schema::dropIfExists('guest_access_lang');
    }
}
