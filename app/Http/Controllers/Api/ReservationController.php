<?php

/**
 * Reservation Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Reservation
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\Http\Helper\PaymentHelper;
use App\Models\Currency;
use App\Models\Fees;
use App\Models\Messages;
use App\Models\Reservation;
use App\Models\ReservationTimes;
use App\Models\SpecialOffer;
use App\Models\SpecialOfferTimes;
use DB;
use JWTAuth;
use Validator;
use Carbon\Carbon;

class ReservationController extends Controller
{
	/**
	 * Constructor
	 *
	 */
	public function __construct(PaymentHelper $payment)
	{
		$this->payment_helper = $payment;
	}

	/**
	 *Display Reservation List
	 *
	 * @param  Get method inputs
	 * @return Response in Json
	 */
	public function reservation_list(Request $request)
	{
		$user = JWTAuth::parseToken()->authenticate();

		$reservations = Reservation::with('host_users.profile_picture','users.profile_picture','space.space_address','currency','reservation_times')->where('host_id', $user->id)->exceptContact()->orderBy('id', 'DESC')->get();

		if ($reservations->count() < 1) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> __('messages.api.no_reservation_found'),
			]);
		}

		$currency_details = Currency::where('code', $user->user_currency_code)->first();

		$result = $reservations->map(function($reservation) {
			$space_address = $reservation->space->space_address;

			$expire_timer = '';
			if ($reservation->status == 'Pending') {
				$from_time 	= strtotime($reservation->created_at_timer);
				$to_time 	= strtotime(date('Y/m/d H:i:s'));
				$diff 		= abs($from_time - $to_time);
				$expire_timer = sprintf('%02d:%02d:%02d', ($diff / 3600), ($diff / 60 % 60), $diff % 60);
			}

			$reservation_data = array(
				'reservation_id' 	=> $reservation->id,
				'space_id' 			=> $reservation->space_id,
				'booking_status' 	=> '',
				'reservation_status'=> $reservation->status,
			);

			$space = array(
				'space_name' 		=> $reservation->space->name,
				'space_type_name'	=> $reservation->space->space_type_name,
				'space_image' 		=> $reservation->space->photo_name,
				'space_location' 	=> $space_address->address_line,
			);

			$other_user = array(
				'other_user_id' 	=> $reservation->user_id,
				'other_user_name' 	=> $reservation->users->full_name,
				'other_thumb_image' => $reservation->users->profile_picture->email_src,
				'other_user_location'=> $reservation->users->live,
				'member_from' 		=> $reservation->users->since,
			);

			$reservation_price = array(
				'reservation_date' 	=> $reservation->dates_subject,
				'checkin' 			=> $reservation->checkin,
				'checkout' 			=> $reservation->checkout,
				'start_time' 		=> optional($reservation->reservation_times)->start_time_formatted,
				'end_time' 			=> optional($reservation->reservation_times)->end_time_formatted,
				'number_of_guests'	=> $reservation->number_of_guests,
				'currency_symbol'	=> $reservation->currency->symbol,
				'total_hours'		=> $reservation->hours,
				'total_cost'		=> $reservation->check_total,
				'review_count' 		=> $reservation->space->reviews_count,
			);

			$special_offer_data = array(
				'special_offer_id' 	=> '',
				'special_offer_status'	=> 'No',
			);

			$other_details = array(
				'can_view_receipt' 	=> ($reservation->status == 'Accepted'),
				'payment_recieved_date' => ($reservation->transaction_id != '') ? $reservation->updated_at : '',
				'expire_timer' 		=> $expire_timer,
			);

			$reservation_price = array_map('strval', $reservation_price);

			return array_merge($reservation_data,$space,$other_user,$reservation_price,$special_offer_data,$other_details);
		});

		return response()->json([
			'status_code' 	=> '1',
			'success_message' => __('messages.api.listed_successful'),
			'data' 			=> $result,
		]);
	}

	protected function cancelReservation($reservation_id,$status,$reason)
	{
		
		$reservation_details = Reservation::find($reservation_id);
		$reservation_details->status = $status;
		$reservation_details->decline_reason = $reason->decline_reason;
		$reservation_details->declined_at = date('Y-m-d H:m:s');

		$reservation_details->save();

		$messages = new Messages;
		$messages->space_id = $reservation_details->space_id;
		$messages->reservation_id = $reservation_details->id;
		$messages->user_to = $reservation_details->user_id;
		$messages->user_from = JWTAuth::parseToken()->authenticate()->id;
		$messages->message = removeEmailNumber($reason->decline_message);
		$messages->message_type = 3;
		$messages->save();

		$this->payment_helper->revert_travel_credit($reservation_details->id);
	}

	/**
     * Reservation Cancel by Host
     *
     * @param array $request Input values
     * @return response json
     */
    public function host_cancel_reservation(Request $request,EmailController $email_controller)
    {
    	$rules 		= array(
    		'reservation_id' => 'required|exists:reservation,id',
    		'cancel_reason'	 => 'required',
    	);
		$attributes	= array('reservation_id' => 'Reservation Id');
		$messages 	= array('required' => ':attribute is required.');

		$validator = Validator::make($request->all(), $rules, $messages, $attributes);

		if($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		$user = JWTAuth::parseToken()->authenticate();
		session(['currency' => $user->currency_code]);

        $reservation_details = Reservation::find($request->reservation_id);

		//check valid user or not
		if ($user->id != $reservation_details->host_id) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> 'Permission Denied',
			]);
		}

        if(in_array($reservation_details->status,['Cancelled','Declined'])) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> 'This Reservation Already Cancelled',
			]);
		}

		if($reservation_details->status == 'Pending') {
			$decline_reason 	= $request->cancel_reason;
			$decline_message 	= $request->cancel_message;
			$reason 			= arrayToObject(compact('decline_reason','decline_message'));
			$status 			= 'Declined';
			$this->cancelReservation($request->reservation_id,$status, $reason);
			return response()->json([
				'status_code' 		=> '1',
				'success_message' 	=> 'Reservation Request has Successfully Declined',
			]);
		}

        // Host Penalty Details from admin panel
        $host_fee_percentage        = Fees::find(2)->value;
        $host_penalty               = Fees::find(3)->value;
        $penalty_currency           = Fees::find(4)->value;
        $penalty_before_days        = Fees::find(5)->value;
        $penalty_after_days         = Fees::find(6)->value;
        $penalty_cancel_limits_count= Fees::find(7)->value;
        $host_payout_amount         = 0;
        $guest_refundable_amount    = 0;
        $host_penalty_amount        = 0;

        $cancel_count               = Reservation::where('host_id', auth()->id())->where('cancelled_by', 'Host')->where('cancelled_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 6 MONTH)'))->get()->count();
        
        // get the days difference between the checkin and the cancellation date
        // if host cancels the reservation on same checkin date, then that should be counted as host cancelling after guest checkin as well
        $datetime1 = Carbon::now();
        $datetime2 = getDateObject(strtotime($reservation_details->checkin));
        $interval_diff = $datetime1->diff($datetime2);
        //$interval = $interval_diff->days;
        $interval = ceil(((($interval_diff->d * 24 + $interval_diff->h) * 60 + $interval_diff->i)*60 + $interval_diff->s)/(24 * 60 * 60));

        $per_hour_price   = $reservation_details->per_hour;
        $total_hours      = $reservation_details->hours;

        $total_hour_price = $per_hour_price * $total_hours;
        if($interval_diff->invert && $reservation_details->status == 'Accepted') // To check the check in is less than today date
        {
            $spend_hour_price = $per_hour_price * ($interval <= $total_hours ? $interval : $total_hours);
            $remain_hour_price= $per_hour_price * (($total_hours - $interval) > 0 ? ($total_hours - $interval) : 0);
        }
        else
        {
            $spend_hour_price = 0;
            $remain_hour_price= $total_hour_price;
        }
        
        $cleaning_fees              = $reservation_details->cleaning;
        $coupon_amount              = $reservation_details->coupon_amount;
        $service_fee                = $reservation_details->service;
        $host_payout_ratio          = (1 - ($host_fee_percentage / 100));

        if(!$interval_diff->invert) // Cancel before checkin
        {
            $refund_hour_price = $total_hour_price;
            $guest_refundable_amount = array_sum([
                $refund_hour_price,
                $cleaning_fees,
                -$coupon_amount,
                $service_fee
            ]);

            $payout_hour_price = 0;
            $host_payout_amount = array_sum([
                $payout_hour_price,
            ]);

            if($cancel_count >= $penalty_cancel_limits_count && $host_penalty == 1)
            { 
                if($interval > 7)
                {
                    $host_penalty_amount= currency_convert($penalty_currency,$reservation_details->currency_code,$penalty_before_days);
                }
                else
                {
                    $host_penalty_amount= currency_convert($penalty_currency,$reservation_details->currency_code,$penalty_after_days);
                }
            }
        }
        else // Cancel after checkin
        {
            $refund_hour_price = $remain_hour_price;
            $guest_refundable_amount = array_sum([
                $refund_hour_price,
                -$coupon_amount,
            ]);

            $payout_hour_price = $spend_hour_price;
            $host_payout_amount = array_sum([
                $payout_hour_price,
                $cleaning_fees,
            ]);

            if($cancel_count >= $penalty_cancel_limits_count && $host_penalty == 1)
            { 
                $host_penalty_amount= currency_convert($penalty_currency,$reservation_details->currency_code,$penalty_after_days);
            }
        }
        
        $host_fee           = ($host_payout_amount * ($host_fee_percentage / 100));
        $host_payout_amount = $host_payout_amount * $host_payout_ratio;
        
        if($reservation_details->status != 'Accepted') {
            $guest_refundable_amount = 0;
            $host_payout_amount = 0;
        }

        $this->payment_helper->payout_refund_processing($reservation_details, $guest_refundable_amount, $host_payout_amount, $host_penalty_amount);
        // Revert travel credit if cancel before checkin
        if(!$interval_diff->invert) {
            $this->payment_helper->revert_travel_credit($reservation_details->id);
        }

        // Update Reservation Times
        $reservation_times = ReservationTimes::where('reservation_id',$reservation_details->id)->first();
        if($reservation_times) {
            $reservation_times->status = 'Available';
            $reservation_times->save();
        }

        $messages = new Messages;
        $messages->space_id        = $reservation_details->space_id;
        $messages->reservation_id = $reservation_details->id;
        $messages->user_to        = $reservation_details->user_id;
        $messages->user_from      = auth()->id();
        $messages->message        = removeEmailNumber($request->cancel_message);
        $messages->message_type   = 11;
        $messages->save();

        $cancel = Reservation::find($request->reservation_id);
        $cancel->host_fee = currency_convert($reservation_details->currency_code,$reservation_details->original_currency_code,$host_fee);
        $cancel->cancelled_by = "Host";
        $cancel->cancelled_reason = $request->cancel_reason;
        $cancel->cancelled_at = date('Y-m-d H:m:s');
        $cancel->status = "Cancelled";
        $cancel->updated_at = date('Y-m-d H:m:s');
        $cancel->save();

        $email_controller->cancel_host($cancel->id);

        return response()->json([
			'status_code' 		=> '1',
			'success_message' 	=> 'Reservation Successfully Cancelled',
		]);
    }

	/**
	 * Ajax function for Conversation reply
	 *
	 * @param array $request  Input values
	 * @return html Reply message html
	 */
	public function pre_approve(Request $request, EmailController $email_controller)
	{
		$reservation_details = Reservation::with('space','reservation_times')->find($request->reservation_id);

		$message = removeEmailNumber($request->message);

		if ($reservation_details->user_id == JWTAuth::parseToken()->authenticate()->id) {
			$messages = new Messages;
			$messages->space_id = $reservation_details->space_id;
			$messages->reservation_id = $reservation_details->id;
			$messages->user_to = $reservation_details->space->user_id;
			$messages->user_from = JWTAuth::parseToken()->authenticate()->id;
			$messages->message = $message;
			$messages->message_type = 5;
			$messages->save();

			return response()->json([
				'status_code' => '1',
				'success_message' => 'Message Send Successfully',
			]);
		}

		$user_details = JWTAuth::parseToken()->authenticate();

		if ($reservation_details->space->user_id == $user_details->id) {
			$message_type = 5;
			if ($request->template == 1) {

				$message_type = 6;

				$special_offer = new SpecialOffer;

                $special_offer->reservation_id  = $reservation_details->id;
                $special_offer->space_id        = $reservation_details->space_id;
                $special_offer->user_id         = $reservation_details->user_id;
                $special_offer->activity_type   = $reservation_details->activity_type;
                $special_offer->activity        = $reservation_details->activity;
                $special_offer->sub_activity    = $reservation_details->sub_activity;
                $special_offer->number_of_guests= $reservation_details->number_of_guests;

                $special_offer->price           = $reservation_details->subtotal;
                $special_offer->currency_code   = $user_details->currency_code;
                $special_offer->type            = 'pre-approval';
                $special_offer->created_at      = date('Y-m-d H:i:s');
                $special_offer->save();

                $reservation_times              = $reservation_details->reservation_times;
                $spl_offer_times = $reservation_times->only(['space_id','start_date','end_date','start_time','end_time']);
                $spl_offer_times['special_offer_id'] = $special_offer->id;

                SpecialOfferTimes::create($spl_offer_times);

                $reservation_details->status = 'pre-approved';
				$reservation_details->save();

				$email_controller->preapproval($reservation_details->id, $message);
			}
			else if ($request->template == 2) {
				$message_type = 7;

                $rules = array(
                    'price' => 'required|numeric'
                );

                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
		          	return response()->json([
		                'status_code'     => '0',
		                'success_message' => $validator->messages()->first(),
		            ]);
				}

                $minimum_amount = currency_convert(DEFAULT_CURRENCY, session('currency'), MINIMUM_AMOUNT); 
                $currency_symbol = Currency::whereCode(session('currency'))->first()->original_symbol;

                $night_price = $request->price;

                if($night_price < $minimum_amount && $night_price != '') {
					return response()->json([
						'status_code' 		=> '0',
						'success_message' 	=> __('validation.min.numeric', ['attribute' => 'price', 'min' => $currency_symbol.$minimum_amount]), 'attribute' => 'price'
					]);
                }

                $special_offer = new SpecialOffer;

                $event_type = $request->event_type;
                $booking_date_times = $request->booking_date_times;

                $special_offer->reservation_id  = $reservation_details->id;
                $special_offer->space_id        = $request->space_id;
                $special_offer->user_id         = $reservation_details->user_id;
                $special_offer->activity_type   = $event_type['activity_type'];
                $special_offer->activity        = $event_type['activity'];
                $special_offer->sub_activity    = $event_type['sub_activity'];

                $special_offer->number_of_guests= $request->number_of_guests;
                $special_offer->price           = $request->price;
                $special_offer->currency_code   = Currency::first()->session_code;
                $special_offer->type            = 'special_offer';
                $special_offer->created_at      = date('Y-m-d H:i:s');
                $special_offer->save();

                $special_offer_id = $special_offer->id;
                $spl_offer_times['space_id']        = $request->space_id;
                $spl_offer_times['special_offer_id']= $special_offer_id;
                $spl_offer_times['start_date']      = $booking_date_times['formatted_start_date'];
                $spl_offer_times['end_date']        = $booking_date_times['formatted_end_date'];
                $spl_offer_times['start_time']      = $booking_date_times['start_time'];
                $spl_offer_times['end_time']        = $booking_date_times['end_time'];

                SpecialOfferTimes::create($spl_offer_times);

                 $email_controller->preapproval($reservation_details->id, $message, 'special_offer');
			}
			else if ($request->template == 'NOT_AVAILABLE') {
				$message_type = 8;

				$blocked_days = $this->get_days($reservation_details->checkin, $reservation_details->checkout);

				// Update Calendar
				for ($j = 0; $j < count($blocked_days) - 1; $j++) {
					$calendar_data = [
						'space_id' => $reservation_details->space_id,
						'date' => $blocked_days[$j],
						'status' => 'Not available',
						'source' => 'Calendar',
					];

					Calendar::updateOrCreate(['space_id' => $reservation_details->space_id, 'date' => $blocked_days[$j]], $calendar_data);
				}
			}
			else if ($request->template == 9) {

				$message_type = 8;
				$message = 'Those Dates are Unavailable. ' . $message;

				$reservation_details->status = 'Declined';
				$reservation_details->save();

				//remove pre_approvel from special offer table
				$get_special_offer_id = Messages::where('reservation_id', $reservation_details->id)->where('special_offer_id', '!=', '')->first();

				if ($get_special_offer_id != '') {
					$id = $get_special_offer_id->special_offer_id;
					$special_offer = SpecialOffer::find($id);
					$reservation_id = $special_offer->reservation_id;
					$type = $special_offer->type;
					$special_offer->delete();
					$messages = Messages::where('special_offer_id', $id)->delete();
				}
				else {
					$messages = new Messages;
					$messages->space_id = $reservation_details->space_id;
					$messages->reservation_id = $reservation_details->id;
					$messages->user_to = $reservation_details->user_id;
					$messages->user_from = $user_details->id;
					$messages->message = $message;
					$messages->message_type = $message_type;
					$messages->special_offer_id = @$special_offer_id;
					$messages->save();

					return response()->json([
						'status_code' => '1',
						'success_message' => 'Decline Successfully.',
					]);
				}
			}

			$messages = new Messages;

			$messages->space_id = $reservation_details->space_id;
			$messages->reservation_id = $reservation_details->id;
			$messages->user_to = $reservation_details->user_id;
			$messages->user_from = $user_details->id;
			$messages->message = $message;
			$messages->message_type = $message_type;
			$messages->special_offer_id = @$special_offer_id;

			$messages->save();
			$success_message = 'Message Sent Successfully';
			
			if ($message_type == 6) {
				$success_message = 'Pre_approval';
			}
			else if ($message_type == 7) {
				$success_message = 'Send Special Offer To Guest';
			}
			else if ($message_type == 8) {
				$success_message = 'Decline Successfully';
			}
			return response()->json([
				'status_code' => '1',
				'success_message' => $success_message,
			]);
		}
	}

    /**
	 * Reservation Request Pre-Accept by Host
	 *
	 * @param array $request Input values
	 * @return redirect to Reservation Request page
	 */
    public function pre_accept(Request $request, EmailController $email_controller)
    {
    	$reservation_details = Reservation::find($request->reservation_id);

    	if(!in_array($reservation_details->status,['Pending','Inquiry'])) {
    		return response()->json([
    			'status_code' 		=> '0',
    			'success_message' 	=> 'Already this Reservation ' . $reservation_details->status,
    		]);
    	}

    	$reservation_details->status = 'Pre-Accepted';
    	$reservation_details->accepted_at = date('Y-m-d H:m:s');
    	$reservation_details->save();

    	$friends_email = explode(',', $reservation_details->friends_email);

    	if (count($friends_email) > 0) {
    		foreach ($friends_email as $email) {
    			if ($email != '') {
    				$email_controller->itinerary($reservation_details->code, $email);
    			}
    		}
    	}

    	$messages = new Messages;
    	$messages->space_id = $reservation_details->space_id;
    	$messages->reservation_id = $reservation_details->id;
    	$messages->user_to = $reservation_details->user_id;
    	$messages->user_from = JWTAuth::parseToken()->authenticate()->id;
    	$messages->message = removeEmailNumber($request->message_to_guest);
    	$messages->message_type = 12;
    	$messages->save();

    	$email_controller->pre_accepted($reservation_details->id);

    	return response()->json([
    		'status_code' => '1',
    		'success_message' => 'Reservation Request has Successfully Pre-Accepted',
    	]);
    }
}