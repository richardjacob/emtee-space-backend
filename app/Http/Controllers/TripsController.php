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

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Models\Reservation;
use App\Models\Messages;
use App\Models\Space;
use App\Models\SpaceCalendar;
use App\Models\Fees;
use App\Models\ReservationTimes;
use App\Http\Start\Helpers;
use App\Http\Helper\PaymentHelper;
use DB;
use URL;
use DateTime;
use Session;

class TripsController extends Controller
{
    protected $helper; // Global variable for Helpers instance
    
    protected $payment_helper; // Global variable for PaymentHelper instance

    public function __construct(PaymentHelper $payment)
    {
        $this->payment_helper = $payment;
        $this->helper = new Helpers;
    }

    /**
     * Load Current Trips page.
     *
     * @return view Current Trips File
     */
    public function current()
    {
        $data['pending_trips'] = Reservation::with('reservation_times','users','space')
            ->user()->exceptContact()
            ->whereIn('status',['Pending', 'Pre-Accepted'])
            ->orderBy('id','desc')
            ->get();


        $data['current_trips'] = Reservation::with('reservation_times', 'users','space')
            ->user()->exceptContact()
            ->whereNotIn('status',['Pending', 'Pre-Accepted', 'Pre-Approved'])
            ->whereHas('reservation_times', function($query) {
                $query->where(function($query) {
                    $query->where('start_date','>=',date('Y-m-d'))->where('end_date','<=',date('Y-m-d'));
                })->orWhere(function($query) {
                    $query->where('start_date','<=',date('Y-m-d'))->where('end_date','>=',date('Y-m-d'));
                });
            })
            ->orderBy('id','desc')
            ->get();

        $current_trips = $data['current_trips']->pluck('id');

        $data['upcoming_trips'] = Reservation::with('reservation_times', 'users','space')
            ->user()->exceptContact()
            ->whereNotIn('status',['','Pending', 'Pre-Accepted', 'Pre-Approved'])
            ->whereHas('reservation_times', function($query) {
                $query->where('start_date','>',date('Y-m-d'));
            })
            ->whereNotIn('id',$current_trips)
            ->orderBy('id','desc')
            ->get();


        return view('trips.current', $data);
    }

    /**
     * Load Previous Trips page.
     *
     * @return view Previous Trips File
     */
    public function previous()
    {
        $data['previous_trips'] = Reservation::with('reservation_times', 'users','space')
            ->user()->exceptContact()
            ->whereNotIn('status',['','Pending', 'Pre-Accepted', 'Pre-Approved'])
            ->whereHas('reservation_times', function($query) {
                $query->where('end_date','<',date('Y-m-d'));
            })
            ->orderBy('id','desc')
            ->get();

        return view('trips.previous', $data);
    }

    /**
     * Load Reservation Receipt file.
     *
     * @return view Receipt
     */
    public function receipt(Request $request)
    {
        $data['reservation_details'] = Reservation::where('code',$request->code)->user()->firstOrFail();

        $data['additional_title'] = $request->code;

        return view('trips.receipt', $data);
    }

    public function get_status(Request $request)
    {
        $id = $request->id;
        $room_id= $request->space_id;
        $checkin= $request->checkin;
        $checkout=$request->checkout;
        $date_from = strtotime($checkin);
        $date_to = strtotime($checkout); 
        $date_ar=array();
        for ($i=$date_from; $i<=$date_to - 1; $i+=86400) {
            $date_ar[]= date("Y-m-d", $i).'<br />';  
        }  
        $check=array();
        for ($i=0; $i < count($date_ar) ; $i++) {
            $check[]=DB::table('calendar')->where([ 'space_id' => $room_id, 'date' => $date_ar[$i], 'status' => 'Not available' ])->get();
        }
        if(count(array_filter($check)) == 0 ) 
        {
            echo "Pre-Accepted";
            exit;
        }
        else
        {
            echo "Already Booked";
            exit;
        }
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
     * Reservation Cancel by Guest
     *
     * @param array $request Input values
     * @return redirect to Current Trips page
     */
    public function guest_cancel_reservation(Request $request,EmailController $email_controller)
    {
        $reservation_details = Reservation::findOrFail($request->id);

        $cancel_status = ['Cancelled','Declined','Expired'];
        
        if(in_array($reservation_details->status,$cancel_status)) {
            return redirect()->route('current_bookings');
        }

        if($reservation_details->status == 'Pending' || $reservation_details->status == 'Pre-Accepted') {
            $this->cancelReservation($request->id,$request->cancel_reason,$request->cancel_message);
            flash_message('success', trans('messages.your_reservations.cancelled_successfully'));
            return redirect()->route('current_bookings');
        }

        $space_details = Space::find($reservation_details->space_id);

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

        // $per_hour_price   = $reservation_details->per_hour;
        // $total_hours      = $reservation_details->hours;
        // $total_hour_price = $per_hour_price * $total_hours;

        $per_hour_price   = $reservation_details->per_hour;
        $per_day_price   = $reservation_details->per_day;
        $per_week_price   = $reservation_details->per_week;
        $per_month_price   = $reservation_details->per_month;
        $day_hour=$reservation_details->days*24;
        $week_hour=($reservation_details->weeks*7)*24;
        $month_hour=($reservation_details->months*30)*24;
        $total_hour=$day_hour+$week_hour+$month_hour+$reservation_details->hours;
        if($interval_diff->i>0)
             $spend_hour=($interval_diff->d*24)+($interval_diff->h+1);
        else
            $spend_hour=$interval_diff->d*24+$interval_diff->h;
            if($total_hour!=0)
            $per_hour_amount=$reservation_details->subtotal/$total_hour;          
            $pay_to_guest=$spend_hour*$per_hour_amount;
            $remain_to_host=($total_hour*$per_hour_amount)-$pay_to_guest;


        // To check the check in is less than today date
        if($interval_diff->invert) {
            $spend_hour_price = round($pay_to_guest);
            $remain_hour_price= round($remain_to_host);
            // $spend_hour_price = $per_hour_price * ($interval <= $total_hours ? $interval : $total_hours);
            // $remain_hour_price= $per_hour_price * (($total_hours - $interval) > 0 ? ($total_hours - $interval) : 0);
        }
        else {
            $spend_hour_price = 0;
            $remain_hour_price= $reservation_details->subtotal;
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

                    $payout_hour_price = $reservation_details->subtotal;
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                        $cleaning_fees,
                    ]);
                }
                // Checkin greater and today date
                else if($interval > 0)
                {
                    $refund_hour_price = $reservation_details->subtotal;
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
                    $refund_hour_price = ($reservation_details->subtotal-$per_hour_price) * (50 /100); // 50% of other than first hour price
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        $cleaning_fees,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $per_hour_price + (($reservation_details->subtotal-$per_hour_price) * (50 /100)); // First hour price and 50% other hour price
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

                    $payout_hour_price = $reservation_details->subtotal;
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                        $cleaning_fees,
                    ]);
                }
                else if($interval >= 5) //  (interval >= 5) condition
                {
                    $refund_hour_price = $reservation_details->subtotal;
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

                    $payout_hour_price = $reservation_details->subtotal; // Total hour price is payout
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

                    $payout_hour_price = $reservation_details->subtotal; // Total hour price is payout
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

                    $payout_hour_price = $reservation_details->subtotal; // Total hour price is payout
                    $host_payout_amount = array_sum([
                        $payout_hour_price,
                        $cleaning_fees,
                    ]);
                }
                else if($interval >= 7) //  (interval >= 7) condition
                {
                    $refund_hour_price = $reservation_details->subtotal * (50/100); // 50% of total hour price;
                    $guest_refundable_amount = array_sum([
                        $refund_hour_price,
                        $cleaning_fees,
                        -$coupon_amount
                    ]);

                    $payout_hour_price = $reservation_details->subtotal * (50/100); // 50% of total hour price;
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

        $this->cancelReservation($request->id,$request->cancel_reason,$request->cancel_message,$host_fee);

        flash_message('success', trans('messages.your_reservations.cancelled_successfully'));
        return redirect()->route('current_bookings');
    }

}