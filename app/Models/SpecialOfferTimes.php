<?php

/**
 * SpecialOffer Times Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    SpecialOffer Times
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialOfferTimes extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'special_offer_times';

	/**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

	public $timestamps = false;

	protected $appends = ['checkin_formatted', 'checkout_formatted', 'times_formatted'];

	// Join with Space table
	public function special_offer()
	{
		return $this->belongsTo('App\Models\SpecialOffer');
	}

	protected function getDateInFormat($field, $format = PHP_DATE_FORMAT)
	{
		$date_str = @$this->attributes[$field];
		return date($format, strtotime($date_str));
	}

	public function getCheckinFormattedAttribute()
	{
		return $this->getDateInFormat('start_date');
	}

	public function getCheckoutFormattedAttribute()
	{
		return $this->getDateInFormat('end_date');
	}

	public function getDatesFormattedAttribute()
	{
		return $this->checkin_formatted.' - '. $this->checkout_formatted;
	}

	public function getTimesFormattedAttribute()
	{
		return $this->start_time_formatted.' - '. $this->end_time_formatted;
	}

	public function getStartTimeFormattedAttribute()
	{
		return $this->getDateInFormat('start_time', view()->shared('time_format'));
	}

	public function getEndTimeFormattedAttribute()
	{
		return $this->getDateInFormat('end_time', view()->shared('time_format'));
	}

	public function getDiffHoursAttribute()
	{
		$checkin_ts	= strtotime($this->start_date.' '.$this->start_time);
        $checkout_ts= strtotime($this->end_date.' '.$this->end_time);

        $checkin	= getDateObject($checkin_ts);
        $checkout 	= getDateObject($checkout_ts);
        logger($this->start_date.' '.$this->start_time);
        logger($this->end_date.' '.$this->end_time);
        return $checkin->diffInHours($checkout);
	}

	public function getBetweenTimesAttribute()
	{
		$start_time = $this->attributes['start_time'];
        $end_time = $this->attributes['end_time'];
        return getTimes($start_time,$end_time);
	}
}