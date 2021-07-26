<?php

use Illuminate\Database\Seeder;

class OurCommunityBannersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       	DB::table('our_community_banners')->delete();

        DB::table('our_community_banners')->insert([
            ["title"=>"Meet Garry & Lianne","description"=>"Click to learn more","image"=>"travel-video-thumbnail.jpg","link"=>"terms_of_service"],
            ["title"=>"Meet Patricia","description"=>"Click to learn more","image"=>"travel-video-thumbnail.jpg","link"=>"privacy_policy"],
            ["title"=>"Meet Garry & Lianne","description"=>"Click to learn more","image"=>"travel-video-thumbnail.jpg","link"=>"host_guarantee"]
        ]);
    }
}
