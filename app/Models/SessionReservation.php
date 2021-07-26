<?php

/**
 * Session Reservations Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Session Reservations
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helper\PaymentHelper;
use JWTAuth;

class SessionReservation extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'event_type' => 'array',
        'booking_date_times' => 'array',
    ];

    // Join with Space table
    public function space()
    {
        return $this->belongsTo('App\Models\Space');
    }

    public function getPriceListAttribute()
    {
        $payment_helper = new PaymentHelper;
        $event_type = $this->event_type;
        $booking_times = $this->booking_date_times;
        $booking_period = ($booking_times['start_date'] == $booking_times['end_date']) ? 'Single' : 'Multiple';

        $price_data = array('space_id' => $this->space_id, 'event_type' => $event_type, 'booking_date_times' => $booking_times, 'number_of_guests' => $this->number_of_guests, 'booking_period' => $booking_period);

        $additional_data = array('special_offer_id' => $this->special_offer_id, 'reservation_id' => $this->reservation_id);

        $coupon_status = $this->validateCouponCode($this->coupon_code);
        if($coupon_status->status) {
            session(['coupon_code' => $coupon_status->coupon_code]);
        }
        else {
            session()->forget('coupon_code');
        }

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

        session(['currency' => $currency_code]);

        $price_list = $payment_helper->price_calculation($this->space_id, (Object)$price_data, (Object)$additional_data);

        return json_decode($price_list);
    }

    public function validateCouponCode($coupon_code)
    {
        $result = CouponCode::where('coupon_code', $coupon_code)->where('status','Active')->first();
        $return_data        = array('status' => false);

        if($result == '') {
            $return_data['status_message'] = __('messages.payments.invalid_coupon');
            return arrayToObject($return_data);
        }
        $user_id = JWTAuth::parseToken()->authenticate()->id;

        // check if coupon already used by the user
        $used_count = Reservation::where('user_id', $user_id)->where('coupon_code', $coupon_code)->count();
        if($used_count > 0) {
            $return_data['status_message'] = __('messages.payments.coupon_already_used');
            return arrayToObject($return_data);
        }

        $datetime1 = getDateObject(date('Y-m-d')); 
        $datetime2 = getDateObject(custom_strtotime($result->expired_at));

        if($datetime1 <= $datetime2) {
            $return_data['status'] = true;
            $return_data['coupon_code'] = $coupon_code;
            return arrayToObject($return_data);
        }

        $return_data['status_message'] = __('messages.payments.expired_coupon');

        return arrayToObject($return_data);
    }
}
