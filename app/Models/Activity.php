<?php

/**
 * Activities Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Activities
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activities';

    public $timestamps = false;

    protected $appends = ['activity_type_name', 'image_url','search_url'];

    // Get Active records Only
    public function scopeActiveOnly($query)
    {
        return $query->whereStatus('Active');
    }

    // Get Active records Only
    public function scopePopularOnly($query)
    {
    	return $query->wherePopular('Yes');
    }

    public function activity_type()
    {
      return $this->belongsTo('App\Models\ActivityType');
    }

    // Join with Subactivities table
    public function sub_activities()
    {
        return $this->hasMany('App\Models\SubActivity');
    }

    public function getActivityTypeNameAttribute()
    {
        return $this->activity_type->name;
    }

    public function getNameAttribute()
    {
        return $this->getTranslatedValue('name');
    }

    // Get Translated value of given column
    protected function getTranslatedValue($field)
    {
        if(!isset($this->attributes[$field])) {
            return '';
        }
        $value = $this->attributes[$field];

        if(request()->segment(1) == ADMIN_URL) {
            return $value;
        }

        $lang_code = getLangCode();
        if($lang_code == 'en') {
            return $value;
        }

        $trans_value = @ActivityLang::where('activity_id', $this->attributes['id'])->where('lang_code', $lang_code)->first()->$field;
        if ($trans_value) {
            return $trans_value;
        }
        return $value;
    }

    public function getSearchUrlAttribute()
    {
        return route('search_page',['activity_type' => $this->attributes['id']]);
    }

    // Get Image Url Attribute
    public function getImageUrlAttribute()
    {
        return getImage($this->attributes['image'],'/images/activities/');
    }
}