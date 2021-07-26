<?php

use Illuminate\Database\Seeder;

class SpaceRulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('space_rules')->delete();

        DB::table('space_rules')->insert([
            ['name' => 'No Open Flame','status' => 'Active'],
            ['name' => 'No Smoking','status' => 'Active'],
            ['name' => 'No Cooking','status' => 'Active'],
            ['name' => 'No Loud Music','status' => 'Active'],
            ['name' => 'No Dancing','status' => 'Active'],
            ['name' => 'No Late Night Parties','status' => 'Active'],
            ['name' => 'No Teenagers (10-18)','status' => 'Active'],
        ]);
    }
}
