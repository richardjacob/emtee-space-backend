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

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\SpaceDataTable;
use App\Models\KindOfSpace;
use App\Models\GuestAccess;
use App\Models\Amenities;
use App\Models\Services;
use App\Models\Style;
use App\Models\SpecialFeature;
use App\Models\SpaceRule;
use App\Models\ActivityType;
use App\Models\SubActivity;
use App\Models\Activity;
use App\Models\Space;
use App\Models\User;
use App\Models\Country;
use App\Models\SpaceLocation;
use App\Models\SpacePhotos;
use App\Models\SpaceDescription;
use App\Models\SpaceDescriptionLang;
use App\Models\SpacePrice;
use App\Models\AvailabilityTimes;
use App\Models\SpaceAvailability;
use App\Models\SpaceActivities;
use App\Models\ActivityPrice;
use App\Models\Currency;
use App\Models\Reservation;
use App\Models\SpaceStepsStatus;
use App\Models\Messages;
use App\Http\Helper\PaymentHelper;
use App\Http\Start\Helpers;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EmailController;
use Validator;
use Session;
use DB;

class SpaceController extends Controller
{


    protected $payment_helper; // Global variable for Helpers instance   

    protected $helper;  // Global variable for instance of Helpers

    public function __construct(PaymentHelper $payment)
    {
        $this->payment_helper = $payment;        
        $this->helper = new Helpers;
        $this->view_data['main_title'] = $this->main_title = 'Space';
        $this->view_data['base_url'] = $this->base_url = route('admin.space');
        $this->view_data['add_url']  = optional(auth()->guard('admin')->user())->can('add_space') ? route('admin.add_space') : '';
        $this->view_data['base_view_path'] = $this->base_view_path = 'admin.space.';
    }

    /**
     * Load Datatable for Rooms
     *
     * @param array $dataTable  Instance of RoomsDataTable
     * @return datatable
     */
    public function index(SpaceDataTable $dataTable)
    {
        return $dataTable->render('admin.common.view',$this->view_data);
    }

    /**
     * Add a New Room
     *
     * @param array $request  Input values
     * @return redirect     to Rooms view
     */
    public function add(Request $request)
    {
        if(!$_POST)
        {
            $data = $this->spaceCommonData();
            $data['users_list']    = User::select('id',DB::raw('CONCAT(id," - ",first_name) AS first_name'))->whereStatus('Active')->pluck('first_name','id');
            $data['times_array']    = generateTimeRange('0:00', '23:00', '1 hour');
            return view('admin.space.add', $data);
        }
        else if($_POST)
        {      
            $space = new Space;
            $space->user_id = $request->user_id;
            $space->status = 'Listed';
            $space->admin_status = 'Approved';
            $space->save();

            $space_id = $space->id;

            $space_data = $request->only('no_of_workstations','shared_or_private','renting_space_firsttime','fully_furnished','space_type','number_of_rooms','number_of_restrooms','sq_ft','number_of_guests','services_extra','booking_type','cancellation_policy');
            $space_data['guest_access'] = $this->getStringForArray($request->guest_accesses);
            $space_data['amenities']    = $this->getStringForArray($request->amenities);
            $space_data['services']     = $this->getStringForArray($request->services);
            $space_data['space_style']  = $this->getStringForArray($request->space_styles);
            $space_data['special_feature'] = $this->getStringForArray($request->special_featureses);
            $space_data['space_rules']  = $this->getStringForArray($request->space_rules);

            $this->saveSpaceData($space_id,$space_data);
            
            //save location
            $this->saveLocationData($space_id,$request->all());
            
            //upload photos
            $this->upload_photos($space_id);
            
            //description save
            $this->saveDescriptionData($space_id,$request->all());
            
            //activity save
            $this->saveActivitiesData($space_id,$request->space_activities);
            
            //activity price save
            $activity_price = json_decode($request->activity_price,true);
            foreach ($activity_price as $price) {
                $price_data = $price['activity_price'];

                $activity_price_data = ActivityPrice::where('space_id',$space_id)
                        ->whereHas('space_activities',function($q) use ($price_data){
                            $q->where('activity_type_id',$price_data['activity_id']);
                        })->first();
                $activity_price_data->currency_code = $request->currency_code;
                $activity_price_data->min_hours = $price_data['min_hours'];
                $activity_price_data->hourly = $price_data['hourly'];
                $activity_price_data->full_day = $price_data['full_day'];
                $activity_price_data->weekly = $price_data['weekly'];
                $activity_price_data->monthly = $price_data['monthly'];
                $activity_price_data->save();
            }

            //availability save
            $this->saveAvailabilityData($space_id,$request->availabilities);
            
            //rules save process 
            $this->saveSecurityDepositData($space_id,$request->only('currency_code','security_deposit'));

            $space = SpaceStepsStatus::find($space_id);
            $space->basics = 1;
            $space->description = 1;
            $space->location = 1;
            $space->photos = 1;
            $space->pricing = 1;
            $space->save();

            flash_message('success', 'Space Added Successfully'); // Call flash message function
            return redirect(ADMIN_URL.'/spaces');
        }
    }

    /**
     * Update Room Details
     *
     * @param array $request    Input values
     * @return redirect     to Rooms View
     */
    public function update(Request $request,$space_id,CalendarController $calendar)
    {


        if($request->isMethod('GET')) {
            $data = $this->spaceCommonData($space_id);
            if(!$data['status']) {
                flash_message('error',$data['status_message']);
                return redirect($this->base_url);
            }
            $data['calendar']  = $calendar->generate($space_id);
            $data['month_calendar']  = $calendar->monthly_generate($space_id);
            $data['times_array']    = generateTimeRange('0:00', '23:00', '1 hour');
            return view('admin.space.edit', $data);
        }
        $space = Space::find($space_id);
        if ($request->submit == 'space_type') {
            $this->saveSpaceData($space_id,$request->only('space_type'));
        }
        elseif($request->submit == 'basics') {
            $this->saveSpaceData($space_id,$request->only('no_of_workstations','shared_or_private','renting_space_firsttime','fully_furnished','number_of_rooms','number_of_rooms','number_of_restrooms','sq_ft', 'size_type'));
        }
        else if($request->submit == 'guest_access') {
            $data['guest_access'] = $this->getStringForArray($request->guest_accesses);
            $this->saveSpaceData($space_id,$data);
        }
        elseif($request->submit == 'guest_count') {
            $this->saveSpaceData($space_id,$request->only('number_of_guests'));
        }
        elseif($request->submit == 'amenities') {
            $data['amenities'] = $this->getStringForArray($request->amenitieses);
            $this->saveSpaceData($space_id,$data);
        }
        elseif($request->submit == 'services') {
            $data['services'] = $this->getStringForArray($request->services);
            $data['services_extra']=$request->services_extra;
            $this->saveSpaceData($space_id,$data);
        }
        elseif($request->submit == 'location') {
            $this->saveLocationData($space_id,$request->all());
        }
        elseif($request->submit == 'photos') {
            $this->change_photo_order($space_id,$request->hidden_image);
            $this->upload_photos($space_id);
        }
        elseif($request->submit == 'style') {
            $data['space_style'] = $this->getStringForArray($request->space_styles);
            $this->saveSpaceData($space_id,$data);
        }
        elseif($request->submit == 'special_features') {
            $data['special_feature'] = $this->getStringForArray($request->special_featureses);
            $this->saveSpaceData($space_id,$data);
        }
        elseif($request->submit == 'space_rules') {
            $data['space_rules'] = $this->getStringForArray($request->space_rules);
            $this->saveSpaceData($space_id,$data);
        }
        elseif($request->submit == 'description') {
            $this->saveDescriptionData($space_id,$request->all());
        }
        elseif($request->submit == 'activity') {
            $this->saveActivitiesData($space_id,$request->space_activities);
        }
        elseif($request->submit == 'price') {
            $this->saveActivitiesPriceData($space_id,$request->activity_price);
            $space_price = SpacePrice::firstOrNew(['space_id' => $space_id]);
            $space_price->space_id = $space_id;
            $space_price->currency_code = $request->currency_code;
            $space_price->save();
        }
        elseif($request->submit == 'availability') {
            $this->saveAvailabilityData($space_id,$request->availabilities);
        }
        elseif($request->submit == 'rules') {
            $this->saveSpaceData($space_id,$request->only('booking_type','cancellation_policy'));
            $this->saveSecurityDepositData($space_id,$request->only('currency_code','security_deposit'));
        }

        flash_message('success', 'Space Updated Successfully');
        return redirect(ADMIN_URL.'/spaces');
    }

    public function change_photo_order($space_id,$data)
    {
        $start = 1;
        foreach($data as $image_id){
            if($image_id != ''){
                SpacePhotos::where('id',$image_id)->update(['order_id' => $start++]);
            }
        }
    }

    protected function getStringForArray($array)
    {
        $arr_str = '';
        if(is_array($array) && count($array) > 0) {
            $arr_str = implode(',', $array);
        }
        return $arr_str;

    }


    // common data shown in space add&edit page
    protected function spaceCommonData($space_id=null)
    {
        $data['space_types']    = KindOfSpace::active()->get()->pluck('name','id');
        $data['guest_accesses'] = GuestAccess::active()->get();
        $data['amenitieses']    = Amenities::active()->get();
        $data['services']       = Services::active()->get();
        $data['space_styles']   = Style::active()->get();
        $data['special_featureses'] = SpecialFeature::active()->get();
        $data['space_rules']    = SpaceRule::active()->get();
        $data['activity_types'] = ActivityType::withActivitiesOnly()->activeOnly()->get();
        $data['activities']     = Activity::activeOnly()->get();
        $data['sub_activities'] = SubActivity::activeOnly()->get();
        $data['country']        = Country::pluck('long_name','short_name');
        $data['days_array']     = getDayOptions();
        $data['status']         = true;
        $data['status_message'] = '';

        if ($space_id==null) {
            $data['prev_guest_access']      = [];
            $data['prev_amenities']         = [];
            $data['prev_space_style']       = [];
            $data['prev_special_feature']   = [];
            $data['prev_space_rule']        = [];
            $space_activities               = json_encode([]);
            $data['prev_activity_type']     = [];
            $data['prev_activities']        = [];
            $data['prev_sub_activities']    = [];
            $data['prev_services'] = [];

            $data['space_availabilities'] = [];
            foreach ($data['days_array'] as $key=>$day) {
                $data['space_availabilities'][] = [
                    'day' => $key,
                    'status' => "All",
                    'day_name' => $day,
                ];
            }
            $data['space_availabilities'] = json_encode($data['space_availabilities']);

            $data['currency_symbol']= Currency::where('code',DEFAULT_CURRENCY)->first()->symbol;
            $data['minimum_amount'] = MINIMUM_AMOUNT;
        }
        else{
            $data['space'] = $space         = Space::with('space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times','space_address','space_photos','space_price')->find($space_id);

            if(!$space) {
                $data['status'] = false;
                $data['status_message'] = 'Invalid Space Id';
                return $data;
            }

            $data['space_availabilities']   = $space->space_availabilities;
            $data['prev_guest_access']      = explode(',', $space->guest_access);
            $data['prev_amenities']         = explode(',', $space->amenities);
            $data['prev_services']          = explode(',', $space->services);
            $data['space_photos']           = SpacePhotos::where('space_id',$space_id)->ordered()->get();
            $data['prev_space_style']       = explode(',', $space->space_style);
            $data['prev_special_feature']   = explode(',', $space->special_feature);
            $data['prev_space_rule']        = explode(',', $space->space_rules);
            $space_activities               = $space->space_activities;

            $space_activities               = $space->space_activities;

            $activity_type_ids              = $space_activities->pluck('activity_type_id')->implode(',');
            $activity_ids                   = $space_activities->pluck('activities')->implode(',');
            $sub_activity_ids               = $space_activities->where('sub_activities','<>',null)->pluck('sub_activities')->implode(',');
            $data['prev_activity_type']     = explode(',', $activity_type_ids);
            $data['prev_activities']        = explode(',', $activity_ids);
            $data['prev_sub_activities']    = explode(',', $sub_activity_ids);

            $currency_code = (@$space->space_price->original_currency_code)?@$space->space_price->original_currency_code:DEFAULT_CURRENCY;
            $data['currency_symbol']= Currency::where('code',$currency_code)->first()->original_symbol;
            $data['minimum_amount'] = currency_convert(DEFAULT_CURRENCY, $currency_code, MINIMUM_AMOUNT);
        }

        return $data;
    }

    /*store space details*/
    protected function saveSpaceData($space_id, $step_data)
    {
        $num_cols = ['number_of_rooms', 'number_of_restrooms', 'number_of_guests'];
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
    protected function saveLocationData($space_id, $loc_data)
    {
        $space_addr = SpaceLocation::where('space_id', $space_id)->first();
        if($space_addr==null){
            $space_addr = new SpaceLocation();
        }

        $space_addr->space_id = $space_id;
        $space_addr->address_line_1 = $loc_data['address_line_1'];
        $space_addr->address_line_2 = $loc_data['address_line_2'];
        $space_addr->city = $loc_data['city'];
        $space_addr->state = $loc_data['state'];
        $space_addr->country = $loc_data['country'];
        $space_addr->postal_code = $loc_data['postal_code'];
        $space_addr->latitude = $loc_data['latitude'];
        $space_addr->longitude = $loc_data['longitude'];
        $space_addr->guidance = $loc_data['guidance'];
        $space_addr->save();

        $space_addr->save();
    }

    /**
     * Ajax List Your Space Add Photos, it will upload multiple files
     *
     * @param array $request    Input values
     * @return json rooms_photos table result
     */
    public function upload_photos($space_id)
    {
        $all_photos = request()->photos;
        if ($all_photos == null) {
            return;
        }
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
                $error_description = trans('validation.mimes',['attribute' => 'Image','values'=>'Jpg,Jpeg,Png,Gif']);
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
    }

    /**
     * Save List Your Space Description Data
     *
     * @param String $space_id
     * @param Array $step_data
     * @return Void
     */
    protected function saveDescriptionData($space_id,$data)
    {
        if (!array_key_exists("language",$data)) {
            $data['language'] = [];
        }
        array_unshift($data['language'],"en");
        foreach ($data['language'] as $key=>$language_code) {
            $desciption = $this->getDescriptionInstance($space_id, $language_code);

            if($language_code == 'en'){
                $step_data['name'] = $data['name'][$key];
                $step_data['summary'] = $data['summary'][$key];
                $this->saveSpaceData($space_id, $step_data);
            }else{
                $desciption->lang_code = $language_code;
                $desciption->name = $data['name'][$key];
                $desciption->summary = $data['summary'][$key];
            }
            $desciption->space_id = $space_id;
            $desciption->save();
        }

        $delete_space_description_lang = SpaceDescriptionLang::whereNotIn('lang_code',$data['language'])->delete();
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
     * Save List Your Space Security Deposit Data
     *
     * @param String $space_id
     * @param Array $security_deposit_data
     * @return Void
     */
    protected function saveSecurityDepositData($space_id, $security_deposit_data)
    {
        $space_price = SpacePrice::firstOrNew(['space_id' => $space_id]);
        $space_price->space_id = $space_id;
        $space_price->currency_code = $security_deposit_data['currency_code'];
        $space_price->security = $security_deposit_data['security_deposit'];

        $space_price->save();
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
        $activity_price = json_decode($activity_price,true);
        foreach ($activity_price as $price_data) {
            $activity_price = ActivityPrice::updateOrCreate(
                ['space_id' => $space_id, 'activity_id' => $price_data['activity_id']],
                ['hourly' => $price_data['original_hourly'], 'min_hours' => $price_data['min_hours'], 'full_day' => $price_data['original_full_day'], 'weekly' => $price_data['original_weekly'],'monthly' => $price_data['original_monthly'], 'currency_code' => $price_data['original_currency_code']]
            );
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
        $availability_data = json_decode($availability_data,true);
        foreach ($availability_data as $day => $availability) {
            AvailabilityTimes::whereIn('id',$availability['removed_availability'])->delete();

            $avail = SpaceAvailability::firstOrCreate(['space_id' => $space_id,'day' => $day]);

            $hourly_status = ($availability['available'] == 'set_hours') ? 'Open' : 'All';
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
                        $avail_hours = AvailabilityTimes::findOrFail($hour_data['id']);
                    }

                    $avail_hours->space_availability_id = $avail->id;
                    $avail_hours->start_time    = $hour_data['start_time'];
                    $avail_hours->end_time      = $hour_data['end_time'];
                    $avail_hours->save();
                }
            }
            else {
                AvailabilityTimes::whereIn('space_availability_id',[$avail->id])->delete();
            }
        }
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
        $activity_data = json_decode($activity_data,true);
        $all_activities = array_keys($activity_data);
        $activities = SpaceActivities::whereSpaceId($space_id)->whereNotIn('activity_type_id',$all_activities)->get();
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
            $activity_price->currency_code = session('currency') ?? view()->shared('default_currency')->code;
            $activity_price->save();
        }
    }

    /**
     * Delete Rooms Photo
     *
     * @param array $request    Input values
     * @return json success   
     */
    public function delete_photo(Request $request)
    {

        $space_id        = $request->space_id;
        $photos          = SpacePhotos::find($request->photo_id);
        $return_data['success'] = 'false';
        if($photos != NULL){
            $photos->delete();
            $return_data['success'] = "true";
        }

        return json_encode(['success'=>'true']);
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
    public function popular(Request $request)
    {
        $space = Space::findOrFail($request->id);

        if($space->popular == 'No') {
            if($space->status != 'Listed') {
                flash_message('error', 'Not able to popular for unlisted listing');
                return back();
            }
            $user_check = User::find($space->user_id);
            if($user_check->status != 'Active') {
                flash_message('error', 'Not able to popular for Not Active users');
                return back();
            }
        }

        $space->popular = ($space->popular == 'Yes') ? 'No' : 'Yes';
        $space->save();

        flash_message('success', 'Updated Successfully');
        return redirect()->route('admin.space');
    }

    /*Admin Verify Listing*/
    public function update_space_status(Request $request)
    {

        $space = Space::find($request->id);
        $user_check = User::find($space->user_id);
        $type = ($request->type == 'admin_status') ? 'verified' : $request->type;
        $type = ucwords(str_replace(['-', '_'], ' ', $type));

        if ($user_check->status != 'Active') {
            flash_message('error', 'Not able to ' . $type . ' for Not active users');
            return back();
        }

        if ($space->status == 'Unlisted') {
            flash_message('error', 'Not able to ' . $type . ' for unlisted listing');
            return back();
        }

        if($request->option == 'Approved') {
            Space::where('id', $request->id)->update(['status' => 'Listed']);
            
            //send admin approved email to host
            $email_controller = new EmailController;
            $email_controller->listing_approved_by_admin($request->id);
        }
        else if($request->option == 'Pending' && $request->type == "verified"){
            Space::where('id', $request->id)->update(['status'=>'Pending']);
        }else if($request->option == 'Pending' && $space->status == 'Resubmit'){
            Space::where('id', $request->id)->update(['status'=>'Pending','admin_status'=>'Pending']);
        }

        Space::where('id', $request->id)->update([$request->type => $request->option]);
        flash_message('success', 'Updated Successfully');

        return redirect($this->base_url);
    }

    /**
     * Ajax function to get Calendar data
     *
     * @param array $request    Input values
     * @param array $calendar   Instance of CalendarController
     * @return html Calendar
     */
    public function space_calendar(Request $request, CalendarController $calendar)
    {   
        $array=[];
        $month          = $request->month;
        $year           = $request->year;
        $array['calendar_data']  = $calendar->generate($request->id, $year, $month);
        $array['month_calendar_data']  = $calendar->monthly_generate($request->id, $year, $month);
        return json_encode($array);
    }

    /**
    * Resubmit Listing in admin 
    */
    public function resubmit_listing(Request $request){

        $space = Space::find($request->space_id);

        $user_check = User::find($space->user_id);

        if ($user_check->status != 'Active') {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', 'Not able to verified for Not Active users');
            return "true";
        }

        if ($space->status == 'Unlisted') {
            
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', 'Not able to verified for unlisted listing');
            return "true";
        }
        $space->admin_status = 'Resubmit';
        $space->status = 'Resubmit';
        $space->save();

        $space_detail = Space::find($request->space_id);
        $message = new Messages;
        $message->space_id = $request->space_id;
        $message->reservation_id = $request->space_id.''.$space_detail->user_id;
        $message->user_from = $space_detail->user_id;
        $message->user_to   = $space_detail->user_id;
        $message->message   = $request->msg;
        $message->message_type = 14;
        $message->save();

        Session::flash('alert-class', 'alert-success');
        Session::flash('message', 'Resubmited Successfully');
        return "true";
    }

    public function delete(Request $request){
        $check = Reservation::whereSpaceId($request->id)->count();

        if($check) {
            flash_message('error', 'This space has some reservations. So, you cannot delete this space.'); // Call flash message function
        }else{        
            $space = Space::find($request->id);
            if($space != null){
                Space::find($request->id)->Delete_All_Space_Relationship(); 
                flash_message('success', 'Deleted Successfully');
            }else{
                flash_message('error', 'This Space Already Deleted.');
            } 
        }
        return redirect()->route('admin.space');
    }
}
