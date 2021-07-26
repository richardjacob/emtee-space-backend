<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('activities');

        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 25);
            $table->integer('activity_type_id')->unsigned();
            $table->foreign('activity_type_id')->references('id')->on('activities_type');
            $table->string('image',100);
            $table->enum('source', ['Local', 'Cloudinary'])->default('Local');
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
        Schema::drop('activities');
    }
}
