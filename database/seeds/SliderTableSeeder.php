<?php

use Illuminate\Database\Seeder;

class SliderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('slider')->delete();
    	
        DB::table('slider')->insert([
            ["image"=>"MakentDefault/xmzhzhd6iv6yuc5wbw3a", 'source' => 'Cloudinary', "order"=> 0, "status"=>"Active"],
            ["image"=>"MakentDefault/kmc4ofy7pxd2lcr8c9xm", 'source' => 'Cloudinary', "order"=> 1, "status"=>"Active"],
            ["image"=>"MakentDefault/qg7y8ylzcpeo4qhskffc", 'source' => 'Cloudinary', "order"=> 2, "status"=>"Active"],
        ]);
    }
}
