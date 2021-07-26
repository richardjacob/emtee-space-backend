<?php

/**
 * Manage Space
 *
 * @package     Makent Space
 * @subpackage  Manage Space
 * @category    Repository
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Repositories;

use App\Http\Controllers\EmailController;
use App\Models\KindOfSpace;
use App\Models\GuestAccess;
use App\Models\Amenities;
use App\Models\Services;
use App\Models\Style;
use App\Models\SpecialFeature;
use App\Models\SpaceRule;
use App\Models\ActivityType;
use App\Models\Activity;
use App\Models\SubActivity;
use App\Models\SpacePrice;
use App\Models\AvailabilityTimes;
use App\Models\SpaceAvailability;
use App\Models\Space;
use App\Models\SpaceLocation;
use App\Models\SpaceStepsStatus;
use App\Models\SpaceActivities;
use App\Models\ActivityPrice;
use App\Models\Country;
use Illuminate\Support\Arr;

trait ManageSpace
{
    /**
     * Get Basic Management Data for space
     *
     * @return Array $data
     */
    protected function getBasicManagementData()
    {
        $data  = array();
        $data['space_types']    = KindOfSpace::active()->get();
        $data['guest_accesses'] = GuestAccess::active()->get();
        $data['amenities']      = Amenities::active()->get();
        $data['services']       = Services::active()->get();

        return $data;
    }

    /**
     * Get Setup Management Data for space
     *
     * @return Array $data
     */
    protected function getSetupManagementData()
    {
        $data  = array();
        $data['space_styles']   = Style::active()->get();
        $data['special_features']= SpecialFeature::active()->get();
        $data['space_rules']    = SpaceRule::active()->get();

        return $data;
    }

    /**
     * Get Ready To Host Management Data for space
     *
     * @return Array $data
     */
    protected function getReadyToHostManagementData()
    {
        $data  = array();
        $data['activity_types'] = ActivityType::withActivitiesOnly()->activeOnly()->get();
        $data['activities']     = Activity::activeOnly()->get();
        $data['sub_activities'] = SubActivity::activeOnly()->get();

        return $data;
    }

    /**
     * Get Basic Management Data for space
     *
     * @return Array $data
     */
    protected function getValidationRulesData($type = 'common')
    {
        if($type == 'new_space') {
            $rules = array(
                'step'              => 'required',
                'space_type'        => 'required',
                'sq_ft'             => 'required',
                'size_type'         => 'required',
                'guest_access'      => 'required',
                'number_of_guests'  => 'required',
                'location_data'     => 'required',
            );
        }
        else if($type == 'common') {
            $rules = array(
                'space_id'  => 'required|exists:space,id',
                'step'      => 'required',
            );
        }
        else if($type == 'location') {
            $rules = array(
                'latitude'      => 'required',
                'longitude'     => 'required',
                'address_line_1'=> 'required',
                'country'       => 'required|exists:country,short_name',
            );
        }

        $attributes = array('space_id' => trans('messages.api.space_id'));
        $messages   = array('required' => trans('messages.api.field_is_required',['attr'=>':attribute']));

        return compact('rules','attributes','messages');
    }

    /**
     * Map function to format Space Details
     *
     * @return Array
     */
    protected function mapSpaceResult($space_details)
    {
        return $space_details->map(function ($space) {
            $activity_price = $space->space_activities[0]->activity_price;
            return [
                'space_id'      => $space->id,
                'name'          => $space->name,
                'space_type_name' => $space->space_type_name,
                'size'          => $space->sq_ft,
                'size_type'     => __('messages.space_detail.'.$space->size_type),
                'photo_name'    => $space->photo_name,
                'rating'        => $space->overall_star_rating->rating_value,
                'is_wishlist'   => $space->overall_star_rating->is_wishlist,
                'reviews_count' => $space->reviews_count,
                'currency_code' => optional($activity_price)->currency_code,
                'currency_symbol'=> html_entity_decode($space->activity_price->currency->symbol),
                'hourly'        => optional($activity_price)->hourly,
                'country_name'  => $space->space_address->country_name,
                'city_name'     => $space->space_address->city ?? '',
                'latitude'      => $space->space_address->latitude,
                'longitude'     => $space->space_address->longitude,
                'instant_book'  => ($space->booking_type == 'instant_book') ? 'Yes' : 'No',
            ];
       });
    }

    /**
     * Get Similar Listing for given Space
     *
     * @param Int $latitude
     * @param Int $longitude
     * @param Int $space_id
     *
     * @return Array
     */
    protected function getSimilarListings($latitude, $longitude, $space_id)
    {
        $similar_listings = Space::with('activity_price')
        ->whereHas('space_address' ,function($query) use ($latitude, $longitude) {
            $query->Select(\DB::raw('*, ( 3959 * acos( cos( radians('.$latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( latitude ) ) ) ) as distance'))
            ->having('distance', '<=', 30);
        })
        ->whereHas('users', function($query) {
            $query->whereStatus('Active');
        })
        ->where('id', '!=', $space_id)
        ->listed()
        ->verified()
        ->get();

        return $similar_listings;
    }

    /**
     * Get Booking Count of Space with given date and times
     *
     * @param Int $space_id
     * @param Date $start_date
     * @param Date $end_date
     * @param Time $start_time
     * @param Time $end_time
     *
     * @return Int $booking_count
     */
    protected function getBookingsCount($space_id, $start_date, $end_date, $start_time, $end_time)
    {
        $booking_count = \DB::Table('reservation_times')
            ->select('*')
            ->whereSpaceId($space_id)
            ->whereStatus('Not available')
            ->whereBetween('start_date',[$start_date, $end_date])
            ->whereRaw("((start_time <= '".$end_time."' and end_time >= '".$start_time."') or (end_time <= '".$end_time."' and start_time >= '".$start_time."'))")
            ->count();

        return $booking_count;
    }

    /**
     * Update Space Status Based on their step status
     *
     * @param int $id Space Id
     * @return true
     */ 
    protected function updateStatus($id)
    {
        $space = Space::whereId($id)->first();
        $steps_status = $space->steps_status;

        $pending_steps = ($steps_status->basics['remaining_steps'] > 0 || $steps_status->setup['remaining_steps'] > 0 || $steps_status->ready_to_host['remaining_steps'] > 0);

        if($pending_steps && $space->status == 'Listed') {
            $space->status = 'Unlisted';
            $space->save();
        }
        else if(!$pending_steps && $space->status == null) {
            $space->status = 'Pending';
            $space->save();
        }

        return true;
    }

    /**
     * Save List Your Space Basics Data
     *
     * @param String $space_id
     * @param Array $step_data
     * @return Void
     */
    protected function saveSpaceData($space_id, $step_data)
    {
        $num_cols = ['number_of_rooms', 'number_of_restrooms', 'number_of_guests', 'floor_number','sq_ft'];
        $space = Space::find($space_id);
        foreach($step_data as $key => $value) {
            if(in_array($key, $num_cols)) {
                $space->$key = $value;
            }
            else {
                $space->$key = removeEmailNumber($value);
            }
        }
        $space->save();
    }

    /**
     * Save List Your Space Location Data
     *
     * @param String $space_id
     * @param Array $loc_data
     * @return Void
     */
    protected function saveLocationData($space_id, $loc_data = array())
    {
        $validate_country = $this->validateCountry($loc_data['country']);

        if(!$validate_country['status']) {
            return json_encode($validate_country);
        }
        $loc_data = array_except($loc_data, ['id', 'space_id', 'country_name', 'address_line']);

        $space_addr = SpaceLocation::where('space_id', $space_id)->first();

        foreach($loc_data as $key => $value) {
            $space_addr->$key = $value;
        }

        $space_addr->save();

        $this->notifyUser($space_id,'SpaceUpdated','Location');
        $this->updateSpaceStatus($space_id,'location',1);
        $return_data = array('status' => true, 'status_message' => 'updated Sucessfully.');
        return json_encode($return_data);
    }

    /**
     * Save List Your Space Activities Data
     *
     * @param String $space_id
     * @param Array $activity_data
     * @return Void
     */
    protected function saveActivitiesData($space_id, $activity_data)
    {
        $all_activities = array_keys($activity_data);
        $activities = SpaceActivities::whereSpaceId($space_id)->whereNotIn('activity_type_id',$all_activities)->get();
        $prev_act_price = ActivityPrice::where('space_id',$space_id)->first();
        if($prev_act_price) {
            $currency_code = $prev_act_price->getOriginal('currency_code');
        }
        else {
            $currency_code = session('currency') ?? view()->shared('default_currency')->code;
        }
        ActivityPrice::whereIn('activity_id',$activities->pluck('id'))->delete();
        SpaceActivities::whereSpaceId($space_id)->whereNotIn('activity_type_id',$all_activities)->delete();

        foreach ($activity_data as $key => $value) {
            $activity = SpaceActivities::firstOrCreate(['space_id' => $space_id, 'activity_type_id' => $key]);
            $activity_ids = data_get($value, '*.activity_id');
            $sub_activity_ids = data_get($value, '*.sub_activities');

            $activity->activities      = implode($activity_ids, ',');
            $activity->sub_activities  = implode($sub_activity_ids, ',');
            $activity->save();

            $activity_price = ActivityPrice::firstOrNew(['space_id' => $space_id,'activity_id' => $activity->id]);
            $activity_price->space_id = $space_id;
            $activity_price->activity_id = $activity->id;
            $activity_price->currency_code = $currency_code;
            $activity_price->save();
        }
    }

    /**
     * Save List Your Space Activities Price Data
     *
     * @param String $space_id
     * @param Array $activity_price
     * @return Void
     */
    protected function saveActivitiesPriceData($space_id, $activity_price)
    {
        foreach ($activity_price as $price_data) {
            $minimum_amount = getMinimumAmount($price_data['currency_code']);
            if($price_data['hourly'] >= $minimum_amount) {
                $main_data = ['space_id' => $space_id,'activity_id' => $price_data['activity_id']];
                $update_data = ['hourly' => $price_data['hourly'], 'min_hours' => $price_data['min_hours'], 'full_day' => $price_data['full_day'],'weekly' => $price_data['weekly'] ?? 0,'monthly' => $price_data['monthly'] ?? 0,'currency_code' => $price_data['currency_code']];
                $activity_price = ActivityPrice::updateOrCreate($main_data, array_merge($main_data,$update_data));
            }
        }
    }


    /**
     * Save List Your Space Availability Data
     *
     * @param String $space_id
     * @param Array $availability_data
     * @return Void
     */
    protected function saveAvailabilityData($space_id, $availability_data)
    {
        $updated_ids = array();
        foreach ($availability_data as $day => $availability) {
            $removed_avail = isset($availability['removed_availability']) ? $availability['removed_availability'] : array();
            AvailabilityTimes::whereIn('id',$removed_avail)->delete();

            $avail = SpaceAvailability::firstOrCreate(['space_id' => $space_id,'day' => $day]);

            if(isset($availability['available'])) {
                $hourly_status = ($availability['available'] == 'set_hours') ? 'Open' : 'All';
            }
            else {
                $hourly_status = ($availability['status'] == 'All') ? 'All' : 'Open';
            }

            $status = ($availability['status'] == 'Closed') ? 'Closed' : $hourly_status;
            $avail->status = $status;
            $avail->save();

            if($status == 'Open') {
                $availability_times = $availability['availability_times'];
                foreach ($availability_times as $key => $hour_data) {
                    if($hour_data['id'] == '') {
                        $avail_hours = new AvailabilityTimes;
                    }
                    else {
                        $avail_hours = AvailabilityTimes::findOrNew($hour_data['id']);
                    }

                    $avail_hours->space_availability_id = $avail->id;
                    $avail_hours->start_time    = $hour_data['start_time'];
                    $avail_hours->end_time      = $hour_data['end_time'];
                    $avail_hours->save();
                    $updated_ids[] = $avail_hours->id;
                }
            }
            else {
                AvailabilityTimes::whereIn('space_availability_id',[$avail->id])->delete();
            }
            AvailabilityTimes::whereIn('space_availability_id',[$avail->id])->whereNotIn('id',$updated_ids)->delete();
        }
    }

    /**
     * Save List Your Space Security Deposit Data
     *
     * @param String $space_id
     * @param Array $security_deposit_data
     * @return Void
     */
    protected function saveSecurityDepositData($space_id, $security_deposit_data)
    {
        if($security_deposit_data['security_deposit'] > 0) {
            $space_price = SpacePrice::firstOrNew(['space_id' => $space_id]);
            $space_price->space_id = $space_id;
            $space_price->currency_code = $security_deposit_data['activity_currency'];
            $space_price->security = $security_deposit_data['security_deposit'];

            $space_price->save();
        }
        else {
            SpacePrice::where('space_id',$space_id)->delete();
        }
    }

    /**
     * Validate Given Country Available
     *
     * @param String $short_name
     * @return Array $return_data
     */
    protected function validateCountry($short_name)
    {
        $return_data = array('status' => true, 'status_message' => 'Country Found.');
        $country = Country::where('short_name', $short_name)->count();

        if(!$country) {
            $return_data = array('status' => false, 'status_message' => 'Country Not Found.');
        }
        return $return_data;
    }

    /**
     * Send Notification mail to User
     *
     * @param String $space_id
     * @param String $type
     */
    protected function notifyUser($space_id, $type, $field = '')
    {
        $email_controller = new EmailController;

        if($type == 'Approval') {
            $email_controller->awaiting_approval_admin($space_id);
            $email_controller->awaiting_approval_host($space_id);
        }

        if($type == 'SpaceUpdated') {
            $email_controller->space_details_updated($space_id, $field);
        }
    }
}