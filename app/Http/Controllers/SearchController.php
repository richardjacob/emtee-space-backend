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

namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Space;
use App\Models\SpaceCalendar;
use App\Models\KindOfSpace;
use App\Models\SubActivity;
use App\Models\Amenities;
use App\Models\Services;
use App\Models\SpecialFeature;
use App\Models\SpaceRule;
use App\Models\Style;
use App\Models\Currency;
use App\Models\ReservationTimes;
use App\Models\SpaceAvailability;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Repositories\SearchSpace;

class SearchController extends Controller
{
    use SearchSpace;
    /**
     * Constructor to Set instance as Global variable
     *
     */
    public function __construct()
    {
        $this->map_server_key = view()->shared('map_server_key');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $previous_currency  = session('search_currency');
        $deleted_currency   = session('deleted_currency');
        $currency           = session('currency');
        $full_address       = $request->location;
        $address            = str_replace(" ", "+", "$full_address");
        $geocode            = file_get_contents_curl('https://maps.google.com/maps/api/geocode/json?key='.$this->map_server_key.'&address='.$address.'&sensor=false');
        $json               = json_decode($geocode);

        $data['currency_symbol'] = Currency::first()->symbol;
        $data['amenities']       = Amenities::active()->get();
        $data['services']        = Services::active()->get();
        $data['space_rules']     = SpaceRule::active()->get();
        $data['special_features']= SpecialFeature::active()->get();
        $data['space_styles']    = Style::active()->get();

        $data['lat']        = 0;
        $data['long']       = 0;
        $data['viewport']   = '';

        if(@$json->{'results'}) {
            $data['lat']  = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $data['long'] = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
            $data['viewport'] = $json->{'results'}[0]->{'geometry'}->{'viewport'};
        }

        $php_date_format    = $request->php_date_format;
        $data['checkin']    = '';
        $data['checkout']    = '';
        $data['start_time'] = $request->start_time;
        $data['end_time']   = $request->end_time;

        if(!empty($request->checkin) && custom_strtotime($request->checkin, $php_date_format)) {
            $data['checkin'] = date(PHP_DATE_FORMAT,custom_strtotime($request->checkin, $php_date_format));
        }
        if(!empty($request->checkout) && custom_strtotime($request->checkout, $php_date_format)) {
            $data['checkout'] = date(PHP_DATE_FORMAT,custom_strtotime($request->checkout, $php_date_format));
        }

        $data['location']           = $request->location;

        $data['guest']              = $request->guests=='' ? 1 : $request->guests;
        $data['min_price']          = $request->min_price;
        $data['max_price']          = $request->max_price;
        $data['instant_book']       = $request->instant_book ? $request->instant_book : 0;

        $data['selected_activity']      = $request->activity_type;
        $data['space_type_selected']    = array();
        if($request->space_type != '') {
            $data['space_type_selected']    = explode(',', $request->space_type);
        }
        $data['amenities_selected']         = explode(',', $request->amenities);
        $data['services_selected']          = explode(',', $request->services);
        $data['space_rules_selected']       = explode(',', $request->space_rules);
        $data['special_features_selected']  = explode(',', $request->special_feature);
        $data['styles_selected']            = explode(',', $request->space_style);
        $data['default_min_price'] = currency_convert(DEFAULT_CURRENCY, $currency, MINIMUM_AMOUNT);
        $data['default_max_price'] = currency_convert(DEFAULT_CURRENCY, $currency, MAXIMUM_AMOUNT);

        if(!$data['min_price'] || $deleted_currency) {
            $data['min_price'] = $data['default_min_price'];
            $data['max_price'] = $data['default_max_price'];
        }
        else if($previous_currency) {
            $data['min_price'] = currency_convert($previous_currency, $currency, $data['min_price']); 
            $data['max_price'] = currency_convert($previous_currency, $currency, $data['max_price']); 
        }
        else {
            $data['min_price'] = currency_convert('', $currency, $data['min_price']);
            $data['max_price'] = currency_convert('', $currency, $data['max_price']);
        }

        session()->forget('search_currency');
        return view('search.search', $data);
    }

    /**
     * Ajax Search Result
     *
     * @param array $request Input values
     * @return json Search results
     */
    public function searchResult(Request $request)
    {
        $space = $this->getSpaceResult($request);
        return response($space);
    }
}