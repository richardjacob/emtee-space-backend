<?php

use Illuminate\Database\Seeder;

class ActivitiesTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activities_type')->delete();
    	
        DB::table('activities_type')->insert([
            ['name' => 'Events', 'image' => 'activity_type_1568964262.png'],
            ['name' => 'Meetings', 'image' => 'activity_type_1568964266.png'],
            ['name' => 'Productions', 'image' => 'activity_type_1568964268.png'],
            ['name' => 'Pop-Ups', 'image' => 'activity_type_1568964432.png'],
        ]);
    }
}