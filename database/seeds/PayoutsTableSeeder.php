<?php

use Illuminate\Database\Seeder;

class PayoutsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('payouts')->delete();

        DB::table('payouts')->insert([
      array('id' => '1','reservation_id' => '10001','space_id' => '10011','spots' => '','correlation_id' => '','user_id' => '10001','user_type' => 'host','account' => '','amount' => '95841','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-21 12:46:12','updated_at' => '2019-10-21 12:46:12'),
      array('id' => '3','reservation_id' => '10007','space_id' => '10004','spots' => '','correlation_id' => '','user_id' => '10004','user_type' => 'host','account' => '','amount' => '52540','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-21 14:53:25','updated_at' => '2019-10-21 14:53:25'),
      array('id' => '4','reservation_id' => '10005','space_id' => '10004','spots' => '','correlation_id' => '','user_id' => '10003','user_type' => 'guest','account' => '','amount' => '9940','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-21 15:08:32','updated_at' => '2019-10-21 15:08:32'),
      array('id' => '5','reservation_id' => '10010','space_id' => '10009','spots' => '','correlation_id' => '3N2Z2RWUW6XY2','user_id' => '10002','user_type' => 'host','account' => 'batsarun@gmail.com','amount' => '323','currency_code' => 'EUR','status' => 'Completed','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-21 16:14:51','updated_at' => '2019-10-21 17:53:30'),
      array('id' => '6','reservation_id' => '10011','space_id' => '10019','spots' => '','correlation_id' => 'B4QQK26JSDTNE','user_id' => '10006','user_type' => 'host','account' => 'cathy@gmail.com','amount' => '404','currency_code' => 'EUR','status' => 'Completed','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-21 16:21:49','updated_at' => '2019-10-21 17:54:33'),
      array('id' => '8','reservation_id' => '10012','space_id' => '10004','spots' => '','correlation_id' => '','user_id' => '10001','user_type' => 'guest','account' => '','amount' => '5680','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-21 16:25:53','updated_at' => '2019-10-21 16:25:53'),
      array('id' => '10','reservation_id' => '10013','space_id' => '10004','spots' => '','correlation_id' => '','user_id' => '10002','user_type' => 'guest','account' => '','amount' => '5680','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 10:35:13','updated_at' => '2019-10-22 10:35:13'),
      array('id' => '11','reservation_id' => '10014','space_id' => '10005','spots' => '','correlation_id' => '','user_id' => '10004','user_type' => 'host','account' => '','amount' => '21298','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 10:36:54','updated_at' => '2019-10-22 10:36:54'),
      array('id' => '12','reservation_id' => '10015','space_id' => '10009','spots' => '','correlation_id' => '','user_id' => '10002','user_type' => 'host','account' => '','amount' => '270','currency_code' => 'USD','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 10:51:28','updated_at' => '2019-10-22 10:51:28'),
      array('id' => '13','reservation_id' => '10016','space_id' => '10005','spots' => '','correlation_id' => '','user_id' => '10004','user_type' => 'host','account' => '','amount' => '63894','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 11:04:34','updated_at' => '2019-10-22 11:04:34'),
      array('id' => '14','reservation_id' => '10017','space_id' => '10012','spots' => '','correlation_id' => '','user_id' => '10001','user_type' => 'host','account' => '','amount' => '8874','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 11:26:14','updated_at' => '2019-10-22 11:26:14'),
      array('id' => '15','reservation_id' => '10018','space_id' => '10004','spots' => '','correlation_id' => 'FNHK447XUA96N','user_id' => '10004','user_type' => 'host','account' => 'Trioangle@gmail.com','amount' => '74','currency_code' => 'EUR','status' => 'Completed','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 11:26:59','updated_at' => '2019-10-22 12:54:38'),
      array('id' => '16','reservation_id' => '10019','space_id' => '10015','spots' => '','correlation_id' => '','user_id' => '10003','user_type' => 'host','account' => '','amount' => '360','currency_code' => 'USD','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 11:28:22','updated_at' => '2019-10-22 11:28:22'),
      array('id' => '17','reservation_id' => '10020','space_id' => '10018','spots' => '','correlation_id' => '','user_id' => '10002','user_type' => 'host','account' => '','amount' => '580','currency_code' => 'USD','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 11:28:58','updated_at' => '2019-10-22 11:28:58'),
      array('id' => '18','reservation_id' => '10021','space_id' => '10009','spots' => '','correlation_id' => '','user_id' => '10002','user_type' => 'host','account' => '','amount' => '44723','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 11:31:59','updated_at' => '2019-10-22 11:31:59'),
      array('id' => '19','reservation_id' => '10022','space_id' => '10011','spots' => '','correlation_id' => '','user_id' => '10001','user_type' => 'host','account' => '','amount' => '159735','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 11:32:30','updated_at' => '2019-10-22 11:32:30'),
      array('id' => '20','reservation_id' => '10023','space_id' => '10005','spots' => '','correlation_id' => '2J93DW6EQY7MS','user_id' => '10004','user_type' => 'host','account' => 'Trioangle@gmail.com','amount' => '540','currency_code' => 'EUR','status' => 'Completed','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 11:35:44','updated_at' => '2019-10-22 12:54:32'),
      array('id' => '21','reservation_id' => '10024','space_id' => '10013','spots' => '','correlation_id' => '','user_id' => '10003','user_type' => 'host','account' => '','amount' => '21298','currency_code' => 'INR','status' => 'Future','penalty_id' => '0','penalty_amount' => '0','created_at' => '2019-10-22 11:37:16','updated_at' => '2019-10-22 11:37:16'),
        ]);
    }
}