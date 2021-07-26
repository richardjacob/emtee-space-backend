<?php

/**
 * Space Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Space
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Models\KindOfSpace;
use App\Models\GuestAccess;
use App\Models\Amenities;
use App\Models\Style;
use App\Models\Services;
use App\Models\SpaceRule;
use App\Models\SpecialFeature;
use App\Models\Space;
use App\Models\SpaceDescription;
use App\Models\SpaceStepsStatus;
use App\Models\SpacePhotos;
use App\Models\AvailabilityTimes;
use App\Models\ActivityPrice;
use App\Models\ActivityType;
use App\Models\SpaceCalendar;
use App\Models\ReservationTimes;
use App\Models\SpaceAvailability;
use App\Models\User;
use App\Models\Currency;
use App\Models\Reviews;
use App\Repositories\ManageSpace;
use Carbon\Carbon;
use Validator;
use JWTAuth;
use DB;
use DateTime;
use DateTimeZone;

class SpaceController extends Controller
{
	use ManageSpace;

	protected $space_status;

	public function __construct()
	{
		$this->map_server_key = view()->shared('map_server_key');
	}

	/**
	 * Validate Given User to modify Space or Not
	 *
	 * @param Integer $space_id
	 * @param Integer $user_id
	 *
	 * @return Boolean
	 */
	protected function validateSpaceAndUser($space_id,$user_id)
	{
		$space_details = Space::where('user_id',$user_id)->where('id',$space_id)->count();
		return ($space_details == 0);
	}

	/**
	 * Format Given Availability Data
	 *
	 * @param Json $avail_data
	 *
	 * @return Response Json
	 */
	protected function formatAvailabilityData($avail_data)
	{
		$return_data = array('status' => false, 'status_message' => __('messages.api.invalid_availability_time'));
		if(!isset($avail_data)) {
			$return_data['status_message'] = __('messages.api.invalid_request');
   			return $return_data;
   		}
		
		// Validate Given Times

		$return_data['status'] 	= true;
		$return_data['data'] 	= $avail_data;
		return $return_data;
	}

	/**
	 * Format Activity Price Data Array
	 *
	 * @param Json $activity_price
	 *
	 * @return Response Object
	 */
	protected function formatActivityPriceData($activity_price)
	{
		$return_data = array('status' => false, 'status_message' => __('messages.api.invalid_activity_price'));
		$activity_price = json_decode($activity_price,true);

		$return_data['status'] = true;
		$return_data['data'] = $activity_price;

		return arrayToObject($return_data);
	}

    /**
	 * Get Basics Data
	 *
	 * @param Collection $space_details
	 *
	 * @return Array $basics_data & $address_data
	 */
	protected function getBasicData($space_details)
	{
		$basics_data = $space_details->only(['number_of_rooms', 'number_of_restrooms', 'number_of_guests', 'floor_number', 'sq_ft','fully_furnished','no_of_workstations','shared_or_private','renting_space_firsttime','size_type', 'guest_access', 'guest_access_other', 'amenities', 'services', 'services_extra']);

		$space_type = KindOfSpace::where('id', $space_details->space_type)->first();
		$basics_data['space_type'] = ['id' => ($space_type->status == 'Active') ? $space_type->id : 0, 'name' => $space_type->name];

		$address_data = $space_details->space_address->only(['address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code', 'latitude', 'longitude', 'guidance']);

		$all_status = $space_details->steps_status;
        $basic_step_data = $all_status->basics;
		$step_data = [
			'status' => ($basic_step_data['total_steps'] - $basic_step_data['completed_steps'] == 0) ? 'completed' : 'pending',
			'remaining_steps' => $basic_step_data['remaining_steps'],
		];

		return array_merge($step_data,$basics_data,$address_data);
	}

	/**
	 * Get Setup Data
	 *
	 * @param Collection $space_details
	 *
	 * @return Array $ready_to_host_data
	 */
	protected function getSetupData($space_details)
	{
		$setup_data = $space_details->only(['space_style', 'special_feature', 'space_rules', 'name', 'summary']);
		$space_description = $space_details->space_description->only(['space','access','interaction','notes','house_rules','neighborhood_overview','transit']);
		$setup_data = array_merge($setup_data,$space_description);

		$space_photos = $space_details->space_photos->map(function($photo) {
			return [
				'id' => $photo->id,
				'highlights' => $photo->highlights,
				'image_url' => $photo->name,
			];
		});
		$setup_data['space_photos'] = $space_photos->toArray();

		$all_status = $space_details->steps_status;
        $setup_step_data = $all_status->setup;
		$step_data = [
			'status' => ($setup_step_data['total_steps'] - $setup_step_data['completed_steps'] == 0) ? 'completed' : 'pending',
			'remaining_steps' => $setup_step_data['remaining_steps'],
		];

		return array_merge($step_data,$setup_data);
	}

	/**
	 * Get Ready to Host Data
	 *
	 * @param Collection $space_details
	 *
	 * @return Array $ready_to_host_data
	 */
	protected function getReadyToHostData($space_details)
	{
		$ready_to_host_data = $space_details->only(['booking_type']);

		$ready_to_host_data['cancellation_policy'] = array(
			array( 'key' => 'Flexible','title' => 'Flexible: Full refund 1 day prior to arrival, except fees','is_selected' => ($space_details->cancellation_policy == 'Flexible')),
			array( 'key' => 'Moderate','title' => 'Moderate: Full refund 5 days prior to arrival, except fees','is_selected' => ($space_details->cancellation_policy == 'Moderate')),
			array( 'key' => 'Strict','title' => 'Strict: 50% refund up until 1 week prior to arrival, except fees','is_selected' => ($space_details->cancellation_policy == 'Strict')),
		);

		if($space_details->activity_price) {
			$ready_to_host_data['currency_code'] = $space_details->activity_price->getOriginal('currency_code');
			$ready_to_host_data['minimum_amount'] = getMinimumAmount($ready_to_host_data['currency_code']);
		}
		else {
			$space_details->load('users');
			$host_user = $space_details->users;
			$ready_to_host_data['currency_code'] = $host_user->currency_code;
			$ready_to_host_data['minimum_amount'] = getMinimumAmount($ready_to_host_data['currency_code']);
		}
		if(optional($space_details->space_price)->security == null) {
			$ready_to_host_data['security'] = '';
		}
		else {
			$ready_to_host_data['security'] = strval($space_details->space_price->getOriginal('security'));
		}

		$space_activities               = $space_details->space_activities;

		$activity_type_ids              = $space_activities->pluck('activity_type_id')->implode(',');
        $activity_ids                   = $space_activities->pluck('activities')->implode(',');
        $sub_activity_ids               = $space_activities->where('sub_activities','<>',null)->pluck('sub_activities')->implode(',');
        $activity_data['prev_activity_type']     = explode(',', $activity_type_ids);
        $activity_data['prev_activities']        = explode(',', $activity_ids);
        $activity_data['prev_sub_activities']    = explode(',', $sub_activity_ids);
        $activity_data = arrayToObject($activity_data);

		$activity_types = ActivityType::with('activities.sub_activities')->withActivitiesOnly()->activeOnly()->get();

		$activities = $activity_types->map(function($activity_type) use($activity_data) {
			$activities = $activity_type->activities->map(function($activity) use($activity_data) {
				
				$sub_activities = $activity->sub_activities->map(function($sub_activity) use($activity_data) {
					return [
						'id'	=> $sub_activity->id,
						'name'	=> $sub_activity->name,
						'is_selected' => in_array($sub_activity->id, $activity_data->prev_sub_activities),
					];
				});

				return [
					'id'	=> $activity->id,
					'name'	=> $activity->name,
					'is_selected' => in_array($activity->id, $activity_data->prev_activities),
					'sub_activities' => $sub_activities,
				];
			});

			return [
				'id' 		=> $activity_type->id,
				'name' 		=> $activity_type->name,
				'image_url' => $activity_type->image_url,
				'is_selected' => in_array($activity_type->id, $activity_data->prev_activity_type),
				'activities' => $activities,
			];
		});

		$activity_price 	= $this->getActivityPriceData($space_details);
		$availability_times = $this->getAvailabilityTimesData($space_details);
		$calendar_data 		= $this->getSpaceCalendarData($space_details->id);

		$ready_to_host_data['activity_types'] = $activities;
		$ready_to_host_data['activity_price'] = $activity_price;
		$ready_to_host_data['availability_times'] = $availability_times;
		$ready_to_host_data['calendar_data'] = $calendar_data;

		$all_status = $space_details->steps_status;
        $ready_host_step_data = $all_status->ready_to_host;
		$step_data = [
			'status' => ($ready_host_step_data['total_steps'] - $ready_host_step_data['completed_steps'] == 0) ? 'completed' : 'pending',
			'remaining_steps' => $ready_host_step_data['remaining_steps'],
		];

		return array_merge($step_data,$ready_to_host_data);
	}

	/**
	 * Get Activity Price Data
	 *
	 * @param Collection $space_details
	 *
	 * @return Array $activity_price
	 */
	protected function getActivityPriceData($space_details)
	{
		$space_activities = $space_details->space_activities;
		$activity_price = $space_activities->map(function($activity) {
			$activity_price = $activity->activity_price;
			return [
				'activity_name' => $activity->activity_type_name,
				'image_url' 	=> $activity->activity_type->image_url,
				'activity_id' 	=> $activity->id,
				'currency_code' => $activity_price->getOriginal('currency_code'),
				'currency_symbol' => $activity_price->original_currency_symbol,
				'hourly' 		=> $activity_price->getOriginal('hourly'),
				'min_hours'		=> $activity_price->getOriginal('min_hours'),
				'full_day' 		=> $activity_price->getOriginal('full_day'),
				'weekly' 		=> $activity_price->getOriginal('weekly'),
				'monthly' 		=> $activity_price->getOriginal('monthly'),
			];
		});
		return $activity_price;
	}

	/**
	 * Get Availability Times Data
	 *
	 * @param Collection $space_details
	 *
	 * @return Array $availability_times
	 */
	protected function getAvailabilityTimesData($space_details)
	{
		$space_availabilities = $space_details->space_availabilities;
		$availability_times = $space_availabilities->map(function($availability) {
			$avail_times = $availability->availability_times->pluckMultiple('id','start_time','end_time');
			return [
				'id' 			=> $availability->id,
				'day' 			=> $availability->day,
				'status' 		=> $availability->status,
				'availability_times'=> $avail_times,
			];
		});
		return $availability_times;
	}

	/**
     * Check Given Time is Blocked or Not
     *
     * @param  Int $space_id
     * @param  Object $availability_data
     * @param  Date $date_obj  Instance of Carbon
     *
     * @return Array
     */
    protected function checkIsBlocked($space_id,$date,$time,$tz = '')
    {
    	$current_day	= Carbon::now();
		if ($tz != '') {
			$current_day = Carbon::now($tz);
		}

		/*$user_current_time = time();
		if (array_key_exists("user_time_zone",$booking_data)) {
			$user_current_time = new \DateTime("now", new \DateTimeZone($booking_data['user_time_zone']));
			$user_current_time = strtotime($user_current_time->format('Y-m-d H:i:s'));
		}*/
		
		$check_date		= $date->format('Y-m-d');
		$start_days = array('Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6);
		$time_stamp=strtotime($date);
		$dayOfWeek = date('l',$time_stamp);		
		if($check_date == $current_day->format('Y-m-d') && $current_day->format('H:i:s') > $time) {
			return ['status' => 'hide'];
		}

    	$space_bookings = ReservationTimes::where('space_id', $space_id)->afterToday()->onlyNotAvailable();
    	$space_calendar = SpaceCalendar::where('space_id', $space_id)->afterToday()->onlyNotAvailable();
    	$space_calendar_avail= SpaceAvailability::with('availability_times')->where('space_id', $space_id)->whereIn('status',['Open','Closed'])->where('day', $start_days[$dayOfWeek])->get();
    	$booking_count = $space_bookings->validateSingleDateTime($check_date,$time)->count();
    	$calendar_count = $space_calendar->validateSingleDateTime($check_date,$time)->count();    	

    	if($space_calendar_avail->first()){
    	foreach ($space_calendar_avail[0]['availability_times'] as $key => $value) {    		
    		$val=AvailabilityTimes::where('space_availability_id',$value->space_availability_id);
    		$query=$val->whereRaw('? between start_time and end_time', [$time]);
    		if($query->first())
    		$avail_total_count =0;    		
    	}} 
    	else
    		$avail_total_count=1;
    	if($booking_count > 0 || $calendar_count > 0|| $avail_total_count > 0)
    	$is_blocked ='true';
    	else    	
    	$is_blocked ='false';
    	return ['status' => $is_blocked];
    }

	/**
     * Get All Available times in given day
     *
     * @param  Int $space_id
     * @param  Object $availability_data
     * @param  Date $date_obj  Instance of Carbon
     *
     * @return Array
     */
    protected function getAvailabilityTimes($space_id,$availability_data,$date_obj,$time_zone)
    {	
		$times_array    = array_keys(view()->shared('times_array'));
		$time_format 	= view()->shared('time_format');
		$day_num 		= $date_obj->format('w');
		$space_details  = Space::with('users')->find($space_id);
		$user_tz 		= $space_details->users->timezone;
		
		if(in_array($day_num, $availability_data['all_available_days'])) {
			foreach ($times_array as $time) {
			
				$host_time = new DateTime($time, new DateTimeZone($user_tz));
				$host_time->setTimeZone(new DateTimeZone($time_zone));
				$time_convert= $host_time->format($time_format);

				$compare_timez = new DateTimeZone($time_zone);	
				$check_time = new DateTime();
		        $check_time->setTimezone($compare_timez );
		        $timeobj = $check_time->format($time_format);
				
		        $repeat_time1 = DateTime::createFromFormat('H:i a', $time_convert);
				$repeat_time2 = DateTime::createFromFormat('H:i a', $timeobj);	
					$check_date=date_format($date_obj,"Y/m/d");
					$now_date=date("Y/m/d");
					if($check_date==$now_date)
					{						
						if($repeat_time1<=$repeat_time2)
						  continue;
					}
				$is_blocked = $this->checkIsBlocked($space_id,$date_obj,$time,$user_tz);
				if($is_blocked['status'] != 'hide') {
					$time_formatted = $time_convert;
					$time_data[] = array(
						'time' 		=> $time_formatted,
						'is_blocked'=> $is_blocked['status'],
					);
				}
			}

		}
		else {
			$availabilities_data = $availability_data['open_availabilities'];
			$availabilities = $availabilities_data->where('day',$day_num)->first();
			$times = $availabilities->availability_times->map(function($avail_time) use (&$time_data,$time_format, $date_obj, $space_id,$user_tz,$time_zone) {
				$avail_times = $avail_time->available_times;


				foreach ($avail_times as $time) {

					$host_time = new DateTime($time, new DateTimeZone($user_tz));
					$host_time->setTimeZone(new DateTimeZone($time_zone));
					$time_convert= $host_time->format($time_format);

					$compare_timez = new DateTimeZone($time_zone);	
					$check_time = new DateTime();
			        $check_time->setTimezone($compare_timez );
			        $timeobj = $check_time->format($time_format);
					
			        $repeat_time1 = DateTime::createFromFormat('H:i a', $time_convert);
					$repeat_time2 = DateTime::createFromFormat('H:i a', $timeobj);	
					$check_date=date_format($date_obj,"Y/m/d");
					$now_date=date("Y/m/d");
					if($check_date==$now_date)
					{
					if($repeat_time1<=$repeat_time2)
					  continue;
					}
					
					$is_blocked = $this->checkIsBlocked($space_id,$date_obj,$time,$user_tz);
					if($is_blocked['status'] != 'hide') {
						$time_formatted = $time_convert;
						$time_data[] = array(
							'time' 		=> $time_formatted,
							'is_blocked'=> $is_blocked['status'],
						);
					}
				}
			});
		}
		return $time_data;
	}

	/**
	 * Get Space Calendar Data
	 *
	 * @param Int $space_id
	 *
	 * @return Array $calendar_data
	 */
	protected function getSpaceCalendarData($space_id)
	{
		$calendar_data = array();

        $times_array    = array_keys(view()->shared('times_array'));
        $not_available_times = array("0" => array(),"1" => array(),"2" => array(),"3" => array(),"4" => array(),"5" => array(),"6" => array());

        $open_availabilities = SpaceAvailability::with('availability_times')->where('space_id', $space_id)->whereIn('status',['Open','Closed'])->get();

        foreach ($open_availabilities as $availabilities) {
        	$day_num = strval($availabilities->day);
        	if($availabilities->status == 'Closed') {
        		$not_available = $times_array;
        	}
        	else {
        		$temp_times = array();
	            foreach ($availabilities->availability_times as $availability_time) {
	            	$avail_times = $availability_time->available_times;
	            	array_pop($avail_times);
	                $temp_times = array_merge($temp_times, Arr::wrap($avail_times));
	            }
	            $not_available = array_values(array_diff($times_array,$temp_times));        		
        	}
            
            $not_available_times[$day_num] = $not_available;
        }
        $not_available_times = (object)$not_available_times;

        $blocked_times  = array();

        $space_calendar = SpaceCalendar::where('space_id', $space_id)->onlyNotAvailable()->get();
        $blocked_times = $space_calendar->map(function($calendar) {
        	return [
        		'start_date' => $calendar->start_date,
        		'end_date' 	 => $calendar->end_date,
        		'start_time' => $calendar->start_time,
        		'end_time' 	 => $calendar->end_time,
        		'notes' 	 => $calendar->notes,
        		'source'	 => $calendar->source,
        	];
        });

        $space_calendar = SpaceCalendar::where('space_id', $space_id)->onlyAvailable()->get();
        $available_times = $space_calendar->map(function($calendar) {
        	return [
        		'start_date' => $calendar->start_date,
        		'end_date' 	 => $calendar->end_date,
        		'start_time' => $calendar->start_time,
        		'end_time' 	 => $calendar->end_time,
        		'notes' 	 => $calendar->notes,
        		'source'	 => $calendar->source,
        	];
        });

        $space_bookings = ReservationTimes::where('space_id', $space_id)->onlyNotAvailable()->get();
        $r_blocked_times = $space_bookings->map(function($booking) {
        	return [
        		'start_date' => $booking->start_date,
        		'end_date' 	 => $booking->end_date,
        		'start_time' => $booking->start_time,
        		'end_time' 	 => $booking->end_time,
        		'source'	 => 'Reservation',
        	];
        });
        if($blocked_times->count() > 0) {
        	$blocked_times = $blocked_times->merge($r_blocked_times);
        }
        else {
        	$blocked_times = $r_blocked_times;
        }
        
        $calendar_data['not_available_times'] 	= $not_available_times;
        $calendar_data['blocked_times'] 		= $blocked_times;
        $calendar_data['available_times'] 		= $available_times;

		return $calendar_data;
	}

	/**
	 * Get Review Detail of given space
	 *
	 * @param Int $space_id
	 * @param String $type guest|host
	 *
	 * @return Array $review_data
	 */
	protected function getReviewData($space_id, $type)
	{
		$reviews_details = Reviews::with('users.profile_picture')->where('space_id', $space_id)->where('review_by', $type)->first();

		$review_data = array(
			'review_message' 	=> isset($reviews_details->comments) ? $reviews_details->comments : '',
			'review_user_name' 	=> isset($reviews_details->users->first_name) ? $reviews_details->users->first_name : '',
			'review_user_image' => isset($reviews_details->users->profile_picture->src) ? $reviews_details->users->profile_picture->src : '',
			'review_date' 		=> isset($reviews_details->date_fy) ? $reviews_details->date_fy : '',
		);

		return $review_data;
	}

	/**
	 * Map function to format Space Details
	 *
	 * @return Array
	 */
    protected function mapSpaceListResult($space_details)
    {
        return $space_details->map(function ($space) {
        	$all_status = (array)$space->steps_status;
	        $all_status = collect($all_status);

	        $total_steps = $all_status->sum(function ($steps_status) {
	            return $steps_status['total_steps'];
	        });
	        $comp_steps = $all_status->sum(function ($steps_status) {
	            return $steps_status['completed_steps'];
	        });
	        $remain_steps = $all_status->sum(function ($steps_status) {
	            return $steps_status['remaining_steps'];
	        });

	        $percent_completed = round(($comp_steps / $total_steps) * 100);

	        $space_name = ($space->name == '') ? $space->sub_name : $space->name;

            return [
	            'space_id' 		=> $space->id,
	            'name' 			=> $space_name,
	            'status'		=> $space->status,
	            'admin_status'	=> $space->admin_status,
	            'photo_name' 	=> $space->photo_name,
	            'completed'		=> $percent_completed,
	            'remaining_steps'=> $remain_steps,
            ];
       });
    }

    /**
     * Update Step Status
     *
     * @param String $space_id
     * @param String $field Column Should Be Updated
     */
    protected function updateSpaceStatus($space_id, $field, $status)
    {
        if(!$this->space_status) {
            $this->space_status = SpaceStepsStatus::find($space_id);
        }
        $this->space_status->$field = $status;
        $this->space_status->save();
    }

    /**
     * Update Space Image Descriptions
     *
     * @param String $space_id
     * @param Array $image_data Image id and Description Should Be Updated
     */
    protected function updateImageData($space_id, $image_data)
    {
    	foreach ($image_data as $image) {
    		$photos = SpacePhotos::find($image['id']);
    		if($photos) {
		        $photos->highlights = $image['highlights'];
	        	$photos->save();
    		}
    	}
    }

	/**
	 * Display Space Details for Manage Space
	 *
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function space_listing_details(Request $request)
	{
		$rules 		= array('space_id' => 'required|exists:space,id');
		$attribute 	= array('space_id' => trans('messages.api.space_id'));
		$messages 	= array('required' => trans('messages.api.field_is_required',['attr'=>':attribute']));

		$validator = Validator::make($request->all(), $rules, $messages);
		$validator->setAttributeNames($attribute);

		if ($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		$user_details = JWTAuth::parseToken()->authenticate();
		$invalid_user = $this->validateSpaceAndUser($request->space_id, $user_details->id);
		if($invalid_user) {
			return response()->json([
				'status_code' => '0',
				'success_message' => trans('messages.api.invalid_space_id'),
			]);
		}

		$space_details = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->where('user_id',$user_details->id)->find($request->space_id);
		
		$data = array(
			'status_code' => '1',
			'success_message' => trans('messages.api.listed_successfully'),
		);

		$data['space_id']	= $space_details->id;
        $data['status'] 	= $space_details->status;
        $data['admin_status']= $space_details->admin_status;
		
		$all_status = (array)$space_details->steps_status;
        $all_status = collect($all_status);

        $total_steps = $all_status->sum(function ($steps_status) {
            return $steps_status['total_steps'];
        });
        $comp_steps = $all_status->sum(function ($steps_status) {
            return $steps_status['completed_steps'];
        });
        
        $data['completed'] 	= round(($comp_steps / $total_steps) * 100);
        $data['first_name'] = $user_details->first_name;

		$data['basics']		= $this->getBasicData($space_details);
		$data['setup']		= $this->getSetupData($space_details);
		$data['ready_to_host']	= $this->getReadyToHostData($space_details);

		return response()->json($data);
	}

	/**
	 * Get Admin Management Data for Basics Step
	 *
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function basics_step_items(Request $request)
	{
		$data = array(
			'status_code' => '1',
			'success_message' => trans('messages.api.basics_listed'),
		);
		$basics_data = $this->getBasicManagementData();

		return response()->json(array_merge($data,$basics_data));
	}

	/**
	 * Get Admin Management Data for Setup Step Data Step
	 *
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function setup_step_items(Request $request)
	{
		$data = array(
			'status_code' => '1',
			'success_message' => trans('messages.api.setup_listed'),
		);
		$setup_data = $this->getSetupManagementData();

		return response()->json(array_merge($data,$setup_data));
	}

	/**
	 * Get Admin Management Data for Ready To Host Step
	 *
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function ready_host_step_items(Request $request)
	{
		$data = array(
			'status_code' => '1',
			'success_message' => trans('messages.api.ready_host_listed'),
		);

		$activity_types = ActivityType::with('activities.sub_activities')->withActivitiesOnly()->activeOnly()->get();

		$ready_to_host_data = $activity_types->map(function($activity_type) {
			$activities = $activity_type->activities->map(function($activity) {
				
				$sub_activities = $activity->sub_activities->map(function($sub_activity) {
					return [
						'id'	=> $sub_activity->id,
						'name'	=> $sub_activity->name,
						'is_selected' => false,
					];
				});

				return [
					'id'	=> $activity->id,
					'name'	=> $activity->name,
					'is_selected' => false,
					'sub_activities' => $sub_activities,
				];
			});

			return [
				'id' 		=> $activity_type->id,
				'name' 		=> $activity_type->name,
				'image_url' => $activity_type->image_url,
				'is_selected' => false,
				'activities' => $activities,
			];
		});
		$data['activity_types'] = $ready_to_host_data;
		return response()->json($data);
	}

	/**
     * Update List Your Space Update Space Data
     *
     * @param array $request Post values from List Your Space Steps
     *
     * @return json return_data
     */
	public function update_space(Request $request)
	{
		$user_details 	= JWTAuth::parseToken()->authenticate();
		$new_space 		= !isset($request->space_id);
		if($new_space) {
			$validate_rules = $this->getValidationRulesData('new_space');
		}
		else {
			$validate_rules = $this->getValidationRulesData('common');
			$space_id = $request->space_id;
		}

		$validator = Validator::make($request->all(), $validate_rules['rules'], $validate_rules['messages'], $validate_rules['attributes']);

		if ($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		if($new_space) {
			$space_id = tap(new Space, function($space) use ($user_details) {
            	$space->user_id = $user_details->id;
		    	$space->save();
			})->id;
		}

		$space_details = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->where('user_id',$user_details->id)->find($space_id);
		
		if(!$space_details) {
			return response()->json([
				'status_code' => '0',
				'success_message' => __('messages.api.invalid_space_id'),
			]);
		}

		if($request->step == 'basics') {
            $this->saveSpaceData($space_id,$request->except('token','space_id','user_id','step','location_data'));
            if(isset($request->location_data)) {
            	$validate_rules = $this->getValidationRulesData('location');
            	$location_data = json_decode($request->location_data,true);
            	if(!isset($location_data)) {
           			$return_data = array(
						'status_code' 		=> '0',
						'success_message' 	=> __('messages.api.invalid_location'),
					);
					return response()->json($return_data);
           		}

           		$validator = Validator::make($location_data, $validate_rules['rules'], $validate_rules['messages'], $validate_rules['attributes']);

				if ($validator->fails()) {
		          	return response()->json([
		                'status_code'     => '0',
		                'success_message' => $validator->messages()->first(),
		            ]);
				}

            	$this->saveLocationData($space_id,$location_data);
            }
        }
        if($request->step == 'setup') {
            $this->saveSpaceData($space_id,$request->except('token','space_id','user_id','step','space_photos'));
            if(isset($request->space_photos)) {
            	$space_photos = collect(json_decode($request->space_photos,true));
				$space_photos = $space_photos->filter(function ($value) {
				    return $value['highlights'] != '';
				});
            	$this->updateImageData($space_id,$space_photos->pluckMultiple('id','highlights'));
            }
        }
        if($request->step == 'ready_to_host') {

       		$this->saveSpaceData($space_id,$request->only('cancellation_policy','booking_type'));
        	if(isset($request->security_deposit)) {
        		$this->saveSecurityDepositData($space_id,$request->only('activity_currency','security_deposit'));
        	}

        	if(isset($request->activity_price)) {
        		$send_security_deposit = true;
        		$activity_price = json_decode($request->activity_price,true);
        		$updated_currency = $activity_price[0]['currency_code'];        		
                $this->saveActivitiesPriceData($space_id,$activity_price);
            }

           	if(isset($request->availability_data)) {
           		$avail_data = json_decode($request->availability_data,true);
           		$availability_result = $this->formatAvailabilityData($avail_data);
           		if(!$availability_result['status']) {
           			$return_data = array(
						'status_code' 		=> '0',
						'success_message' 	=> $availability_result['status_message'],
					);
					return response()->json($return_data);
           		}
           		$send_calendar_data = true;
           		$availability_data[$avail_data['day']] = $avail_data;
                $this->saveAvailabilityData($request->space_id,$availability_data);
            }
        }

        $space = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->find($space_id);

        // Update Listed / Unlisted / Pending Status Based on steps Status
        $this->updateStatus($space_id);

        if($request->step == 'ready_to_host') {
            if($space->steps_status->ready_to_host['remaining_steps'] == 0) {
                $this->updateSpaceStatus($space_id,'pricing',1);
            }
        }

        if($new_space) {
            $this->updateSpaceStatus($space_id,'basics',1);
        }

		$return_data = array(
			'status_code' => '1',
			'success_message' => __('messages.api.update_success'),
			'space_id'	=> $space_id,
		);
		if($new_space) {
        	$return_data['success_message'] = __('messages.api.created_successfully');
        }

        if(isset($send_security_deposit)) {
        	$space_details->load('users');
			$return_data['currency_code'] = $updated_currency;
        	if(optional($space_details->space_price)->security == null) {
				$return_data['security'] = '';
			}
			else {
				$security = optional($space_details->space_price)->security;
				$return_data['security'] = strval(currency_convert($space_details->space_price->currency_code,$updated_currency,$security));
			}
        }
        
        if(isset($send_calendar_data)) {
        	$return_data['calendar_data']= $this->getSpaceCalendarData($space_details->id);
        }

		return response()->json($return_data);
	}

	/**
     * Save List Your Space Description Data
     *
     * @param String $space_id
     * @param Array $loc_data
     * @return Response in Json
     */
	public function update_space_description(Request $request)
	{
		$rules = array(
			'space_id' 	=> 'required|exists:space,id',
			'step' 		=> 'required',
			'name'		=> 'required',
			'summary'	=> 'required',
		);
		$attribute 	= array('space_id' => __('messages.api.space_id'));
		$messages 	= array('required' => __('messages.api.field_is_required',['attr'=>':attribute']));

		$validator = Validator::make($request->all(), $rules, $messages);
		$validator->setAttributeNames($attribute);

		if ($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		$user_details = JWTAuth::parseToken()->authenticate();
		$invalid_user = $this->validateSpaceAndUser($request->space_id, $user_details->id);
		if($invalid_user) {
			return response()->json([
				'status_code' => '0',
				'success_message' => __('messages.api.invalid_space_id'),
			]);
		}

		$space_details = Space::with('space_description')->find($request->space_id);

		$space_details->name = $request->name;
		$space_details->summary = $request->summary;
		$space_details->push();

		$return_data = array(
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.update_success'),
		);

		return response()->json($return_data);
	}

	public function space_image_upload(Request $request)
	{
		$rules = [
			'space_id' 	=> 'required',
			'image' 	=> 'required|image|mimes:jpg,png,jpeg,gif,webp',
		];

		$space_id = $request->space_id;

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'status_code' 	=> '0',
				'status_message'=> $validator->messages()->first(),
			]);
		}

		$user_details = JWTAuth::parseToken()->authenticate();
		$space_details = Space::where('user_id',$user_details->id)->find($request->space_id);
		
		if(!$space_details) {
			return response()->json([
				'status_code' => '0',
				'success_message' => trans('messages.api.invalid_space_id'),
			]);
		}

		$image = $request->file('image');
		$target_dir = '/images/space/'.$space_id;

        $compress_size = array(
            ['quality' => 80, 'width' => 1440, 'height' => 960],
            ['quality' => 80, 'width' => 1349, 'height' => 402],
            ['quality' => 80, 'width' => 450, 'height' => 250],
        );

        $upload_result = uploadImage($image,$target_dir,'',$compress_size);

        if($upload_result['status'] != 'Success') {
            $error_description = $upload_result['status_message'];
            $upload_errors = array('error_title' => ' Photo Error', 'error_description' => trans('messages.lys.invalid_image'));
        }
        else {
        	$last_photo  = SpacePhotos::whereSpaceId($space_id)->latest('order_id')->first();
        	$last_order_id  = optional($last_photo)->order_id;

            $photos             = new SpacePhotos;
            $photos->space_id   = $space_id;
            $photos->name       = $upload_result['file_name'];
            $photos->source     = $upload_result['upload_src'];
            $photos->order_id   = ++$last_order_id;
            $photos->save();
        }

        $photos_list = SpacePhotos::where('space_id',$space_id)->ordered()->get();
        if($photos_list->count() > 0) {
            $this->updateSpaceStatus($space_id,'photos',1);
        }
        else {
            $this->updateSpaceStatus($space_id,'photos',0);
        }

		$photos_list = $photos_list->map(function($photo) {
			return [
				'id'	=> $photo->id,
				'highlights' => $photo->highlights,
				'image_url' => $photo->name,
			];
		});

		return response()->json([
			'status_code' 		=> "1",
			'success_message' 	=> __('messages.api.update_success'),
			'photos_list' 		=> $photos_list,
			'space_image_id' 	=> $photos->id,
			'image_url'			=> $photos->name,
		]);
	}

	/**
	 * Delete Space Image
	 *
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function delete_image(Request $request)
	{
		$rules = [
			'space_id' 	=> 'required',
			'image_id' 	=> 'required|exists:space_photos,id',
		];

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'status_code' 	=> '0',
				'status_message'=> $validator->messages()->first(),
			]);
		}

		$user_details = JWTAuth::parseToken()->authenticate();
		$invalid_user = $this->validateSpaceAndUser($request->space_id, $user_details->id);
		if($invalid_user) {
			return response()->json([
				'status_code' => '0',
				'success_message' => trans('messages.api.invalid_space_id'),
			]);
		}

        SpacePhotos::find($request->image_id)->delete();

        $space_id = $request->space_id;
        $photos_list = SpacePhotos::where('space_id',$space_id)->ordered()->get();
        if($photos_list->count() > 0) {
            $this->updateSpaceStatus($space_id,'photos',1);
        }
        else {
            $this->updateSpaceStatus($space_id,'photos',0);
        }

		$photos_list = $photos_list->map(function($photo) {
			return [
				'id'	=> $photo->id,
				'highlights' => $photo->highlights,
				'image_url' => $photo->name,
			];
		});

		return response()->json([
			'status_code' 		=> "1",
			'success_message' 	=> __('messages.api.update_success'),
			'photos_list' 		=> $photos_list,
		]);
	}

	/**
	 * Update Calendar Data
	 *
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function update_calendar(Request $request)
	{
		$rules 		= array(
			'space_id' 	=> 'required|exists:space,id',
			'start_date'=> 'required|date_format:"Y-m-d"',
			'start_time'=> 'required|date_format:"H:i:s"',
			'available_status'=> 'required',
		);
		$attribute 	= array('space_id' => trans('messages.api.space_id'));
		$messages 	= array('required' => trans('messages.api.field_is_required',['attr'=>':attribute']));

		$validator = Validator::make($request->all(), $rules, $messages);
		$validator->setAttributeNames($attribute);

		if ($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		$space_id = $request->space_id;

		$user_details = JWTAuth::parseToken()->authenticate();
		$invalid_user = $this->validateSpaceAndUser($space_id, $user_details->id);
		if($invalid_user) {
			return response()->json([
				'status_code' => '0',
				'success_message' => trans('messages.api.invalid_space_id'),
			]);
		}

		$start_time_obj = getDateObject(strtotime($request->start_date.' '.$request->start_time));

		$start_date = $start_time_obj->format('Y-m-d');
        $start_time = $start_time_obj->format('H:i:s');
		$start_time_obj->addHour();
        $end_date = $start_time_obj->format('Y-m-d');
        $end_time = $start_time_obj->format('H:i:s');
        $calendar_status = ($request->available_status == 'No') ? 'Not available':'Available';

        $is_reservation = $this->getBookingsCount($space_id,$start_date,$end_date,$start_time,$end_time);

        if($is_reservation == 0) {
            $check_data = [
                'space_id'		=> $space_id,
                'start_date'  	=> $start_date,
                'end_date'   	=> $end_date,
                'start_time'   	=> $start_time,
                'end_time'   	=> $end_time,
            ];

            $data = [
                'space_id'=> $space_id,
                'status'  => $calendar_status,
                'notes'   => $request->notes,
                'source'  => 'Calendar'
            ];
            SpaceCalendar::updateOrCreate($check_data, $data);
        }
        // Delete All Available Records without notes
        SpaceCalendar::whereSpaceId($space_id)->Onlyavailable()->whereSource('Calendar')->where('notes','')->delete();

		$calendar_data = $this->getSpaceCalendarData($request->space_id);

		return response()->json([
			'status_code' 		=> "1",
			'success_message' 	=> __('messages.api.update_success'),
			'calendar_data' 	=> $calendar_data,
		]);
	}

	/**
	 * Update Space Activity Details
	 *
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function update_activities(Request $request)
	{
		$rules 		= array(
			'space_id' 	=> 'required|exists:space,id',
		);
		$attribute 	= array('space_id' => trans('messages.api.space_id'));
		$messages 	= array('required' => trans('messages.api.field_is_required',['attr'=>':attribute']));

		$validator = Validator::make($request->all(), $rules, $messages);
		$validator->setAttributeNames($attribute);

		if ($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		$user_details = JWTAuth::parseToken()->authenticate();
		$invalid_user = $this->validateSpaceAndUser($request->space_id, $user_details->id);
		if($invalid_user) {
			return response()->json([
				'status_code' => '0',
				'success_message' => trans('messages.api.invalid_space_id'),
			]);
		}
		
		$activities_data = array();
		$activity_data = collect(json_decode($request->activity_data,true));

		$selected_activities = $activity_data->where('is_selected',true);
		$selected_activities->each(function ($activity_type) use (&$activities_data) {

			$activity_data = array();
			$activities = collect($activity_type['activities']);
			$activities = $activities->where('is_selected',true);

			$activities->each(function ($activity) use (&$activity_data,&$activities_data) {
				$sub_activities = collect($activity['sub_activities']);
				$sub_activities = $sub_activities->where('is_selected',true)->pluck('id')->implode(',');

				$activity_data[] = ['activity_id' => $activity['id'],'sub_activities' => $sub_activities];
			});

		    $activities_data[$activity_type['id']] = $activity_data;
		});

        $this->saveActivitiesData($request->space_id,$activities_data);

		$space_details = Space::with('space_activities.activity_type','space_activities.activity_price')->find($request->space_id);
		$currency_code = $space_details->activity_price->getOriginal('currency_code');
		$minimum_amount = getMinimumAmount($currency_code);
		$activity_price = $this->getActivityPriceData($space_details);

		$currency_details       = Currency::whereCode($currency_code)->first();
        $currency_symbol        = optional($currency_details)->original_symbol;

		return response()->json([
			'status_code' 		=> "1",
			'success_message' 	=> __('messages.api.update_success'),
			'currency_code' 	=> $currency_code,
			'currency_symbol' 	=> $currency_symbol,
			'minimum_amount' 	=> $minimum_amount,
			'activity_price' 	=> $activity_price,
		]);
	}

	/**
	 * Display Space Details
	 *
	 * @param Get method request inputs
	 *
	 * @return Response Json
	 */
	public function space_detail(Request $request)
	{
		$rules 		= array(
			'space_id' 	=> 'required|exists:space,id',
		);
		$attribute 	= array('space_id' => trans('messages.api.space_id'));
		$messages 	= array('required' => trans('messages.api.field_is_required',['attr'=>':attribute']));

		$validator = Validator::make($request->all(), $rules, $messages);
		$validator->setAttributeNames($attribute);

		if($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}
		$space_id = $request->space_id;

		$space_details = Space::with('users.profile_picture','space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price','activity_price')->find($space_id);

		if($request->token) {
			$user_details = JWTAuth::parseToken()->authenticate();
			$currency_code = $user_details->currency_code;
			$currency_symbol= Currency::original_symbol($currency_code);

			if($space_details->status != "Listed" && $space_details->user_id != $user_details->id) {
				return response()->json([
					'status_code' 		=> '2',
					'success_message' 	=> __('messages.api.space_not_available'),
				]);
		    }

			$can_book = ($user_details->id == $space_details->user_id) ? 'No' : 'Yes';
		}
		else {
			$default_currency = Currency::defaultCurrency()->first();
			$currency_code 	= $default_currency->code;
			$currency_symbol= $default_currency->original_symbol;
			$can_book 		= 'Yes';
		}

        $amenities      = explode(',', $space_details->amenities);
		$guest_access   = explode(',', $space_details->guest_access);
        $services       = explode(',', $space_details->services);
        $space_style    = explode(',', $space_details->space_style);
        $special_feature= explode(',', $space_details->special_feature);
        $space_rules    = explode(',', $space_details->space_rules);

		$all_amenities = Amenities::active()->whereIn('id', $amenities)->get()->pluckMultiple('id','name','image_name');
		$all_guest_access = GuestAccess::active()->whereIn('id', $guest_access)->get()->pluckMultiple('id','name');
		$all_services = Services::active()->whereIn('id', $services)->get()->pluckMultiple('id','name');
		$all_space_style = Style::active()->whereIn('id', $space_style)->get()->pluckMultiple('id','name');
		$all_special_feature = SpecialFeature::active()->whereIn('id', $special_feature)->get()->pluckMultiple('id','name');
		$all_space_rules = SpaceRule::active()->whereIn('id', $space_rules)->get()->pluckMultiple('id','name');

		$space_data = $space_details->only(['name','summary','user_id','host_name','sq_ft_text','cancellation_policy']);
		$loc_data = $space_details->space_address->only(['address_line_1','address_line_2', 'city', 'state', 'country', 'postal_code', 'latitude', 'longitude', 'guidance']);

		$the_space_data = $space_details->only(['space_type_name','number_of_guests','number_of_rooms','number_of_restrooms','floor_number']);
		
		$the_space = collect($the_space_data)->map(function($value,$key) {
			$key = ($key == 'space_type_name') ? 'space_type' : $key;
			$key = ($key == 'number_of_guests') ? 'maximum_guests' : $key;
			return [
				'key' => ucwords(str_replace('_', ' ', $key)),
				'value' => strval($value),
			];
		})->toArray();

		$the_space = array_values($the_space);

		$space_data['the_space'] = $the_space;

		$review_data = array_merge(['review_count' => $space_details->reviews_count,'rating' => $space_details->overall_star_rating->rating_value],$this->getReviewData($space_id,'guest'));

		$similar_data = $space_details->space_address->only(['latitude','longitude']);
		$similar_listings = $this->getSimilarListings($similar_data['latitude'], $similar_data['longitude'], $space_details->id);
		$similar_listings = $this->mapSpaceResult($similar_listings);

		$space_photos = $space_details->space_photos->map(function($photo) {
			return [
				'id' => $photo->id,
				'name' => $photo->name,
				'highlights' => $photo->highlights,
			];
		});

		$space_activities = $space_details->space_activities->map(function($activity) {
			$activity_price = $activity->activity_price;
			$activity_price->load('currency');
			return [
				'id' 		=> $activity->id,
				'image_url'	=> $activity->activity_type->image_url,
				'name' 		=> $activity->activity_type_name,
				'hourly' 	=> $activity_price->hourly,
				'min_hours' => $activity_price->min_hours,
				'full_day' 	=> $activity_price->full_day,
				'currency_code'	=> $activity_price->currency->code,
				'currency_symbol'=> $activity_price->currency->symbol,
			];
		});

		$space_availabilities = $space_details->space_availabilities;

		$availability_times = $space_availabilities->map(function($availability) {
			$availability_times = $availability->availability_times;

			if($availability->status == 'Open') {
				$availability_text = $availability_times->pluck('formatted_times')->implode(',');
			}
			else {
				$availability_text = ($availability->status == 'Closed') ? __('messages.inbox.not_available') : __('messages.space_detail.all_day');
			}
			// dd($availability);
			return [
				'day_type' => $availability->day,
				'key' => $availability->day_name,
				'status' => ($availability->status == 'Closed') ? 'Closed' : 'Open',
				'value' => $availability_text,
			];
		});

		$space_data['space_photos'] = $space_photos;
		$space_data['space_activities'] = $space_activities;
		$space_data['location_data'] = $loc_data;
		$space_data['maximum_guests'] = $space_details->number_of_guests;
		$space_data['availability_times'] = $availability_times;

		$return_data = array_merge([
			'status_code' 		=> "1",
			'success_message' 	=> __('messages.api.listed_successfully'),
			'space_id'			=> $space_id,
			'space_url'			=> $space_details->link,
			'instant_book'  	=> ($space_details->booking_type == 'instant_book') ? 'Yes' : 'No',
			'host_profile_pic'	=> $space_details->users->profile_picture->src,
			'can_book'			=> $can_book,
			'hourly' 			=> $space_details->activity_price->hourly ?? '0',
			'currency_code'		=> $space_details->activity_price->currency->code ?? $currency_code,
			'currency_symbol'	=> $space_details->activity_price->currency->symbol ?? $currency_symbol,
			'is_wishlist'   	=> $space_details->overall_star_rating->is_wishlist,
		],$space_data,$review_data);

		$additional_data = [
			'amenities'			=> $all_amenities,
			'guest_access'		=> $all_guest_access,
			'services'			=> $all_services,
			'services_extra'	=> strval($space_details->services_extra),
			'space_style'		=> $all_space_style,
			'special_feature'	=> $all_special_feature,
			'space_rules'		=> $all_space_rules,
			'similar_listings'	=> $similar_listings->toArray(),
		];
		$result = array_merge($return_data,$additional_data);

		return response()->json($result);
	}

	/**
	 * Get All Space Details
	 *
	 * @param Get method request inputs
	 *
	 * @return Response Json
	 */
	public function listings(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$listed_result = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->where('user_id',$user_details->id)->listed()->verified()->get();

        $unlisted_result = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->where('user_id',$user_details->id)->where(function ($query) {
            $query->where('status', 'Unlisted')->orWhere('status', 'Pending')->orWhere('status', 'Resubmit')->orWhere('admin_status', 'Pending')->orWhere('admin_status', 'Resubmit')->orWhereNull('status');
        })->get();

		if ($listed_result->count() == 0 && $unlisted_result->count() == 0) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> trans('messages.api.no_data_found'),
			]);
		}

		$listed_space = $this->mapSpaceListResult($listed_result);
		$Unlisted_space = $this->mapSpaceListResult($unlisted_result);

		return response()->json([
			'status_code' 		=> "1",
			'success_message' 	=> __('messages.api.listed_successfully'),
			'listed' 			=> $listed_space,
			'unlisted' 			=> $Unlisted_space,
		]);
	}

	/**
     * Get Minimum Amount of Given Currency
     *
     * @param  Get method request inputs
     *
     * @return Response in Json
     */
    public function get_min_amount(Request $request)
    {
        $minimum_amount         = getMinimumAmount($request->currency_code);
        $currency_details       = Currency::whereCode($request->currency_code)->first();
        $currency_symbol        = optional($currency_details)->original_symbol;
        
        return response()->json([
			'status_code' 		=> "1",
			'success_message' 	=> __('messages.api.listed_successful'),
			'currency_code' 	=> $request->currency_code,
			'currency_symbol' 	=> $currency_symbol,
			'minimum_amount' 	=> $minimum_amount,
		]);
    }

    /**
     * Display All Review Details
     *
     * @param  Get method request inputs
     *
     * @return Response in Json
     */
    public function review_detail(Request $request)
    {
    	$rules 		= array('space_id' => 'required|exists:space,id','page'	=> 'required|numeric|min:1');
		$attribute 	= array('space_id' => __('messages.api.space_id'),'page' => __('messages.api.page'));
		$messages 	= array('required' => __('messages.api.field_is_required',['attr'=>':attribute']));

		$validator = Validator::make($request->all(), $rules, $messages,$attribute);

		if ($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

        //get review details
        $reviews = Reviews::with('users_from.profile_picture')->where('space_id', $request->space_id)->where('review_by', 'guest');
		$total_review = $reviews->count();

		if($total_review == 0 ) {
        	return response()->json([
                'status_code' 		=> '0',
                'success_message' 	=> __('messages.api.no_reviews_found'),
            ]);
        }

        $accuracy_value 	= roundHalfInteger($reviews->sum('accuracy') / $total_review);
        $check_in_value 	= roundHalfInteger($reviews->sum('checkin') / $total_review);
        $cleanliness_value	= roundHalfInteger($reviews->sum('cleanliness') / $total_review);
        $communication_value= roundHalfInteger($reviews->sum('communication') / $total_review);
        $location_value 	= roundHalfInteger($reviews->sum('location') / $total_review);
        $value 				= roundHalfInteger($reviews->sum('value') / $total_review);
        $rating_value 		= roundHalfInteger($reviews->sum('rating') / $total_review);

        $result_reviews = $reviews->orderByRaw('RAND(1234)')->paginate(20)->toJson();

        $data_result = json_decode($result_reviews);
		if ($data_result->total == 0 || empty($data_result->data)) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> __('messages.api.no_data_found'),
			]);
		}

		$review_result = collect($data_result->data);
		$result_data = $review_result->map(function ($review) {
			$review_date = getDateObject(strtotime($review->created_at));
			return [
				'review_user_name' 	=> $review->users_from->full_name,
				'review_user_image'	=> $review->users_from->profile_picture->src,
				'review_date'		=> $review_date->format('M Y'),
				'review_message'	=> $review->comments,
			];
		});

		$result = array(
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.listed_successful'),
			'total_page'		=> $data_result->last_page,
			'total_review' 		=> strval($total_review),
			'accuracy'		 	=> strval($accuracy_value),
			'check_in'		 	=> strval($check_in_value),
			'cleanliness'		=> strval($cleanliness_value),
			'communication'		=> strval($communication_value),
			'location'		 	=> strval($location_value),
			'value' 			=> strval($value),
			'rating'		 	=> strval($rating_value),
			'data' 				=> $result_data,
		);

		return response()->json($result);
    }

    /**
     * Display All Space Activities Based on given Space Id
     *
     * @param  Get method request inputs
     *
     * @return Response in Json
     */
    public function space_availabilities(Request $request)
    {
    	$rules 		= array('space_id' => 'required|exists:space,id');
		$attribute 	= array('space_id' => __('messages.api.space_id'));
		$messages 	= array('required' => __('messages.api.field_is_required',['attr' => ':attribute']));

		$validator = Validator::make($request->all(), $rules, $messages,$attribute);

		if($validator->fails()) {
			return response()->json([
				'status_code'     => '0',
				'success_message' => $validator->messages()->first(),
            ]);
		}

		$user_details = JWTAuth::parseToken()->authenticate();
		$space_details = Space::viewOnly()->with('users','space_availabilities.availability_times')->find($request->space_id);

		

		$result = array(
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.listed_successful'),
			'maximum_guests' 	=> $space_details->number_of_guests,
			'availability_times'=> $availability_times,
		);

		return response()->json($result);
    }

    /**
     * Display All Space Activities Based on given Space Id
     *
     * @param  Get method request inputs
     *
     * @return Response in Json
     */
    public function space_activities(Request $request)
    {
    	$rules 		= array('space_id' => 'required|exists:space,id');
		$attribute 	= array('space_id' => __('messages.api.space_id'));
		$messages 	= array('required' => __('messages.api.field_is_required',['attr' => ':attribute']));

		$validator = Validator::make($request->all(), $rules, $messages,$attribute);

		if($validator->fails()) {
			return response()->json([
				'status_code'     => '0',
				'success_message' => $validator->messages()->first(),
            ]);
		}

		$space_details = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times')->find($request->space_id);
		$space_activities = $space_details->space_activities;

		$activity_types = $space_activities->implode('activity_type_id',',');
        $activities     = $space_activities->implode('activities',',');
        $sub_activities = $space_activities->implode('sub_activities',',');

        $activity_types = explode(',',$activity_types);
        $activities     = explode(',',$activities);
        $sub_activities = explode(',',$sub_activities);

        $all_activities  = ActivityType::with(['activities' => function($query) use ($activities,$sub_activities) {
            $query->with(['sub_activities' => function($query) use ($sub_activities) {
	            $query->whereIn('id',$sub_activities)->activeOnly();
	        }])->whereIn('id',$activities)->activeOnly();
        }])->whereIn('id',$activity_types)->activeOnly()->get();

        $activities_data = $all_activities->map(function($activity_type) use ($space_activities) {
			$space_activity = $space_activities->where('activity_type_id',$activity_type->id)->first();

			$activity_price = optional($space_activity)->activity_price;

			$activities = $activity_type->activities->map(function($activity) {
				
				$sub_activities = $activity->sub_activities->map(function($sub_activity) {
					return [
						'id'	=> $sub_activity->id,
						'name'	=> $sub_activity->name,
					];
				});

				return [
					'id'	=> $activity->id,
					'name'	=> $activity->name,
					'sub_activities' => $sub_activities,
				];
			});

			$activity_price->load('currency');
			return [
				'id' 		=> $activity_type->id,
				'name' 		=> $activity_type->name,
				'image_url' => $activity_type->image_url,
				'currency_code'	=> optional($activity_price)->currency_code,
				'currency_symbol'=> optional($activity_price)->currency->symbol,
				'hourly'	=> strval(optional($activity_price)->hourly),
				'min_hours'	=> strval(optional($activity_price)->min_hours),
				'full_day'	=> strval(optional($activity_price)->full_day),
				'activities' => $activities,
			];
		});

        $result = array(
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.listed_successful'),
			'activity_types' 	=> $activities_data,
		);

		return response()->json($result);
    }

    /**
     * Display All Availability Times in Given Range
     *
     * @param  Get method request inputs
     *
     * @return Response in Json
     */
    public function get_availability_times(Request $request)
    {
    	$rules 		= array(
    		'space_id' 		=> 'required|exists:space,id',
    		'start_date'	=> 'required|date_format:"Y-m-d"',
    		'end_date'		=> 'required|date_format:"Y-m-d"',
    	);
		$attribute 	= array('space_id' => __('messages.api.space_id'));
		$messages 	= array('required' => __('messages.api.field_is_required',['attr' => ':attribute']));

		$validator = Validator::make($request->all(), $rules, $messages,$attribute);

		if($validator->fails()) {
			return response()->json([
				'status_code'     => '0',
				'success_message' => $validator->messages()->first(),
            ]);
		}

        $space_id 		= $request->space_id;
		$c_date 		= date('Y-m-d');
		$start_date 	= strtotime($request->start_date);
		$end_date 		= strtotime($request->end_date);

        $space_availabilities = SpaceAvailability::with('availability_times')->where('space_id', $space_id)->get();
        $not_available_days = $space_availabilities->where('status','Closed')->pluck('day')->toArray();
        $all_available_days = $space_availabilities->where('status','All')->pluck('day')->toArray();
        $open_availabilities = $space_availabilities->where('status','Open');
        
        $availability_data = compact('all_available_days','open_availabilities');

		$start_date_obj = getDateObject($start_date);
		$end_date_obj 	= getDateObject($end_date);

		$start_day = $start_date_obj->format('w');
		$end_day = $end_date_obj->format('w');
    	if(in_array($start_day, $not_available_days) || in_array($end_day, $not_available_days)) {
			return [
				'status' 			=> false,
				'success_message' 	=> __('messages.api.invalid_date_selection'),
			];
		}

		$start_times = $this->getAvailabilityTimes($space_id,$availability_data,$start_date_obj,$request->time_zone);

		$end_times = $this->getAvailabilityTimes($space_id,$availability_data,$end_date_obj,$request->time_zone);
		$space_details2  = Space::with('users')->find($space_id);
		$user_tz2 		= $space_details2->users->timezone;
		return response()->json([
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.listed_successful'),
			'start_date' 		=> $request->start_date,
			'end_date' 			=> $request->end_date,
			'host_time_zone'	=> $user_tz2,
			'start_times' 		=> count($start_times)>1?$start_times:null,
			'end_times' 		=> count($end_times)>1?$start_times:null,
		]);
    }
}