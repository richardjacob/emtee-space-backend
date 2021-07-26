<?php

use Illuminate\Database\Seeder;

class AmenitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('amenities')->delete();
    	
        DB::table('amenities')->insert([
        		['name' => 'Essentials','description' => 'Essentials','icon' => 'essentials.png', 'mobile_icon' => 'j'],
        		['name' => 'TV','description' => '','icon' => 'tv.png', 'mobile_icon' => 'z'],
        		['name' => 'Cable TV','description' => '','icon' => 'cabletv.png', 'mobile_icon' => 'f'],
        		['name' => 'Air Conditioning ','description' => '','icon' => 'ac.png', 'mobile_icon' => 'b'],
        		['name' => 'Heating','description' => 'Heating','icon' => 'heating1.png', 'mobile_icon' => 'o'],
        		['name' => 'Kitchen','description' => 'Kitchen','icon' => 'kitchen.png', 'mobile_icon' => 's'],
        		['name' => 'Internet','description' => 'Internet','icon' => 'internetwired.png', 'mobile_icon' => 'r'],
        		['name' => 'Wireless Internet','description' => 'Wireless Internet','icon' => 'wireless.jpeg', 'mobile_icon' => 'B'],
        		['name' => 'Hot Tub','description' => '','icon' => 'hottub.png', 'mobile_icon' => 'p'],
        		['name' => 'Washer','description' => 'Washer','icon' => 'washer.png', 'mobile_icon' => 'A'],
        		['name' => 'Pool','description' => 'Pool','icon' => 'pool.png', 'mobile_icon' => 'w'],
        		['name' => 'Dryer','description' => 'Dryer','icon' => 'dryerorg.png', 'mobile_icon' => 'n'],
        		['name' => 'Breakfast','description' => 'Breakfast','icon' => 'breakfast1.png', 'mobile_icon' => 'e'],
        		['name' => 'Free Parking on Premises','description' => '','icon' => 'parking.png', 'mobile_icon' => 'u'],
        		['name' => 'Gym','description' => 'Gym','icon' => 'gym.png', 'mobile_icon' => 'm'],
        		['name' => 'Elevator in Building','description' => '','icon' => 'elevator.png', 'mobile_icon' => 'i'],
        		['name' => 'Indoor Fireplace','description' => '','icon' => 'fireplace.png', 'mobile_icon' => 'l'],
        		['name' => 'Buzzer/Wireless Intercom','description' => '','icon' => 'buzzer.png', 'mobile_icon' => 'q'],
        		['name' => 'Doorman','description' => '','icon' => 'doorman.png', 'mobile_icon' => 'g'],
        		['name' => 'Shampoo','description' => '','icon' => 'shampoo.png', 'mobile_icon' => 'x'],
        		['name' => 'Family/Kid Friendly','description' => 'Family/Kid Friendly','icon' => 'family.jpeg', 'mobile_icon' => 'k'],
        		['name' => 'Smoking Allowed','description' => '','icon' => 'smoking.png', 'mobile_icon' => 'y'],
        		['name' => 'Suitable for Events','description' => 'Suitable for Events','icon' => 'events.png', 'mobile_icon' => 'c'],
        		['name' => 'Pets Allowed','description' => '','icon' => 'petsallowed.png', 'mobile_icon' => 'v'],
        		['name' => 'Pets live on this property','description' => '','icon' => 'petslive.png', 'mobile_icon' => 't'],
        		['name' => 'Wheelchair Accessible','description' => 'Wheelchair Accessible','icon' => 'wheelchair.png', 'mobile_icon' => 'a'],
        		['name' => 'Smoke Detector','description' => 'Smoke Detector','icon' => 'smokedet.jpeg', 'mobile_icon' => 't'],
        		['name' => 'Carbon Monoxide Detector','description' => 'Carbon Monoxide Detector','icon' => 'codetect.png', 'mobile_icon' => 't'],
        		['name' => 'First Aid Kit','description' => '','icon' => 'firstaidkit.jpeg', 'mobile_icon' => 't'],
        		['name' => 'Safety Card','description' => 'Safety Card','icon' => 'safety.png', 'mobile_icon' => 't'],
        		['name' => 'Fire Extinguisher','description' => 'Essentials','icon' => 'fire.png', 'mobile_icon' => 't'],
        	]);
    }
}
