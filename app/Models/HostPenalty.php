<?php

/**
 * Email Settings Model
 *
 * @package     Makent
 * @subpackage  Model
 * @category    Email Settings
 * @author      Trioangle Product Team
 * @version     1.6
 * @link        http://trioangle.com
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\CurrencyConversion;

class HostPenalty extends Model
{
    use CurrencyConversion;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'host_penalty';

    protected $convert_fields = [];

    // Get Penalty Amount
    public function getConvertedAmountAttribute()
    {
        return $this->currency_calc('amount');
    }

    // Get Penalty Remaining Amount
    public function getConvertedRemainAmountAttribute()
    {
        return $this->currency_calc('remain_amount');
    }

    // Calculation for current currency conversion of given price field
    public function currency_calc($field)
    {
        $rate = Currency::whereCode($this->attributes['currency_code'])->first()->rate;
        $amount = @$this->attributes[$field] / $rate;

        $session_currency = session('currency');

        // Admin Panel send without currency conversion
        if(request()->segment(1) == 'admin') {
            // return @$this->attributes[$field];
        }

        if(request()->segment(1) == 'api') {
            $session_currency = JWTAuth::parseToken()->authenticate()->currency_code;
        }

        if(!$session_currency || request()->segment(1) == ADMIN_URL) {
            $session_currency = Currency::where('default_currency', 1)->first()->code;
        }

        $session_rate = Currency::whereCode($session_currency)->first()->rate;

        return round($amount * $session_rate);
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_code', 'code');
    }
    
}
