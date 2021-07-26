<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('space_description', function (Blueprint $table) {
            $table->integer('space_id')->unique()->unsigned();
            $table->foreign('space_id')->references('id')->on('space');
            $table->text('space');
            $table->text('access');
            $table->text('interaction');
            $table->text('notes');
            $table->text('house_rules');
            $table->text('neighborhood_overview');
            $table->text('space_rules');
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
        Schema::dropIfExists('space_description');
    }
}
