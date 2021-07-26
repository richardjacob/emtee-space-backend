<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();

        DB::table('permissions')->insert([
              ['name' => 'manage_admin', 'display_name' => 'Manage Admin', 'description' => 'Manage Admin Users'],
              ['name' => 'users', 'display_name' => 'View Users', 'description' => 'View Users'],
              ['name' => 'add_user', 'display_name' => 'Add User', 'description' => 'Add User'],
              ['name' => 'edit_user', 'display_name' => 'Edit User', 'description' => 'Edit User'],
              ['name' => 'delete_user', 'display_name' => 'Delete User', 'description' => 'Delete User'],
              ['name' => 'manage_amenities', 'display_name' => 'Manage Amenities', 'description' => 'Manage Amenities'],
              ['name' => 'manage_kind_of_space', 'display_name' => 'Manage Kind Of Space', 'description' => 'Manage Kind Of Space'],
              ['name' => 'manage_guest_access', 'display_name' => 'Manage Guest Access', 'description' => 'Manage Guest Access'],
              ['name' => 'manage_services', 'display_name' => 'Manage Services & Extras', 'description' => 'Manage Services & Extras'],
              ['name' => 'manage_style', 'display_name' => 'Manage Style', 'description' => 'Manage Style'],
              ['name' => 'manage_special_feature', 'display_name' => 'Manage Special Features', 'description' => 'Manage Special Features'],
              ['name' => 'manage_space_rules', 'display_name' => 'Manage Space Rules', 'description' => 'Manage Space Rules'],
              ['name' => 'manage_activities', 'display_name' => 'Manage Activities', 'description' => 'Manage Activities'],
              ['name' => 'manage_currency', 'display_name' => 'Manage Currency', 'description' => 'Manage Currency'],
              ['name' => 'manage_language', 'display_name' => 'Manage Language', 'description' => 'Manage Language'],
              ['name' => 'manage_country', 'display_name' => 'Manage Country', 'description' => 'Manage Country'],
              ['name' => 'api_credentials', 'display_name' => 'Api Credentials', 'description' => 'Api Credentials'],
              ['name' => 'payment_gateway', 'display_name' => 'Payment Gateway', 'description' => 'Payment Gateway'],
              ['name' => 'email_settings', 'display_name' => 'Email Settings', 'description' => 'Email Settings'],
              ['name' => 'site_settings', 'display_name' => 'Site Settings', 'description' => 'Site Settings'],
              ['name' => 'reservations', 'display_name' => 'Reservations', 'description' => 'Reservations'],
              ['name' => 'space', 'display_name' => 'View Space', 'description' => 'View Space'],
              ['name' => 'add_space', 'display_name' => 'Add Space', 'description' => 'Add Space'],
              ['name' => 'edit_space', 'display_name' => 'Edit Space', 'description' => 'Edit Space'],
              ['name' => 'delete_space', 'display_name' => 'Delete Space', 'description' => 'Delete Space'],
              ['name' => 'manage_pages', 'display_name' => 'Manage Pages', 'description' => 'Manage Pages'],
              ['name' => 'manage_fees', 'display_name' => 'Manage Fees', 'description' => 'Manage Fees'],
              ['name' => 'join_us', 'display_name' => 'Join Us', 'description' => 'Join Us'],
              ['name' => 'manage_metas', 'display_name' => 'Manage Metas', 'description' => 'Manage Metas'],
              ['name' => 'reports', 'display_name' => 'Reports', 'description' => 'Reports'],
              ['name' => 'manage_home_sliders', 'display_name' => 'Manage Home Page Sliders', 'description' => 'Manage Home Page Sliders'],
              ['name' => 'manage_reviews', 'display_name' => 'Manage Reviews', 'description' => 'Manage Reviews'],
              ['name' => 'send_email', 'display_name' => 'Send Email', 'description' => 'Send Email'],
              ['name' => 'manage_help', 'display_name' => 'Manage Help', 'description' => 'Manage Help'],
              ['name' => 'manage_coupon_code', 'display_name' => 'Manage Coupon Code', 'description' => 'Manage Coupon Code'],
              ['name' => 'manage_referral_settings', 'display_name' => 'Manage Referrals Settings', 'description' => 'Manage Referrals Settings'],
              ['name' => 'manage_wishlists', 'display_name' => 'Manage Wish Lists', 'description' => 'Manage Wish Lists'],
              ['name' => 'manage_login_sliders', 'display_name' => 'Manage Login Slider', 'description' => 'Manage Login Slider'],
              ['name' => 'manage_our_community_banners', 'display_name' => 'Manage Our Community', 'description' => 'Manage Our Communtiy'],
              ['name' => 'manage_disputes', 'display_name' => 'Manage Disputes', 'description' => 'Manage Disputes'],
              ['name' => 'manage_referrals', 'display_name' => 'Manage Referrals', 'description' => 'Manage Referrals'],
        	]);
    }
}
