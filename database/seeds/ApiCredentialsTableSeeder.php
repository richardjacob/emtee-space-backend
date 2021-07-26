<?php

use Illuminate\Database\Seeder;

class ApiCredentialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('api_credentials')->delete();
        
        DB::table('api_credentials')->insert([
            ['name' => 'client_id', 'value' => '433021387036498', 'site' => 'Facebook'],
            ['name' => 'client_secret', 'value' => '84aa1d0248e91c0e6ed2f6562ed44bec', 'site' => 'Facebook'],
            ['name' => 'client_id', 'value' => '957591660291-r1922prvlr1d4ac3otp3016cu2aveq01.apps.googleusercontent.com', 'site' => 'Google'],
            ['name' => 'client_secret', 'value' => '2KKmK-xsLyd1gn0qIrsRBtey', 'site' => 'Google'],
            ['name' => 'client_id', 'value' => '814qxyvczj5t7z', 'site' => 'LinkedIn'],
            ['name' => 'client_secret', 'value' => 'mkuRNAxW9TSp22Zf', 'site' => 'LinkedIn'],
            ['name' => 'key', 'value' => 'AIzaSyB6lCQnISdsSUVFdcQYxaHxXXjvKDn9wcs', 'site' => 'GoogleMap'],
            ['name' => 'server_key', 'value' => 'AIzaSyB6lCQnISdsSUVFdcQYxaHxXXjvKDn9wcs', 'site' => 'GoogleMap'],
            ['name' => 'key', 'value' => 'd7b78816', 'site' => 'Nexmo'],
            ['name' => 'secret', 'value' => '99a1dde9a6079c4a', 'site' => 'Nexmo'],
            ['name' => 'from', 'value' => 'Nexmo', 'site' => 'Nexmo'],
            ['name' => 'cloudinary_name', 'value' => 'demomakent', 'site' => 'Cloudinary'],
            ['name' => 'cloudinary_key', 'value' => '726751962386466', 'site' => 'Cloudinary'],
            ['name' => 'cloudinary_secret', 'value' => 'hrevdKZ1nBCVLcpPtHYy_Qwc3tc', 'site' => 'Cloudinary'],
            ['name' => 'cloud_base_url', 'value' => 'http://res.cloudinary.com/demomakent', 'site' => 'Cloudinary'],
            ['name' => 'cloud_secure_url', 'value' => 'https://res.cloudinary.com/demomakent', 'site' => 'Cloudinary'],
            ['name' => 'cloud_api_url', 'value' => 'https://api.cloudinary.com/v1_1/demomakent', 'site' => 'Cloudinary'],
            ['name' => 'service_id','value' => 'com.trioangle.makentspaceServiceId','site' => 'Apple'],
            ['name' => 'team_id', 'value' => 'W89HL6566S', 'site' => 'Apple'],
            ['name' => 'key_id', 'value' => 'HWDFVB355K', 'site' => 'Apple'],
            ['name' => 'key_file', 'value' => 'key.txt', 'site' => 'Apple'],

        ]);
    }
}
