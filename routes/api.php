<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Without Login API's
Route::get('register', 'TokenAuthController@register');
Route::get('authenticate', 'TokenAuthController@authenticate');
Route::get('signup', 'TokenAuthController@signup');
Route::post('common_data', 'TokenAuthController@common_data');
Route::get('login', 'TokenAuthController@login');
Route::post('apple_callback', 'TokenAuthController@apple_callback');
Route::get('forgotpassword', 'TokenAuthController@forgotpassword');
Route::get('emailvalidation', 'TokenAuthController@emailvalidation');

Route::get('activities_list', 'HomeController@activities_list');
Route::get('country_list', 'HomeController@country_list');
Route::get('currency_list', 'HomeController@currency_list');
Route::get('stripe_supported_country_list', 'HomeController@stripe_supported_country_list');

Route::get('search_filters', 'SearchController@search_filters');
Route::get('space', 'SpaceController@space_detail');
Route::get('review_detail', 'SpaceController@review_detail');
Route::get('user_profile_details', 'UserController@user_profile_details');

Route::get('space_activities', 'SpaceController@space_activities');
Route::get('get_availability_times', 'SpaceController@get_availability_times');

// With Login API's
Route::group(['middleware' => ['jwt.verify', 'disable_user', 'url_decode']], function ()  {
	Route::get('explore', 'SearchController@explore_space');
	// Manage Space API
	Route::get('listing', 'SpaceController@listings');
	Route::get('disable_listing ', 'SpaceController@disable_space');
	Route::get('basics_step_items', 'SpaceController@basics_step_items');
	Route::get('setup_step_items', 'SpaceController@setup_step_items');
	Route::get('ready_host_step_items', 'SpaceController@ready_host_step_items');
	Route::get('space_listing_details', 'SpaceController@space_listing_details');
	Route::match(array('GET', 'POST'),'update_space', 'SpaceController@update_space');
	Route::get('update_space_description', 'SpaceController@update_space_description');
	Route::match(array('GET', 'POST'),'update_activities', 'SpaceController@update_activities');
	Route::match(array('GET', 'POST'), 'space_image_upload', 'SpaceController@space_image_upload');
	Route::get('delete_image', 'SpaceController@delete_image');
	Route::match(array('GET', 'POST'),'update_calendar', 'SpaceController@update_calendar');
	Route::get('get_min_amount', 'SpaceController@get_min_amount');

	// Common API's for Hosts and Guests
	Route::get('language','UserController@change_language');
	Route::get('currency_change', 'UserController@change_currency');
	Route::get('user_details', 'UserController@user_details');
	Route::get('view_profile', 'UserController@view_profile');
	Route::get('edit_profile', 'UserController@edit_profile');
	Route::get('payout_details', 'UserController@payout_details');
	Route::get('payout_changes', 'UserController@payout_changes');
	Route::match(['get', 'post'], 'add_payout_perference', 'UserController@add_payout_perference');
	Route::match(array('GET', 'POST'), 'upload_profile_image', 'UserController@upload_profile_image');
	Route::get('logout', 'UserController@logout');

	// Payment API's
	Route::get('contact_request', 'PaymentController@contact_request');
	Route::get('store_payment_data', 'PaymentController@store_payment_data');
	Route::get('apply_coupon', 'PaymentController@apply_coupon');
	Route::get('remove_coupon', 'PaymentController@remove_coupon');
	Route::get('get_payment_data', 'PaymentController@get_payment_data');
	Route::get('complete_booking', 'PaymentController@complete_booking');
	Route::post('generate_stripe_key', 'PaymentController@generate_stripe_key');

	// Inbox API's
	Route::get('inbox', 'MessagesController@inbox');
	Route::get('conversation_list', 'MessagesController@conversation_list');
	Route::get('send_message', 'MessagesController@send_message');
	Route::get('price_breakdown', 'MessagesController@price_breakdown');

	// Guest User Related API's
	Route::get('booking_types', 'TripsController@booking_types');
	Route::get('booking_details', 'TripsController@booking_details');
	Route::get('guest_cancel_reservation', 'TripsController@guest_cancel_reservation');

	// Host User Related API's
	Route::get('reservation_list', 'ReservationController@reservation_list');
	Route::get('host_cancel_reservation', 'ReservationController@host_cancel_reservation');
	Route::get('pre_approve', 'ReservationController@pre_approve');
	Route::get('accept', 'ReservationController@pre_accept');

	// Wishlist Related API's
	Route::get('add_wishlist', 'WishlistsController@add_wishlist');
	Route::get('get_wishlist', 'WishlistsController@get_wishlist');
	Route::get('get_particular_wishlist', 'WishlistsController@get_particular_wishlist');
	Route::get('edit_wishlist', 'WishlistsController@edit_wishlist');
	Route::get('delete_wishlist', 'WishlistsController@delete_wishlist');
});