<?php

use Illuminate\Database\Seeder;

class KindOfSpaceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('kind_of_space')->delete();
  	
      DB::table('kind_of_space')->insert([
        ["name" => "Apartment","image"=>"sp_home_3.jpeg"],
        ["name" => "Photo Studio","image"=>"sp_home_4.jpeg"],
        ["name" => "Commercial Loft","image"=>"sp_home_5.jpeg"],
        ["name" => "Auditorium","image"=>"sp_home_6.jpeg"],
        ["name" => "Banquet Hall","image"=>"sp_home_7.jpeg"],
        ["name" => "Restaurant","image"=>"sp_home_8.jpeg"],
        ["name" => "Boat","image"=>"sp_home_9.jpeg"],
        ["name" => "Commercial Kitchen","image"=>"sp_home_10.jpeg"],
        ["name" => "Event Space","image"=>"sp_home_11.jpeg"],
        ["name" => "Gallery","image"=>"sp_home_12.jpeg"],
        ["name" => "Garden","image"=>"sp_home_13.jpeg"],
        ["name" => "House","image"=>"sp_home_14.jpeg"],
        ["name" => "Loft","image"=>"sp_home_15.jpeg"],
        ["name" => "Mansion/Estate","image"=>"sp_home_16.jpeg"],
        ["name" => "Outdoor Space","image"=>"sp_home_17.jpeg"],
        ["name" => "Rooftop","image"=>"sp_home_18.jpeg"],
        ["name" => "Storefront","image"=>"sp_home_19.jpeg"],
        ["name" => "Warehouse","image"=>"sp_home_20.jpeg"],
      ]);
    }
}
