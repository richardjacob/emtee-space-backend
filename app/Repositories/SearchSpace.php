<?php

/**
 * Search Space
 *
 * @package     Makent Space
 * @subpackage  Search Space
 * @category    Repository
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Repositories;

use Illuminate\Support\Arr;
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
use DB;
use JWTAuth;
use Validator;

trait SearchSpace
{
    /**
     * Merge Arrays to Single Array and return unique Array
     *
     * @return Array $unique_arr
     */
    protected function getUniqueMergeArray()
    {
        $inputs = func_get_args();
        $inputs = Arr::wrap($inputs);
        if(count($inputs) == 0) {
            return array();
        }
        $inputs     = Arr::flatten($inputs);
        $unique_arr = array_unique($inputs);
        return $unique_arr;
    }

    protected function getNotAvailableSpace($checkin,$checkin_day)
    {
        $date_based_ids = array();
        $not_avail_cal = SpaceCalendar::dateNotAvailable($checkin)->distinct()->pluck('space_id')->toArray();
        $not_avail_avail = SpaceAvailability::with('availability_times')->where('day', $checkin_day)->onlyNotAvailable()->distinct()->pluck('space_id')->toArray();

        $date_based_ids = $this->getUniqueMergeArray($not_avail_cal,$not_avail_avail);

        return $this->getUniqueMergeArray($date_based_ids);
    }

    protected function getSpaceResult($request)
    {
        $full_address   = $request->location;
        $map_details    = $request->map_details;
        $checkin        = $request->checkin;
        $checkout       = $request->checkout;
        $start_time     = $request->start_time;
        $end_time       = $request->end_time;
        $guest          = $request->guest;
        $min_price      = $request->min_price;
        $max_price      = $request->max_price;
        $instant_book   = $request->instant_book;
        $activity_type  = $request->activity_type;
        $amenities      = ($request->amenities == '') ? [] : $request->amenities;
        $services       = ($request->services == '') ? [] : $request->services;
        $space_rules    = ($request->space_rules == '') ? [] : $request->space_rules;
        $special_feature= ($request->special_feature == '') ? [] : $request->special_feature;
        $space_style    = ($request->space_style == '') ? [] : $request->space_style;
        $space_type     = ($request->space_type == '') ? [] : $request->space_type;
        $country_code   = '';
        $checkin_day   = '';
        
        if(!is_array($amenities)) {
            $amenities = explode(',', $amenities);
        }
        if(!is_array($services)) {
            $services = explode(',', $services);
        }
        if(!is_array($space_rules)) {
            $space_rules = explode(',', $space_rules);
        }
        if(!is_array($special_feature)) {
            $special_feature = explode(',', $special_feature);
        }
        if(!is_array($space_style)) {
            $space_style = explode(',', $space_style);
        }
        if(!is_array($space_type)) {
            $space_type = explode(',', $space_type);
        }

        $check_date = ($checkin != '' && $checkout !='');
        $single_day = ($checkin != '' && $checkin == $checkout);
        $check_single_time = ($single_day && $start_time != '' && $end_time != '');
        $check_multiple_time = ($check_date && $start_time != '' && $end_time != '');

        if(!$min_price) {
            $min_price = currency_convert(DEFAULT_CURRENCY, '', 0);
            $max_price = currency_convert(DEFAULT_CURRENCY, '', MAXIMUM_AMOUNT);
        }

        $max_price_check = currency_convert('', DEFAULT_CURRENCY, $max_price);

        $data['viewport'] = '';
        $address      = str_replace([" ","%2C"], ["+",","], $full_address);
        $geocode      = file_get_contents_curl('https://maps.google.com/maps/api/geocode/json?key='.$this->map_server_key.'&address='.$address.'&sensor=false&libraries=places');
        $json         = json_decode($geocode);

        // Get Location Details based on Map
        if($map_details != '') {
            $map_detail =   explode('~', $map_details);
            $zoom       =   $map_detail[0];
            $bounds     =   $map_detail[1];
            $minLat     =   $map_detail[2];
            $minLong    =   $map_detail[3];
            $maxLat     =   $map_detail[4];
            $maxLong    =   $map_detail[5];
            $cLat       =   $map_detail[6]; 
            $cLong      =   $map_detail[7];

            if($minLong > $maxLong) {
                if($maxLong > 0) {
                    $maxLong = $minLong;
                    $minLong = "-180"; 
                }
                else {
                    $maxLong = "180";
                }
            }
        }
        else {
            $data['lat']    = 0;
            $data['long']   = 0;
            $minLat         = -1000;
            $maxLat         = 1000;
            $minLong        = -1000;
            $maxLong        = 1000;

            if(@$json->{'results'}) {
                foreach ($json->{'results'}[0]->{'address_components'} as $value) {
                   if($value->types[0] == 'country') {
                        $country_code = $value->short_name;
                   }
                }

                if($json->{'results'}[0]->{'types'}[0] == 'country') {
                    $country_code = $json->{'results'}[0]->{'address_components'}[0]->{'short_name'};
                }
                $data['viewport'] = $json->{'results'}[0]->{'geometry'}->{'viewport'};

                $minLat = $json->{'results'}[0]->{'geometry'}->{'viewport'}->{'southwest'}->{'lat'};
                $maxLat = $json->{'results'}[0]->{'geometry'}->{'viewport'}->{'northeast'}->{'lat'};
                $minLong = $json->{'results'}[0]->{'geometry'}->{'viewport'}->{'southwest'}->{'lng'};
                $maxLong = $json->{'results'}[0]->{'geometry'}->{'viewport'}->{'northeast'}->{'lng'};
            }
        }

        $minLat     =   $minLat +0;
        $minLong    =   $minLong+0;
        $maxLat     =   $maxLat +0;
        $maxLong    =   $maxLong+0;

        // Date And Time Filter Starts
        $not_avail_space_ids = [];
        $reserved_ids = [];

        if($checkin != '') {
            $checkin_obj = getDateObject($checkin);
            $checkin     = $checkin_obj->format('Y-m-d');
            $checkin_day = $checkin_obj->dayOfWeek;
        }
        if($checkout != '') {
            $checkout_obj = getDateObject($checkout);
            $checkout     = $checkout_obj->format('Y-m-d');
            $checkout_day = $checkout_obj->dayOfWeek;
        }

        // Check Both date and time are not avilable
        if($single_day) {
            $not_avail_space_ids = $this->getNotAvailableSpace($checkin,$checkin_day);
        }
        else if($check_multiple_time) {
            $s_checkin    = strtotime($checkin.' '.$start_time);
            $s_checkout   = strtotime($checkout.' '.$end_time);

            $not_avail_space_ids = SpaceCalendar::onlyNotAvailable()
                ->validateDateTime($s_checkin, $s_checkout)
                ->distinct()->pluck('space_id')->toArray();

            $reserved_ids = ReservationTimes::onlyNotAvailable()
                ->validateDateTime($s_checkin, $s_checkout)
                ->distinct()->pluck('space_id')->toArray();
        }
        else if($check_date) {
            $not_avail_space_ids = SpaceCalendar::dateNotAvailable($checkin, $checkout)->distinct()->pluck('space_id')->toArray();
            $reserved_ids = ReservationTimes::dateNotAvailable($checkin, $checkout)->distinct()->pluck('space_id')->toArray();
        }

        $not_avail_space_ids = $this->getUniqueMergeArray($not_avail_space_ids,$reserved_ids);

        // Basic Filters Start
        $space = Space::listed()->verified()->with(['space_address','space_photos','space_activities.activity_price','users.profile_picture','activity_price.currency',
            'saved_wishlists' => function($query) {
                $query->where('user_id', @auth()->id());
            }])
            ->whereHas('space_address', function($query) use($request,$minLat, $maxLat, $minLong, $maxLong) {

                if(isset($request->latitude) && isset($request->longitude)){               
                        $query->select(DB::raw('*, ( 3959 * acos( cos( radians('.$request->latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$request->longitude.') ) + sin( radians('.$request->latitude.') ) * sin( radians( latitude ) ) ) ) as distance'))->having('distance', '<=', 5);
                       
                }else{
                    $query->whereRaw("latitude between $minLat and $maxLat and longitude between $minLong and $maxLong");                        
                    }

               
            })
            ->whereHas('users', function($query) {
                $query->whereStatus('active');
            })
            ->whereNotIn('id', $not_avail_space_ids);

        if($check_single_time) {
            $space = $space->whereHas('space_availabilities', function($query) use($checkin_day, $start_time, $end_time) {
                $query->availableWithTime($checkin_day,$start_time, $end_time);
            });
        }
        else if($check_multiple_time) {

        }

        if($country_code != '') {
            $space = $space->whereHas('space_address', function($query) use($country_code) {
                $query->where('country',$country_code);
            });
        }

        if(count($space_type)) {
            $space = $space->whereIn('space_type', $space_type);
        }

        if($activity_type != '') {
            $space = $space->with(['space_activities' => function($query) use($activity_type) {
                $query->with('activity_price')->whereRaw('find_in_set('.$activity_type.', activities)');
            }])
            ->whereHas('space_activities', function($query) use($activity_type) {
                $query->with('activity_price')->whereRaw('find_in_set('.$activity_type.', activities)');
            });
        }

        $space_where['space.number_of_guests']  = $guest;

        if($instant_book == 1) {
            $space_where['space.booking_type'] = 'instant_book';
        }

        if($space_where) {
            foreach($space_where as $row => $value) {
                $value = ($value == '') ? 0 : $value;
                $operator = '=';
                if($row == 'space.number_of_guests') {
                    $operator = '>=';
                }

                $space = $space->where($row, $operator, $value);
            }
        }

        if(count($amenities)) {
            foreach($amenities as $amenities_value) {
                $space = $space->whereRaw('find_in_set('.$amenities_value.', amenities)');
            }
        }
        if(count($services)) {
            foreach($services as $services_value) {
                $space = $space->whereRaw('find_in_set('.$services_value.', services)');
            }
        }
        if(count($space_rules)) {
            foreach($space_rules as $space_rules_value) {
                $space = $space->whereRaw('find_in_set('.$space_rules_value.', space_rules)');
            }
        }
        if(count($special_feature)) {
            foreach($special_feature as $special_features_value) {
                $space = $space->whereRaw('find_in_set('.$special_features_value.', special_feature)');
            }
        }
        if(count($space_style)) {
            foreach($space_style as $style) {
                $space = $space->whereRaw('find_in_set('.$style.', space_style)');
            }
        }
        // Basic Filters End

        // Price Filter Start
        $currency_rate = Currency::where('code', Currency::first()->session_code)->first()->rate;
        if($activity_type != '') {
            $activity_type_id = \DB::Table('activities')->where('id', $activity_type)->first()->activity_type_id;
            $space = $space->with(['space_activities' => function($query) use($activity_type_id, $min_price, $max_price, $currency_rate) {
                $query->where('activity_type_id',$activity_type_id)->with(['activity_price' => function($query) use($min_price, $max_price, $currency_rate) {
                    $query->join('currency', 'currency.code', '=', 'activities_price.currency_code')->whereRaw('ROUND(((hourly / currency.rate) * '.$currency_rate.')) >= '.$min_price.' and ROUND(((hourly / currency.rate) * '.$currency_rate.')) <= '.$max_price);
                }])
                ->whereHas('activity_price' , function($query) use($min_price, $max_price, $currency_rate) {
                    $query->join('currency', 'currency.code', '=', 'activities_price.currency_code')->whereRaw('ROUND(((hourly / currency.rate) * '.$currency_rate.')) >= '.$min_price.' and ROUND(((hourly / currency.rate) * '.$currency_rate.')) <= '.$max_price);
                });
            }])
            ->whereHas('space_activities', function($query) use($activity_type_id, $min_price, $max_price, $currency_rate) {
                $query->where('activity_type_id',$activity_type_id)->with(['activity_price' => function($query) use($min_price, $max_price, $currency_rate) {
                    $query->join('currency', 'currency.code', '=', 'activities_price.currency_code')->whereRaw('ROUND(((hourly / currency.rate) * '.$currency_rate.')) >= '.$min_price.' and ROUND(((hourly / currency.rate) * '.$currency_rate.')) <= '.$max_price);
                }])
                ->whereHas('activity_price' , function($query) use($min_price, $max_price, $currency_rate) {
                    $query->join('currency', 'currency.code', '=', 'activities_price.currency_code')->whereRaw('ROUND(((hourly / currency.rate) * '.$currency_rate.')) >= '.$min_price.' and ROUND(((hourly / currency.rate) * '.$currency_rate.')) <= '.$max_price);
                });
            });
        }
        // Price Filter End

        $space = $space->orderByRaw('RAND(1234)')->paginate(20)->toJson();

        return $space;
    }
}