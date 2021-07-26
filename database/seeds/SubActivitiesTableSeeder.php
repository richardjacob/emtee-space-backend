<?php

use Illuminate\Database\Seeder;

class SubActivitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sub_activities')->delete();
    	
        DB::table('sub_activities')->insert([
            ['name' => 'Cocktail/Happy Hour', 'activity_id' => 1],
            ['name' => 'Dinner', 'activity_id' => 1],
            ['name' => 'Social/Family Gathering', 'activity_id' => 1],
            ['name' => 'Late Night Party', 'activity_id' => 2],
            ['name' => 'Corporate Event Party', 'activity_id' => 2],
            ['name' => 'Conference', 'activity_id' => 3],
            ['name' => 'Fashion Show', 'activity_id' => 3],
            ['name' => 'Festival', 'activity_id' => 3],
        ]);
    }
}