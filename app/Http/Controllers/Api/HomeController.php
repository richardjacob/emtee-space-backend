<?php

/**
 * Home Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Home
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Start\Helpers;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Reservation;
use App\Models\SavedWishlists;
use App\Models\Activity;
use App\Models\User;
use JWTAuth;
use Validator;

class HomeController extends Controller
{
	/**
     * Constructor
     *
     */
	public function __construct()
	{
		$this->helper = new Helpers;
	}

	/**
     * List all Activities ordered by popular first
     *
     * @return json response
     */
    public function activities_list()
    {
    	$activities = Activity::activeOnly()->orderBy('popular')->get()->pluckMultiple('id','name','image_url');

        return response()->json([
            'status_code'   	=> '1',
            'success_message'   => __('messages.api.listed_successful'),
            'activities' 		=> $activities
        ]);
    }

	/**
	 * Display Currency List
	 *
	 * @param  Get method inputs
	 * @return Response in Json
	 */
	public function currency_list(Request $request)
	{
		//Get Currency Details
		$currency_details = Currency::where('status', 'Active')->select('code', 'symbol')->orderBy('code', 'asc')->get()->toArray();

		//Store Currency Code and Symbol On Array Format
		foreach($currency_details as $currency) {
			$currency_list[] = array(
				'code' => $currency['code'],
				'symbol' => $currency['original_symbol'],
			);
		}
		if(!empty($currency_list)) {
			return response()->json([
				'status_code' => '1',
				'success_message' => 'Currency Details Listed Successfully',
				'currency_list' => $currency_list,
			]);
		}
		return response()->json([
			'status_code' => '0',
			'success_message' => 'Currency Details Not Found',
		]);
	}

	/**
	 * Display Country List
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */
	public function country_list(Request $request)
	{
		$data = Country::select(
			'id as country_id',
			'long_name as country_name',
			'short_name as country_code'
		)->get();
		return response()->json([
			'status_code' => '1',
			'success_message' => 'Country Listed Successfully',
			'country_list' => $data,
		]);
	}

	/**
	 * Display Country List
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */
	public function stripe_supported_country_list(Request $request)
	{
		$data = Country::select(
			'id as country_id',
			'long_name as country_name',
			'short_name as country_code'
		)->where('stripe_country','Yes')->get();

		$data = $data->map(function($data){
			return [
				'country_id' => $data->country_id,
				'country_name' => $data->country_name,
				'country_code' => $data->country_code,
				'currency_code'	=> $this->helper->getStripeCurrency($data->country_code),
			];
		});
		
		return response()->json([
			'status_code' => '1',
			'success_message' => 'Country Listed Successfully',
			'country_list' => $data,
		]);
	}
}