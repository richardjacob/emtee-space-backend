<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        DB::table('profile_picture')->delete();
        DB::table('users_verification')->delete();
        DB::table('users_verification_documents')->delete();
        DB::table('users_phone_numbers')->delete();
        DB::table('payout_preferences')->delete();

        DB::table('users')->insert([
            ['id' => '10001','first_name' => 'John','last_name' => 'Ivan','email' => 'john@gmail.com','password' => bcrypt('trioangle'), 'remember_token' => NULL,'dob' => '1986-02-03','gender' => 'Male','live' => 'Paris, France','about' => 'I want to make your event experience perfect. Event organizers need a space with supplies to avoid walking outside to make purchases. Other spaces  are expensive. Save money and use my space.','school' => 'Kingsworth International School','work' => 'Home computer programmer','timezone' => 'UTC','languages' => '4,6,7,10,15','email_language' => 'en','fb_id' => NULL,'google_id' => NULL,'linkedin_id' => NULL,'currency_code' => NULL,'status' => 'Active','verification_status' => 'No','created_at' => '2019-10-18 14:48:13','updated_at' => '2019-10-22 12:27:06','deleted_at' => NULL],
            ['id' => '10002','first_name' => 'Tony','last_name' => 'Ion','email' => 'tony@gmail.com','password' => bcrypt('trioangle'), 'remember_token' => NULL,'dob' => '1984-08-16','gender' => 'Female','live' => 'NYC, United states ','about' => 'Hello! I’m Tony, I enjoy hosting and welcoming event organizers from all around the world to my space. I’ve lived in NYC for over 10 years, and I’m happy to give advice and answer any questions you have about your event in this incredible city. I look forward to hosting you!','school' => 'Columbia School of Social Work','work' => 'Social Worker','timezone' => 'UTC','languages' => '5,6,7,9,10','email_language' => 'en','fb_id' => NULL,'google_id' => NULL,'linkedin_id' => NULL,'currency_code' => NULL,'status' => 'Active','verification_status' => 'No','created_at' => '2019-10-18 14:48:13','updated_at' => '2019-10-22 12:29:46','deleted_at' => NULL],
            ['id' => '10003','first_name' => 'Mick','last_name' => 'Hans','email' => 'mick@gmail.com','password' => bcrypt('trioangle'), 'remember_token' => NULL,'dob' => '1996-02-02','gender' => 'Male','live' => 'NJ , United states','about' => 'I\'m a HAND ART ILLUSTRATOR/ GRAPHIC ARTIST.I design the images to be used in all the products related to the gift wrap and paper products markets. I spent many years also designing fashion textiles in NYC............. I\'m from CALIFORNIA, where I also designed a lot of surfer graphics (clothing)........... I ALSO am passionate about historic preservation!!!... and discovering new neighbourhoods around NEW YORK CITY to explore..','school' => 'Brooklyn College Academy','work' => 'Graphic Artist','timezone' => 'UTC','languages' => '5,6,7,9,10','email_language' => 'en','fb_id' => NULL,'google_id' => NULL,'linkedin_id' => NULL,'currency_code' => NULL,'status' => 'Active','verification_status' => 'No','created_at' => '2019-10-18 14:48:13','updated_at' => '2019-10-22 12:32:08','deleted_at' => NULL],
            ['id' => '10004','first_name' => 'Trioangle','last_name' => 'Makent','email' => 'trioanglemakent@gmail.com', 'password' => bcrypt('trioangle'), 'remember_token' => NULL,'dob' => '1986-01-14','gender' => 'Male','live' => 'Newyork , USA','about' => 'I am a long-time resident in Williams burg and my business focus is in computer systems security and video intercom systems. My goal in renting out this space is not just making a few extra dollars but also meeting interesting people who are imaginative, creative and innovative so that my own narrow mind may expand.','school' => 'Stuyvesant High School','work' => 'System Admin','timezone' => 'UTC','languages' => '4,6,8,9,10','email_language' => 'en','fb_id' => NULL,'google_id' => NULL,'linkedin_id' => NULL,'currency_code' => NULL,'status' => 'Active','verification_status' => 'Verified','created_at' => '2019-10-18 14:48:13','updated_at' => '2019-10-19 12:38:14','deleted_at' => NULL],
            ['id' => '10005','first_name' => 'Alex ','last_name' => 'Adam','email' => 'alex@gmail.com','password' => bcrypt('trioangle'), 'remember_token' => NULL,'dob' => '1984-07-14','gender' => NULL,'live' => '','about' => '','school' => '','work' => '','timezone' => 'UTC','languages' => '','email_language' => 'en','fb_id' => NULL,'google_id' => NULL,'linkedin_id' => NULL,'currency_code' => NULL,'status' => NULL,'verification_status' => 'No','created_at' => '2019-10-21 15:21:30','updated_at' => '2019-10-21 15:21:30','deleted_at' => NULL],
            ['id' => '10006','first_name' => 'Catherine ','last_name' => 'Cathy','email' => 'catherine@gmail.com','password' => bcrypt('trioangle'), 'remember_token' => NULL,'dob' => '1996-02-11','gender' => 'Female','live' => '','about' => '','school' => '','work' => '','timezone' => 'UTC','languages' => '','email_language' => 'en','fb_id' => NULL,'google_id' => NULL,'linkedin_id' => NULL,'currency_code' => NULL,'status' => 'Active','verification_status' => 'No','created_at' => '2019-10-21 15:43:40','updated_at' => '2019-10-21 16:19:21','deleted_at' => NULL],
        ]);

        DB::table('profile_picture')->insert([
            ['user_id' => '10001','src' => 'profile_pic_1566887120.jpg', 'photo_source' => 'Local'],
            ['user_id' => '10002','src' => 'profile_pic_1462128586.jpg', 'photo_source' => 'Local'],
            ['user_id' => '10003','src' => 'profile_pic_1566887413.jpg', 'photo_source' => 'Local'],
            ['user_id' => '10004','src' => 'profile_pic_1571468309.jpeg', 'photo_source' => 'Local'],
            ['user_id' => '10005','src' => 'profile_pic_1571652586.jpg', 'photo_source' => 'Local'],
            ['user_id' => '10006','src' => 'profile_pic_1571653096.jpg', 'photo_source' => 'Local'],
        ]);

        DB::table('users_verification')->insert([
            array('user_id' => '10001','email' => 'yes','facebook' => 'yes','google' => 'yes','linkedin' => 'yes','phone' => 'yes','fb_id' => '','google_id' => '','linkedin_id' => ''),
            array('user_id' => '10002','email' => 'yes','facebook' => 'yes','google' => 'yes','linkedin' => 'yes','phone' => 'yes','fb_id' => '','google_id' => '','linkedin_id' => ''),
            array('user_id' => '10003','email' => 'yes','facebook' => 'yes','google' => 'yes','linkedin' => 'yes','phone' => 'yes','fb_id' => '','google_id' => '','linkedin_id' => ''),
            array('user_id' => '10004','email' => 'yes','facebook' => 'yes','google' => 'yes','linkedin' => 'yes','phone' => 'yes','fb_id' => '','google_id' => '','linkedin_id' => ''),
            array('user_id' => '10005','email' => 'no','facebook' => 'no','google' => 'no','linkedin' => 'no','phone' => 'no','fb_id' => '','google_id' => '','linkedin_id' => ''),
            array('user_id' => '10006','email' => 'no','facebook' => 'no','google' => 'no','linkedin' => 'no','phone' => 'no','fb_id' => '','google_id' => '','linkedin_id' => ''),
        ]);

        DB::table('users_verification_documents')->insert([
            ['id' => '1','user_id' => '10004','name' => 'id_document_1571468743.png','type' => 'id_document','status' => 'Verified','created_at' => '2019-10-19 12:35:43','updated_at' => '2019-10-19 12:38:14'],
        ]);

        DB::table('users_phone_numbers')->insert([
            array('id' => '1','user_id' => '10001','phone_code' => '1','phone_number' => '98765432101','status' => 'Confirmed'),
            array('id' => '2','user_id' => '10002','phone_code' => '1','phone_number' => '98765432102','status' => 'Confirmed'),
            array('id' => '3','user_id' => '10003','phone_code' => '1','phone_number' => '98765432103','status' => 'Confirmed'),
            array('id' => '4','user_id' => '10004','phone_code' => '1','phone_number' => '98765432104','status' => 'Confirmed')
        ]);

        DB::table('payout_preferences')->insert([
            ['user_id' => '10004','address1' => 'a','address2' => 'kjb','city' => 'jb','state' => 'j','postal_code' => 'jb','country' => 'UG','payout_method' => 'PayPal','paypal_email' => 'Trioangle@gmail.com','currency_code' => 'EUR','default' => 'yes','routing_number' => '','account_number' => '','holder_name' => '','holder_type' => 'Individual','document_id' => NULL,'document_image' => NULL,'phone_number' => NULL,'address_kanji' => NULL,'bank_name' => NULL,'branch_name' => NULL,'branch_code' => NULL,'ssn_last_4' => NULL,'deleted_at' => NULL,'created_at' => '2019-10-19 16:01:46','updated_at' => '2019-10-19 16:01:46'],
            ['user_id' => '10004','address1' => '18 Armstrong St. Brooklyn','address2' => '','city' => 'New York','state' => 'New York','postal_code' => '11216','country' => 'US','payout_method' => 'Stripe','paypal_email' => 'acct_1FVzwNHuGqgE3G29','currency_code' => 'USD','default' => 'no','routing_number' => '110000000','account_number' => '000123456789','holder_name' => 'Trioangle','holder_type' => 'Individual','document_id' => 'file_1FVzwTHuGqgE3G29nq2MGes6','document_image' => '10004_user_document_1571660697.jpg','phone_number' => '','address_kanji' => '[]','bank_name' => '','branch_name' => '','branch_code' => '','ssn_last_4' => '1234','deleted_at' => NULL,'created_at' => '2019-10-21 17:55:01','updated_at' => '2019-10-21 17:55:01'],
            ['user_id' => '10006','address1' => 'czs','address2' => 'zv','city' => 'vxv','state' => 'xczvv','postal_code' => 'xzv','country' => 'AF','payout_method' => 'PayPal','paypal_email' => 'cathy@gmail.com','currency_code' => 'EUR','default' => 'yes','routing_number' => '','account_number' => '','holder_name' => '','holder_type' => 'Individual','document_id' => NULL,'document_image' => NULL,'phone_number' => NULL,'address_kanji' => NULL,'bank_name' => NULL,'branch_name' => NULL,'branch_code' => NULL,'ssn_last_4' => NULL,'deleted_at' => NULL,'created_at' => '2019-10-21 17:54:23','updated_at' => '2019-10-21 17:54:23'],
        ]);
    }
}