<?php

use Illuminate\Database\Seeder;

class WishlistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('wishlists')->delete();
        DB::table('saved_wishlists')->delete();
        
        DB::table('wishlists')->insert([
            array('id' => '1','user_id' => '10001','name' => 'San Francisco','privacy' => '0','pick' => 'No'),
            array('id' => '2','user_id' => '10002','name' => 'London','privacy' => '0','pick' => 'Yes'),
            array('id' => '3','user_id' => '10003','name' => 'New York','privacy' => '0','pick' => 'Yes'),
            array('id' => '4','user_id' => '10004','name' => 'Jersey City','privacy' => '0','pick' => 'Yes'),
            array('id' => '8','user_id' => '10001','name' => 'Ciudad de Mexico','privacy' => '0','pick' => 'No'),
            array('id' => '9','user_id' => '10004','name' => 'Ciudad de Mexico','privacy' => '0','pick' => 'No'),
        ]);
        
        DB::table('saved_wishlists')->insert([
            array('id' => '1','user_id' => '10001','space_id' => '10004','wishlist_id' => '1','note' => ''),
            array('id' => '2','user_id' => '10002','space_id' => '10004','wishlist_id' => '2','note' => ''),
            array('id' => '3','user_id' => '10003','space_id' => '10007','wishlist_id' => '3','note' => ''),
            array('id' => '7','user_id' => '10004','space_id' => '10008','wishlist_id' => '4','note' => ''),
        ]);
    }
}