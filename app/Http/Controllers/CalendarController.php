<?php

/**
 * Calendar Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Calendar
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IcalController;
use App\Models\SpacePrice;
use App\Models\Space;
use App\Models\SpaceCalendar;
use App\Models\SpaceAvailability;
use App\Models\AvailabilityTimes;
use App\Models\ImportedIcal;
use App\Models\Reservation;
use App\Models\ReservationTimes;
use App\Http\Helper\PaymentHelper;
use Validator;
use DateTime;
use DatePeriod;
use DateInterval;
class CalendarController extends Controller
{
    public $start_day = 'Monday';   // Global Variable for Start Day of Calendar

    protected $payment_helper;

    /**
     * Constructor
     */
    public function __construct(PaymentHelper $payment)
    {
        $this->payment_helper = $payment;
    }

    /**
     * Get a Calendar HTML
     *
     * @param int $space_id  Room Id for get the Calendar data 
     * @param int $year     Year of Calendar
     * @param int $month    Month of Calendar
     * @return html
     */
    public function generate($space_id, $year = '', $month = '')
    {
        $space = Space::with('space_activities.activity_price.currency')->find($space_id);

        $space_activities = $space->space_activities;
        $space_availabilities = SpaceAvailability::with(['availability_times'])->whereSpaceId($space_id)->get();

        $this_start_day = $this->start_day;

        if($year == '') {
            $year  = date('Y');
        }
        if($month == '') {
            $month = date('m');
        }

        $calendar_data = array();

        $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $start_days = array_flip(getDayOptions());

        $start_day  = (!isset($start_days[$this_start_day])) ? 0 : $start_days[$this_start_day];

        $today_time = mktime(12, 0, 0, $month, 1, $year);
        $today_date = getdate($today_time);
        $day        = $start_day + 1 - $today_date["wday"];

        $prev_time  = mktime(12, 0, 0, $month-1, 1, $year);
        $next_time  = mktime(12, 0, 0, $month+1, 1, $year);

        $last_time  = mktime(12, 0, 0, $month, $total_days, $year);
        $last_date  = getdate($last_time);
        $total_dates= $total_days + ($last_date["wday"] != ($start_day-1) ? ( 6 + $start_day - $last_date["wday"] ) : 0);

        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');

        if($day > 1) {
            $day -= 7;
        }

        $k = 0;
        $added_events = array();
        $added_bookings = array();

        while($day <= $total_dates) {
            for($ti = 0; $ti < 24; $ti++) {
                $this_time  = mktime($ti, 0, 0, $month, $day, $year);
                $this_date  = date('Y-m-d', $this_time);
                $cur_time   = date('H:i:s', $this_time);

                $day_key    = date('w',strtotime($this_date.' '.$cur_time));

                $availabilities = $space_availabilities->where('day',$day_key)->first();
                $available_times = $availabilities->availability_times;

                $calendar_data[$k]['id']    = $this_date.'_'.$cur_time;
                $calendar_data[$k]['start'] = $this_date.' '.$cur_time;
                $calendar_data[$k]['end']   = $this_date.' '.$cur_time;

                if($this_date < $current_date) {
                    $calendar_data[$k]['classNames'] = 'status-p ';
                }
                else if($this_date == $current_date)
                {
                    $calendar_data[$k]['classNames'] = ' today-w ';   
                }
                else if($availabilities->status == 'Closed') {
                    $calendar_data[$k]['classNames'] = 'status-n ';
                }
                else if($availabilities->status == 'Open') {
                    $check_ids = $available_times->pluck('id')->toArray();
                    $avail_time = AvailabilityTimes::where('start_time','<=',$cur_time)->where('end_time','>',$cur_time)->whereIn('id',$check_ids)->count();
                    $calendar_data[$k]['classNames'] = ($avail_time == 0) ? 'status-n ':'';
                }
                else if($this_date == $current_date) {
                    $calendar_data[$k]['classNames'] = ($current_time <= $cur_time) ? 'status-p ':'';
                }
                else {
                    $calendar_data[$k]['classNames'] = '';
                }
              
                $space_calendar = SpaceCalendar::where('space_id', $space_id)
                ->where(function($query) use ($this_date) {
                    $query->where('start_date', '<=', $this_date)->where('end_date', '>=', $this_date);
                })
                ->where(function($query) use ($cur_time,$this_date) {

                    return $query->whereRaw("UNIX_TIMESTAMP(TIMESTAMP(start_date,start_time))<=UNIX_TIMESTAMP(TIMESTAMP('".$this_date."','".$cur_time."')) and UNIX_TIMESTAMP(TIMESTAMP(end_date,end_time))>UNIX_TIMESTAMP(TIMESTAMP('".$this_date."','".$cur_time."'))");
                    // $query->where('start_time', '<=', $cur_time)->where('end_time', '>', $cur_time);
                })
                ->first();  
               
                $space_bookings = ReservationTimes::with('reservation.users','reservation.activity_types')
                    ->whereSpaceId($space_id)
                    ->whereStartDate($this_date)
                    ->where(function($query) use ($cur_time) {
                        $query->where('start_time', '<=', $cur_time)->where('end_time', '>', $cur_time);
                    })
                    ->onlyNotAvailable()
                    ->first();

                if(isset($space_bookings) && $space_bookings->status == 'Not available') {
                    $event_id = $space_bookings->id;

                    if(in_array($event_id, $added_bookings)) {
                        continue;
                    }

                    $calendar_data[$k]['start']         = $space_bookings->start_date.' '.$space_bookings->start_time;
                    $calendar_data[$k]['end']           = $space_bookings->end_date.' '.$space_bookings->end_time;
                    $activity_type_name                 = $space_bookings->reservation->activity_types->name;
                    $user_name                          = $space_bookings->reservation->users->first_name;
                    $calendar_data[$k]['title']         = $activity_type_name.' for '.$user_name;

                    $calendar_data[$k]['classNames']    .= ($space_bookings->status != 'Available') ? ' status-r': '';
                    array_push($added_bookings, $event_id);
                }
                else if(isset($space_calendar)) {
                    $event_id = $space_calendar->id;

                    if(in_array($event_id, $added_events)) {
                        continue;
                    }

                    $calendar_data[$k]['start']         = $space_calendar->start_date.' '.$space_calendar->start_time;
                    $calendar_data[$k]['end']           = $space_calendar->end_date.' '.$space_calendar->end_time;
                    $calendar_data[$k]['notes']         = $space_calendar->notes;

                    $calendar_data[$k]['description']   = $space_calendar->status;
                    // $calendar_data[$k]['title']         = $space_calendar->notes;
                    $calendar_data[$k]['classNames']    .= ($space_calendar->status != 'Available') ? ' status-b': 'status-a';
                    array_push($added_events, $event_id);
                }
                else {
                    $calendar_data[$k]['rendering']     = 'background';
                    $calendar_data[$k]['description']   = 'Available';
                }

                $k++;
            }
            $day++;
        }

        return $calendar_data;
    }
    public function monthly_generate($space_id, $year = '', $month = '')
    {   
        $space = Space::find($space_id);
        $space_availabilities = SpaceAvailability::with(['availability_times'])->whereSpaceId($space_id)->get();

        $this_start_day = 'monday';
        if ($year == '')
        {
            $year  = date('Y');
        }
        if ($month == '')
        {
            $month = date('m');
        }
        $calendar_data = array();

        $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $start_days = array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);
        $start_day  = ( ! isset($start_days[$this_start_day])) ? 0 : $start_days[$this_start_day];
        
        $today_time = mktime(12, 0, 0, $month, 1, $year);
        $today_date = getdate($today_time);
        $day        = $start_day + 1 - $today_date["wday"];

        $prev_time  = mktime(12, 0, 0, $month-1, 1, $year);
        $next_time  = mktime(12, 0, 0, $month+1, 1, $year);
        
        $last_time  = mktime(12, 0, 0, $month, $total_days, $year);
        $last_date  = getdate($last_time);
        $total_dates= $total_days + ($last_date["wday"] != ($start_day-1) ? ( 6 + $start_day - $last_date["wday"] ) : 0);

        $current_date= date('Y-m-d');
        $current_time= time();
       
        if ($day > 1)
        {
            $day -= 7;
        }

        $k = 0;
        while($day <= $total_dates)
        {
            $this_time = mktime(12, 0, 0, $month, $day, $year);
            $this_date = date('Y-m-d', $this_time);
            $calendar_data[$k]['date'] = $this_date;
            $calendar_data[$k]['day'] = date('d', $this_time);
            $calendar_data[$k]['classNames'] = '';
            $day_key    = date('w',strtotime($this_date));
            $availabilities = $space_availabilities->where('day',$day_key)->first();
            
             $calendar_data[$k]['id']    = $this_date.'_month';
             $calendar_data[$k]['start'] = $this_date;
             $calendar_data[$k]['end']   = $this_date;

            if($this_date < $current_date)
            {
                $calendar_data[$k]['classNames'] = ' status-prev ';
            }            
            elseif($this_date == $current_date)
            {
                $calendar_data[$k]['classNames'] .= ' today-m ';   
            }
            elseif($availabilities->status == 'Closed')
            {
             $calendar_data[$k]['classNames'] .= ' status-not_available ';   
            }
            $space_bookings1 = ReservationTimes::with('reservation.users','reservation.activity_types')
                    ->whereSpaceId($space_id)->where('start_date', '<=', $this_date)->where('end_date', '>=', $this_date)->where('status','Not available')->first();
            $space_calendar1 = SpaceCalendar::where('space_id', $space_id)
                ->where(function($query) use ($this_date) {
                    $query->where('start_date', '<=', $this_date)->where('end_date', '>=', $this_date);
                })->first();
          
            if($space_bookings1){
            $bookings = ReservationTimes::with('reservation.users','reservation.activity_types')
                    ->whereSpaceId($space_id)->where('start_date', '<=', $this_date)->where('end_date', '>=', $this_date)->where('status','Not available')->get();
            $to_time=strtotime('00:00');
            foreach ($bookings as $space_bookings) {
            $between_days = getDays(strtotime($space_bookings->start_date), strtotime($space_bookings->end_date));

            $c_start_time=$space_bookings->start_time;

            if(reset($between_days) == end($between_days))
            {
            $c_end_time=$space_bookings->end_time;
            }
            else if($this_date == reset($between_days)) {
                $c_end_time = '23:59:00';
            }
            else if($this_date == end($between_days)) {
                $c_start_time = '00:00:00'; 
                 $c_end_time=$space_bookings->end_time;
            }
            else {
                $c_start_time = '00:00:00';
                $c_end_time = '23:59:00';
            } 
            $start_time = new DateTime(date('H:i:s',strtotime($c_start_time)));
            $end_time = new DateTime(date('H:i:s',strtotime($c_end_time)));
            $interval = $start_time->diff($end_time);
            $tot_time=$interval->format('%H:%I');
            $to_time+=strtotime($tot_time);                
            }
            if(date('H:i',(int)$to_time)>'23:00'){
             if($this_date < $current_date)
            {
                $calendar_data[$k]['classNames'] = ' status-prev ';
            }else            
             $calendar_data[$k]['classNames'] .= ' status-res ';
            }
            else{
                 if($this_date <$current_date)
                {
                    $calendar_data[$k]['classNames'] = ' status-prev ';
                }
                else
                $calendar_data[$k]['classNames'] .= ' status-res-rem ';}
            }
            // Calender Block classNames
            else if($space_calendar1){
            $calendar = SpaceCalendar::where('space_id', $space_id)
            ->where(function($query) use ($this_date) {
                $query->where('start_date', '<=', $this_date)->where('end_date', '>=', $this_date);
            })->get();
            $to_time=strtotime('00:00');
            foreach ($calendar as $space_calendar) {
            $between_days = getDays(strtotime($space_calendar->start_date), strtotime($space_calendar->end_date));
            $c_start_time=$space_calendar->start_time;
            if(reset($between_days) == end($between_days))
            {
            $c_end_time=$space_calendar->end_time;
            }
            else if($this_date == reset($between_days)) {
                $c_end_time = '23:59:00';
            }
            else if($this_date == end($between_days)) {
                $c_start_time = '00:00:00'; 
                 $c_end_time=$space_calendar->end_time;
            }
            else {
                $c_start_time = '00:00:00';
                $c_end_time = '23:59:00';
            }
            $start_time = new DateTime(date('H:i:s',strtotime($c_start_time)));
            $end_time = new DateTime(date('H:i:s',strtotime($c_end_time)));
            $interval = $start_time->diff($end_time);
            $tot_time=$interval->format('%H:%I');
            $to_time+=strtotime($tot_time);                
            }         
           
            if(date('H:i',(int)$to_time)>'23:00'){
             if($this_date < $current_date)
             {
                $calendar_data[$k]['classNames'] .= ' status-prev ';
             }
             else
             $calendar_data[$k]['classNames'] .= ($space_calendar->status != 'Available') ? ' status-block': 'avail_clr';
             $calendar_data[$k]['notes']  =$space_calendar->notes;
             $calendar_data[$k]['description']   = $space_calendar->status;
             }
             else{
             if($this_date <$current_date)
             {
                $calendar_data[$k]['classNames'] .= ' status-prev ';
             }
             else
             $calendar_data[$k]['classNames'] .= ($space_calendar->status != 'Available') ? '': 'avail_clr';
            $calendar_data[$k]['notes']  =$space_calendar->notes;             
             $calendar_data[$k]['description'] = 'Available';

             }
           
                }
            else{
                    if($this_date <$current_date)
                     {
                        $calendar_data[$k]['classNames'] = ' status-prev ';
                     }
                     else
                     $calendar_data[$k]['classNames'].=" avail_clr ";
                    $calendar_data[$k]['description']   = 'Available';
                }
                    $calendar_data[$k]['rendering']     = 'background';
            
            $to_time=strtotime('00:00');
            $day++;
            $k++;
        }  
      

        return $calendar_data;
    }
}