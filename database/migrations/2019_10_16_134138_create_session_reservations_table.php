<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_reservations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('space_id')->unsigned();
            $table->foreign('space_id')->references('id')->on('space');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('event_type');
            $table->text('booking_date_times');
            $table->integer('number_of_guests');
            $table->integer('reservation_id')->nullable();
            $table->integer('special_offer_id')->nullable();
            $table->string('coupon_code',30);
            $table->enum('cancellation', ['Flexible', 'Moderate', 'Strict'])->default('Flexible');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE session_reservations AUTO_INCREMENT = 10001");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_reservations');
    }
}