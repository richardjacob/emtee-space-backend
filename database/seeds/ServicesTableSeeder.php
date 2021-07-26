<?php

use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services')->delete();
    	
        DB::table('services')->insert([
            ['name' => 'Food'],
            ['name' => 'Lighting System'],
            ['name' => 'Furniture Rental'],
            ['name' => 'Event Manager'],
            ['name' => 'Security Crew'],
            ['name' => 'Cleaning'],
            ['name' => 'Trash Removal'],
            ['name' => 'Photography'],
            ['name' => 'A/v'],
        ]);
    }
}