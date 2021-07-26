<?php

use Illuminate\Database\Seeder;

class HomePageSlidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('home_page_sliders')->delete();
        DB::table('home_page_sliders')->insert([
            ["image" => "home_page_slider_1.webp", "order" => 1, 'source' => 'Local'],
            ["image" => "home_page_slider_2.webp", "order" => 2, 'source' => 'Local'],
            ["image" => "home_page_slider_3.jpg", "order" => 3, 'source' => 'Local'],
            ["image" => "home_page_slider_4.jpeg", "order" => 4, 'source' => 'Local'],
            ["image" => "home_page_slider_5.jpeg", "order" => 5, 'source' => 'Local'],
        ]);
    }
}
