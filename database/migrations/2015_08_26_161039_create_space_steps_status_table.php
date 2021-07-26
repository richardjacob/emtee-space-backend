<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceStepsStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('space_steps_status', function (Blueprint $table) {
            $table->integer('space_id')->unique()->unsigned();
            $table->foreign('space_id')->references('id')->on('space');
            $table->enum('basics',['0','1'])->default('0');
            $table->enum('description',['0','1'])->default('0');
            $table->enum('location',['0','1'])->default('0');
            $table->enum('photos',['0','1'])->default('0');
            $table->enum('pricing',['0','1'])->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('space_steps_status');
    }
}
