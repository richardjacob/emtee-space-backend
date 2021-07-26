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

class SpaceCalendar extends Model
{
    use CalendarScopes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'space_calendar';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = [];

    public function space()
    {
    	return $this->belongsTo('App\Models\Space','space_id','id');
    }

    public function getPeriodAttribute()
    {
        $period = ($this->start_date == $this->end_date) ? 'Single' : 'Multiple';

        return $period;
    }

    public function getBetweenTimesAttribute()
    {
        $start_time = $this->attributes['start_time'];
        $end_time = $this->attributes['end_time'];
        return getTimes($start_time,$end_time);
    }
}