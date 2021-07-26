<?php

/**
 * Space Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Space
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Config;
use DateTime;
use DateTimeZone;
use JWTAuth;
use DB;

class Space extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'space';

	protected $fillable = ['summary', 'name'];

	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    	'id' => 'string',
    	'sq_ft' => 'integer',
    ];

	public $mandatory_fields = ['space_type','sq_ft','size_type','guest_access','number_of_guests','latitude','longitude','name','summary','activity_type_id','activities','sub_activities','hourly'];

	protected $appends = ['link','space_descriptions','space_type_name', 'reviews_count' ,'overall_star_rating', 'reviews_count_lang', 'photo_name', 'host_name', 'sq_ft_text'];

	protected $integer_cols = ['space_type','number_of_rooms','number_of_restrooms','number_of_guests','floor_number'];

	public function setSpaceTypeAttribute($input)
	{
		$space_type = KindOfSpace::where('id', $input)->first();
		$this->attributes['space_type'] = $input;
		$this->attributes['sub_name']  = $space_type->name .' in '. optional($this->space_address)->city;
	}

	// Save Model values to database without Trigger any events
	public function saveQuietly(array $options = [])
	{
	    return static::withoutEvents(function () use ($options) {
	        return $this->save($options);
	    });
	}

	// Handle all null values in api
	public function getAttribute($attribute)
    {
        if(request()->segment(1) == 'api') {
            $value = parent::getAttribute($attribute);
            if($value == null) {
            	if(in_array($attribute, $this->integer_cols)) {
            		return 0;
            	}
            	return '';
            }
            return $value;
        }
        return parent::getAttribute($attribute);
    }

	// Get host user data
	public function scopeUser($query)
	{
		return $query->where('user_id', auth()->id());
	}

	// Get The Listed Room
	public function scopeListed($query)
	{
		return $query->where('status', 'Listed');
	}

	// Get The Verified Room
	public function scopeVerified($query)
	{
		return $query->where('admin_status', 'Approved');
	}

	// Get The Verified Listed Room
	public function scopeviewOnly($query)
	{
		return $query->listed()->verified();
	}

	// Join with space_address table
	public function space_address()
	{
		return $this->hasOne('App\Models\SpaceLocation');
	}

	// Join with space_activities table
	public function space_activities()
	{
		return $this->hasMany('App\Models\SpaceActivities');
	}

	// Join with first activity_price table for show in search,detail page and etc.
	public function activity_price()
	{
		return $this->hasOne('App\Models\ActivityPrice');
	}

	// Join with space_price table
	public function space_price()
	{
		return $this->hasOne('App\Models\SpacePrice')->withDefault(['security' => '']);
	}

	//Get space photo all
	public function space_photos()
	{
		return $this->hasMany('App\Models\SpacePhotos')->ordered();
	}

	//Get space photo all
	public function space_description()
	{
		return $this->hasOne('App\Models\SpaceDescription');
	}

	// Join with space_availabilities table
	public function space_availabilities()
	{
		return $this->hasMany('App\Models\SpaceAvailability');
	}

	public function space_calendar()
	{
		return $this->hasMany('App\Models\SpaceCalendar', 'space_id', 'id');
	}

	// Join with users table
	public function users()
	{
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

	// Join with saved_wishlists table
	public function saved_wishlists()
	{
		return $this->hasOne('App\Models\SavedWishlists');
	}

	// Join with reviews table
	public function reviews()
	{
		return $this->hasMany('App\Models\Reviews', 'space_id', 'id')->where('user_to', @$this->attributes['user_id']);
	}

	// Get Translated value of given column
	protected function getTranslatedValue($field)
	{
		if(!isset($this->attributes[$field])) {
			return '';
		}
		$value = $this->attributes[$field];

		if(request()->segment(1) == 'manage_listing' || request()->segment(1) == ADMIN_URL) {
			return $value;
		}

		$lang_code = getLangCode();
		if ($lang_code == 'en') {
			return $value;
		}
		$trans_value = @SpaceDescriptionLang::where('space_id', $this->attributes['id'])->where('lang_code', $lang_code)->first()->$field;
		if ($trans_value) {
			return $trans_value;
		}
		return $value;
	}

	// Get Basics completed Steps
	protected function basicsCompleted()
	{
		$completed = 0;
		$space_location = $this->space_address;

		if($this->attributes['space_type'] != '')
			$completed++;
		if($this->attributes['sq_ft'] != '')
			$completed++;
		if($this->attributes['number_of_guests'] > 0)
			$completed++;
		if($this->attributes['guest_access'] != '')
			$completed++;
		if($space_location->latitude != '' && $space_location->longitude != '')
			$completed++;

		return $completed;
	}

	// Get Setup step completed Steps
	protected function setupCompleted()
	{
		$completed = 0;

		if($this->attributes['name'] != '' && $this->attributes['summary'] != '')
			$completed++;
		if($this->space_photos->count() > 0)
			$completed++;

		return $completed;
	}

	// Get ready to host step completed Steps
	protected function readyToHostCompleted()
	{
		$completed = 0;
		if(!isset($this->attributes['id'])) {
			return $completed;
		}
		$space_activities = SpaceActivities::with('activity_price')->where('space_id',$this->attributes['id'])->get();
		$activities_count = $space_activities->count();
		if($activities_count > 0) {
			$completed++;
		}
		$price_completed = true;
		foreach ($space_activities as $activity) {
			if(optional($activity->activity_price)->min_hours == 0 || optional($activity->activity_price)->hourly == 0) {
				$price_completed = false;
			}
		}
		if($price_completed && $activities_count > 0)
			$completed++;

		return $completed;
	}

	protected function getRatingResult($type)
	{
		$valid_types = array('rating', 'accuracy', 'location' ,'communication', 'checkin', 'cleanliness', 'value');
		if(!isset($this->attributes['id']) || !in_array($type, $valid_types)) {
			return '';
		}

		$reviews = Reviews::where('space_id', $this->attributes['id'])->where('user_to', $this->attributes['user_id']);

		if (request()->segment(1) == 'api') {

			$result['rating_value'] = '0';
			$result['is_wishlist']  = "No";

			if ($reviews->count() > 0) {
				$rating_value = roundHalfInteger($reviews->sum($type) / $reviews->count());
				$result['rating_value'] = strval($rating_value);
			}
			
			if(request()->token) {
				$user_details = JWTAuth::parseToken()->authenticate();
				$result_wishlist = SavedWishlists::with('wishlists')->where('space_id', $this->attributes['id'])->where('user_id', $user_details->id)->count();

				if ($result_wishlist > 0) {
					$result['is_wishlist'] = "Yes";
				}
			}
			return arrayToObject($result);
		}

		$rating_html = '';		

		if ($reviews->count() > 0) {
			$rating_html = '<div class="star-rating"> <div class="foreground">';
			$average = $reviews->sum($type) / $reviews->count();

			$whole = floor($average);
			$fraction = $average - $whole;

			for ($i = 0; $i < $whole; $i++) {
				$rating_html .= ' <i class="icon icon-star"></i>';
			}

			if ($fraction >= 0.5) {
				$rating_html .= ' <i class="icon icon-star-half"></i>';
			}

			$rating_html .= ' </div> <div class="star-bg background mb_blck">';
			$rating_html .= '<i class="icon icon-star"></i> <i class="icon icon-star"></i> <i class="icon icon-star"></i> <i class="icon icon-star"></i> <i class="icon icon-star"></i>';
			$rating_html .= ' </div> </div>';
			return $rating_html;
		}
		return $rating_html;
	}

	// Get steps_count using sum of space_step
	public function getStepsCountAttribute()
	{
		if(!isset($this->attributes['id'])) {
			$total_steps = \Schema::getColumnListing('space_steps_status');
	        $total_steps = count($total_steps) - 1; // Decrease space_id Column
	        return $total_steps;
		}
		$result = SpaceStepsStatus::find($this->attributes['id']);
		return $result->total_steps - ($result->basics + $result->description + $result->location + $result->photos + $result->pricing);
	}

	// Get Each Step status
	public function getStepsStatusAttribute()
	{
		$result = new \stdClass();
		$result->basics			= collect(['total_steps' => '5']);
		$result->setup 			= collect(['total_steps' => '2']);
		$result->ready_to_host	= collect(['total_steps' => '2']);

		$basics_completed  		= $this->basicsCompleted();
		$setup_completed  		= $this->setupCompleted();
		$hosting_completed  	= $this->readyToHostCompleted();

        $result->basics['completed_steps'] 		= $basics_completed;
        $result->setup['completed_steps'] 		= $setup_completed;
        $result->ready_to_host['completed_steps'] = $hosting_completed;

        $result->basics['remaining_steps'] 		= 5 - $basics_completed;
        $result->setup['remaining_steps'] 		= 2 - $setup_completed;
        $result->ready_to_host['remaining_steps'] = 2 - $hosting_completed;

		return $result;
	}

	// Get Reviews Count
	public function getReviewsCountAttribute()
	{
		return $this->reviews->count();
	}

	// Overall Reviews Star Rating
	public function getOverallStarRatingAttribute()
	{
		return $this->getRatingResult('rating');
	}

	// Accuracy Reviews Star Rating
	public function getAccuracyStarRatingAttribute()
	{
		return $this->getRatingResult('accuracy');
	}

	// Location Reviews Star Rating
	public function getLocationStarRatingAttribute()
	{
		return $this->getRatingResult('location');
	}

	// Communication Reviews Star Rating
	public function getCommunicationStarRatingAttribute()
	{
		return $this->getRatingResult('communication');
	}

	// Checkin Reviews Star Rating
	public function getCheckinStarRatingAttribute()
	{
		return $this->getRatingResult('checkin');
	}

	// Cleanliness Reviews Star Rating
	public function getCleanlinessStarRatingAttribute()
	{
		return $this->getRatingResult('cleanliness');
	}

	// Value Reviews Star Rating
	public function getValueStarRatingAttribute()
	{
		return $this->getRatingResult('value');
	}
	// Reviews Count
	public function getReviewsCountLangAttribute()
	{
		return ucfirst(trans_choice('messages.header.review', $this->reviews_count));
	}

	// Get Space First Image URL
	public function getPhotoNameAttribute()
	{
		$result = SpacePhotos::where('space_id', @$this->attributes['id'])->ordered();
		if($result->count() > 0) {
			return $result->first()->name;
		}
		return asset('images/default_image.png');
	}

	// Get host name from users table
	public function getHostNameAttribute()
	{
		return optional($this->users)->first_name;
	}

	public function getLinkAttribute()
	{
		$site_settings_url = @SiteSettings::where('name', 'site_url')->first()->value;
		$url = \App::runningInConsole() ? $site_settings_url : url('/');
		$this_link = $url . '/space/' . $this->id;
		return $this_link;
	}

	// Get Created at Time for Space Listed
	public function getCreatedTimeAttribute()
	{
		$new_str = new DateTime($this->attributes['updated_at'], new DateTimeZone(Config::get('app.timezone')));
		if (request()->segment(1) == ADMIN_URL) {
			$timezone = User::find($this->attributes['user_id'])->timezone;
		}
		else {
			$timezone = auth()->user()->timezone;
		}
		$new_str->setTimeZone(new DateTimeZone($timezone));

		return date(PHP_DATE_FORMAT, strtotime($this->attributes['updated_at'])) . ' at ' . $new_str->format(view()->shared('time_format'));
	}

	// delete for rooms relationship data (for all table) $this->attributes['id']
	public function Delete_All_Space_Relationship()
	{
		if ($this->attributes['id'] != '') {
			SpaceDescriptionLang::where('space_id',$this->attributes['id'])->delete();
			SpaceDescription::where('space_id',$this->attributes['id'])->delete();
			SpaceLocation::where('space_id',$this->attributes['id'])->delete();
			SpacePrice::where('space_id',$this->attributes['id'])->delete();
			ActivityPrice::where('space_id',$this->attributes['id'])->delete();
			SpaceActivities::where('space_id',$this->attributes['id'])->delete();
			SpacePhotos::where('space_id',$this->attributes['id'])->delete();
			SpaceStepsStatus::where('space_id',$this->attributes['id'])->delete();
			AvailabilityTimes::whereHas('space_availability',function($q){
				$q->where('space_id',$this->attributes['id']);
			})->delete();
			SpaceAvailability::where('space_id',$this->attributes['id'])->delete();
			SpaceCalendar::where('space_id',$this->attributes['id'])->delete();
			Messages::where('space_id',$this->attributes['id'])->delete();
			SavedWishlists::where('space_id', $this->attributes['id'])->delete();
			Space::where('id', $this->attributes['id'])->delete();
		}
	}

	public function getNameAttribute()
	{
		return $this->getTranslatedValue('name');
	}

	public function getSummaryAttribute()
	{
		return $this->getTranslatedValue('summary');
	}

	public function getSpaceDescriptionsAttribute()
	{
		if(!isset($this->attributes['id']) || !$this->space_description) {
			return array();
		}

		$description = $this->space_description->toArray();
		$space_description['en'] = array_except($description,'space_id');
		$space_description['en']['name'] = $this->attributes['name'];
		$space_description['en']['summary'] = $this->attributes['summary'];
		$space_description['en']['language_name'] = 'English';

		$description = SpaceDescriptionLang::with('language')->where('space_id', $this->attributes['id'])->get();

		foreach ($description as $desc) {
			if($desc->lang_code != '') {
				$desc_data = array_only($desc->toArray(),['name', 'summary']);
				$desc_data['language_name'] = optional($desc->language)->name;
				$space_description[$desc->lang_code] = $desc_data;
			}
		}
		return $space_description;
	}

	public function getSpaceTypeNameAttribute()
	{
		$type_id = isset($this->attributes['space_type']) ? $this->attributes['space_type']: '';
		$space_type = KindOfSpace::where('id', $type_id)->first();
		return optional($space_type)->name;
	}

	public function getSqFtTextAttribute()
	{
		if(!isset($this->attributes['id'])) {
			return '';
		}
		return $this->attributes['sq_ft'].' '.__('messages.space_detail.'.$this->attributes['size_type']);
	}

	public function canBookMultipleDay()
	{
		$open_availability = $this->space_availabilities->where('status','All')->count();
		return ($open_availability == 7);
	}
}