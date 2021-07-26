<?php

/**
 * Wishlists Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Wishlists
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\Space;
use App\Models\SavedWishlists;
use App\Models\Wishlists;
use JWTAuth;
use Validator;

class WishlistsController extends Controller
{
	/**
	 * Add new Wishlists
	 *
	 * @param  Get method inputs
	 * @return Response in Json
	 */
	public function add_wishlist(Request $request)
	{
		$rules['space_id'] = 'required|exists:space,id';
		if ($request->list_id != '') {
			$rules['list_id'] = 'exists:wishlists,id';
		}
		$attributes = array('space_id' => 'Space Id', 'list_id' => 'List Id');
		$messages = array('required' => ':attribute is required.');
		$validator = Validator::make($request->all(), $rules, $messages, $attributes);

		if($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		if(!$request->list_id && !$request->list_name) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> 'Try again',
			]);
		}

		$user_details = JWTAuth::parseToken()->authenticate();

		if ($request->list_id != '') {
			$check_wishlist = Wishlists::where('user_id', $user_details->id)->where('id', $request->list_id)->count();

			if ($check_wishlist > 0) {
				$check_save_wishlist = SavedWishlists::whereWishlistId($request->list_id)->whereUserId($user_details->id)->whereSpaceId($request->space_id)->count();

				if ($check_save_wishlist < 1) {
					$save_wishlist 				= new SavedWishlists;
					$save_wishlist->space_id 	= $request->space_id;
					$save_wishlist->wishlist_id = $request->list_id;
					$save_wishlist->user_id 	= $user_details->id;
					$save_wishlist->save();

					return response()->json([
						'status_code' 	=> '1',
						'success_message' => 'Wishlist Added Sucessfully',

					]);
				}
				return response()->json([
					'status_code' => '0',
					'success_message' => 'Wishlist Already Selected',
				]);
			}
		}

		if ($request->list_name != '') {
			$wishlist 			= new Wishlists;
			$wishlist->name 	= urldecode($request->list_name);
			$wishlist->user_id 	= $user_details->id;
			$wishlist->privacy 	= ($request->privacy_settings == '1') ? '1' : '0';
			$wishlist->save();

			$save_wishlist 				= new SavedWishlists;
			$save_wishlist->space_id 	= $request->space_id;
			$save_wishlist->wishlist_id = $wishlist->id;
			$save_wishlist->user_id 	= $user_details->id;
			$save_wishlist->save();

			return response()->json([
				'status_code' => '1',
				'success_message' => 'Wishlist Added Sucessfully',
				'list_id' => $wishlist->id,
			]);
		}
	}

	/**
	 * Display Wishlist Resource
	 *
	 * @param  Get method inputs
	 * @return Response in Json
	 */
	public function get_wishlist(Request $request)
	{
		$user = JWTAuth::parseToken()->authenticate();
		$result = Wishlists::with('saved_wishlists.space')
			->wherehas('saved_wishlists', function($query) {
				$query->wherehas('space', function ($query) {
					$query->viewOnly();
				});
			})
			->where('user_id',$user->id)
			->get();

		if ($result->count() == 0) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> __('messages.api.wishlist_not_found'),
			]);
		}

		$list = $result->map(function ($wishlist) {
			return [
				'list_id' 		=> $wishlist->id,
				'list_name' 	=> urldecode($wishlist->name),
				'space_count'	=> $wishlist->space_count,
				'space_thumb_images' => $wishlist->all_space_image,
				'privacy'		=> $wishlist->privacy,
			];
		});

		return response()->json([
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.listed_successful'),
			'wishlist_data' 	=> $list,
		]);
	}

	/**
	 *Display particular wishlist based on wishlist id
	 *
	 * @param  Get method inputs
	 * @return Response in Json
	 */
	public function get_particular_wishlist(Request $request)
	{
		$rules = array('list_id' => 'required|exists:wishlists,id');
		$attributes = array('list_id' => 'List Id');
		$messages = array('required' => ':attribute is required.');
		$validator = Validator::make($request->all(), $rules, $messages, $attributes);

		if($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}
		$user_details = JWTAuth::parseToken()->authenticate();
		$currency_details = Currency::where('code', $user_details->currency_code)->first();

		//Check Wishlist or Not
		$wishlists = Wishlists::where('user_id', $user_details->id)->where('id', $request->list_id)->count();

		if ($wishlists == 0) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> __('messages.api.wishlist_not_found'),
			]);
		}

		$result = SavedWishlists::with('space.space_address','space.space_activities.activity_price','space.space_photos','space.space_price')
			->wherehas('space')
			->where('user_id', $user_details->id)
			->where('wishlist_id', $request->list_id)
			->get();

		$data = $result->map(function($saved_wishlist) use($user_details) {
			$space_details = $saved_wishlist->space;
			$currency_details = Currency::where('code', $user_details->currency_code)->first();
			return [
				'space_id' 			=> strval($space_details->id),
				'space_name' 		=> $space_details->name,
				'space_type_name' 	=> $space_details->space_type_name,
				'number_of_guests' 	=> $space_details->number_of_guests,
				'space_thumb_image' => $space_details->photo_name,
				'rating_value' 		=> $space_details->overall_star_rating->rating_value,
				'reviews_count' 	=> strval($space_details->reviews_count),
				'is_wishlist' 		=> $space_details->overall_star_rating->is_wishlist,
				'is_instant_book'	=> ($space_details->booking_type == 'instant_book'),
				'country_name' 		=> $space_details->space_address->country_name,
				'currency_code' 	=> $user_details->currency_code,
				'currency_symbol' 	=> $currency_details->original_symbol,
				'hourly_price' 		=> strval($space_details->activity_price->hourly),
			];
		});

		return response()->json([
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.listed_successful'),
			'wishlist_details' 	=> $data,
		]);
	}

	/**
	 * Delete wishlist
	 *
	 * @param  Get method inputs
	 * @param  Get only list_id delete all list
	 * @param  Get space_id  delete specific room
	 * @return Response in Json
	 */
	public function delete_wishlist(Request $request)
	{
		if ($request->space_id != '' && $request->list_id == '') {
			$rules 		= array('space_id' => 'required');
			$attributes = array('space_id' => 'Space Id');
		}
		elseif ($request->space_id == '' && $request->list_id != '') {
			$rules 		= array('list_id' => 'required|exists:wishlists,id');
			$attributes = array('list_id' => 'List Id');
		}
		else {
			return response()->json([
				'status_code' => '0',
				'success_message' => __('messages.api.invalid_request'),
			]);
		}

		$messages 	= array('required' => ':attribute is required.');
		$validator 	= Validator::make($request->all(), $rules, $messages, $attributes);

		if($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		$user_details = JWTAuth::parseToken()->authenticate();

		if($request->space_id != '') {
			$delete = SavedWishlists::whereSpaceId($request->space_id)->whereUserId($user_details->id);

			if ($delete->count() > 0) {
				$delete->delete();
				return response()->json([
					'status_code' => '1',
					'success_message' => 'Wishlist Deleted Successfully',
				]);
			}
			return response()->json([
				'status_code' => '0',
				'success_message' => 'Saved Wishlist Not Found',
			]);
		}

		// delete wishlist
		$delete = Wishlists::whereId($request->list_id)->whereUserId($user_details->id);

		if ($delete->count()) {
			SavedWishlists::whereWishlistId($request->list_id)->delete();
			$delete->delete();

			return response()->json([
				'status_code' => '1',
				'success_message' => 'Wishlist Deleted Successfully',
			]);
		}

		return response()->json([
			'status_code' => '0',
			'success_message' => __('messages.api.wishlist_not_found'),
		]);
	}

	/**
	 * Update wishlist
	 *
	 * @param  Get method inputs
	 * @param  Get list_id and privacy_type  they update privacy
	 * @param  Get list_id and list_name they update list name
	 * @return Response in Json
	 */
	public function edit_wishlist(Request $request)
	{
		$rules = array('list_id' => 'required|exists:wishlists,id');
		if ($request->privacy_type != '') {
			$rules['privacy_type'] = 'required|numeric|min:0|max:1';
		}
		if ($request->list_name != '') {
			$rules['list_name'] = 'required';
		}

		$attributes = array(
			'list_id' 		=> 'List Id',
			'list_name'		=> 'List Name',
			'privacy_type' 	=> 'Privacy Type',
		);
		$messages = array('required' => ':attribute is required.');

		$validator = Validator::make($request->all(), $rules, $messages, $attributes);
		if($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		$wishlist = Wishlists::find($request->list_id);

		if ($request->list_id != '' && $request->privacy_type != '') {
			$wishlist->privacy = $request->privacy_type;
		}

		if ($request->list_id != '' && $request->list_name != '') {
			$wishlist->name = urldecode($request->list_name);
		}
		$wishlist->save();

		return response()->json([
			'status_code' => '1',
			'success_message' => __('messages.api.update_success'),
		]);
	}
}