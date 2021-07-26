<?php

/**
 * Payment Helper
 *
 * @package     Makent Space
 * @subpackage  Helper
 * @category    Helper
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Helper;

use App\Models\Space;
use App\Models\SpaceActivities;
use App\Models\ActivityPrice;
use App\Models\Activity;
use App\Models\SubActivity;
use App\Models\SpacePrice;
use App\Models\SpaceCalendar;
use App\Models\Currency;
use App\Models\SpecialOffer;
use App\Models\Reservation;
use App\Models\Fees;
use App\Models\HostPenalty;
use App\Models\CouponCode;
use App\Models\Payouts;
use App\Models\Referrals;
use App\Models\AppliedTravelCredit;
use App\Models\SpaceAvailability;
use App\Models\ReservationTimes;
use Carbon\Carbon;
use JWTAuth;

class PaymentHelper
{
	/**
	* Common Function for Price Calculation
	*
	* @param int $space_id   Space Id
	* @param Object $price_data Data For Calculate Price
	* @param Object $optional_details Data For Calculate Price
	* @return json   Calculation Result
	*/
	public function price_calculation($space_id, $price_data, $optional_details = NULL)
	{
		$return_data = $this->getDefaultReturnData();

		$validate_data = $this->ValidatePriceData($space_id,$price_data);

		if($validate_data['status'] != 'Success') {
			$return_data['status_message'] = $validate_data['status_message'];
			return json_encode($return_data);
		}

		$space_details 			= $validate_data['space_details'];
		$activity_details 		= $validate_data['selected_activity'];
		$activity_price 		= $validate_data['activity_price'];
		$booking_data 			= $price_data->booking_date_times;
		// Check Date and time available or not
		$valid_time = $this->validateDateTime($space_id,$booking_data);

		if(!$valid_time) {
			$return_data['not_available'] 	= true;
			$return_data['status_message'] 	= __('messages.booking.times_not_available');
			return json_encode($return_data);
		}

		// Basic Hour/Day Calculation
		$total_days 	= 1;
		if($price_data->booking_period == 'Multiple') {
			$start_date_time = $booking_data['formatted_start_date'].' '.$booking_data['start_time'];
			$end_date_time = $booking_data['formatted_end_date'].' '.$booking_data['end_time'];
			$total_hours = $this->getTotalHours($start_date_time,$end_date_time,'Y-m-d H:i:s');
			$total_days = $this->getTotalDays($booking_data['formatted_start_date'],$booking_data['formatted_end_date'],'Y-m-d');

			$total_days = ($total_days == 0 )? 1 : $total_days;
		}
		else {
			$total_hours = $this->getTotalHours($booking_data['start_time'],$booking_data['end_time']);
		}
		
		$total_hour_val=$total_hours/24;

		if($total_hours <= 8) {
		$total_price = ($activity_price->hourly * $total_hours);
		$count_total_hour=$total_hours;
		$count_total_days=0;
		$count_total_week=0;
		$count_total_month=0;
		}
		elseif($activity_price->full_day && $total_hours > 8 && $total_hour_val<7) {
			
		if($total_hours < 24){
		$total_price = $activity_price->full_day * 1;	
		$count_total_hour=0;
		$count_total_days=1;
		$count_total_week=0;
		$count_total_month=0;
		}
		else{
		$day_convert_hour=(int)$total_hour_val*24;
		$balance_hour_val=$total_hours-$day_convert_hour;		
		$total_day_calc = $activity_price->full_day * (int)$total_hour_val;
		$count_total_days=(int)$total_hour_val;		
		$count_total_week=0;
		$count_total_month=0;
		if($balance_hour_val<=8)
		{			
		$total_hour_calc = $activity_price->hourly * $balance_hour_val;
		$count_total_hour=$balance_hour_val;
		}
		else
		{			
		$total_hour_calc = $activity_price->full_day * 1;
		$count_total_days=(int)$total_hour_val+1;
		$count_total_hour=0;
		}

		
		$total_price = $total_day_calc+$total_hour_calc;
		}
		}		
		elseif($activity_price->weekly && $total_hour_val < 30) {
		$total_weel_val=$total_hour_val/7;
		$total_day_val=((int)$total_weel_val*7)*24;
		$balance_week_val=$total_hours-$total_day_val;			
		$balance_day_val=$balance_week_val/24;
		$balance_hour_val=$balance_day_val- (int)$balance_day_val;
		$balance_hour_val2=$balance_hour_val*24;
		$count_total_days=(int)$balance_day_val;
		if($balance_hour_val2<=8){		
		$total_hour_calc = $activity_price->hourly * $balance_hour_val2;
		$count_total_hour=$balance_hour_val2;
		}
		else{		
		$total_hour_calc = $activity_price->full_day * 1;
		$count_total_hour=0;
		$count_total_days=(int)$balance_day_val+1;
		}

		$count_total_week=(int)$total_weel_val;
		$count_total_month=0;
		$total_day_calc = $activity_price->full_day * (int)$balance_day_val;
		$total_week_calc = $activity_price->weekly * (int)$total_weel_val;
		$total_price = $total_day_calc+$total_hour_calc+$total_week_calc;		
		}
		elseif($activity_price->monthly && $total_hour_val>=30) {
			
		$total_month_val=$total_hour_val/30;
    
		$total_week_val=(((int)$total_month_val)*30)*24;
		$balance_week_val=$total_hours-$total_week_val;	
		$balance_week_val2=$balance_week_val/24;	
		$balance_week_val3=$balance_week_val2/7;	
		$balance_week_val4=((int)$balance_week_val3*7)*24;	
		$balance_day_val=($balance_week_val-$balance_week_val4)/24;
		$balance_hour_val=($balance_day_val-(int)$balance_day_val);
		$balance_hour_val2=$balance_hour_val*24;

		$count_total_days=(int)$balance_day_val;
		if($balance_hour_val2<=8){
		$total_hour_calc = $activity_price->hourly * $balance_hour_val2;
		$count_total_hour=$balance_hour_val2;
		}
		else
		{
		$total_hour_calc = $activity_price->full_day * 1;
		$count_total_days=(int)$balance_day_val+1;
		$count_total_hour=0;
		}
		$count_total_week=(int)$balance_week_val3;
		$count_total_month=(int)$total_month_val;
		$total_day_calc = $activity_price->full_day * (int)$balance_day_val;
		$total_week_calc = $activity_price->weekly * (int)$balance_week_val3;
		$total_month_calc = $activity_price->monthly * (int)$total_month_val;
		$total_price = $total_day_calc+$total_hour_calc+$total_week_calc+$total_month_calc;		
		
		}

		
		/*if($activity_price->full_day && $total_hours > 8) {
			$total_price = ($activity_price->full_day * $total_days);
		}
		else {
			$total_price = ($activity_price->hourly * $total_hours);
		}*/

		if($activity_price->min_hours > $total_hours) {
			$remain_hours = $activity_price->min_hours - $total_hours;
			$total_hours  = $activity_price->min_hours;
			$total_price += ($activity_price->hourly * $remain_hours);
		}
		$additional_total_price=$total_price;	

		$display_name = '';
		if(isset($activity_details['sub_activity'])) {
			$display_name .= $activity_details['sub_activity'].', ';
		}
		if(isset($activity_details['activity'])) {
			$display_name .= $activity_details['activity'];
		}
		
		$return_data['activity_type'] 	= $price_data->event_type['activity_type'];
		$return_data['activity'] 		= $price_data->event_type['activity'];
		$return_data['sub_activity'] 	= $price_data->event_type['sub_activity'];

		$return_data['display_name'] 	= $display_name;
		$return_data['service_fee'] 	= $this->calculateServiceFee($total_price);
		
		$return_data['host_fee']        = $this->calculateHostFee($total_price);
		$return_data['total_hours'] 	= $total_hours;
		$return_data['total_hour_price']= round($total_price);
		$return_data['original_code']	= $activity_price->getOriginal('currency_code');
		$return_data['base_hour_price'] = round($activity_price->hourly);
		$return_data['security_fee']	= $validate_data['space_price']->security;
		$return_data['additional_total_price']	= round($additional_total_price);
		$return_data['full_day_amount']	= round($activity_price->full_day);
		$return_data['hour_amount']		= round($activity_price->hourly);
		$return_data['weekly_amount']	= round($activity_price->weekly);
		$return_data['monthly_amount']	= round($activity_price->monthly);
		$return_data['count_total_hour']	= round($count_total_hour);
		$return_data['count_total_days']	= round($count_total_days);
		$return_data['count_total_week']	= round($count_total_week);
		$return_data['count_total_month']	= round($count_total_month);
		$return_data['subtotal']		= round($total_price);
		$return_data['per_hour']		= round($activity_price->hourly);
		$return_data['total_price']		= round($total_price + $return_data['service_fee']);
		$return_data['total_price_hour']		= round($total_price + $return_data['service_fee']);
		$return_data['status']			= 'Available';
		$return_data['booking_period']	= $price_data->booking_period;
		$return_data['status_message'] 	= '';

		// Reservation
		$reservation                  = Reservation::find(optional($optional_details)->reservation_id);
		if($reservation) {
			$return_data['security_fee'] 	= $reservation->security;
			//$return_data['total_hours'] 	= $reservation->hours;
			if(request()->segment(1) == 'api'){
			$return_data['total_hours'] 	= ($reservation->days==0) ? $reservation->hours : (($reservation->days * 24) + $reservation->hours);
			}else{
				$return_data['total_hours'] 	= $reservation->hours;
			}
			
			$return_data['per_hour'] 		= $reservation->per_hour;
			$return_data['base_hour_price'] = $reservation->base_per_hour;
			$return_data['total_hour_price']= $reservation->subtotal;
			$return_data['service_fee']		= $reservation->service;
			$return_data['host_fee']		= $reservation->host_fee;
			$return_data['total_price']		= $reservation->total;
			$return_data['payout']  		= $reservation->payout;
		}

		// Special Offer
		$special_offer_id = optional($optional_details)->special_offer_id;
		$special_offer    = SpecialOffer::find($special_offer_id);
		if($special_offer && $special_offer->type == 'special_offer') {
			$return_data['special_offer']    = "yes";
			$return_data['total_hour_price'] = $total_price = $special_offer->price;
			$return_data['per_hour']       	= round($total_price/$total_hours);
			$return_data['base_hour_price'] = round($total_price/$total_hours);
			$return_data['service_fee'] 	= $this->calculateServiceFee($total_price);			
			$return_data['host_fee']        = $this->calculateHostFee($total_price);
			$return_data['subtotal']        = $special_offer->price;
		}

		// Coupon Code or Travel Credit
		$coupon_amount_total          = $return_data['subtotal'] + $return_data['service_fee'];
		if(session('coupon_code')) {
			$coupon_code                = session('coupon_code');
			if($coupon_code == 'Travel_Credit') {
				$coupon_amount            = session('coupon_amount');
				$return_data['coupon_amount']  = ($coupon_amount_total >= $coupon_amount) ? $coupon_amount : $coupon_amount_total;
			}
			else {
				$coupon_details           = CouponCode::where('coupon_code', $coupon_code)->first();
				if($coupon_details) {
					$coupon_amount = currency_convert($coupon_details->currency_code,session('currency'),$coupon_details->amount);

					$return_data['coupon_amount']  = ($coupon_amount_total >= $coupon_amount) ? $coupon_amount : $coupon_amount_total;
				}
			}
			$return_data['coupon_code']      = $coupon_code;
		}

		$payment_total                	= array_sum([$return_data['subtotal'], $return_data['service_fee'], ]) - array_sum([$return_data['coupon_amount'], ]);
		$return_data['payment_total']   = $payment_total > 0 ? $payment_total : 0;
		$return_data['payout']          = $return_data['payment_total'] - $return_data['service_fee'] - $return_data['host_fee'];

		return json_encode($return_data);
	}

	protected function getCurrencyCode()
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
            return $currency_code;
        }
        return session('currency');
	}

	protected function getDefaultReturnData()
	{
		$return_data['status'] 				= 'Not available';
		$return_data['not_available'] 		= false;
		$return_data['status_message'] 		= __('messages.booking.something_went_wrong');
		$return_data['booking_period'] 		= 'Multiple';
		$return_data['currency_code']       = $this->getCurrencyCode();
		$return_data['per_hour']            = 0; // per hour price calculated overall
		$return_data['base_hour_price'] 	= 0; // base per hour price calculated only hours
		$return_data['total_hours'] 		= 0; // Total Hours
		$return_data['total_hour_price'] 	= 0; // Total Hours Price
		$return_data['security_fee'] 		= 0;
		$return_data['host_fee']         	= 0;
		$return_data['service_fee']         = 0;
		$return_data['subtotal']            = 0; // Total price include all price except service fee
		$return_data['total_price']		 	= 0; // Total Hours include all price
		
		$return_data['coupon_code']         = 0;
		$return_data['coupon_amount']       = 0;		
		$return_data['special_offer']       = '';
		$return_data['payment_total']       = 0;
		$return_data['payout']              = 0;

		return $return_data;
	}

	protected function ValidatePriceData($space_id,$price_data)
	{
		$result['status'] = 'Success';
		$space_details = Space::with('space_price','space_activities.activity_type','space_activities.activity_price','space_availabilities.availability_times')->find($space_id);

		if(!$space_details) {
			$result['status'] = 'Failed';
			$result['status_message'] = __('messages.booking.invalid_details');
			return $result;
		}

		$activity_type 	= $price_data->event_type['activity_type'];
		$activity_details = $space_details->space_activities->where('activity_type_id',$activity_type)->first();
		$activity_price   = optional($activity_details)->activity_price;

		if(!$activity_price) {
			$result['status'] = 'Failed';
			$result['status_message'] = __('messages.booking.activity_removed_by_host');
			return $result;
		}

		if($space_details->number_of_guests < $price_data->number_of_guests) {
			$result['status'] = 'Failed';
			$result['status_message'] = 'Maximum Guest is '.$space_details->number_of_guests;
			return $result;
		}

		if(!isset($price_data->event_type['sub_activity'])) {
			$result['status'] = 'Failed';
			$result['status_message'] = __('messages.booking.activity_removed_by_host');
			return $result;
		}

		$activity = Activity::find($price_data->event_type['activity']);
		$subactivity = SubActivity::find($price_data->event_type['sub_activity']);

		$selected_activity['activity'] = optional($activity)->name;
		$selected_activity['sub_activity'] = optional($subactivity)->name;

		$result['space_details'] 	= $space_details;
		$result['activity_details']	= $activity_details;
		$result['selected_activity']= $selected_activity;
		$result['activity_price'] 	= $activity_price;
		$result['space_price'] 		= $space_details->space_price;

		return $result;
	}

	protected function getTotalHours($start_date, $end_date, $format = 'H:i:s')
	{
		$start_date = Carbon::createFromFormat($format, $start_date);
		$end_date 	= Carbon::createFromFormat($format, $end_date);

		if($end_date->format('H:i') == "23:59") {
			$end_date->addMinute();
		}
		return $start_date->diffInHours($end_date);
	}

	protected function getTotalDays($start_date, $end_date, $format = 'Y-m-d')
	{
		$start_date = Carbon::createFromFormat($format, $start_date);
		$end_date 	= Carbon::createFromFormat($format, $end_date);
		return $start_date->diffInDays($end_date);
	}

	protected function calculateServiceFee($price)
	{
		$service_fee_percentage = Fees::find(1)->value;
		$min_service_fee        = Fees::find(8)->value;
		$fee_currency           = Fees::find(9)->value;
		$min_service_fee  		= currency_convert($fee_currency,'',$min_service_fee);

		$service_fee   = round(($service_fee_percentage / 100) * $price);
		if($service_fee < $min_service_fee && $service_fee_percentage) {
			$service_fee = $min_service_fee;
		}
		
		return $service_fee;
	}

	protected function calculateHostFee($price)
	{
		$host_fee_percentage    = Fees::find(2)->value;

		$host_fee = round(($host_fee_percentage / 100) * $price);
		return $host_fee;
	}

	protected function validateDateTime($space_id, $booking_data)
	{
		$start_date = $booking_data['formatted_start_date'];
		$start_time = $c_start_time = $booking_data['start_time'];
		
		if(!isset($booking_data['formatted_end_date'])) {
			$end_date 	= $start_date;
			$end_time	= $c_end_time = $start_time;
		}
		else {
			$end_date 	= $booking_data['formatted_end_date'];
			$end_time	= $c_end_time = $booking_data['end_time'];
		}
		$reserve_id = $booking_data['id'] ?? '';
		$booking_period = ($start_date == $end_date) ? 'Single' : 'Multiple';

		$checkin 	= strtotime($start_date.' '.$start_time);
		$checkout 	= strtotime($end_date.' '.$end_time);

		$user_current_time = time();
		if (array_key_exists("user_time_zone",$booking_data)) {
			$user_current_time = new \DateTime("now", new \DateTimeZone($booking_data['user_time_zone']));
			$user_current_time = strtotime($user_current_time->format('Y-m-d H:i:s'));
		}

		if($start_date > $end_date || ($start_date == $end_date && $start_time >= $end_time) || ($user_current_time >= $checkin || $user_current_time >= $checkout) ) {
			return false;
		}

		$between_days = getDays(strtotime($start_date), strtotime($end_date));
		foreach ($between_days as $day) {
			$checkin_day = getDateObject($day)->format('w');

			if($booking_period == 'Multiple') {
				if($day == reset($between_days)) {
				    $c_end_time = '23:59:00';
				}
				else if($day == end($between_days)) {
					$c_start_time = '00:00:00';
				    $c_end_time = $end_time;
				}
				else {
					$c_start_time = '00:00:00';
					$c_end_time = '23:59:00';
				}
			}

			$avail_time = SpaceAvailability::with('availability_times')
			->whereSpaceId($space_id)
			->availableWithTime($checkin_day,$c_start_time, $c_end_time)
			->count();

			if($avail_time == 0) {
				return false;
			}
		}

		$blocked_count = SpaceCalendar::whereSpaceId($space_id)
			->onlyNotAvailable()
			->validateDateTime($checkin, $checkout)
			->count();

		$booking_count = ReservationTimes::whereSpaceId($space_id)
			->onlyNotAvailable()
			->validateDateTime($checkin, $checkout)
			->count();

		return ($blocked_count == 0 && $booking_count == 0);
	}

	/**
     * Penalty Amount Check
     *
     * @param total $amount   Given amount
     * @return check if any penalty for this host then return remaining amount
     */
	public function check_host_penalty($penalty, $reservation_amount,$reservation_currency_code)
	{
		$penalty_id = '';
		$penalty_amount = '';

		if($penalty->count() > 0 ) {
			$host_amount = $reservation_amount;
			foreach ($penalty as $pen) {
				// Convert the host amount to peanlty currency to compare
				$host_amount = currency_convert($reservation_currency_code,$pen->currency_code,$host_amount);

				$remaining_amount = $pen->remain_amount;
				if($host_amount >= $remaining_amount) {
					$host_amount = $host_amount - $remaining_amount;

					$pen->remain_amount    = 0;
					$pen->status           = "Completed";
					$pen->save();

					$penalty_id .= $pen->id.',';
					$penalty_amount .= $remaining_amount.',';
				}
				else {
					$amount_reamining = $remaining_amount - $host_amount;

					$pen->remain_amount  = $amount_reamining;
					$pen->save();

					$penalty_id .= $pen->id.',';
					$penalty_amount .= $amount_reamining.',';

					$host_amount = 0;
				}
				// Revert the host amount to reservation currency code
				$host_amount = currency_convert($pen->currency_code,$reservation_currency_code,$host_amount);
			}
			$penalty_id     = rtrim($penalty_id, ',');
			$penalty_amount = rtrim($penalty_amount, ',');
		}
		else {
			$host_amount = $reservation_amount;
			$penalty_id     = 0;
			$penalty_amount = 0;
		}

		$result['host_amount']     = $host_amount;
		$result['penalty_id']      = $penalty_id;
		$result['penalty_amount']  = $penalty_amount;

		return $result;
	}

	public function revert_travel_credit($reservation_id)
	{
		$applied_referrals = AppliedTravelCredit::whereReservationId($reservation_id)->get();

		foreach($applied_referrals as $row) {
			$referral = Referrals::find($row->referral_id);

			if($row->type == 'main') {
				$referral->credited_amount = $referral->credited_amount + currency_convert($row->currency_code, $referral->currency_code, $row->original_amount);
			}
			else {
				$referral->friend_credited_amount = $referral->friend_credited_amount + currency_convert($row->currency_code, $referral->currency_code, $row->original_amount);
			}

			$referral->save();
			$applied_referrals = AppliedTravelCredit::find($row->id)->delete();
		}
	}

    /**
     * To process the payouts and refunds based on reservations
     *
     * @param App\Models\Reservation $reservation
     * @param Int $guest_refundable_amount 
     * @param Int $host_payout_amount 
     */
    public function payout_refund_processing($reservation, $guest_refundable_amount = 0, $host_payout_amount = 0, $host_penalty_amount = 0)
    {
      	// Create new / Find Payout row for Guest & Host
    	$guest_check_data = array(
    		'user_id' => $reservation->user_id,
    		'reservation_id' => $reservation->id,
    	);
    	$guest_refund = Payouts::firstOrNew($guest_check_data);

    	$host_check_data = array(
    		'user_id' => $reservation->host_id,
    		'reservation_id' => $reservation->id,
    	);
    	$host_payout = Payouts::firstOrNew($host_check_data);

      	// Revert already applied penalty for this reservation
    	if(@$host_payout->penalty_id != 0 && @$host_payout->penalty_id != '')
    	{
    		$penalty_id = explode(",",$host_payout->penalty_id);
    		$penalty_amt = explode(",",$host_payout->penalty_amount);
    		$i =0;
    		foreach($penalty_id as $row) 
    		{
    			$old_amt = HostPenalty::where('id',$row)->get();

    			if(@$penalty_amt[$i]){
    				$upated_amt = $old_amt[0]->remain_amount + $penalty_amt[$i];
    				HostPenalty::where('id',$row)->update(['remain_amount' => $upated_amt,'status' => 'Pending' ]); 
    			}
    			$i++;
    		}
    	}

      	// Process and Save guest refund amount
    	if($guest_refundable_amount > 0) {
    		if(!@$guest_refund->id) {
    			$guest_refund->reservation_id = $reservation->id;
    			$guest_refund->space_id        = $reservation->space_id;
    			$guest_refund->user_id        = $reservation->user_id;
    			$guest_refund->user_type      = 'guest';
    			$guest_refund->currency_code  = $reservation->currency_code;
    			$guest_refund->status         = 'Future';
    			$guest_refund->save();
    		}
    		$guest_refund->currency_code  = $reservation->currency_code;
    		$guest_refund->amount         = $guest_refundable_amount;
    		$guest_refund->currency_code  = $reservation->currency_code;
    		$guest_refund->save();
    	}

      	// Save the host penalty amount for this reservation
    	if($host_penalty_amount > 0) {
    		$penalty = new HostPenalty;
    		$penalty->reservation_id = $reservation->id;
    		$penalty->space_id        = $reservation->space_id;
    		$penalty->user_id        = $reservation->host_id;
    		$penalty->remain_amount  = $host_penalty_amount;
    		$penalty->amount         = $host_penalty_amount;
    		$penalty->currency_code  = $reservation->currency_code;
    		$penalty->status         = 'Pending';
    		$penalty->save();
    	}

      	// Process and Save the host payout amount with host penalty
    	if($host_payout_amount > 0) {
        	// Apply penaly for final host payout amount
    		$penalty = HostPenalty::where('user_id',$reservation->host_id)->where('remain_amount','!=',0)->get();
    		$penalty_result = $this->check_host_penalty($penalty,$host_payout_amount,$reservation->currency_code);
    		$host_amount    = $penalty_result['host_amount'];
    		$penalty_id     = $penalty_result['penalty_id'];
    		$penalty_amount = $penalty_result['penalty_amount'];

    		if(!@$host_payout->id) {
    			$host_payout->reservation_id = $reservation->id;
    			$host_payout->space_id       = $reservation->space_id;
    			$host_payout->user_id        = $reservation->host_id;
    			$host_payout->user_type      = 'host';
    			$host_payout->currency_code  = $reservation->currency_code;
    			$host_payout->status         = 'Future';
    			$host_payout->save();
    		}
    		$host_payout->currency_code  = $reservation->currency_code;
    		$host_payout->amount         = $host_amount;
    		$host_payout->penalty_amount = $host_payout_amount - $host_amount;
    		$host_payout->penalty_id     = $penalty_id;
    		$host_payout->save();
    	}
    	else {
    		if($host_payout) {
    			$host_payout->delete();
    		}
    	}
    }
}