<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKindOfSpaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('kind_of_space');

        Schema::create('kind_of_space', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 25);
            $table->string('image', 200);
            $table->enum('status', ['Active','Inactive'])->default('Active');
            $table->enum('popular',['Yes', 'No'])->default('No');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('kind_of_space');
    }
}
