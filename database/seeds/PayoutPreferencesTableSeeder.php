<?php

use Illuminate\Database\Seeder;

class PayoutPreferencesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payout_preferences')->delete();

        DB::table('payout_preferences')->insert([

        		['id' => '1','user_id' => '10002','address1' => 'Chennai','address2' => '','city' => 'Chennai','state' => 'Tamil Nadu','postal_code' => '436523','country' => 'IN','payout_method' => 'PayPal','paypal_email' => 'batsarun@gmail.com','currency_code' => 'EUR','default' => 'yes','created_at' => '2017-08-17 07:15:34','updated_at' => '2017-08-17 07:15:34']


        		]);
    }
}
