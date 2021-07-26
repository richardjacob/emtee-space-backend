<?php

/**
 * Calendar Scopes
 *
 * @package     Makent Space
 * @subpackage  CalendarScopes
 * @category    Repository
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Repositories;

trait CalendarScopes
{
    /**
     *  Scope to Get Only data with Not Available Status
     * @return Query Builder
     */
    public function scopeOnlyAvailable($query)
    {
        return $query->where('status', 'Available');
    }

    /**
     *  Scope to Get Only data with Not Available Status
     * @return Query Builder
     */
    public function scopeOnlyNotAvailable($query)
    {
        return $query->where('status', 'Not available');
    }

    /**
     *  Scope to Get Only data After Today Date
     * @return Query Builder
     */
    public function scopeAfterToday($query)
    {
        $c_date = date('Y-m-d');
        return $query->where('start_date', '>=' ,$c_date);
    }

    /**
     *  Scope to Validate Given Start date Time and End Date Time
     * @param String $start_date Start date
     * @param String $end_date End date
     * @param String $start_time Start time
     * @param String $end_time End time
     * @return Query Builder
     */
    public function scopeDateTimeNotAvailable($query, $start_date = '', $end_date = '', $start_time = '', $end_time = '')
    {
        return $query->dateNotAvailable($start_date,$end_date)->timeNotAvailable($start_time, $end_time);
    }

    /**
     *  Scope to Validate Given Start date and End Date
     * @param String $start_date Start date
     * @param String $end_date End date
     * @return Query Builder
     */
    public function scopeDateNotAvailable($query, $start_date = '', $end_date = '')
    {
        return $query->whereRaw("((start_date <= '".$end_date."' and end_date >= '".$start_date."') or (end_date <= '".$end_date."' and start_date >= '".$start_date."'))");
    }

    /**
     *  Scope to Validate Given Start Time and End Time
     * @param String $start_time Start time
     * @param String $end_time End time
     * @return Query Builder
     */
    public function scopeTimeNotAvailable($query, $start_time = '', $end_time = '')
    {
        return $query->whereRaw("((start_time <= '".$end_time."' and end_time >= '".$start_time."') or (end_time <= '".$end_time."' and start_time >= '".$start_time."'))");
    }

    /**
     *  Scope to Validate Both Start date, time and End date, time
     * @param Int $checkin Start date, time timestamp
     * @param Int $checkout End date, time timestamp
     * @return Query Builder
     */
    public function scopeValidateDateTime($query, $checkin, $checkout)
    {
        return $query->whereRaw("((UNIX_TIMESTAMP(TIMESTAMP(start_date,start_time)) < '".$checkout."' and UNIX_TIMESTAMP(TIMESTAMP(end_date,end_time)) > '".$checkin."') or (UNIX_TIMESTAMP(TIMESTAMP(end_date,end_time)) < '".$checkout."' and UNIX_TIMESTAMP(TIMESTAMP(start_date,start_time)) > '".$checkin."'))");
    }

    /**
     *  Scope to Validate Givent date and Time is not available 
     * @param Int $date date
     * @param Int $time time
     * @return Query Builder
     */
    public function scopeValidateSingleDateTime($query, $date, $time)
    {
        return $query->whereRaw('? between start_date and end_date', [$date])->whereRaw('start_time <= ? and end_time > ?',[$time,$time]);
    }
}