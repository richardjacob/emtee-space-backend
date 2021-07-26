<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('space', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name', 35);
            $table->string('sub_name', 50);
            $table->string('summary', 500);
            $table->integer('space_type')->unsigned();
            $table->integer('number_of_rooms')->unsigned()->unsigned();
            $table->integer('number_of_restrooms')->unsigned()->nullable();
            $table->enum('fully_furnished',['Yes', 'No'])->default('No');
            $table->integer('no_of_workstations');             
            $table->enum('shared_or_private',['Yes', 'No'])->default('No');
            $table->enum('renting_space_firsttime',['Yes', 'No'])->default('No');
            $table->bigInteger('number_of_guests')->unsigned()->nullable();
            $table->integer('floor_number')->nullable();
            $table->string('sq_ft',15)->nullable();
            $table->enum('size_type',['sq_ft', 'acre'])->default('sq_ft');
            $table->string('guest_access',50)->nullable();
            $table->string('guest_access_other',500)->nullable();
            $table->string('amenities',50)->nullable();
            $table->string('services',50)->nullable();
            $table->string('services_extra',500)->nullable();
            $table->string('space_style',50)->nullable();
            $table->string('special_feature',50)->nullable();
            $table->string('space_rules',50)->nullable();
            $table->enum('booking_type', ['request_to_book', 'instant_book'])->default('instant_book');
            $table->enum('cancellation_policy', ['Flexible', 'Moderate', 'Strict'])->default('Flexible');            
            $table->enum('popular',['Yes', 'No'])->default('No');
            $table->integer('views_count');
            $table->enum('status',['Pending', 'Listed', 'Unlisted', 'Resubmit'])->nullable();
            $table->enum('admin_status',['Pending', 'Approved','Resubmit'])->default('Pending');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE space AUTO_INCREMENT = 10001");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('space');
    }
}
