<?php

/**
 * Activity Price Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Activity Price
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use JWTAuth;
use Session;
use App\Repositories\CurrencyConversion;

class ActivityPrice extends Model
{
	use CurrencyConversion;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'activities_price';

	public $timestamps = false;	

	protected $guarded = [];

	protected $appends = ['original_hourly','original_full_day','original_weekly','original_monthly'];

	protected $convert_fields = ['hourly','full_day','weekly','monthly'];

	public function setFullDayAttribute($value)
	{
		$this->attributes['full_day'] = ($value == '' || $value == 0) ? null : $value;
	}

	// Save Model values to database without Trigger any events
	public function saveQuietly(array $options = [])
	{
	    return static::withoutEvents(function () use ($options) {
	        return $this->save($options);
	    });
	}

	// Join with currency table
	public function space_activities()
	{
		return $this->belongsTo('App\Models\SpaceActivities', 'activity_id', 'id');
	}

	public function getOriginalFullDayAttribute($value)
	{
		return $this->attributes['full_day'] ?? 0;
	}

	public function getOriginalHourlyAttribute($value)
	{
		return $this->attributes['hourly'] ?? 0;
	}
	public function getOriginalWeeklyAttribute($value)
	{
		return $this->attributes['weekly'] ?? 0;
	}
	public function getOriginalMonthlyAttribute($value)
	{
		return $this->attributes['monthly'] ?? 0;
	}

	public function getOriginalCurrencySymbolAttribute($value)
	{
		$currency = Currency::whereCode($this->attributes['currency_code'])->first();
		return $currency->original_symbol;
	}
}