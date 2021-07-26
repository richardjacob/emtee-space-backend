<?php

/**
 * Activities Type Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Activities Type
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activities_type';

    public $timestamps = false;

    protected $appends = ['image_url'];

    // Get Active records Only
    public function scopeActiveOnly($query)
    {
        return $query->whereStatus('Active');
    }

    // Get records Only having activities
    public function scopeWithActivitiesOnly($query)
    {
        return $query->whereHas('activities',function($query) {
            $query->activeOnly();
        });
    }

    // Join with Activities table
    public function activities()
    {
        return $this->hasMany('App\Models\Activity');
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

        $trans_value = @ActivityTypeLang::where('activity_type_id', $this->attributes['id'])->where('lang_code', $lang_code)->first()->$field;
        if ($trans_value) {
            return $trans_value;
        }
        return $value;
    }

    public function getNameAttribute()
    {
        return $this->getTranslatedValue('name');
    }

    // Get Image Url Attribute
    public function getImageUrlAttribute()
    {
        return getImage($this->attributes['image'],'/images/activities/');
    }
}