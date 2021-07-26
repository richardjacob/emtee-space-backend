<?php

/**
 * Availability Times
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Availability Times
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\CalendarScopes;

class AvailabilityTimes extends Model
{
use CalendarScopes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'availability_times';
    
    public $timestamps = false;

    protected $time_format = 'h:i A';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['formatted_times'];

    // Convert Given field to time format
    protected function getTime($field)
    {
        return date($this->time_format,strtotime($this->attributes[$field]));
    }

    public function space_availability()
    {
        return $this->belongsTo('App\Models\SpaceAvailability','space_availability_id','id');
    }

    // Get Start and End time in time format
    public function getFormattedTimesAttribute()
    {
        return $this->formatted_start_time.' - '.$this->formatted_end_time;
    }

    // Get Start time in time format
    public function getFormattedStartTimeAttribute()
    {
        return $this->getTime('start_time');
    }

    // Get End time in time format
    public function getFormattedEndTimeAttribute()
    {
        return $this->getTime('end_time');
    }

    // Return Other Times except start and end time
    public function getAvailableTimesAttribute()
    {
        $this->load('space_availability');
        $availability = $this->space_availability;
        $space_detail = Space::with('users')->find($availability->space_id);
        $host_user_tz = $space_detail->users->timezone;

        $start_time = $this->attributes['start_time'];
        $end_time = $this->attributes['end_time'];
        return getTimes($start_time,$end_time,$host_user_tz);
    }
}