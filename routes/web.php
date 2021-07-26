<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

/**
 * No Authentication required routes
 */

Route::group(['middleware' => ['install', 'locale', 'session_check']], function () {
	Route::get('/', 'HomeController@index')->name('home_page');
	Route::view('profile_settings', 'common.responsive_menu')->name('profile_settings');
	Route::get('ajax_home', 'HomeController@ajax_home');
	Route::get('ajax_home_explore', 'HomeController@ajax_home_explore');
});

// Route::get('email_test/{id}/{hours}', 'EmailController@pre_accepted');
Route::get('googleAuthenticate', 'UserController@googleAuthenticate');
Route::get('facebooklogin', 'HomeController@generateFacebookUrl');
//linkedin login
Route::redirect('linkedin','login');

/**
 * Access only without authertication
 */
Route::get('linkedinLoginVerification', 'UserController@linkedinLoginVerification');
Route::get('linkedinConnect', 'UserController@linkedinConnect');
Route::group(['middleware' => ['locale', 'guest:user', 'session_check','protection']], function () {
	Route::view('login', 'home.login')->name('user_login');
	Route::get('signup_login', 'HomeController@signup_login')->name('signup_login');
	Route::post('apple_callback', 'UserController@appleCallback');
	Route::post('create', 'UserController@create');
	Route::post('authenticate', 'UserController@authenticate')->name('login');
	Route::get('facebookAuthenticate', 'UserController@facebookAuthenticate');
	Route::match(array('GET', 'POST'), 'forgot_password', 'UserController@forgot_password');
	Route::get('users/set_password/{secret?}', 'UserController@set_password');
	Route::post('users/set_password', 'UserController@set_password');
	Route::get('c/{username}', 'ReferralsController@invite_referral');
	Route::get('user_disabled', 'UserController@user_disabled')->name('user_disabled');
	Route::get('users/signup_email', 'UserController@signup_email')->name('complete_signup');
	Route::post('users/finish_signup_email', 'UserController@finish_signup_email');
	Route::post('users/finish_signup_linkedin_email', 'UserController@finish_signup_linkedin_email');
});

/**
 * No Authentication required routes
 */
Route::group(['middleware' => ['locale', 'session_check']], function () {
	Route::view('contact', 'home.contact');
	Route::match(['get', 'post'], 'contact_create', 'HomeController@contact_create');
	Route::post('set_session', 'HomeController@set_session');
	Route::get('s', 'SearchController@index')->name('search_page');
	Route::match(['get', 'post'], 'searchResult', 'SearchController@searchResult');
	Route::post('rooms_photos', 'SearchController@rooms_photos');
	Route::post('get_lang_details/{space_id}', 'SpaceController@get_lang_details');
	Route::post('get_language_list', 'SpaceController@get_language_list')->name('get_language_list');
	Route::get('currency_cron', 'CronController@currency');
	Route::get('cron/expire', 'CronController@expire');
	Route::get('cron/travel_credit', 'CronController@travel_credit');
	Route::get('cron/review_remainder', 'CronController@review_remainder');
	Route::get('cron/host_remainder_pending_reservaions', 'CronController@host_remainder_pending_reservaions');
	Route::get('users/show/{id}', 'UserController@show')->where('id', '[0-9]+')->name('show_profile');
	Route::view('home/cancellation_policies', 'home.cancellation_policies');
	Route::get('help', 'HomeController@help')->name('help_home');
	Route::get('help/topic/{id}/{category}', 'HomeController@help')->name('help_category');
	Route::get('help/article/{id}/{question}', 'HomeController@help')->name('help_question');
	Route::get('ajax_help_search', 'HomeController@ajax_help_search');
	Route::get('wishlist_list', 'WishlistController@wishlist_list');
	Route::post('get_wishlists_space', 'WishlistController@get_wishlists_space');
	Route::get('wishlists/{id}', 'WishlistController@wishlist_details')->where('id', '[0-9]+')->name('wishlists_details');
	Route::get('users/{id}/wishlists', 'WishlistController@my_wishlists');
	Route::get('invite', 'ReferralsController@invite')->name('invite');
	Route::get('wishlists/popular', 'WishlistController@popular');
	Route::get('wishlists/picks', 'WishlistController@picks');
});

/**
 * Access only with authentication
 */
Route::group(['middleware' => ['locale', 'auth:user', 'session_check','protection']], function () {
	Route::get('dashboard', 'UserController@dashboard')->name('dashboard');
	Route::get('users/edit', 'UserController@edit')->name('edit_profile');
	Route::match(['get', 'post'], 'users/get_users_phone_numbers', 'UserController@get_users_phone_numbers');
	Route::post('users/update_users_phone_number', 'UserController@update_users_phone_number');
	Route::post('users/remove_users_phone_number', 'UserController@remove_users_phone_number');
	Route::post('users/verify_users_phone_number', 'UserController@verify_users_phone_number');
	Route::get('users/edit/media', 'UserController@media')->name('edit_profile_media');
	Route::get('users/edit_verification', 'UserController@verification')->name('edit_verification');
	Route::get('users/get_verification_documents', 'UserController@get_verification_documents');
    Route::post('users/delete_document','UserController@delete_document');
    Route::post('users/upload_verification_documents','UserController@upload_verification_documents');
	Route::get('facebookConnect', 'UserController@facebookConnect');
	Route::get('facebookDisconnect', 'UserController@facebookDisconnect');
	Route::get('googleConnect/{id}', 'UserController@googleConnect')->where('id', '[0-9]+');
	Route::get('googleDisconnect', 'UserController@googleDisconnect');
	Route::get('linkedinDisconnect', 'UserController@linkedinDisconnect');
	Route::post('users/image_upload', 'UserController@image_upload');
	Route::post('users/image_takephoto', 'UserController@image_takephoto');
	Route::post('users/remove_images', 'UserController@remove_images');
	Route::get('users/reviews', 'UserController@reviews')->name('user_reviews');
	Route::match(['get', 'post'], 'reviews/edit/{id}', 'UserController@reviews_edit')->where('id', '[0-9]+')->name('reviews_edit');
	Route::post('users/update/{id}', 'UserController@update')->where('id', '[0-9]+');
	Route::get('users/confirm_email/{code?}', 'UserController@confirm_email')->name('confirm_email');
	Route::get('users/request_new_confirm_email', 'UserController@request_new_confirm_email');
	Route::get('users/security', 'UserController@security')->name('security');
	Route::post('wishlist_create', 'WishlistController@create')->name('wishlist_create');
	Route::post('create_new_wishlist', 'WishlistController@create_new_wishlist');
	Route::post('edit_wishlist/{id}', 'WishlistController@edit_wishlist')->where('id', '[0-9]+');
	Route::get('delete_wishlist/{id}', 'WishlistController@delete_wishlist')->where('id', '[0-9]+');
	Route::post('remove_saved_wishlist/{id}', 'WishlistController@remove_saved_wishlist')->where('id', '[0-9]+');
	Route::post('add_note_wishlist/{id}', 'WishlistController@add_note_wishlist')->where('id', '[0-9]+');
	Route::post('save_wishlist', 'WishlistController@save_wishlist')->name('save_wishlist');
	Route::get('wishlists/my', 'WishlistController@my_wishlists')->name('my_wishlists');
	Route::post('share_email/{id}', 'WishlistController@share_email')->where('id', '[0-9]+')->name('wishlist_share.email');
	Route::match(['get', 'post'], 'users/payout_preferences/{id}', 'UserController@payout_preferences')->where('id', '[0-9]+')->name('payout_preferences');
	Route::match(['get', 'post'], 'users/update_payout_preferences/{id}', 'UserController@update_payout_preferences')->where('id', '[0-9]+');
	Route::match(['get', 'post'], 'users/stripe_payout_preferences', 'UserController@stripe_payout_preferences');
	Route::get('users/payout_delete/{id}', 'UserController@payout_delete')->where('id', '[0-9]+');
	Route::get('users/payout_default/{id}', 'UserController@payout_default')->where('id', '[0-9]+');
	Route::get('users/transaction_history', 'UserController@transaction_history')->name('transaction_history');
	Route::post('users/result_transaction_history', 'UserController@result_transaction_history');
	Route::get('transaction_history/csv/{id}', 'UserController@transaction_history_csv')->where('id', '[0-9]+');
	Route::post('change_password', 'UserController@change_password');
	Route::get('account', function () {
		return redirect()->route('payout_preferences',[Auth::user()->id]);
	})->name('account');
	Route::get('space', 'SpaceController@index')->name('space');

	// List Your Space Routes
	Route::get('manage_listing/basics', 'SpaceController@manage_listing')->name('manage_space.new');
	Route::post('manage_listing/update_space', 'SpaceController@update_space')->name('update_space');
	Route::match(['get','post'],'manage_listing/{space_id}/{page}', 'SpaceController@manage_listing')->where(['id' => '[0-9]+', 'page' => 'home|basics|setup|ready_to_host'])->name('manage_space');

	Route::group(['prefix' => 'manage_listing','middleware' => 'manage_listing_auth'], function () {
		Route::match(['get','post'],'{space_id}/{page}', 'SpaceController@manage_listing')->where(['id' => '[0-9]+', 'page' => 'home|basics|setup|ready_to_host'])->name('manage_space');
		Route::post('add_photos', 'SpaceController@upload_photos')->name('upload_photos');
		Route::post('change_photo_order', 'SpaceController@change_photo_order')->name('change_photo_order');
		Route::post('delete_photo', 'SpaceController@delete_photo')->name('delete_photo');
		Route::post('photo_description', 'SpaceController@photo_highlights')->name('photo_description');
		Route::post('photos_list', 'SpaceController@photos_list')->name('photos_list');

		Route::post('update_price', 'SpaceController@update_price')->name('update_price');

		Route::post('get_all_trans_desc', 'SpaceController@getAllTransDescription')->name('get_all_trans_desc');
		Route::post('add_description', 'SpaceController@add_description')->name('add_description');
		Route::post('delete_description', 'SpaceController@delete_description')->name('delete_description');

		Route::post('calendar_edit', 'SpaceController@calendar_edit')->name('calendar_edit');
	});

	Route::post('get_min_amount', 'SpaceController@get_min_amount')->name('get_min_amount');
	Route::get('listing/{id}/duplicate', 'SpaceController@duplicate')->where(['id' => '[0-9]+'])->name('duplicate_space');

	Route::get('inbox', 'InboxController@index')->name('inbox');
	Route::match(['get', 'post'], 'payments/book/{id?}', 'PaymentController@index')->where('id', '[0-9]+')->name('payment.home');
	Route::post('payments/apply_coupon', 'PaymentController@apply_coupon')->name('apply_coupon');
	Route::post('payments/remove_coupon', 'PaymentController@remove_coupon')->name('remove_coupon');
	Route::match(['get', 'post'], 'payments/create_booking', 'PaymentController@create_booking');
	Route::match(['get', 'post'], 'payments/pre_accept', 'PaymentController@pre_accept');
	Route::get('payments/success', 'PaymentController@success');
	Route::get('payments/cancel', 'PaymentController@cancel');
	Route::post('users/ask_question/{id}', 'PaymentController@contact_request')->where('id', '[0-9]+')->name('contact_request');

	// Message
	Route::post('inbox/archive', 'InboxController@archive');
	Route::post('inbox/star', 'InboxController@star');
	Route::post('inbox/message_by_type', 'InboxController@message_by_type');
	Route::post('inbox/all_message', 'InboxController@all_message');
	Route::get('z/q/{id}', 'InboxController@guest_conversation')->where('id', '[0-9]+')->name('guest_conversation');
	Route::get('messaging/qt_with/{id}', 'InboxController@host_conversation')->where('id', '[0-9]+')->name('host_conversation');
	Route::post('messaging/qt_reply', 'InboxController@reply')->name('reply_message');
    Route::get('admin_messages/{id}','InboxController@admin_messages')->name('admin_messages');
	Route::get('messaging/remove_special_offer/{id}', 'InboxController@remove_special_offer')->where('id', '[0-9]+')->name('remove_special_offer');
    Route::get('messaging/admin/{id}', 'InboxController@admin_message')->where('id', '[0-9]+')->name('admin_resubmit_message');;
	Route::post('inbox/calendar', 'InboxController@calendar')->name('host_calendar');
	Route::post('inbox/message_count', 'InboxController@message_count');
	// Reservation
	Route::get('reservation/{id}', 'ReservationController@index')->where('id', '[0-9]+')->name('reservation_request');
	Route::post('reservation/accept/{id}', 'ReservationController@accept')->where('id', '[0-9]+');
	Route::post('reservation/decline/{id}', 'ReservationController@decline')->where('id', '[0-9]+');
	Route::get('reservation/expire/{id}', 'ReservationController@expire')->where('id', '[0-9]+');
	Route::get('my_bookings', 'ReservationController@my_reservations')->name('my_bookings');
	Route::get('reservation/itinerary', 'ReservationController@print_confirmation');
	Route::get('reservation/requested', 'ReservationController@requested')->name('reservation_requested');
	Route::post('reservation/itinerary_friends', 'ReservationController@itinerary_friends')->name('itinerary_friends');
	// Cancel Reservation
	Route::match(['get', 'post'], 'trips/guest_cancel_reservation', 'TripsController@guest_cancel_reservation')->name('guest_cancel_reservation');
	Route::match(['get', 'post'], 'reservation/host_cancel_reservation', 'ReservationController@host_cancel_reservation')->name('host_cancel_reservation');
	Route::match(['get', 'post'], 'checking/{id}', 'TripsController@get_status')->where('id', '[0-9]+');
	Route::match(['get', 'post'], 'reservation/cencel_request_send', 'ReservationController@cencel_request_send');
	// Trips
	Route::get('current_bookings', 'TripsController@current')->name('current_bookings');
	Route::get('previous_bookings', 'TripsController@previous')->name('previous_bookings');
	Route::get('reservation/receipt', 'TripsController@receipt')->name('receipt');
	Route::post('invite/share_email', 'ReferralsController@share_email');
	Route::post('disputes/create', 'DisputesController@create_dispute');
	Route::get('disputes', 'DisputesController@index')->name('disputes');
	Route::post('get_disputes', 'DisputesController@get_disputes');
	Route::get('dispute_details/{id}', 'DisputesController@details');
	Route::get('dispute_documents_slider/{id}', 'DisputesController@documents_slider');
	Route::post('dispute_keep_talking/{id}', 'DisputesController@keep_talking');
	Route::post('dispute_documents_upload/{id}', 'DisputesController@documents_upload')->name('upload_dispute_doc');
	Route::post('dispute_involve_site/{id}', 'DisputesController@involve_site');
	Route::post('dispute_accept_amount/{id}', 'DisputesController@accept_amount');
	Route::post('dispute_pay_amount/{id}', 'DisputesController@pay_amount');
	Route::get('dispute_pay_amount_success/{id}', 'DisputesController@pay_amount_success');
	Route::get('dispute_pay_amount_cancel/{id}', 'DisputesController@pay_amount_cancel');
	Route::post('dispute_details/dispute_delete_document', 'DisputesController@dispute_delete_document');
});

/**
 * No Authentication required routes
 */
Route::group(['middleware' => ['locale', 'session_check','protection'],'prefix' => 'space'], function () {
	Route::get('{id}', 'SpaceController@space_details')->where('id', '[0-9]+')->name('space_details');
	Route::post('space_calendar', 'SpaceController@space_calendar')->name('space_calendar');
	Route::post('space_activities', 'SpaceController@getSpaceActivity')->name('space_activities');
	Route::post('price_calculation', 'SpaceController@price_calculation')->name('price_calculation');
	Route::post('check_availability', 'SpaceController@check_availability');
	Route::post('current_date_check', 'SpaceController@current_date_check');
	Route::post('checkin_date_check', 'SpaceController@checkin_date_check');
});

Route::get('logout', function () {
	Auth::guard('user')->logout();
	return Redirect::to('login');
})->name('logout');

Route::get('in_secure', function () {
	return view('errors.in_secure');
});

// Route::get('phpinfo', 'HomeController@phpinfo');

Route::get('show__l-log', 'HomeController@showLog');
Route::get('clear__l-log', 'HomeController@clearLog');
Route::get('update__env--content', 'HomeController@updateEnv');


// Static Page Route
Route::get('{name}', 'HomeController@static_pages')->middleware(['install', 'locale', 'session_check']);