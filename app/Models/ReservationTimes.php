<?php

/**
 * Reservation Times Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Reservation Times
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\CalendarScopes;

class ReservationTimes extends Model
{
	use CalendarScopes;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reservation_times';

	protected $appends = ['checkin_formatted', 'checkout_formatted', 'times_formatted'];

	public $timestamps = false;

	// Join with Space table
	public function space()
	{
		return $this->belongsTo('App\Models\Space');
	}

	// Join with Space table
	public function reservation()
	{
		return $this->belongsTo('App\Models\Reservation');
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
		return $this->getDateInFormat('start_time',view()->shared('time_format'));
	}

	public function getEndTimeFormattedAttribute()
	{
		return $this->getDateInFormat('end_time',view()->shared('time_format'));
	}

	public function getBookingPeriodAttribute()
	{
		$booking_period = ($this->start_date == $this->end_date) ? 'Single' : 'Multiple';

		return $booking_period;
	}

	public function getBetweenTimesAttribute()
	{
		$start_time = $this->attributes['start_time'];
        $end_time = $this->attributes['end_time'];
        return getTimes($start_time,$end_time);
	}
}