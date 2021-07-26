<?php

/**
 * Payouts Model
 *
 * @package		Makent Space
 * @subpackage  Model
 * @category    Payouts
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use App\Repositories\CurrencyConversion;

class Payouts extends Model
{
	use CurrencyConversion;

	/**
     * The database table used by the model.
     *
     * @var string
     */
	protected $table = 'payouts';

	public $appends = ['currency_symbol', 'date'];

	protected $fillable = ['user_id', 'reservation_id'];

	protected $convert_fields = ['amount'];

	// Join to Reservation table
	public function reservation()
	{
		return $this->belongsTo('App\Models\Reservation','reservation_id','id');
	}

	// Join with users table
	public function users()
	{
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

    // Get Date with new format
	public function getDateAttribute()
	{
		return date(PHP_DATE_FORMAT, strtotime($this->attributes['updated_at']));
	}

    // Get Date with new format
	public function getTotalPenaltyAmountAttribute()
	{
		$penalty_ids = explode(',', $this->attributes['penalty_id']) ?: array();
		$penalty_amts = explode(',', $this->attributes['penalty_amount']) ?: array();
		$penalty_currencies  = HostPenalty::whereIn('id', $penalty_ids)->get()->pluck('original_currency_code');

		$penalty_amount_converted = array();
		foreach($penalty_ids as $k => $id) {
			$penalty_amount_converted[] = $this->currency_convert(@$penalty_currencies[$k], '', @$penalty_amts[$k]);
		}
		return array_sum($penalty_amount_converted);
	}

    // Calculation for current currency conversion of given amount
	public function currency_convert($from = '', $to = '', $price)
	{
		if($from == '') {
			if(session('currency') && request()->segment(1) != ADMIN_URL)
				$from = session('currency');
			else
				$from = Currency::where('default_currency', 1)->first()->code;
		}
		if($to == '') {
			if(session('currency') && request()->segment(1) != ADMIN_URL)
				$to = session('currency');
			else
				$to = Currency::where('default_currency', 1)->first()->code;
		}

		$rate = Currency::whereCode($from)->first()->rate;

		$usd_amount = $price / $rate;

		$session_rate = Currency::whereCode($to)->first()->rate;

		return ceil($usd_amount * $session_rate);
	}

	// Get Currency Symbol
	public function getCurrencySymbolAttribute()
	{
		$default_currency = Currency::where('default_currency',1)->first()->code;

		return DB::table('currency')->where('code', (session('currency')) ? session('currency') : $default_currency)->first()->symbol;
	}

	public function getSpotsArrayAttribute()
	{
		$spots_array = explode(',', @$this->attributes['spots']);
		$spots_array = array_map('intval', $spots_array);
		return $spots_array;
	}
}