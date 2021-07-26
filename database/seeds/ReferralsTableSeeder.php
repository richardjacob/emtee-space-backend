<?php

use Illuminate\Database\Seeder;

class ReferralsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('referrals')->delete();
    	DB::table('applied_travel_credit')->delete();

        DB::table('referrals')->insert([
			array('id' => '1','user_id' => '10004','friend_id' => '10005','credited_amount' => '1420','friend_credited_amount' => '1420','if_friend_guest_amount' => '0','if_friend_host_amount' => '5679','creditable_amount' => '7099','currency_code' => 'INR','status' => 'Pending','created_at' => '2019-10-21 15:21:30','updated_at' => '2019-10-21 15:21:30'),
			array('id' => '2','user_id' => '10004','friend_id' => '10006','credited_amount' => '0','friend_credited_amount' => '0','if_friend_guest_amount' => '0','if_friend_host_amount' => '0','creditable_amount' => '7099','currency_code' => 'INR','status' => 'Completed','created_at' => '2019-10-21 15:43:40','updated_at' => '2019-10-22 11:28:22'),
        ]);

        DB::table('applied_travel_credit')->insert([
			array('id' => '1','reservation_id' => '10010','referral_id' => '2','amount' => '1420','type' => 'friend','currency_code' => 'INR'),
			array('id' => '4','reservation_id' => '10019','referral_id' => '1','amount' => '0','type' => 'main','currency_code' => 'USD'),
			array('id' => '5','reservation_id' => '10019','referral_id' => '2','amount' => '100','type' => 'main','currency_code' => 'USD'),
        ]);
    }
}