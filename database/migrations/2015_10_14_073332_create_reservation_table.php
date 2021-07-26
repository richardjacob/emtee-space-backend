<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',10);
            $table->integer('space_id')->unsigned();
            $table->foreign('space_id')->references('id')->on('space');
            $table->integer('host_id')->unsigned();
            $table->foreign('host_id')->references('id')->on('users');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('activity_type');
            $table->integer('activity')->nullable();
            $table->integer('sub_activity')->nullable();
            $table->integer('number_of_guests');
            $table->string('currency_code',10);
            $table->foreign('currency_code')->references('code')->on('currency');
            $table->integer('hours');
            $table->integer('days');
            $table->integer('weeks');
            $table->integer('months');
            $table->integer('per_hour');
            $table->integer('base_per_hour');
            $table->integer('per_day');
            $table->integer('per_week');
            $table->integer('per_month');
            $table->integer('cleaning');
            $table->integer('security');
            $table->integer('service');
            $table->string('coupon_code',50);
            $table->integer('coupon_amount');
            $table->integer('subtotal');
            $table->integer('host_fee');
            $table->integer('total');
            $table->enum('host_penalty',['0', '1'])->default('0');
            $table->string('paypal_currency',10)->nullable();
            $table->string('transaction_id',50);
            $table->enum('paymode', ['PayPal', 'Credit Card'])->nullable();
            $table->enum('cancellation', ['Flexible', 'Moderate', 'Strict'])->default('Flexible');
            $table->string('first_name',30)->nullable();
            $table->string('last_name',30)->nullable();
            $table->string('postal_code',20)->nullable();
            $table->string('country',5)->nullable();
            $table->foreign('country')->references('short_name')->on('country');
            $table->enum('status', ['Pending', 'Accepted', 'Declined', 'Expired', 'Checkin', 'Checkout', 'Completed', 'Cancelled','Pre-Accepted','Pre-Approved'])->nullable();
            $table->enum('type', ['contact', 'reservation'])->nullable();
            $table->text('friends_email');
            $table->enum('cancelled_by', ['Guest', 'Host'])->nullable();
            $table->string('cancelled_reason',300);
            $table->string('decline_reason',300);
            $table->integer('host_remainder_email_sent');
            $table->integer('special_offer_id');
            $table->timestamp('accepted_at');
            $table->timestamp('expired_at');
            $table->timestamp('declined_at');
            $table->timestamp('cancelled_at');
            $table->timestamps();
            $table->string('date_check',5);
        });

        $statement = "ALTER TABLE reservation AUTO_INCREMENT = 10001;";

        DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation');
    }
}
