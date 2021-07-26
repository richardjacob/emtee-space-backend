<?php

use Illuminate\Database\Seeder;

class SpecialFeaturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('special_features')->delete();
    	
        DB::table('special_features')->insert([
            ['name' => 'Art'],
            ['name' => 'Dining Table'],
            ['name' => 'Fire Pit'],
            ['name' => 'Garden'],
            ['name' => 'Large Windows'],
            ['name' => 'Library'],
            ['name' => 'Modern Bathroom'],
            ['name' => 'Natural Light'],
            ['name' => 'Open Kitchen'],
            ['name' => 'Pool'],
            ['name' => 'Screening Room'],
            ['name' => 'Wood Floors'],
        ]);
    }
}