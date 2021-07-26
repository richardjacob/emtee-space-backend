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

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EmailController;
use App\Http\Helper\PaymentHelper;
use App\Models\Language;
use App\Models\GuestAccess;
use App\Models\Country;
use App\Models\Amenities;
use App\Models\Style;
use App\Models\Services;
use App\Models\SpaceRule;
use App\Models\SpecialFeature;
use App\Models\Space;
use App\Models\SpaceDescription;
use App\Models\SpaceDescriptionLang;
use App\Models\SpaceLocation;
use App\Models\SpaceStepsStatus;
use App\Models\SpacePhotos;
use App\Models\SpacePrice;
use App\Models\SpaceActivities;
use App\Models\ActivityPrice;
use App\Models\Activity;
use App\Models\SpaceCalendar;
use App\Models\Currency;
use App\Models\ReservationTimes;
use App\Models\SavedWishlists;
use App\Models\SpaceAvailability;
use App\Models\User;
use DB;
use DateTime;
use App\Repositories\ManageSpace;

class SpaceController extends Controller
{
    use ManageSpace;
    
    protected $space_status;

    /**
     * Load Your Listings View
     *
     * @return your listings view file
     */
    public function index()
    {
        $data['listed_result']   = Space::user()->listed()->verified()->get();

        $data['unlisted_result'] = Space::user()->where(function ($query){
            $query->where('status', 'Unlisted')->orWhere('status', 'Pending')->orWhere('status', 'Resubmit')->orWhere('admin_status', 'Pending')->orWhere('admin_status', 'Resubmit')->orWhereNull('status');
        })->get();
        return view('space.listings', $data);
    }

    // For coupon code destroy
    protected function forgetCoupon()
    {
        session()->forget('coupon_code');
        session()->forget('coupon_amount');
        session()->forget('remove_coupon');
        session()->forget('manual_coupon');
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
     * Get Space Steps Data
     *
     * @return Array $steps_data
     */
    protected function getStepsData()
    {
        $basics_data = array(
            'total_steps' => 7,
            'mandatory_steps' => array(
                '1' => 'space_type',
                '2' => 'sq_ft',
                '3' => 'number_of_guests',
                '6' => 'space_address',
                '7' => 'guest_access',
            ),
        );

        $setup_data = array(
            'total_steps' => 5,
            'mandatory_steps' => array(
                '1' => 'photos',
                '5' => 'description',
            ),
        );

        $ready_to_host_data = array(
            'total_steps' => 5,
            'mandatory_steps' => array(
                '1' => 'activities',
                '2' => 'activity_price',
                '3' => 'availability',
                '4' => 'cancellation',
            ),
        );

        $steps_data = collect([
            'basics'        => $basics_data,
            'setup'         => $setup_data,
            'ready_to_host' => $ready_to_host_data,
        ]);

        return $steps_data;
    }

    /**
     * Get Management Data for space
     *
     * @return Array $data
     */
    protected function getManagementData()
    {
        $data  = array();

        $data['countries']      = Country::all()->pluck('long_name','short_name');
        $data['times_array']    = generateTimeRange('0:00', '23:00', '1 hour');
        $data['days_array']     = getDayOptions();

        $basics_data        = $this->getBasicManagementData();
        $setup_data         = $this->getSetupManagementData();
        $ready_to_host_data = $this->getReadyToHostManagementData();

        return array_merge($data, $basics_data, $setup_data, $ready_to_host_data);
    }

    /**
     * Get Space Data for new space
     *
     * @return Array $steps_data
     */
    protected function getNewSpaceData()
    {
        $data['result'] = new Space;
        $data['result']->size_type = 'sq_ft';
        $data['result']->space_address = new SpaceLocation;
        $data['space_status']       = new SpaceStepsStatus;
        $data['space_id']           = '';
        $data['space_step']         = 'basics';
        $data['step_num']           = 1;
        $data['prev_guest_access']  = array();
        $data['prev_amenities']     = array();
        $data['prev_services']      = array();
        $data['prev_space_style']   = array();
        $data['new_space']          = true;
        $data['activity_currency']  = session('currency') ?? view()->shared('default_currency')->code;
        return $data;
    }

    /**
     * Get Instance to save description
     *
     * @return Instance $instance SpaceDescription || SpaceDescriptionLang
     */
    protected function getDescriptionInstance($space_id, $lang_code)
    {
        if($lang_code == 'en') {
            $instance = SpaceDescription::firstOrCreate(['space_id' => $space_id]);
        }
        else {
            $instance = SpaceDescriptionLang::firstOrCreate(['space_id' => $space_id, 'lang_code' => $lang_code]);
        }
        return $instance;
    }

    /**
     * Manage Listing
     *
     * @param array $request    Post values from List Your Space first page
     * @param array $calendar   Instance of CalendarController
     * @return list your space main view file
     */
    public function manage_listing(Request $request, CalendarController $calendar)
    {
        // Values For Dropdowns
        $data           = $this->getManagementData();
        $data['steps']  = $this->getStepsData();

        if(!$request->space_id) {
            $data = array_merge($data, $this->getNewSpaceData());
            return view('list_your_space.main', $data);
        }

        $data['result'] = $space_details = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->findOrFail($request->space_id);

        $data['space_id']   = $space_details->id;
        $data['space_step'] = $request->page;
        $step_num           = $request->step_num ?? 1;

        $data['prev_guest_access']      = explode(',', $space_details->guest_access);
        $data['prev_amenities']         = explode(',', $space_details->amenities);
        $data['prev_services']          = explode(',', $space_details->services);
        $data['prev_space_style']       = explode(',', $space_details->space_style);
        $data['prev_special_feature']   = explode(',', $space_details->special_feature);
        $data['prev_space_rule']        = explode(',', $space_details->space_rules);

        $space_activities               = $space_details->space_activities;

        $activity_type_ids              = $space_activities->pluck('activity_type_id')->implode(',');
        $activity_ids                   = $space_activities->pluck('activities')->implode(',');
        $sub_activity_ids               = $space_activities->where('sub_activities','<>',null)->pluck('sub_activities')->implode(',');
        $data['prev_activity_type']     = explode(',', $activity_type_ids);
        $data['prev_activities']        = explode(',', $activity_ids);
        $data['prev_sub_activities']    = explode(',', $sub_activity_ids);

        $all_status = (array)$space_details->steps_status;
        $all_status = collect($all_status);

        $total_steps = $all_status->sum(function ($steps_status) {
            return $steps_status['total_steps'];
        });

        $comp_steps = $all_status->sum(function ($steps_status) {
            return $steps_status['completed_steps'];
        });

        $percent_completed = round(($comp_steps / $total_steps) * 100);
        $data['percent_completed']      = $percent_completed;

        $data['activity_price']         = ActivityPrice::with(['space_activities'])->where('space_id',$request->space_id)->get();
        $data['activity_currency'] = '';
        if($space_details->activity_price) {
            $data['activity_currency']      = $space_details->activity_price->getOriginal('currency_code');
        }

        if(!$data['activity_currency']) {
            $data['activity_currency'] = view()->shared('default_currency')->code;
        }

        $data['space_status']   = SpaceStepsStatus::where('space_id',$request->space_id)->first();

        if($request->page == 'ready_to_host' && $request->step_num == 5 && $request->wantsJson()) {
            $data_calendar     = json_decode($request['data']);
            $year              = optional($data_calendar)->year;
            $month             = optional($data_calendar)->month;
            $data['calendar']  = $calendar->generate($request->space_id, $year, $month);
            $data['month_calendar']  = $calendar->monthly_generate($request->space_id, $year, $month);
        }
        else {
            $data['calendar']  = $calendar->generate($request->space_id);
            $data['month_calendar']  = $calendar->monthly_generate($request->space_id);
        }
       
        $currency_details       = Currency::whereCode($data['activity_currency'])->first();
        $data['currency_symbol']= optional($currency_details)->original_symbol;
        $data['minimum_amount'] = currency_convert(DEFAULT_CURRENCY, $data['activity_currency'], MINIMUM_AMOUNT);

        if($request->page == 'home') {
            return view('list_your_space.home', $data);
        }

        $data['step_num']   = ($data['steps'][$data['space_step']]['total_steps'] >= $step_num) ? $step_num : 1;

        if($request->wantsJson()) {
            return view('list_your_space.'.$data['space_step'], $data);
        }
        session()->forget('ajax_redirect_url');

        return view('list_your_space.main', $data);
    }

    public function getAllTransDescription(Request $request)
    {
        $result = DB::select( DB::raw("select * from language where language.value not in (SELECT language.value FROM `language` JOIN space_description_lang on (space_description_lang.lang_code = language.value AND space_description_lang.space_id = '$request->space_id')) AND  language.status = 'Active' AND language.name != 'English'") );

        return json_encode($result);
    }

    /**
     * Update List Your Space Update Space Data
     *
     * @param array $request Post values from List Your Space Steps
     * @return json return_data
     */
    public function update_space(Request $request)
    {
        $return_data['success'] = 'true';
        if($request->space_id == '') {
            $new_space = true;

            $request->space_id = tap(new Space, function($space) {
                $space->user_id = auth()->id();
                $space->save();
            })->id;

            $return_data['redirect'] = route('manage_space', ['space_id' => $request->space_id,'page' => 'home']);
        }

        if($request->step == 'basics') {
            $this->saveSpaceData($request->space_id,$request->except('space_id','user_id','step','location_data'));
            $this->saveLocationData($request->space_id,$request->location_data);
        }

        if($request->step == 'setup') {
            $this->saveSpaceData($request->space_id,$request->except('space_id','user_id','step','description_data'));
            $this->saveDescriptionData($request->space_id,$request->description_data);
        }

        if($request->step == 'ready_to_host') {
            $this->saveSpaceData($request->space_id,$request->except('space_id','user_id','step','space_activities', 'activity_price','availability','activity_currency','security_deposit'));
            if(isset($request->space_activities)) {
                $this->saveActivitiesData($request->space_id,$request->space_activities);
            }
            if(isset($request->activity_price)) {
                $this->saveActivitiesPriceData($request->space_id,$request->activity_price);
            }
            if(isset($request->availability)) {
                $this->saveAvailabilityData($request->space_id,$request->availability);
            }
            $this->saveSecurityDepositData($request->space_id,$request->only('activity_currency','security_deposit'));
        }

        // Update Listed / Unlisted / Pending Status Based on steps Status
        $this->updateStatus($request->space_id);

        $space = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->find($request->space_id);

        if($request->step == 'ready_to_host') {
            if($space->steps_status->ready_to_host['remaining_steps'] == 0) {
                $this->updateSpaceStatus($request->space_id,'pricing',1);
            }
        }

        if(isset($new_space)) {
            $this->updateSpaceStatus($request->space_id,'basics',1);
        }

        $return_data['space'] = $space;
        return json_encode($return_data);
    }

    /**
     * Save List Your Space Description Data
     *
     * @param String $space_id
     * @param Array $step_data
     * @return Void
     */
    protected function saveDescriptionData($space_id, $desc_data)
    {
        foreach ($desc_data as $lang_code => $desc) {
            $desciption = $this->getDescriptionInstance($space_id, $lang_code);
            $exclude_cols = ['language_name','space_rules'];
            if($lang_code == 'en') {
                array_push($exclude_cols,'name','summary');
            }

            $desc = array_except($desc,$exclude_cols);

            foreach($desc as $key => $value) {
                $desciption->$key = $value;
            }
            $desciption->save();
        }
    }

    /**
     * Update List Your Space Steps Count, It will calling from ajax update functions
     *
     * @param int $id    Room Id
     * @return true
     */ 
    public function update_status($id)
    {
        $result_rooms = Space::whereId($id)->first();

        $rooms_status = RoomsStepsStatus::find($id);
        $rooms_status->description  = 0;
        $rooms_status->basics       = 0;
        $rooms_status->photos       = 0;
        $rooms_status->pricing      = 0;
        $rooms_status->calendar     = 0;

        if($result_rooms->name != '' && $result_rooms->summary != '' ) {
            $rooms_status->description = 1;
        }

        $bed_types       = DB::table('bed_type')->where('status','Active')->select('id')->get()->pluck('id');
        $tot_bed_count = RoomsBeds::where('space_id', $id)->where('count', '>', 0)->whereIn('bed_id',$bed_types)->where('bed_room_no','!=','common')->get()->count();

        if($tot_bed_count > 0) {
            $rooms_status->basics = 1;
        }

        $photos_count = SpacePhotos::where('space_id', $id)->count();
        if($photos_count != 0) {
            $rooms_status->photos = 1;
        }

        $price = RoomsPrice::find($id);
        if($price != NULL && $price->night != 0 ) {
            $rooms_status->pricing = 1;
        }

        if($result_rooms->calendar_type != NULL) {
            $rooms_status->calendar = 1;
        }

        $rooms_status->save(); // Update Rooms Steps Count

        if($result_rooms->steps_count > 0 && $result_rooms->status != ''){
            $result_rooms->status = 'Unlisted';
            $result_rooms->verified = 'Pending';
            $result_rooms->save();

            //send awaiting for approval email to admin & host
            $this->notifyUser($id,'Approval');
        }

        if($result_rooms->steps_count == 0 && $result_rooms->status == 'Unlisted' ){
            $result_rooms->status = 'Pending';
            $result_rooms->verified = 'Pending';
            $result_rooms->save();

            $this->notifyUser($id,'Approval');
        }
        elseif ($result_rooms->steps_count == 0 && ($result_rooms->status == '' || $result_rooms->status == NULL)) {
            $this->notifyUser($id,'Approval');
        }

        return true;
    }

    /**
     * Ajax List Your Space Add Photos, it will upload multiple files
     *
     * @param array $request    Input values
     * @return json rooms_photos table result
     */
    public function upload_photos(Request $request,EmailController $email_controller)
    {
        $all_photos = $request->photos;
        if(!isset($all_photos) || count($all_photos) == 0) {
            return json_encode(array('error_title' => ' Photo Error', 'error_description' => 'No Photos Selected'));
        }

        $space_id       = $request->space_id;
        $return_data    = array();
        $upload_errors  = array();
        $last_photo  = SpacePhotos::whereSpaceId($space_id)->latest('order_id')->first();
        $last_order_id  = optional($last_photo)->order_id;

        foreach($all_photos as $key => $image) {

            $target_dir = '/images/space/'.$space_id;

            $compress_size = array(
                ['quality' => 80, 'width' => 1440, 'height' => 960],
                ['quality' => 80, 'width' => 1349, 'height' => 402],
                ['quality' => 80, 'width' => 450, 'height' => 250],
            );

            $upload_result = uploadImage($image,$target_dir,$key,$compress_size);

            if($upload_result['status'] != 'Success') {
                $error_description = $upload_result['status_message'];
                if(count($all_photos) > 1) {
                    $error_description = trans('messages.lys.invalid_image');
                }
                $upload_errors = array('error_title' => ' Photo Error', 'error_description' => $error_description);
            }
            else {
                $photos             = new SpacePhotos;
                $photos->space_id   = $space_id;
                $photos->name       = $upload_result['file_name'];
                $photos->source     = $upload_result['upload_src'];
                $photos->order_id   = ++$last_order_id;
                $photos->save();
            }
        }

        $this->notifyUser($space_id,'SpaceUpdated','Photos');

        $return_data['photos_list'] = SpacePhotos::where('space_id',$space_id)->ordered()->get();
        $return_data['error'] = $upload_errors;

        if($return_data['photos_list']->count() > 0) {
            $this->updateSpaceStatus($space_id,'photos',1);
        }
        else {
            $this->updateSpaceStatus($space_id,'photos',0);
        }

        $return_data['space'] = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->find($space_id);

        return response()->json($return_data);
    }

    public function change_photo_order(Request $request)
    {
        $return_data['success'] = 'true';

        $space_id = $request->space_id;
        $start = 1;
        foreach($request->image_order as $image_id) {
            SpacePhotos::where('id',$image_id)->update(['order_id' => $start++]);
        }

        $return_data['photos_list'] = SpacePhotos::where('space_id', $space_id)->ordered()->get();

        return response()->json($return_data);
    }

    /**
     * Ajax List Your Space Delete Photo
     *
     * @param array $request    Input values
     * @return json success, steps_count
     */
    public function delete_photo(Request $request,EmailController $email_controller)
    {
        $space_id        = $request->space_id;
        $photos          = SpacePhotos::find($request->photo_id);
        $return_data['success'] = 'false';
        if($photos != NULL){
            $photos->delete();
            $return_data['success'] = "true";
        }

        $return_data['photos_list'] = SpacePhotos::where('space_id',$space_id)->ordered()->get();

        if($return_data['photos_list']->count() > 0) {
            $this->updateSpaceStatus($space_id,'photos',1);
        }
        else {
            $this->updateSpaceStatus($space_id,'photos',0);
        }

        $this->notifyUser($space_id,'SpaceUpdated','Photos');

        $return_data['space'] = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->find($space_id);

        return response()->json($return_data);
    }

    /**
     * Get Photos List of Space
     *
     * @param array $request    Input values
     * @return json rooms_photos table result
     */
    public function photos_list(Request $request)
    {
        $space_photos = SpacePhotos::where('space_id', $request->space_id)->ordered()->get();
        return response()->json($space_photos);
    }

    /**
     * Ajax List Your Space Photos Highlights
     *
     * @param array $request    Input values
     * @return json success
     */
    public function photo_highlights(Request $request)
    {
        $photos = SpacePhotos::find($request->photo_id);
        $photos->highlights = $request->data;
        $photos->save();

        return json_encode(['success'=>'true']);
    }

    /**
     * Load Space Detail View
     *
     * @param array $request    Input values
     * @return view space_detail
     */
    public function space_details(Request $request)
    {
        $data['space_id']   = $request->id;
        $user_id            = auth()->id();

        $data['result'] = $space_details = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->findOrFail($request->id);

        $data['is_wishlist']  = SavedWishlists::where('user_id',$user_id)->where('space_id',$request->id)->count();

        $data['user_details'] = User::find($space_details->user_id);

        if($space_details->user_id != $user_id && ($data['user_details']->status != 'Active' || $space_details->status != 'Listed') ) {
            abort('404');
        }

        if($space_details->user_id != $user_id && $space_details->status == 'Listed' ) {
            $space_details->views_count += 1;
            $space_details->save();
        }

        $guest_access   = explode(',', $space_details->guest_access);
        $amenities      = explode(',', $space_details->amenities);
        $services       = explode(',', $space_details->services);
        $space_style    = explode(',', $space_details->space_style);
        $special_feature= explode(',', $space_details->special_feature);
        $space_rules    = explode(',', $space_details->space_rules);

        $data['amenities']       = Amenities::whereIn('id', $amenities)->get();
        $data['guest_access']    = GuestAccess::whereIn('id', $guest_access)->get();
        $data['services']        = Services::whereIn('id', $services)->get();
        $data['space_style']     = Style::whereIn('id', $space_style)->get();
        $data['special_feature'] = SpecialFeature::whereIn('id', $special_feature)->get();
        $data['space_rules']     = SpaceRule::whereIn('id', $space_rules)->get();

        $data['space_photos']     = $space_details->space_photos;
        $data['space_activities'] = $space_details->space_activities;
        $data['space_availabilities'] = $space_details->space_availabilities;

        $activity_types = $space_details->space_activities->implode('activity_type_id',',');
        $activities     = $space_details->space_activities->implode('activities',',');
        $sub_activities = $space_details->space_activities->implode('sub_activities',',');

        $activity_types = explode(',',$activity_types);
        $activities     = explode(',',$activities);
        $sub_activities = explode(',',$sub_activities);

        $data['activities']  = Activity::with(['sub_activities' => function($query) use ($sub_activities) {
            $query->whereIn('id',$sub_activities);
        }])->whereIn('activity_type_id',$activity_types)->whereIn('id',$activities)->activeOnly()->get();

        $space_address            = $space_details->space_address;
        $latitude                 = $space_address->latitude;
        $longitude                = $space_address->longitude;

        $data['booking_date_times'] = array('start_date' => '');
        $data['guests']             = $request->guests;
        $data['activity_type_selected'] = $request->activity_type;
        $data['can_book_multiple']  = $space_details->canBookMultipleDay();
        $data['booking_period']     = $data['can_book_multiple'] ? 'Multiple' : 'Single';

        if($request->checkin != '' && $request->checkout != '') {
            $checkin = getDateObject($request->checkin);
            $checkout = getDateObject($request->checkout);
            $date_times['start_date'] = $checkin->format(PHP_DATE_FORMAT);
            $date_times['formatted_start_date'] = $checkin->format('Y-m-d');
            $date_times['end_date']   = $checkout->format(PHP_DATE_FORMAT);
            $date_times['formatted_end_date']   = $checkout->format('Y-m-d');
            $date_times['start_time'] = $request->start_time;
            $date_times['end_time']   = $request->end_time;
            $date_times['start_week_day']= $checkin->format('w');
            $date_times['end_week_day']= $checkin->format('w');

            $data['booking_date_times'] = $date_times;
            $data['booking_period']     = ($request->checkin == $request->checkout) ? 'Single' : 'Multiple';

            $data['guests']          = '1';
            if($space_details->number_of_guests >= $request->guests) {
                $data['guests'] = $request->guests;
            }
        }

        $data['similar']    = $this->getSimilarListings($latitude, $longitude, $request->id);
        $activities_price   = ActivityPrice::whereSpaceId($request->id)->where('hourly','>',0)->first();
        $data['currency_symbol']  = html_entity_decode(optional($activities_price)->currency_symbol);
        $data['default_price']  = optional($activities_price)->hourly;
        $data['title']  =   $space_details->name.' in '.$space_details->space_address->city;

        $data['user_time_zone'] = (\Auth::guest())?'':\Auth::user()->timezone;

        return view('space.space_detail', $data);
    }

    /**
     * Ajax Space Calendar Blocked Dates
     *
     * @param array $request    Input values
     * @return json calendar results
     */
    public function space_calendar(Request $request)
    {
        // For coupon code destroy
        $this->forgetCoupon();

        $c_date = date('Y-m-d');

        $space_id = $request->space_id;
        $not_available_days = SpaceAvailability::where('space_id', $space_id)->whereStatus('Closed')->get()->pluck('day');

        $open_availabilities = SpaceAvailability::with('availability_times')->where('space_id', $space_id)->whereStatus('Open')->get();
        $not_available_times = array();
        $times_array    = array_keys(view()->shared('times_array'));

        foreach ($open_availabilities as $availabilities) {
            $day_num = $availabilities->day;
            $temp_times = array();
            foreach ($availabilities->availability_times as $availability_time) {
                $temp_times = array_merge($temp_times, $availability_time->available_times);
            }
            $not_available = array_values(array_diff($times_array,$temp_times));
            $not_available_times[$day_num] = $not_available;
        }

        $blocked_times  = array();
        $blocked_days  = array();
        $not_available_dates = array();

        $space_calendar = SpaceCalendar::where('space_id', $space_id)->where('end_date','>',$c_date)->onlyNotAvailable()->get();
        foreach ($space_calendar as $calendar) {
            $between_times = $calendar->between_times;
            array_shift($between_times);
            array_pop($between_times);
            $blocked_times[$calendar->start_date] = $between_times;
        }

        $space_bookings = ReservationTimes::where('space_id', $space_id)->where('end_date','>',$c_date)->onlyNotAvailable()->get();
        foreach ($space_bookings as $booking) {
            if($booking->booking_period == 'Single') {
                $between_times = getTimes($booking->start_time, $booking->end_time);
                array_shift($between_times);
                array_pop($between_times);
                $blocked_times[$booking->start_date] = $between_times;
            }
            else {
                $between_days = getDays($booking->start_date,$booking->end_date);
                foreach ($between_days as $day) {
                    if($day == reset($between_days)) {
                        $between_times = getTimes($booking->start_time, '23:59:00');
                        $blocked_times[$day] = $between_times;
                    }
                    else if($day == end($between_days)) {
                        $between_times = getTimes('00:00:00', $booking->end_time);
                        $blocked_times[$day] = $between_times;
                    }
                    else {
                        // $between_times = getTimes('00:00:00', '23:59:00');
                        // $blocked_times[$day] = $between_times;
                        $not_available_dates[] = $day;
                    }
                }
            }
        }

        return response()->json(compact('not_available_days','not_available_times', 'blocked_times', 'blocked_days','not_available_dates'));
    }

    public function getSpaceActivity(Request $request)
    {
        $space_id = $request->space_id;

        $space_details = Space::with('space_activities.activity_type','space_activities.activity_price')->findOrFail($space_id);

        $activity_types = $space_details->space_activities->implode('activity_type_id',',');
        $activities     = $space_details->space_activities->implode('activities',',');
        $sub_activities = $space_details->space_activities->implode('sub_activities',',');

        $activity_types = explode(',',$activity_types);
        $activities     = explode(',',$activities);
        $sub_activities = explode(',',$sub_activities);

        $space_activities  = Activity::with(['sub_activities' => function($query) use ($sub_activities) {
            $query->whereIn('id',$sub_activities);
        }])->whereIn('activity_type_id',$activity_types)->whereIn('id',$activities)->activeOnly()->get();

        return response()->json(compact('space_activities'));
    }

    /**
     * Ajax Rooms Detail Price Calculation while choosing date
     *
     * @param array $request    Input values
     * @return json price list
     */
    public function price_calculation(Request $request)
    {
        $this->forgetCoupon();

        $price_data = $request->only(['event_type','booking_date_times','number_of_guests','booking_period']);

        $payment_helper = new PaymentHelper;
      
        return $payment_helper->price_calculation($request->space_id, (Object)$price_data);
    }

    /**
     * Ajax Update List Your Space Calendar Dates Price, Status
     *
     * @param array $request    Input values
     * @return empty
     */
    public function calendar_edit(Request $request)
    {  
        $return_data['success'] = 'true';
        $space_id   = $request->space_id;
        $start_date = date('Y-m-d', strtotime($request->start_date));
        $start_time = $request->start_time;

        $end_date   = date('Y-m-d', strtotime($request->end_date));
        $end_time   = $request->end_time;
      
        if($request->type=="Month"){
        $old_start=SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->start_date)->where('end_date','!=',$request->start_date)->first(); 
        $old_end=SpaceCalendar::where('space_id',$request->space_id)->where('end_date',$request->start_date)->where('start_date','!=',$request->start_date)->first();
        $old_end2=SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->end_date)->where('end_date','!=',$request->start_date)->first();
        $same_date=SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->start_date)->where('end_date',$request->start_date)->get();
        $same_end__date=SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->end_date)->where('end_date',$request->end_date)->get();
        $coverd_date=SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->start_date)->where('end_date',$request->end_date)->get();
        $between_date=SpaceCalendar::where('space_id',$request->space_id)->where('start_date','<',$request->start_date)->where('end_date','>',$request->start_date)->first();
        $between_date_beg_end=SpaceCalendar::where('space_id',$request->space_id)->where('start_date','>=',$request->start_date)->where('end_date','<=',$request->end_date)->get();
       

         $c_start_time = '00:00:00'; 
         $c_end_time = '23:59:00';
         if($between_date){
         $stack_data=$between_date;
         $sdate= new DateTime(date('Y-m-d',strtotime("-1 days", strtotime($request->start_date))));
         $estartdate= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($request->end_date))));
         $enddate= new DateTime(date('Y-m-d',strtotime($between_date->end_date)));
         $etime=$stack_data->end_time;
         $notes=$stack_data->notes;
         $status=$stack_data->status;
         $source=$stack_data->source;
         $espace_id=$stack_data->space_id;
         $between_date->end_date = $sdate->format('Y-m-d');
         $between_date->end_time = $c_end_time;
         $between_date->save();
         $store=new SpaceCalendar;
         $store->start_date=$estartdate->format('Y-m-d');
         $store->end_date=$enddate->format('Y-m-d');
         $store->start_time=$c_start_time;
         $store->end_time=$etime;
         $store->notes=$notes;
         $store->status=$status;
         $store->source=$source;
         $store->space_id=$espace_id;
         $store->save();
        }  
        
       if(!$coverd_date->first()){  
       if($same_date->first()&&!$same_end__date->first()){
        if($between_date_beg_end->first())
        {
            SpaceCalendar::where('space_id',$request->space_id)->where('start_date','>=',$request->start_date)->where('end_date','<=',$request->end_date)->forceDelete();
        }
       if($same_date){
        SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->start_date)->where('end_date',$request->start_date)->forceDelete();           
       }
       if($old_start){ 
      
         $sdate1= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($old_start->start_date))));
         $old_start->start_date = $sdate1->format('Y-m-d');
         $old_start->start_time = $c_start_time;
         $old_start->save();
       }
       if($old_end){        
         $sdate2= new DateTime(date('Y-m-d',strtotime("-1 days", strtotime($old_end->end_date))));
         $old_end->end_date = $sdate2->format('Y-m-d');
         $old_end->end_time = $c_end_time;       
         $old_end->save();        
       }
       if($request->start_date!=$request->end_date)
        if($old_end2){       
         $sdate1= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($old_start->start_date))));
         $old_end2->start_date = $sdate1->format('Y-m-d');
         $old_end2->start_time = $c_start_time;
         $old_end2->save();
       }  
       }
       else
       {   

         SpaceCalendar::where('space_id',$request->space_id)->where('start_date','>=',$request->start_date)->where('end_date','<=',$request->end_date)->forceDelete(); 
      if($same_date){
        SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->start_date)->where('end_date',$request->start_date)->forceDelete();           
       }
       if($old_start){ 
      
         $sdate1= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($old_start->start_date))));
         $old_start->start_date = $sdate1->format('Y-m-d');
         $old_start->start_time = $c_start_time;
         $old_start->save();
       }
       if($old_end){        
         $sdate2= new DateTime(date('Y-m-d',strtotime("-1 days", strtotime($old_end->end_date))));
         $old_end->end_date = $sdate2->format('Y-m-d');
         $old_end->end_time = $c_end_time;       
         $old_end->save();        
       } 
       if($request->start_date!=$request->end_date)
        if($old_end2){       
         $sdate1= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($old_start->start_date))));
         $old_end2->start_date = $sdate1->format('Y-m-d');
         $old_end2->start_time = $c_start_time;
         $old_end2->save();
       }  
       }
       }
       else{  
         if($same_date->first()&&!$same_end__date->first()){

        if($old_start){            
        if($old_start->start_date!=$request->start_date && $old_start->end_date!=$request->end_date){
        if($between_date_beg_end->first())
        {
            SpaceCalendar::where('space_id',$request->space_id)->where('start_date','>=',$request->start_date)->where('end_date','<=',$request->end_date)->forceDelete();
        }
        if($old_start){ 
      
         $sdate1= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($old_start->start_date))));
         $old_start->start_date = $sdate1->format('Y-m-d');
         $old_start->start_time = $c_start_time;
         $old_start->save();
       }
       if($old_end){
         $sdate2 = new DateTime(date('Y-m-d',strtotime("-1 days", strtotime($old_end->end_date))));
         $old_end->end_date = $sdate2->format('Y-m-d');
         $old_end->end_time = $c_end_time;
         $old_end->save();        
       }
       if($request->start_date!=$request->end_date)
        if($old_end2){       
         $sdate1= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($old_start->start_date))));
         $old_end2->start_date = $sdate1->format('Y-m-d');
         $old_end2->start_time = $c_start_time;
         $old_end2->save();
       }  
         SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->start_date)->where('end_date',$request->end_date)->forceDelete();
       }
        else{
       
       
        if($between_date_beg_end->first())
        {
            SpaceCalendar::where('space_id',$request->space_id)->where('start_date','>=',$request->start_date)->where('end_date','<=',$request->end_date)->forceDelete();
        }
      if($same_date){
        SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->start_date)->where('end_date',$request->start_date)->forceDelete();           
       }
        if($old_start){       
         $sdate1= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($old_start->start_date))));
         $old_start->start_date = $sdate1->format('Y-m-d');
         $old_start->start_time = $c_start_time;
         $old_start->save();
       }
       if($old_end){
         $sdate2 = new DateTime(date('Y-m-d',strtotime("-1 days", strtotime($old_end->end_date))));
         $old_end->end_date = $sdate2->format('Y-m-d');
         $old_end->end_time = $c_end_time;
         $old_end->save();        
       }
       if($request->start_date!=$request->end_date)
       if($old_end2){       
         $sdate1= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($old_start->start_date))));
         $old_end2->start_date = $sdate1->format('Y-m-d');
         $old_end2->start_time = $c_start_time;
         $old_end2->save();
       }
     }
     }
     }
     else
     {
      if($same_date->first()&&!$same_end__date->first()){
      if($same_date){
        SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->start_date)->where('end_date',$request->start_date)->forceDelete();           
       }}else{
     
        if($coverd_date->first())
        {
        SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->start_date)->where('end_date',$request->end_date)->forceDelete();
        }
        
        SpaceCalendar::where('space_id',$request->space_id)->where('start_date','>=',$request->start_date)->where('end_date','<=',$request->start_date)->forceDelete();
         if($same_date){
        SpaceCalendar::where('space_id',$request->space_id)->where('start_date',$request->start_date)->where('end_date',$request->start_date)->forceDelete();           
       }
       if($old_start){ 
      
         $sdate1= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($old_start->start_date))));
         $old_start->start_date = $sdate1->format('Y-m-d');
         $old_start->start_time = $c_start_time;
         $old_start->save();
       }
       if($old_end){        
         $sdate2= new DateTime(date('Y-m-d',strtotime("-1 days", strtotime($old_end->end_date))));
         $old_end->end_date = $sdate2->format('Y-m-d');
         $old_end->end_time = $c_end_time;       
         $old_end->save();        
       } 
        if($request->start_date!=$request->end_date)
        if($old_end2){       
         $sdate1= new DateTime(date('Y-m-d',strtotime("+1 days", strtotime($old_start->start_date))));
         $old_end2->start_date = $sdate1->format('Y-m-d');
         $old_end2->start_time = $c_start_time;
         $old_end2->save();
       }
       }
       
     }
     }}
       
    
        $is_reservation = $this->getBookingsCount($space_id,$start_date,$end_date,$start_time,$end_time);
        if($is_reservation == 0) {
            $check_data = [
                'space_id'=> $space_id,
                'start_date'  => $start_date,
                'end_date'   => $end_date,
                'start_time'   => $start_time,
                'end_time'   => $end_time,
            ];

            $data = [
                'space_id'=> $space_id,
                'status'  => $request->status,
                'notes'   => $request->notes,
                'source'  => 'Calendar'
            ];
            SpaceCalendar::updateOrCreate($check_data, $data);
        }
        // Delete All Available Records without notes
        SpaceCalendar::whereSpaceId($space_id)->Onlyavailable()->whereSource('Calendar')->where('notes','')->delete();

        $this->notifyUser($space_id,'SpaceUpdated','Calendar');
        return response()->json($return_data);
    }

    public function get_lang_details(Request $request)
    {
        $data = SpaceDescriptionLang::with(['language'])->where('space_id', $request->space_id)->get();
        return json_encode($data);
    }

    public function get_language_list()
    {
        $data = Language::translatable()->where('name', '!=', 'English')->get();
        return json_encode($data);
    }

    public function add_description(Request $request)
    {
        $language = new SpaceDescriptionLang;
        $language->space_id       = $request->space_id;
        $language->lang_code      = $request->lang_code;
        $language->name           = '';
        $language->summary        = '';
        $language->save();

        $result = SpaceDescriptionLang::with(['language'])->where('space_id', $request->space_id)->where('lang_code', $request->lang_code)->first();
        $space_desc_lang = array();
        $space_desc_lang['language_name'] = $result->language_name;
        $space_desc_lang['name'] = $result->name;
        $space_desc_lang['summary'] = $result->summary;

        return json_encode($space_desc_lang);
    }

    public function delete_description(Request $request)
    {
        SpaceDescriptionLang::where('space_id', $request->space_id)->where('lang_code', $request->current_tab)->delete();
        return json_encode(['success'=>'true']);
    } 

    /**
     * Update Calendar Special Price After update Room Currency
     *
     * @param int $id    Room Id
     * @param string $from    From Currency
     * @param string $to    To Currency
     * @return true
     */ 
    public function update_calendar_currency($space_id,$from,$to)
    {
        $calendar_details = Calendar::where('space_id',$space_id)->where('date','>=',date('Y-m-d'))->get();
        foreach ($calendar_details as $calendar) {
            $new_amount = currency_convert($from, $to, $calendar->price);
            $calendar->price = $new_amount;
            $calendar->save();
        }
    }

    /**
     * Duplicate a Space
     *
     * @param array $request  Input values
     * @return redirect to Rooms view
     */
    public function duplicate($space_id)
    {
        $org_space = Space::find($space_id);
        $dup_space = $org_space->replicate(['popular','views_count','admin_status']);
        $dup_space->status = 'Pending';
        // Create New Space without trigger any events
        $dup_space->saveQuietly();

        $new_space_id = $dup_space->id;

        // Duplicate Space step status Table
        $org_step_status = SpaceStepsStatus::where('space_id',$space_id)->first();
        $dup_step_status = $org_step_status->replicate();
        $dup_step_status->space_id = $new_space_id;
        $dup_step_status->save();

        // Duplicate Space Location Table
        $org_address = SpaceLocation::where('space_id',$space_id)->first();
        $dup_address = $org_address->replicate();
        $dup_address->space_id = $new_space_id;
        $dup_address->save();

        // Duplicate Space Activities Table
        $org_activity = SpaceActivities::with('activity_price')->where('space_id',$space_id)->get();
        foreach($org_activity as $activity) {
            $dup_activity = $activity->replicate();
            $dup_activity->space_id = $new_space_id;
            $dup_activity->save();

            $activity_price = $activity->activity_price;            
            $dup_act_price = $activity_price->replicate();
            $dup_act_price->space_id = $new_space_id;
            $dup_act_price->activity_id = $dup_activity->id;
            $dup_act_price->saveQuietly();
        }

        // Duplicate Space Availability Table
        $day_options = getDayOptions();
        foreach ($day_options as $day_num => $day_name) {
            $org_availability = SpaceAvailability::with('availability_times')->where('space_id',$space_id)->where('day',$day_num)->first();
            $dup_availability = $org_availability->replicate();
            $dup_availability->space_id = $new_space_id;
            $dup_availability->save();
        }

        // Duplicate Space Description Table
        $org_description = SpaceDescription::where('space_id',$space_id)->first();
        $dup_desc = $org_description->replicate();
        $dup_desc->space_id = $new_space_id;
        $dup_desc->save();

        // Duplicate Space Description Lang Table
        $space_desc_lang = SpaceDescriptionLang::where('space_id',$space_id)->get();
        foreach($space_desc_lang as $desc_lang){
            $dup_lang_desc = $desc_lang->replicate();
            $dup_lang_desc->space_id = $new_space_id;
            $dup_lang_desc->save();
        }

        // Duplicate Space Price Table
        $org_price = SpacePrice::find($space_id);
        if($org_price) {
            $dup_price = $org_price->replicate();
            $dup_price->space_id = $new_space_id;
            $dup_price->save();
        }

        // Duplicate Space Photos Table
        $old_path = public_path().'/images/space/'.$space_id;
        $new_path = public_path().'/images/space/'.$new_space_id;
        if (\File::isDirectory($old_path)) {
            \File::copyDirectory( $old_path, $new_path);
        }
        $org_photos = SpacePhotos::where('space_id',$space_id)->get();
        foreach($org_photos as $org_photo) {
            $dup_photo = $org_photo->replicate();
            $dup_photo->space_id = $new_space_id;
            $dup_photo->save();
        }

        // Duplicate Space Calendar Table
        $org_calendars = SpaceCalendar::where('space_id', $space_id)->where('source','Calendar')->get();
        foreach($org_calendars as $org_calendar) {
            $dup_calendar = $org_calendar->replicate();
            $dup_calendar->space_id = $new_space_id;
            $dup_calendar->save();
        }

        flash_message('success', 'Space Added Successfully');
        return redirect()->back();
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
        return response()->json(['status' => 'Success', 'minimum_amount' => $minimum_amount, 'currency_code' => $request->currency_code, 'currency_symbol' => $currency_symbol]);
    }
}