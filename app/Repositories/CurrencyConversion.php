<?php

/**
 * CurrencyConversion
 *
 * @package     Makent Space
 * @subpackage  CurrencyConversion
 * @category    Repository
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use JWTAuth;

use App\Models\Currency;

trait CurrencyConversion
{
    public $currency_code_field = 'currency_code';

    public $convert_currency_code;

    public $is_convert = true;

    public $admin_url = '';

    public function __construct()
    {
        if(!defined('ADMIN_URL')) {
            $this->admin_url = 'admin';
        }
        else {
            $this->admin_url = ADMIN_URL;
        }
        if($this->isAdminPanel()) {
            $currency_code = Currency::defaultCurrency()->first()->code;
            $this->convert_currency_code =  $currency_code;
        }
        else {
            $this->convert_currency_code = $this->getSessionOrDefaultCode();
        }
    }

    // Join with currency table
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_code', 'code');
    }

    public function getSessionOrDefaultCode()
    {
        if(request()->segment(1) == 'api' || strlen(request()->token) > 25) {
            try{
                $user_details = JWTAuth::parseToken()->authenticate();
                if($user_details->currency_code) {
                    $currency_code = Currency::where('code', $user_details->currency_code)->first()->code;
                }
            }
            catch(\Exception $e) {
            }

            if(!isset($currency_code)) {
                $currency_code = Currency::defaultCurrency()->first()->code;
            }
        }
        else {
            $currency_code = session('currency');
        }

        $currency_code = $this->CheckCurrency($currency_code);

        if(!$currency_code || $this->isAdminPanel()) {
            $currency_code = Currency::defaultCurrency()->first()->code;
        }

        return $currency_code;
    }

    public function CheckCurrency($currency_code)
    {
        $currency = Currency::where('code',$currency_code)->where('status',"Active")->first();

        if(!$currency)
        {
            $currency_code = Currency::defaultCurrency()->first()->code;

        }
        return $currency_code;
    }

    public function getConvertCurrencyCode()
    {
        return $this->convert_currency_code;
    }

    public function setConvertCurrencyCode($currency_code = '')
    {
        if($currency_code == '') {
            $currency_code = $this->getSessionOrDefaultCode();
        }
        $this->convert_currency_code = $currency_code;
        return $this;
    }

    public function isAdminPanel()
    {
        return request()->segment(1) == $this->admin_url;
    }

    public function isManageListing()
    {
        $current_route = '';
        if(request()->route()) {
            $current_route = request()->route()->getName();
        }
        return ($current_route == 'manage_space' || $current_route == 'update_space');
    }

    public function getIsConvert()
    {
        return $this->is_convert;
    }

    public function original()
    {
        $this->is_convert = false;
        return $this;
    }

    public function session()
    {
        $this->is_convert = true;
        return $this;
    }

    public function getCurrencyCodeField()
    {
        return $this->currency_code_field;
    }

    public function setCurrencyCodeField($currency_code_field)
    {
        $this->currency_code_field = $currency_code_field;
        return $this;     
    }

    public function isConvertableAttribute($attribute)
    {
        return in_array($attribute, $this->getConvertFileds());
    }

    public function getConvertFileds()
    {
        return $this->convert_fields ?: array();
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        if ($this->canConvert()) {
            foreach($this->convert_fields as $field) {
                $attributes[$field] = $this->getAttribute($field);
            }
            $attributes['currency_code'] = $this->getToCurrencyCode();
        }

        return $attributes;
    }

    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['currency_symbol', 'original_currency_code']));

        return parent::getArrayableAppends();
    }

    public function canConvert()
    {
        return ($this->getIsConvert() && !$this->isManageListing());
    }

    public function getAttribute($attribute)
    {
        if($this->canConvert()) {
            if ($this->isConvertableAttribute($attribute)) {
                $value = parent::getAttribute($attribute);
                $converted_value = $this->getConvertedValue($value);
                return $converted_value;
            }

            if($attribute == 'currency_code') {
                return $this->getToCurrencyCode();
            }
        }
        return parent::getAttribute($attribute);
    }

    public function getSessionCurrencyAttribute()
    {
        return Currency::whereCode($this->getSessionOrDefaultCode())->first();
    }

    public function getCurrencySymbolAttribute()
    {
        if($this->getSessionCurrencyAttribute()) {
            return $this->getSessionCurrencyAttribute()->symbol;
        }
        return '$';
    }

    public function getOriginalCurrencyCodeAttribute()
    {
        return $this->getOriginal('currency_code');
    }

    public function getFromCurrencyCode()
    {
        $field = $this->getCurrencyCodeField();
        return parent::getAttribute($field) ?: '';
    }

    public function getToCurrencyCode()
    {
        $code = $this->getConvertCurrencyCode();
        return $code;
    }

    public function getConvertedValue($price)
    {
        $from = $this->getFromCurrencyCode();
        $to = $this->getToCurrencyCode();
        $converted_price = $this->currency_convert($from, $to, $price);
        return $converted_price;
    }

    /*// Calculation for current currency conversion of given price field
    public function currency_calc($field)
    {
        $rate = Currency::whereCode($this->attributes['currency_code'])->first()->rate;
        $amount = @$this->attributes[$field] / $rate;

        $session_currency = session('currency');

        if(request()->segment(1) == 'api') {
            $session_currency = JWTAuth::parseToken()->authenticate()->currency_code;
        }

        if(!$session_currency || request()->segment(1) == ADMIN_URL) {
            $session_currency = Currency::where('default_currency', 1)->first()->code;
        }

        $session_rate = Currency::whereCode($session_currency)->first()->rate;

        return round($amount * $session_rate);
    }*/

    public function currency_convert($from = '', $to = '', $price = 0)
    {
        if($from == '') {
            $from = $this->getSessionOrDefaultCode();
        }

        if($to == '') {
            $to = $this->getSessionOrDefaultCode();
        }

        $rate = Currency::whereCode($from)->first()->rate;
        $session_rate = Currency::whereCode($to)->first();

        if($session_rate) {
            $session_rate = $session_rate->rate;
        }
        else {
            $session_rate = '1';
        }

        if($rate!="0.0") {
            if($price)
                $usd_amount = $price / $rate;
            else
                $usd_amount = 0;
        }
        else {
            echo "Error Message : Currency value '0' (". $from . ')';
            die;
        }
        return round($usd_amount * $session_rate);
    }

    public function get_currency_from_ip($ip_address = '')
    {
        $ip_address = $ip_address ?: request()->getClientIp();
        $default_currency = Currency::active()->defaultCurrency()->first();
        $currency_code    = @$default_currency->code;
        if(session()->get('currency_code')) {
            $currency_code = session()->get('currency_code');
        }
        else if($ip_address!='') {
            $result = array();
            try {
              $result = file_get_contents_curl('http://www.geoplugin.net/php.gp?ip='.$ip_address);
            }
            catch(\Exception $e) {
            }
            if(@$result['geoplugin_currencyCode'])
            {
                $currency_code =  @$result['geoplugin_currencyCode'];
            }
            session()->put('currency_code', $currency_code);
        }
        return $currency_code;
    }
}