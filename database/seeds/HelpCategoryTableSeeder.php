<?php

use Illuminate\Database\Seeder;

class HelpCategoryTableSeeder extends Seeder
{
	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('help_category')->delete();
		DB::table('help_category')->insert([
			array('id' => '1','name' => 'Getting Started','description' => 'Getting Started','status' => 'Active'),
			array('id' => '2','name' => 'Account & Profile','description' => 'Account & Profile','status' => 'Active'),
			array('id' => '3','name' => 'Hosting','description' => 'Hosting','status' => 'Active'),
			array('id' => '4','name' => 'Traveling','description' => 'Traveling','status' => 'Active'),
			array('id' => '5','name' => 'Reviews','description' => 'Reviews','status' => 'Active'),
		]);
	}
}