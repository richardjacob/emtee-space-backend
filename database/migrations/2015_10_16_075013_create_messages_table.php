<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('space_id')->unsigned();
            $table->integer('reservation_id')->unsigned()->nullable();
            $table->integer('user_to')->unsigned();
            $table->foreign('user_to')->references('id')->on('users');
            $table->integer('user_from')->unsigned();
            $table->foreign('user_from')->references('id')->on('users');
            $table->text('message')->nullable();
            $table->integer('message_type')->unsigned();
            $table->foreign('message_type')->references('id')->on('message_type');
            $table->enum('read', ['0', '1']);
            $table->enum('archive', ['0', '1']);
            $table->enum('star', ['0', '1']);
            $table->integer('special_offer_id')->nullable();
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
        Schema::dropIfExists('messages');
    }
}
