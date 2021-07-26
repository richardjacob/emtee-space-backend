<?php

use Illuminate\Database\Seeder;

class ActivitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activities')->delete();
    	
        DB::table('activities')->insert([
            ['name' => 'Host Fashion Shoots', 'activity_type_id' => 1, 'image' => 'Optimized-adult-business-meeting-business-people-1438072.jpg','popular' => 'Yes'],
            ['name' => 'Brainstorm with the team', 'activity_type_id' => 2, 'image' => 'Optimized-shutterstock_1299336298-min.jpg','popular' => 'Yes'],
            ['name' => 'Bring your video to life', 'activity_type_id' => 3, 'image' => 'Optimized-apple-desk-devices-326511.jpg','popular' => 'Yes'],
            ['name' => 'Showcase your pop-up', 'activity_type_id' => 4, 'image' => 'Optimized-blur-coffee-connection-2041383.jpg','popular' => 'Yes'],
            ['name' => 'Build your business', 'activity_type_id' => 3, 'image' => 'Optimized-advice-advise-advisor-7097.jpg','popular' => 'Yes'],
            ['name' => 'Get real work done - even from a rooftop!', 'activity_type_id' => 1, 'image' => 'Optimized-advice-advise-advisor-7075-min.jpg','popular' => 'Yes'],
            ['name' => 'Share a desk with fellow creatives', 'activity_type_id' => 1, 'image' => 'Optimized-shutterstock_227836897-min.jpg','popular' => 'Yes'],
            ['name' => 'Prefer to fly solo? Book a private desk!', 'activity_type_id' => 1, 'image' => 'Optimized-shutterstock_439437442-min.jpg','popular' => 'Yes'],

            // ['name' => 'Fashion Shoot', 'activity_type_id' => 1, 'image' => 'events.webp','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Team Meeting', 'activity_type_id' => 2, 'image' => 'meeting.webp','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Production', 'activity_type_id' => 3, 'image' => 'production.webp','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Pop-Up', 'activity_type_id' => 4, 'image' => 'popup.webp','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Film Shoots', 'activity_type_id' => 3, 'image' => 'film_shoot.jpg','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Birthday Parties', 'activity_type_id' => 5, 'image' => 'birthday_party.jpg','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Affordable Spaces', 'activity_type_id' => 1, 'image' => 'wedding.webp','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Brand Activities', 'activity_type_id' => 1, 'image' => 'brand_activity.jpg','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Outdoor Events', 'activity_type_id' => 1, 'image' => 'outdoor_event.webp','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Musical Performance', 'activity_type_id' => 6, 'image' => 'musical_performance.jpg','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Experiential Marketing', 'activity_type_id' => 4, 'image' => 'experimental_marketing.jpeg','status' => 'Inactive','popular' => 'Yes'],
            // ['name' => 'Team Offsites', 'activity_type_id' => 2, 'image' => 'team_offsite.webp','status' => 'Inactive','popular' => 'Yes'],
        ]);
    }
}