<?php
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * Admin panel routes
 */

Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('login', 'AdminController@login')->name('admin_login');
    Route::get('get_sliders', 'AdminController@get_sliders');
});

Route::group(['middleware' => ['auth:admin','protection']], function () {
    Route::get('/', function () {
        return redirect()->route('admin_dashboard');
    });
    Route::get('logout', 'AdminController@logout')->name('admin_logout');

    // Dashboard
    Route::get('dashboard', 'AdminController@index')->name('admin_dashboard');

    // Manage Admin Users , Roles & Permissions
    Route::group(['middleware' => 'admin_can:manage_admin'], function () {
        Route::get('admin_users', 'AdminusersController@index')->name('admin_users');
        Route::match(array('GET', 'POST'), 'add_admin_user', 'AdminusersController@add');
        Route::match(array('GET', 'POST'), 'edit_admin_user/{id}', 'AdminusersController@update')->where('id', '[0-9]+');
        Route::get('delete_admin_user/{id}', 'AdminusersController@delete')->where('id', '[0-9]+');
        Route::get('roles', 'RolesController@index')->name('roles');;
        Route::match(array('GET', 'POST'), 'add_role', 'RolesController@add');
        Route::match(array('GET', 'POST'), 'edit_role/{id}', 'RolesController@update')->where('id', '[0-9]+');
        Route::get('delete_role/{id}', 'RolesController@delete')->where('id', '[0-9]+');
        Route::match(array('GET', 'POST'), 'add_permission', 'PermissionsController@add');
        Route::match(array('GET', 'POST'), 'edit_permission/{id}', 'PermissionsController@update')->where('id', '[0-9]+');
        Route::get('delete_permission/{id}', 'PermissionsController@delete')->where('id', '[0-9]+');
    });

    // Manage Users
    Route::get('users', 'UsersController@index')->middleware('admin_can:users')->name('users');
    Route::get('add_user', 'UsersController@create')->middleware('admin_can:add_user')->name('create_user');
    Route::post('add_user', 'UsersController@store')->middleware('admin_can:add_user')->name('store_user');
    Route::GET('edit_user/{id}', 'UsersController@edit')->where('id', '[0-9]+')->middleware('admin_can:edit_user')->name('edit_user');
    Route::POST('edit_user/{id}', 'UsersController@update')->middleware('admin_can:edit_user')->where('id', '[0-9]+')->name('update_user');
    Route::get('delete_user/{id}', 'UsersController@destroy')->middleware('admin_can:delete_user')->where('id', '[0-9]+')->name('delete_user');

    // Manage spaces
    Route::get('spaces', 'SpaceController@index')->middleware('admin_can:space')->name('admin.space');
    Route::match(array('GET', 'POST'), 'add_space', 'SpaceController@add')->middleware('admin_can:add_space')->name('admin.add_space');
    Route::post('admin_update_space', 'SpaceController@update_video');
    Route::match(array('GET', 'POST'), 'edit_space/{id}', 'SpaceController@update')->where('id', '[0-9]+')->middleware('admin_can:edit_space')->name('admin.edit_space');
    Route::get('delete_space/{id}', 'SpaceController@delete')->where('id', '[0-9]+')->middleware('admin_can:delete_space');
    Route::get('popular_space/{id}', 'SpaceController@popular')->where('id', '[0-9]+')->where('id', '[0-9]+')->name('admin.popular_space');
    Route::get('recommended_space/{id}', 'SpaceController@recommended')->where('id', '[0-9]+')->where('id', '[0-9]+')->name('admin.recommend_space');
    Route::post('space_calendar/{id}', 'SpaceController@space_calendar')->where('id', '[0-9]+');
    Route::post('resubmit_listing','SpaceController@resubmit_listing');
    Route::post('delete_photo', 'SpaceController@delete_photo');
    Route::post('admin_pricelist', 'SpaceController@update_price');
    Route::post('featured_image', 'SpaceController@featured_image');
    Route::post('photo_highlights', 'SpaceController@photo_highlights');
    Route::get('space/users_list', 'SpaceController@users_list');
    Route::post('space/delete_price_rule/{id}', 'SpaceController@delete_price_rule')->where('id', '[0-9]+');
    Route::get('update_space_status/{id}/{type}/{option}', 'SpaceController@update_space_status')->name('update_space_status');

    // Manage Reservations
    Route::group(['middleware' => 'admin_can:reservations'], function() {
        Route::get('reservations', 'ReservationsController@index')->name('reservations');
        Route::get('reservation/detail/{id}', 'ReservationsController@detail')->where('id', '[0-9]+');
        Route::get('reservation/conversation/{id}', 'ReservationsController@conversation')->where('id', '[0-9]+');
        Route::get('reservation/need_payout_info/{id}/{type}', 'ReservationsController@need_payout_info');
        Route::post('reservation/payout', 'ReservationsController@payout');
        Route::get('host_penalty', 'HostPenaltyController@index')->name('host_penalty');
    });

    // Manage Disputes
    Route::group(['middleware' => 'admin_can:manage_disputes'], function () {
        Route::get('disputes', 'DisputesController@index')->name('admin.disputes');
        Route::get('dispute/details/{id}', 'DisputesController@details')->name('admin.dispute_details');
        Route::get('dispute/close/{id}', 'DisputesController@close');
        Route::post('dispute_admin_message/{id}', 'DisputesController@admin_message');
        Route::get('dispute_confirm_amount/{id}', 'DisputesController@confirm_amount');
    });

    // Manage Email Settings & Send email
    Route::match(array('GET', 'POST'), 'email_settings', 'EmailController@index')->middleware(['admin_can:email_settings'])->name('email_settings');
    Route::match(array('GET', 'POST'), 'send_email', 'EmailController@send_email')->middleware(['admin_can:send_email'])->name('send_email');

    // Manage Reviews
    Route::group(['middleware' => 'admin_can:manage_reviews'], function () {
        Route::match(array('GET', 'POST'), 'reviews', 'ReviewsController@index')->name('reviews');
        Route::match(array('GET', 'POST'), 'edit_review/{id}', 'ReviewsController@update')->where('id', '[0-9]+');
    });

     // Manage Referrals Routes
    Route::group(['middleware' => 'admin_can:manage_referrals'], function () {
        Route::get('referrals', 'ReferralsController@index')->name('referrals');
        Route::get('referral_details/{id}', 'ReferralsController@details')->where('id', '[0-9]+');
    });

    // Manage Wishlists
    Route::group(['middleware' => 'admin_can:manage_wishlists'], function () {
    Route::match(array('GET', 'POST'), 'wishlists', 'WishlistController@index')->name('wishlists');
    Route::match(array('GET', 'POST'), 'pick_wishlist/{id}', 'WishlistController@pick')->where('id', '[0-9]+')->name('pick_wishlist');
    });

    // Manage Coupon Code
    Route::group(['middleware' => 'admin_can:manage_coupon_code'], function () {
        Route::get('coupon_code', 'CouponCodeController@index')->name('coupon_code');
        Route::match(array('GET', 'POST'), 'add_coupon_code', 'CouponCodeController@add');
        Route::match(array('GET', 'POST'), 'edit_coupon_code/{id}', 'CouponCodeController@update')->where('id', '[0-9]+');
        Route::get('delete_coupon_code/{id}', 'CouponCodeController@delete');
    });

    // Manage Reports
    Route::group(['middleware' => 'admin_can:reports'], function () {
        Route::match(['GET', 'POST'], 'reports', 'ReportsController@index')->name('reports');
        Route::get('reports/export/{from}/{to}/{category}', 'ReportsController@export');
    });

    // Manage Home Cities
    Route::group(['middleware' => 'admin_can:manage_home_sliders'], function () {
        Route::get('homepage_sliders', 'HomePageSlidersController@index')->name('homepage_sliders');
        Route::match(array('GET', 'POST'), 'add_home_slider', 'HomePageSlidersController@add')->name('homepage_sliders.add');
        Route::match(array('GET', 'POST'), 'edit_home_slider/{id}', 'HomePageSlidersController@update')->where('id', '[0-9]+')->name('homepage_sliders.update');
        Route::get('delete_home_slider/{id}', 'HomePageSlidersController@delete')->where('id', '[0-9]+')->name('homepage_sliders.delete');
    });

    // Manage Login Slider
    Route::group(['middleware' => 'admin_can:manage_login_sliders'],function () {
        Route::get('slider', 'SliderController@index')->name('slider');
        Route::match(array('GET', 'POST'), 'add_slider', 'SliderController@add');
        Route::match(array('GET', 'POST'), 'edit_slider/{id}', 'SliderController@update')->where('id', '[0-9]+');
        Route::get('delete_slider/{id}', 'SliderController@delete')->where('id', '[0-9]+');
    });

    // Manage Our Community Banner
    Route::group(['middleware' => 'admin_can:manage_our_community_banners'],function () {
        Route::get('our_community_banners', 'OurCommunityBannersController@index')->name('our_community_banners');
        Route::match(array('GET', 'POST'), 'add_our_community_banners', 'OurCommunityBannersController@add');
        Route::match(array('GET', 'POST'), 'edit_our_community_banners/{id}', 'OurCommunityBannersController@update')->where('id', '[0-9]+');
        Route::get('delete_our_community_banners/{id}', 'OurCommunityBannersController@delete')->where('id', '[0-9]+');
    });

    // Manage Help
    Route::group(['middleware' => 'admin_can:manage_help'], function () {
        Route::get('help_category', 'HelpCategoryController@index')->name('help_category');
        Route::match(array('GET', 'POST'), 'add_help_category', 'HelpCategoryController@add');
        Route::match(array('GET', 'POST'), 'edit_help_category/{id}', 'HelpCategoryController@update')->where('id', '[0-9]+');
        Route::get('delete_help_category/{id}', 'HelpCategoryController@delete')->where('id', '[0-9]+');
        Route::get('help_subcategory', 'HelpSubCategoryController@index')->name('help_subcategory');
        Route::match(array('GET', 'POST'), 'add_help_subcategory', 'HelpSubCategoryController@add');
        Route::match(array('GET', 'POST'), 'edit_help_subcategory/{id}', 'HelpSubCategoryController@update')->where('id', '[0-9]+');
        Route::get('delete_help_subcategory/{id}', 'HelpSubCategoryController@delete')->where('id', '[0-9]+');
        Route::get('help', 'HelpController@index')->name('help');
        Route::match(array('GET', 'POST'), 'add_help', 'HelpController@add')->name('add_help');
        Route::match(array('GET', 'POST'), 'edit_help/{id}', 'HelpController@update')->where('id', '[0-9]+')->name('edit_help');
        Route::get('delete_help/{id}', 'HelpController@delete')->where('id', '[0-9]+')->name('delete_help');
        Route::post('ajax_help_subcategory/{id}', 'HelpController@ajax_help_subcategory')->where('id', '[0-9]+')->name('ajax_help_subcategory');
    });

    // Manage Amenities
    Route::group(['middleware' => 'admin_can:manage_amenities'], function () {
        Route::get('amenities', 'AmenitiesController@index')->name('amenities');
        Route::match(array('GET', 'POST'), 'add_amenity', 'AmenitiesController@add')->name('create_amenity');
        Route::match(array('GET', 'POST'), 'edit_amenity/{id}', 'AmenitiesController@update')->where('id', '[0-9]+')->name('edit_amenity');
        Route::get('delete_amenity/{id}', 'AmenitiesController@delete')->where('id', '[0-9]+');
        Route::get('amenities_type', 'AmenitiesTypeController@index')->name('amenities_type');
        Route::match(array('GET', 'POST'), 'add_amenities_type', 'AmenitiesTypeController@add');
        Route::match(array('GET', 'POST'), 'edit_amenities_type/{id}', 'AmenitiesTypeController@update')->where('id', '[0-9]+');
        Route::get('delete_amenities_type/{id}', 'AmenitiesTypeController@delete')->where('id', '[0-9]+');
    });

    // Manage Space Type
    Route::group(['middleware' => 'admin_can:manage_kind_of_space'], function () {
        Route::get('space_type', 'KindOfSpaceController@index')->name('kind_of_space');
        Route::get('add_space_type', 'KindOfSpaceController@create')->name('create_kind_of_space');
        Route::post('add_space_type', 'KindOfSpaceController@store')->name('store_kind_of_space');
        Route::GET('edit_space_type/{id}', 'KindOfSpaceController@edit')->where('id', '[0-9]+')->name('edit_kind_of_space');
        Route::POST('edit_space_type/{id}', 'KindOfSpaceController@update')->where('id', '[0-9]+')->name('update_kind_of_space');
        Route::get('delete_space_type/{id}', 'KindOfSpaceController@destroy')->where('id', '[0-9]+')->name('delete_kind_of_space');
         Route::get('popular_space_type/{id}', 'KindOfSpaceController@popular')->where('id', '[0-9]+')->where('id', '[0-9]+')->name('admin.popular_space_type');
    });

    // Manage Guest Access
    Route::group(['middleware' => 'admin_can:manage_guest_access'], function () {
        Route::get('guest_access', 'GuestAccessController@index')->name('guest_access');
        Route::get('add_guest_access', 'GuestAccessController@create')->name('create_guest_access');
        Route::post('add_guest_access', 'GuestAccessController@store')->name('store_guest_access');
        Route::GET('edit_guest_access/{id}', 'GuestAccessController@edit')->where('id', '[0-9]+')->name('edit_guest_access');
        Route::POST('edit_guest_access/{id}', 'GuestAccessController@update')->where('id', '[0-9]+')->name('update_guest_access');
        Route::get('delete_guest_access/{id}', 'GuestAccessController@destroy')->where('id', '[0-9]+')->name('delete_guest_access');
    });

    // Manage Services & Extras
    Route::group(['middleware' => 'admin_can:manage_services'], function () {
        Route::get('services', 'ServicesController@index')->name('services');
        Route::get('add_services', 'ServicesController@create')->name('create_services');
        Route::post('add_services', 'ServicesController@store')->name('store_services');
        Route::GET('edit_services/{id}', 'ServicesController@edit')->where('id', '[0-9]+')->name('edit_services');
        Route::POST('edit_services/{id}', 'ServicesController@update')->where('id', '[0-9]+')->name('update_services');
        Route::get('delete_services/{id}', 'ServicesController@destroy')->where('id', '[0-9]+')->name('delete_services');
    });

    // Manage Style
    Route::group(['middleware' => 'admin_can:manage_style'], function () {
        Route::get('style', 'StyleController@index')->name('styles');
        Route::get('add_style', 'StyleController@create')->name('create_style');
        Route::post('add_style', 'StyleController@store')->name('store_style');
        Route::GET('edit_style/{id}', 'StyleController@edit')->where('id', '[0-9]+')->name('edit_style');
        Route::POST('edit_style/{id}', 'StyleController@update')->where('id', '[0-9]+')->name('update_style');
        Route::get('delete_style/{id}', 'StyleController@destroy')->where('id', '[0-9]+')->name('delete_style');
    });

    // Manage Special Feature
    Route::group(['middleware' => 'admin_can:manage_special_feature'], function () {
        Route::get('special_feature', 'SpecialFeatureController@index')->name('special_features');
        Route::get('add_special_feature', 'SpecialFeatureController@create')->name('create_special_feature');
        Route::post('add_special_feature', 'SpecialFeatureController@store')->name('store_special_feature');
        Route::GET('edit_special_feature/{id}', 'SpecialFeatureController@edit')->where('id', '[0-9]+')->name('edit_special_feature');
        Route::POST('edit_special_feature/{id}', 'SpecialFeatureController@update')->where('id', '[0-9]+')->name('update_special_feature');
        Route::get('delete_special_feature/{id}', 'SpecialFeatureController@destroy')->where('id', '[0-9]+')->name('delete_special_feature');
    });

    // Manage Space Rules
    Route::group(['middleware' => 'admin_can:manage_space_rules'], function () {
        Route::get('space_rules', 'SpaceRulesController@index')->name('space_rules');
        Route::get('add_space_rules', 'SpaceRulesController@create')->name('create_space_rule');
        Route::post('add_space_rules', 'SpaceRulesController@store')->name('store_space_rule');
        Route::GET('edit_space_rules/{id}', 'SpaceRulesController@edit')->where('id', '[0-9]+')->name('edit_space_rule');
        Route::POST('edit_space_rules/{id}', 'SpaceRulesController@update')->where('id', '[0-9]+')->name('update_space_rule');
        Route::get('delete_space_rules/{id}', 'SpaceRulesController@destroy')->where('id', '[0-9]+')->name('delete_space_rule');
    });

    // Manage Activities Type
    Route::group(['middleware' => 'admin_can:manage_activities'], function () {
        Route::get('activity_types', 'ActivityTypeController@index')->name('activity_types');
        Route::get('add_activity_type', 'ActivityTypeController@create')->name('create_activity_type');
        Route::post('add_activity_type', 'ActivityTypeController@store')->name('store_activity_type');
        Route::GET('edit_activity_type/{id}', 'ActivityTypeController@edit')->where('id', '[0-9]+')->name('edit_activity_type');
        Route::POST('edit_activity_type/{id}', 'ActivityTypeController@update')->where('id', '[0-9]+')->name('update_activity_type');
        Route::get('delete_activity_type/{id}', 'ActivityTypeController@destroy')->where('id', '[0-9]+')->name('delete_activity_type');
    });

    // Manage Activities
    Route::group(['middleware' => 'admin_can:manage_activities'], function () {
        Route::get('activities', 'ActivityController@index')->name('activities');
        Route::get('add_activity', 'ActivityController@create')->name('create_activity');
        Route::post('add_activity', 'ActivityController@store')->name('store_activity');
        Route::GET('edit_activity/{id}', 'ActivityController@edit')->where('id', '[0-9]+')->name('edit_activity');
        Route::POST('edit_activity/{id}', 'ActivityController@update')->where('id', '[0-9]+')->name('update_activity');
        Route::get('delete_activity/{id}', 'ActivityController@destroy')->where('id', '[0-9]+')->name('delete_activity');
        Route::get('popular_activity/{id}', 'ActivityController@popular')->where('id', '[0-9]+')->where('id', '[0-9]+')->name('admin.popular_activity');
    });

    // Manage Sub Activities
    Route::group(['middleware' => 'admin_can:manage_activities'], function () {
        Route::get('sub_activities', 'SubActivityController@index')->name('sub_activities');
        Route::get('add_sub_activity', 'SubActivityController@create')->name('create_sub_activity');
        Route::post('add_sub_activity', 'SubActivityController@store')->name('store_sub_activity');
        Route::GET('edit_sub_activity/{id}', 'SubActivityController@edit')->where('id', '[0-9]+')->name('edit_sub_activity');
        Route::POST('edit_sub_activity/{id}', 'SubActivityController@update')->where('id', '[0-9]+')->name('update_sub_activity');
        Route::get('delete_sub_activity/{id}', 'SubActivityController@destroy')->where('id', '[0-9]+')->name('delete_sub_activity');
    });

    // Manage Pages
    Route::group(['middleware' => 'admin_can:manage_pages'], function () {
        Route::get('pages', 'PagesController@index')->name('pages');
        Route::match(array('GET', 'POST'), 'add_page', 'PagesController@add')->name('add_page');
        Route::match(array('GET', 'POST'), 'edit_page/{id}', 'PagesController@update')->where('id', '[0-9]+')->name('edit_page');
        Route::match(array('GET', 'POST'), 'page_status_check/{id}', 'PagesController@chck_status')->where('id', '[0-9]+');
        Route::get('delete_page/{id}', 'PagesController@delete')->where('id', '[0-9]+')->name('delete_page');;
    });

    // Manage Currency
    Route::group(['middleware' => 'admin_can:manage_currency'], function () {
        Route::get('currency', 'CurrencyController@index')->name('currency');
        Route::match(array('GET', 'POST'), 'add_currency', 'CurrencyController@add');
        Route::match(array('GET', 'POST'), 'edit_currency/{id}', 'CurrencyController@update')->where('id', '[0-9]+');
        Route::get('delete_currency/{id}', 'CurrencyController@delete')->where('id', '[0-9]+');
    });

    // Manage Language
    Route::group(['middleware' => 'admin_can:manage_language'], function () {
        Route::get('language', 'LanguageController@index')->name('language');
        Route::match(array('GET', 'POST'), 'add_language', 'LanguageController@add');
        Route::match(array('GET', 'POST'), 'edit_language/{id}', 'LanguageController@update')->where('id', '[0-9]+');
        Route::get('delete_language/{id}', 'LanguageController@delete')->where('id', '[0-9]+');
    });

    // Manage Country
    Route::group(['middleware' => 'admin_can:manage_country'], function () {
        Route::get('country', 'CountryController@index')->name('country');
        Route::match(array('GET', 'POST'), 'add_country', 'CountryController@add');
        Route::match(array('GET', 'POST'), 'edit_country/{id}', 'CountryController@update')->where('id', '[0-9]+');
        Route::get('delete_country/{id}', 'CountryController@delete')->where('id', '[0-9]+');
    });

    // Manage Referral Settings
    Route::match(array('GET', 'POST'), 'referral_settings', 'ReferralSettingsController@index')->middleware(['admin_can:manage_referral_settings'])->name('referral_settings');

    // Manage Fees
    Route::group(['middleware' => 'admin_can:manage_fees'], function () {
        Route::match(array('GET', 'POST'), 'fees', 'FeesController@index')->name('fees');
        Route::match(array('GET', 'POST'), 'host_service_fees', 'FeesController@host_service_fees');
        Route::match(array('GET', 'POST'), 'fees/host_penalty_fees', 'FeesController@host_penalty_fees');
    });

    // Manage Metas
    Route::group(['middleware' => 'admin_can:manage_metas'], function () {
        Route::match(array('GET', 'POST'), 'metas', 'MetasController@index')->name('metas');
        Route::match(array('GET', 'POST'), 'edit_meta/{id}', 'MetasController@update')->where('id', '[0-9]+');
    });

    // Manage API Credentials
    Route::match(array('GET', 'POST'), 'api_credentials', 'ApiCredentialsController@index')->middleware(['admin_can:api_credentials'])->name('api_credentials');

    // Manage Payment Gateway
    Route::match(array('GET', 'POST'), 'payment_gateway', 'PaymentGatewayController@index')->middleware(['admin_can:payment_gateway'])->name('payment_gateway');

    // Manage Join Us
    Route::match(array('GET', 'POST'), 'join_us', 'JoinUsController@index')->middleware(['admin_can:join_us'])->name('join_us');

    // Manage Site Settings
    Route::match(array('GET', 'POST'), 'site_settings', 'SiteSettingsController@index')->middleware(['admin_can:site_settings'])->name('site_settings');
});

Route::post('authenticate', 'AdminController@authenticate')->name('admin_authenticate');