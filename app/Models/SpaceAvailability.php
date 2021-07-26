<?php

/**
 * Space Calendar Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Space Calendar
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;
use App\Repositories\CalendarScopes;

class SpaceAvailability extends Model
{
    use CalendarScopes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'space_availability';
    
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['day_name','original_status'];

    public function scopeOnlyNotAvailable($query)
    {
        return $query->where('status', 'Closed');
    }

    public function scopeAvailableWithTime($query, $c_day, $start_time, $end_time)
    {
        return $query->where('day',$c_day)
        ->whereStatus('All')
        ->orWhere((function($query) use($start_time, $end_time) {
            $query->whereStatus('Open')->wherehas('availability_times',function($query) use($start_time, $end_time) {
                $query->where('start_time', '<=', $start_time)->where('end_time', '>=',$end_time);
            });
        }));
    }

    public function availability_times()
    {
        return $this->hasMany('App\Models\AvailabilityTimes');
    }

    public function getDayNameAttribute()
    {
        $day_options = getDayOptions();
        return $day_options[$this->attributes['day']];
    }

    public function getOriginalStatusAttribute()
    {
        return $this->attributes['status'];
    }
}