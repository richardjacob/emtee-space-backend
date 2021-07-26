<?php

use Illuminate\Database\Seeder;

class RoomsPriceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rooms_price')->delete();
    	
        DB::table('rooms_price')->insert([
        		['space_id' => '10001','night' => '350','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10002','night' => '0','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10003','night' => '95','cleaning' => '10','additional_guest' => '10','guests' => '1','security' => '0','weekend' => '0','currency_code' => 'EUR'],
  ['space_id' => '10004','night' => '150','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10005','night' => '250','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10006','night' => '180','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10007','night' => '120','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10008','night' => '120','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'EUR'],
  ['space_id' => '10009','night' => '500','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10010','night' => '150','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10011','night' => '350','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10012','night' => '320','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10013','night' => '77','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10014','night' => '1500','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10015','night' => '150','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10016','night' => '350','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10017','night' => '0','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD'],
  ['space_id' => '10018','night' => '0','cleaning' => '0','additional_guest' => '0','guests' => '0','security' => '0','weekend' => '0','currency_code' => 'USD']
        	]);
    }
}
