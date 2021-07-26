<?php

use Illuminate\Database\Seeder;

class CalendarTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('calendar')->delete();

        DB::table('calendar')->insert([
['id' => '1','space_id' => '10015','date' => '2017-08-14','price' => '0','notes' => NULL,'status' => 'Not available','created_at' => '2017-08-14 12:33:06','updated_at' => '2017-08-14 12:33:06'],
  ['id' => '2','space_id' => '10015','date' => '2017-08-15','price' => '0','notes' => NULL,'status' => 'Not available','created_at' => '2017-08-14 12:33:06','updated_at' => '2017-08-14 12:33:06'],
  ['id' => '3','space_id' => '10006','date' => '2017-08-17','price' => '0','notes' => NULL,'status' => 'Not available','created_at' => '2017-08-17 07:13:07','updated_at' => '2017-08-17 07:13:07'],
  ['id' => '4','space_id' => '10003','date' => '2017-08-17','price' => '0','notes' => NULL,'status' => 'Not available','created_at' => '2017-08-17 13:12:37','updated_at' => '2017-08-17 13:12:37'],
  ['id' => '5','space_id' => '10008','date' => '2017-08-17','price' => '0','notes' => NULL,'status' => 'Not available','created_at' => '2017-08-17 13:20:29','updated_at' => '2017-08-17 13:20:29'],
  ['id' => '6','space_id' => '10010','date' => '2017-08-17','price' => '0','notes' => NULL,'status' => 'Not available','created_at' => '2017-08-17 14:24:30','updated_at' => '2017-08-17 14:24:30'],
  ['id' => '7','space_id' => '10004','date' => '2017-08-17','price' => '0','notes' => NULL,'status' => 'Not available','created_at' => '2017-08-17 14:25:18','updated_at' => '2017-08-17 14:25:18'],
  ['id' => '9','space_id' => '10013','date' => '2017-08-17','price' => '0','notes' => NULL,'status' => 'Not available','created_at' => '2017-08-17 14:29:47','updated_at' => '2017-08-17 14:29:47'],
  ['id' => '10','space_id' => '10007','date' => '2017-08-17','price' => '0','notes' => NULL,'status' => 'Not available','created_at' => '2017-08-17 14:32:15','updated_at' => '2017-08-17 14:32:15']
        	]);
    }
}
