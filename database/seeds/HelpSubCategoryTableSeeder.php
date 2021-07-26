<?php

use Illuminate\Database\Seeder;

class HelpSubCategoryTableSeeder extends Seeder
{
	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('help_subcategory')->delete();

    	DB::table('help_subcategory')->insert([
    		array('id' => '1','category_id' => '1','name' => 'How it Works','description' => 'How it Works','status' => 'Active'),
			array('id' => '2','category_id' => '1','name' => 'How to Travel','description' => 'How to Travel','status' => 'Active'),
			array('id' => '3','category_id' => '1','name' => 'How to Host','description' => 'How to Host','status' => 'Active'),
			array('id' => '4','category_id' => '2','name' => 'Language & Currency','description' => 'Language & Currency','status' => 'Active'),
			array('id' => '5','category_id' => '3','name' => 'Deciding to Host','description' => 'Deciding to Host','status' => 'Active'),
			array('id' => '6','category_id' => '3','name' => 'Your Listings','description' => 'Your Listings','status' => 'Active'),
			array('id' => '7','category_id' => '3','name' => 'Your Reservations','description' => 'Your Reservations','status' => 'Active'),
			array('id' => '8','category_id' => '3','name' => 'Getting Paid','description' => 'Getting Paid','status' => 'Active'),
			array('id' => '9','category_id' => '4','name' => 'Finding a Place','description' => 'Finding a Place','status' => 'Active'),
			array('id' => '10','category_id' => '4','name' => 'Booking a Place','description' => 'Booking a Place','status' => 'Active'),
			array('id' => '11','category_id' => '4','name' => 'Paying','description' => 'Paying','status' => 'Active'),
			array('id' => '12','category_id' => '4','name' => 'Your Trip','description' => 'Your Trip','status' => 'Active'),
			array('id' => '13','category_id' => '2','name' => 'Sign Up','description' => 'Sign Up','status' => 'Active'),
			array('id' => '14','category_id' => '2','name' => 'Manage Your Profile','description' => 'Manage Your Profile','status' => 'Active'),
			array('id' => '15','category_id' => '2','name' => 'Security & Password','description' => 'Security & Password','status' => 'Active'),
    	]);
    }
}