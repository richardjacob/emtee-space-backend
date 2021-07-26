<?php

/**
 * Sub Activities Type Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Sub Activities Type
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubActivity extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sub_activities';

    public $timestamps = false;

    protected $appends = ['activity_name'];

    // Get Active records Only
    public function scopeActiveOnly($query)
    {
        return $query->whereStatus('Active');
    }

    // Join with Activities table
    public function activity()
    {
      return $this->belongsTo('App\Models\Activity');
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

        $trans_value = @SubActivityLang::where('activity_id', $this->attributes['id'])->where('lang_code', $lang_code)->first()->$field;
        if ($trans_value) {
            return $trans_value;
        }
        return $value;
    }

    public function getActivityNameAttribute()
    {
        return $this->activity->name;
    }

    public function getNameAttribute()
    {
        return $this->getTranslatedValue('name');
    }
}