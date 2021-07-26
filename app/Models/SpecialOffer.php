<?php

/**
 * SpecialOffer Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    SpecialOffer
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\CurrencyConversion;

class SpecialOffer extends Model
{
    use CurrencyConversion;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'special_offer';

    public $timestamps = false;

    protected $convert_fields = ['price'];

    public $appends = ['dates_subject','booking_link','checkin_with_time','checkout_with_time'];

    public function space()
    {
        return $this->belongsTo('App\Models\Space','space_id','id');
    }

    // Join with currency table
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_code','code');
    }

    // Join with messages table
    public function messages()
    {
        return $this->belongsTo('App\Models\Messages','id','special_offer_id');
    }

    // Join with special_offer_times table
    public function special_offer_times()
    {
        return $this->hasOne('App\Models\SpecialOfferTimes');
    }

    public function space_calendar()
    {
        return $this->hasMany('App\Models\SpaceCalendar', 'space_id', 'space_id');
    }

    protected function getDateInFormat($field, $format = PHP_DATE_FORMAT)
    {
        $date_str = @$this->$field;
        return date($format, strtotime($date_str));
    }

    public function getIsBookedAttribute()
    {
        $booked_remove_offer = Reservation::where('special_offer_id',$this->attributes['id'])->count();

        return ($booked_remove_offer) ? false : true;
    }

    public function getCheckinAttribute()
    {
        $spl_offer_times = $this->special_offer_times;
        return optional($spl_offer_times)->start_date;
    }

    public function getCheckoutAttribute()
    {
        $spl_offer_times = $this->special_offer_times;
        return optional($spl_offer_times)->end_date;
    }

    public function getCheckinFormattedAttribute()
    {
        return $this->getDateInFormat('checkin');
    }

    public function getCheckoutFormattedAttribute()
    {
        return $this->getDateInFormat('checkout');
    }

    // Get This reservation date is avaablie
    public function getAvablityAttribute()
    {
        return 0;
        $calendar_not_available = $this->calendar()->where('date','>=',$this->checkin)->where('date', '<', $this->checkout)->where('status', 'Not available')->get();
        return ($calendar_not_available->count() > 0) ? 1 : 0;
    }

    // Get Date for Email Subject
    public function getCheckinWithTimeAttribute()
    {
        $booking_times = $this->special_offer_times;
        if(is_null($booking_times)) {
            return '';
        }

        return $booking_times->checkin_formatted .' '.$booking_times->start_time_formatted;
    }

    // Get Date for Email Subject
    public function getCheckoutWithTimeAttribute()
    {
        $booking_times = $this->special_offer_times;
        if(is_null($booking_times)) {
            return '';
        }

        return $booking_times->checkout_formatted .' '.$booking_times->end_time_formatted;
    }

    // Get Date for Email Subject
    public function getDatesSubjectAttribute()
    {
        return $this->checkin_with_time.' - '.$this->checkout_with_time;
    }

    public function getBookingPeriodAttribute()
    {
        $booking_period = ($this->checkin == $this->checkout) ? 'Single' : 'Multiple';

        return $booking_period;
    }

    public function getBookingLinkAttribute()
    {
        $attributes = array(
            'id'                => $this->attributes['space_id'],
            'space_id'          => $this->attributes['space_id'],
            'special_offer_id'  => $this->attributes['id'],
            'number_of_guests'  => $this->attributes['number_of_guests'],
            'booking_period'    => $this->booking_period,
        );
        $link = route('payment.home',$attributes);
        return $link;
    }
}