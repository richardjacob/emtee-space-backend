<?php

/**
 * Search Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Search
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Start\Helpers;
use App\Repositories\SearchSpace;
use App\Repositories\ManageSpace;
use Validator;
use JWTAuth;
use DB;

class SearchController extends Controller
{
	use SearchSpace,ManageSpace;

	protected $helper;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		$this->helper = new Helpers;
		$this->map_server_key = view()->shared('map_server_key');
	}

	/**
	 * Display a listing of the resource
	 *
	 * @return Response in Json
	 */
	public function search_filters(Request $request)
	{
		$basics_data = $this->getBasicManagementData();
		unset($basics_data['guest_accesses']);
		$setup_data = $this->getSetupManagementData();

		$data = array(
			'status_code' => '1',
			'success_message' => trans('messages.api.search_filters_listed'),
		);
		$activities	= view()->shared('header_activties');
		$data['activities']	= $activities->map(function ($activity) {
		    return $activity->only(['id', 'name','popular','image_url']);
		});

		return response()->json(array_merge($data,$basics_data,$setup_data));
	}

	/**
	 * Display a listing of the resource
	 *
	 * @return Response in Json
	 */
	function explore_space(Request $request)
	{
		$rules = array('page' => 'required|integer|min:1');
		if(isset($request->checkin) && isset($request->checkin)) {
			$rules['checkin'] = 'required|date_format:d-m-Y';
			$rules['checkout'] = 'required|date_format:d-m-Y|after:today|after_or_equal:checkin';
		}
		if(isset($request->location)) {
			$rules['location'] = 'required';
		}
		if(isset($request->guests)) {
			$rules['guests'] = 'required|integer';
		}
		if(isset($request->instant_book)) {
			$rules['instant_book'] = 'required|integer|between:0,1';
		}
		if(isset($request->min_price) || isset($request->max_price)) {
			$rules['min_price'] = 'required|numeric';
			$rules['max_price'] = 'required|numeric';
		}
		if(isset($request->amenities) && $request->amenities != '') {
			$len = strlen($request->amenities);
			$rules['amenities'] = 'required';
			
			if($len == 1) {
				$rules['amenities'] = 'required|numeric|min:1';
			}
			if($len > 1) {
				$data = explode(',', $request->amenities);
				if(in_array("", $data)) {
					return response()->json([
						'success_message' => 'Invalid Amenities Format',
						'status_code' => '0',
					]);
				}
				$rules['amenities'] = 'required|regex:/[^[0-9]+[,]?[0-9]{1,2}$]*/';
			}
		}
		if(isset($request->latitude) && isset($request->longitude)) {
			$rules['latitude'] = 'required';
			$rules['longitude'] = 'required';
		}
		if(isset($request->map_details)) {
			$rules['map_details'] = 'required';
		}

		$messages = array(
			'required' 				=> ':attribute is required.',
			'instant_book.integer' 	=> ':The instant book must be 0 or 1.',
			'instant_book.between' 	=> ':The instant book must be 0 or 1.',
			'page.integer'			=> ':The page no allowed only Integer value',
			'guests.between' 		=> ':The guests may not be greater than 16.',
		);

		$validator = Validator::make($request->all(), $rules, $messages);

		if($validator->fails()) {
          	return response()->json([
                'success_message' => $validator->messages()->first(),
                'status_code'     => '0'
            ]);
		}

		$currency_code =  $this->helper->get_user_currency_code();
        $default_min_price = currency_convert(DEFAULT_CURRENCY, $currency_code, MINIMUM_AMOUNT);
        $default_max_price = currency_convert(DEFAULT_CURRENCY, $currency_code, MAXIMUM_AMOUNT);
		session(['get_token' => $request->token]);

		if($request->page == '') {
			return response()->json([
				'success_message' => 'Undefind Page No',
				'status_code' => '0',
			]);
		}

		$space = $this->getSpaceResult($request);

		$data_result = json_decode($space);
		if ($data_result->total == 0 || empty($data_result->data)) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> trans('messages.api.no_data_found'),
			]);
		}
		$space_result = collect($data_result->data);
		$result_data = $this->mapSpaceResult($space_result);

		$result = array(
			'status_code' 		=> '1',
			'success_message' 	=> trans('messages.api.listed_successfully'),
			'total_page'		=> $data_result->last_page,
			'min_price' 		=> $default_min_price,
			'max_price' 		=> $default_max_price,
			'data' 				=> $result_data,
		);

		return response()->json($result);
	}
}