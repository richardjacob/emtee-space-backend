<?php

/**
 * Bookings Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Bookings
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\EmailController;
use App\Http\Helper\PaymentHelper;
use App\Models\SpaceCalendar;
use App\Models\Reservation;
use App\Models\ReservationTimes;
use App\Models\Fees;
use App\Models\Messages;
use DateTime;
use JWTAuth;
use Validator;

class TripsController extends Controller
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		$this->payment_helper = new PaymentHelper;
	}

	/**
     * Cancel Reservation
     *
     * @param array $reserve_id Reservation Id
     * @return redirect to Current Trips page
     */
    protected function cancelReservation($reserve_id, $cancel_reason, $cancel_message,$host_fee = 0)
    {
        // Update reservation status and other details
        $reservation = Reservation::find($reserve_id);
        $reservation->cancelled_by = "Guest";
        if($host_fee > 0) {
            $reservation->host_fee = $host_fee;
        }
        $reservation->cancelled_reason = $cancel_reason;
        $reservation->cancelled_at = date('Y-m-d H:m:s');
        $reservation->status = "Cancelled";
        $reservation->updated_at = date('Y-m-d H:m:s');
        $reservation->save();

        // Send message for cancellation
        $messages = new Messages;
        $messages->space_id       = $reservation->space_id;
        $messages->reservation_id = $reservation->id;
        $messages->user_to        = $reservation->host_id;
        $messages->user_from      = auth()->id();
        $messages->message        = removeEmailNumber($cancel_message);
        $messages->message_type   = 10;
        $messages->save();

        $email_controller = new EmailController;
        $email_controller->cancel_guest($reservation->id);
    }

	/**
     * Map function to format Reservation Details
     *
     * @return Array
     */
    protected function mapReservationData($reservations)
    {
        return $reservations->map(function ($reservation) {
        	$space_address = $reservation->space->space_address;
            $space_location = $space_address->address_line;

			$expire_timer = '';
            $booking_status = '';
			if ($reservation->status == 'Pending') {
				$from_time 	= strtotime($reservation->created_at_timer);
				$to_time 	= strtotime(date('Y/m/d H:i:s'));
				$diff 		= abs($from_time - $to_time);
				$expire_timer = sprintf('%02d:%02d:%02d', ($diff / 3600), ($diff / 60 % 60), $diff % 60);
			}
            if($reservation->status == 'Pre-Accepted') {
                $space_location = $space_address->country_name;
                $date = date('Y-m-d');
                if ($reservation->checkin < $date) {
                    $booking_status = 'Already Booked';
                }
                else {
                    $status_check_data = $reservation->only('space_id','checkin','checkout','number_of_guests');
                    // $booking_status = $this->get_status($status_check_data
                    $booking_status = 'Available';
                }
            }

			$reservation_data = array(
				'reservation_id' 	=> $reservation->id,
				'space_id' 			=> $reservation->space_id,
				'reservation_status'=> $reservation->status,
                'booking_status'    => $booking_status,
			);

            if($reservation->status == 'Accepted')
            $check_in_guidance=$reservation->space_location->guidance??'';
            else
            $check_in_guidance='';

			$space = array(
				'space_name' 		=> $reservation->space->name,
				'space_type_name'	=> $reservation->space->space_type_name,
				'space_image' 		=> $reservation->space->photo_name,
				'space_location' 	=> $space_location,
                'check_in_guidance' => $check_in_guidance,
			);

			$guest_user = array(
				'guest_user_id' 	=> $reservation->user_id,
				'guest_user_name' 	=> $reservation->users->full_name,
				'guest_thumb_image' => $reservation->users->profile_picture->email_src,
				'guest_user_location'=> $reservation->users->live,
				'member_from' 		=> $reservation->users->since,
			);

			$host_user = array(
				'host_user_id' 		=> $reservation->host_id,
				'host_user_name' 	=> $reservation->host_users->full_name,
				'host_thumb_image' 	=> $reservation->host_users->profile_picture->email_src,
			);

			$reservation_price = array(
				'reservation_date' 	=> $reservation->dates_subject,
				'checkin' 			=> $reservation->checkin,
				'checkout' 			=> $reservation->checkout,
				'start_time' 		=> optional($reservation->reservation_times)->start_time_formatted,
				'end_time' 			=> optional($reservation->reservation_times)->end_time_formatted,
				'number_of_guests'	=> $reservation->number_of_guests,
				'currency_symbol'	=> $reservation->currency->symbol,
				'total_cost'		=> $reservation->check_total,
			);

			$other_details = array(
				'can_view_receipt' 	=> ($reservation->status == 'Accepted'),
				'expire_timer' 		=> $expire_timer,
			);



			$reservation_price = array_map('strval', $reservation_price);

			return array_merge($reservation_data,$space,$guest_user,$host_user,$reservation_price,$other_details);
        });
    }

	/**
	 * Display Bookings type
	 *
	 * @param  Get method inputs
	 * @return Response in Json
	 */
	public function booking_types(Request $request)
	{
		$user = JWTAuth::parseToken()->authenticate();

		$pending_bookings = Reservation::with('reservation_times','users','space')
            ->user()->exceptContact()
            ->whereIn('status',['Pending', 'Pre-Accepted'])
            ->orderBy('id','desc')
            ->count();

        $current_bookings = Reservation::with('reservation_times', 'users','space')
            ->user()->exceptContact()
            ->whereNotIn('status',['Pending', 'Pre-Accepted', 'Pre-Approved'])
            ->whereHas('reservation_times', function($query) {
                $query->whereStartDate(date('Y-m-d'));
            })
            ->orderBy('id','desc')
            ->get();

        $current_booking_ids = $current_bookings->pluck('id');
        $current_bookings = $current_bookings->count();

        $upcoming_bookings = Reservation::with('reservation_times', 'users','space')
            ->user()->exceptContact()
            ->whereNotIn('status',['','Pending', 'Pre-Accepted', 'Pre-Approved'])
            ->whereHas('reservation_times', function($query) {
                $query->where('start_date','>',date('Y-m-d'));
            })
            ->whereNotIn('id',$current_booking_ids)
            ->orderBy('id','desc')
            ->count();

		// Get Previous Bookings Count
		$previous_bookings = Reservation::with('reservation_times', 'users','space')
            ->user()->exceptContact()
            ->whereNotIn('status',['','Pending', 'Pre-Accepted', 'Pre-Approved'])
            ->whereHas('reservation_times', function($query) {
                $query->where('start_date','<',date('Y-m-d'));
            })
            ->orderBy('id','desc')
            ->count();

		$bookings = [];

		if ($pending_bookings > 0) {
			$booking['key'] 	= 'pending_bookings';
			$booking['value'] 	= __('messages.new_space.pending_bookings');
			$booking['count'] 	= $pending_bookings;
			array_push($bookings, $booking);
		}

		if ($current_bookings > 0) {
			$booking['key'] 	= 'current_bookings';
			$booking['value'] 	= __('messages.new_space.current_bookings');
			$booking['count'] 	= $current_bookings;
			array_push($bookings, $booking);
		}

		if ($upcoming_bookings > 0) {
			$booking['key'] 	= 'upcoming_bookings';
			$booking['value'] 	= __('messages.new_space.upcoming_bookings');
			$booking['count'] 	= $upcoming_bookings;
			array_push($bookings, $booking);
		}

		if ($previous_bookings > 0) {
			$booking['key'] 	= 'previous_bookings';
			$booking['value'] 	= __('messages.new_space.previous_bookings');
			$booking['count'] 	= $previous_bookings;
			array_push($bookings, $booking);
		}

		if(count($bookings) == 0) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> __('messages.api.no_bookings_found'),
			]);
		}

		return response()->json([
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.booking_type_listed_success'),
			'booking_types' 	=> $bookings,
		]);
	}

	/**
	 * Display Booking details
	 *
	 * @param  Get method inputs
	 * @return Response in Json
	 */
	public function booking_details(Request $request)
	{ 
        // SpaceLocation::where('space_id',)
		$types_array = array('pending_bookings', 'current_bookings', 'upcoming_bookings', 'previous_bookings');
        $rules      = array(
            'booking_type' => 'required|in:'.implode(',', $types_array),
        );
        $attributes = array(
            'reservation_id' => 'Reservation Id'
        );
        $messages   = array(
            'required' => ':attribute is required.',
            'booking_type.in' => __('messages.api.invalid_booking_type'),
        );
        $validator  = Validator::make($request->all(), $rules, $messages, $attributes);

        if($validator->fails()) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
        }

		$user = JWTAuth::parseToken()->authenticate();

		if($request->booking_type == 'pending_bookings') {
			$bookings = Reservation::with('reservation_times','users','space','space_location')
            ->user()->exceptContact()
            ->whereIn('status',['Pending', 'Pre-Accepted'])
            ->orderBy('id','desc')
            ->get();
		}
		if($request->booking_type == 'current_bookings') {
			$bookings = Reservation::with('reservation_times', 'users','space','space_location')
            ->user()->exceptContact()
            ->whereNotIn('status',['Pending', 'Pre-Accepted', 'Pre-Approved'])
            ->whereHas('reservation_times', function($query) {
                $query->whereStartDate(date('Y-m-d'));
            })
            ->orderBy('id','desc')
            ->get();
		}
		if($request->booking_type == 'upcoming_bookings') {
			$current_booking_ids = Reservation::with('reservation_times', 'users','space')
	            ->user()->exceptContact()
	            ->whereNotIn('status',['Pending', 'Pre-Accepted', 'Pre-Approved'])
	            ->whereHas('reservation_times', function($query) {
	                $query->whereStartDate(date('Y-m-d'));
	            })
	            ->orderBy('id','desc')
	            ->get()->pluck('id');

	        $bookings = Reservation::with('reservation_times', 'users','space','space_location')
	            ->user()->exceptContact()
	            ->whereNotIn('status',['','Pending', 'Pre-Accepted', 'Pre-Approved'])
	            ->whereHas('reservation_times', function($query) {
	                $query->where('start_date','>',date('Y-m-d'));
	            })
	            ->whereNotIn('id',$current_booking_ids)
	            ->orderBy('id','desc')
	            ->get();
		}
		if($request->booking_type == 'previous_bookings') {
			$bookings = Reservation::with('reservation_times', 'users','space','space_location')
            ->user()->exceptContact()
            ->whereNotIn('status',['','Pending', 'Pre-Accepted', 'Pre-Approved'])
            ->whereHas('reservation_times', function($query) {
                $query->where('start_date','<',date('Y-m-d'));
            })
            ->orderBy('id','desc')
            ->get();
		}

        $booking_data = $this->mapReservationData($bookings);

		if(count($booking_data) == 0) {
			return response()->json([
				'status_code' 		=> '0',
				'success_message' 	=> __('messages.api.no_data_found'),
			]);
		}

		return response()->json([
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.listed_successful'),
			'bookings' 			=> $booking_data,
		]);
	}

	/**
     * Reservation Cancel by Guest
     *
     * @param array $request Input values
     * @return redirect to Current Trips page
     */
    public function guest_cancel_reservation(Request $request,EmailController $email_controller)
    {
    	$rules 		= array(
    		'reservation_id' => 'required|exists:reservation,id',
    		'cancel_reason'  => 'required',
    		'cancel_message' => 'required',
    	);
		$attributes = array('reservation_id' => 'Reservation Id');
		$messages 	= array('required' => ':attribute is required.');
		$validator 	= Validator::make($request->all(), $rules, $messages, $attributes);

		if ($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

        $reservation_details = Reservation::findOrFail($request->reservation_id);
        $user = JWTAuth::parseToken()->authenticate();

        if($user->id != $reservation_details->user_id) {
			return response()->json([
				'status_code' 		=> '0',
                'success_message' => __('messages.api.permission_denied'),
			]);
		}
        
        if($reservation_details->status=='Cancelled' || $reservation_details->status=='Declined' || $reservation_details->status=='Expired') {
            return response()->json([
                'status_code'     => '0',
                'success_message' => __('messages.api.booking_already_cancelled'),
            ]);
        }

        if($reservation_details->status == 'Pending' || $reservation_details->status == 'Pre-Accepted') {
            $this->cancelReservation($request->reservation_id,$request->cancel_reason,$request->cancel_message);
            return response()->json([
                'status_code'     => '1',
                'success_message' => __('messages.api.booking_cancelled'),
            ]);
        }

        if($reservation_details->status != 'Accepted') {
            return redirect()->route('current_bookings');
        }

        $host_fee_percentage  = Fees::find(2)->value > 0 ? Fees::find(2)->value : 0;
        $host_payout_amount = $reservation_details->subtotal;
        $guest_refundable_amount = 0;

        $datetime1 = new DateTime(date('Y-m-d')); 
        $datetime2 = new DateTime(date('Y-m-d', strtotime($reservation_details->checkin)));
        $interval_diff = $datetime1->diff($datetime2);
        $interval = $interval_diff->days;

        $per_hour_price   = $reservation_details->per_hour;
        $total_hours      = $reservation_details->hours;

        $total_hour_price = $per_hour_price * $total_hours;

        // To check the check in is less than today date
        if($interval_diff->invert) {
            $spend_hour_price = $per_hour_price * ($interval <= $total_hours ? $interval : $total_hours);
            $remain_hour_price= $per_hour_price * (($total_hours - $interval) > 0 ? ($total_hours - $interval) : 0);
        }
        else {
            $spend_hour_price = 0;
            $remain_hour_price= $total_hour_price;
        }

        $cleaning_fees              = $reservation_details->cleaning;
        $coupon_amount              = $reservation_details->coupon_amount;
        $service_fee                = $reservation_details->service;
        $host_payout_ratio          = (1 - ($host_fee_percentage / 100));

        if($reservation_details->cancellation == "Flexible") {
            // To check the check in is less than today date
            if($interval_diff->invert) {
                if($interval > 0) //  (interval < 0) condition
                {
                    $refund_hour_price = $remain_hour_price;
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $spend_hour_price;
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                        $cleaning_fees,
                    ]);
                }
            }
            else
            {
                // Checkin and today date are equal
                if($interval == 0)
                {
                    $refund_hour_price = 0;
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $total_hour_price;
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                        $cleaning_fees,
                    ]);
                }
                // Checkin greater and today date
                else if($interval > 0)
                {
                    $refund_hour_price = $total_hour_price;
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        $cleaning_fees,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = 0;
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                    ]);
                }
            }
        }

        else if($reservation_details->cancellation == "Moderate") {
            if($interval_diff->invert) // To check the check in is less than today date
            {
                if($interval > 0) //  (interval < 0) condition
                {
                    $refund_hour_price = $remain_hour_price * (50 / 100); // 50 % of remain hour price
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $spend_hour_price + ($remain_hour_price * (50 / 100)); // spend hour price and 50% remain hour price
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                        $cleaning_fees,
                    ]);
                }
            }
            else {
                if($interval < 5 && $interval > 0) //  (interval < 5 && interval >= 0) condition
                {
                    $refund_hour_price = ($total_hour_price-$per_hour_price) * (50 /100); // 50% of other than first hour price
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        $cleaning_fees,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $per_hour_price + (($total_hour_price-$per_hour_price) * (50 /100)); // First hour price and 50% other hour price
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                    ]);
                }
                else if($interval == 0) //  (interval < 5 && interval >= 0) condition
                {
                    $refund_hour_price = 0;
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $total_hour_price;
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                        $cleaning_fees,
                    ]);
                }
                else if($interval >= 5) //  (interval >= 5) condition
                {
                    $refund_hour_price = $total_hour_price;
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        $cleaning_fees,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = 0;
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                    ]);
                }
            }
        }

        else if($reservation_details->cancellation == "Strict") {
            if($interval_diff->invert) // To check the check in is less than today date
            {
                if($interval > 0) //  (interval < 0) condition
                {
                    $refund_hour_price = 0; // Total hour price is non refundable
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $total_hour_price; // Total hour price is payout
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                        $cleaning_fees,
                    ]);
                }
            }
            else
            {
                if($interval < 7 && $interval > 0) //  (interval < 7 && interval >= 0) condition
                {
                    $refund_hour_price = 0; // Total hour price is non refundable
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        $cleaning_fees,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $total_hour_price; // Total hour price is payout
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                    ]);
                }
                if($interval == 0) //  (interval < 7 && interval >= 0) condition
                {
                    $refund_hour_price = 0; // Total hour price is non refundable
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $total_hour_price; // Total hour price is payout
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                        $cleaning_fees,
                    ]);
                }
                else if($interval >= 7) //  (interval >= 7) condition
                {
                    $refund_hour_price = $total_hour_price * (50/100); // 50% of total hour price;
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        $cleaning_fees,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $total_hour_price * (50/100); // 50% of total hour price;
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                    ]);
                }
            }
        }

        $host_fee           = ($host_payout_amount * ($host_fee_percentage / 100));
        $host_payout_amount = $host_payout_amount * $host_payout_ratio;

        $this->payment_helper->payout_refund_processing($reservation_details, $guest_refundable_amount, $host_payout_amount);

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

        $this->cancelReservation($request->reservation_id,$request->cancel_reason,$request->cancel_message,$host_fee);
        return response()->json([
            'status_code'     => '1',
            'success_message' => __('messages.api.booking_cancelled'),
        ]);
    }
}