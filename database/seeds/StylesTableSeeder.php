<?php

use Illuminate\Database\Seeder;

class StylesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('styles')->delete();
    	
        DB::table('styles')->insert([
            ['name' => 'Classic'],
            ['name' => 'Industrial'],
            ['name' => 'Intimate'],
            ['name' => 'Luxurious'],
            ['name' => 'Modern'],
            ['name' => 'Raw'],
            ['name' => 'Rustic'],
            ['name' => 'Whimsical'],
        ]);
    }
}