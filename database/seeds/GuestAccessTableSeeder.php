<?php

use Illuminate\Database\Seeder;

class GuestAccessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('guest_access')->delete();

        DB::table('guest_access')->insert([
            ['name' => 'Delivery Access','status' => 'Active'],
            ['name' => 'Garage Door','status' => 'Active'],
            ['name' => 'Elevator','status' => 'Active'],
            ['name' => 'Parking Near By','status' => 'Active'],
            ['name' => 'Stairs','status' => 'Active'],
        ]);
    }
}
