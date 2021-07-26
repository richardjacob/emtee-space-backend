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

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Http\Helper\PaymentHelper;
use App\Models\Reservation;
use App\Models\Messages;
use App\Http\Start\Helpers;
use App\Models\Fees;
use App\Models\ReservationTimes;
use DateTime;
use DB;

class ReservationController extends Controller
{
    protected $helper; // Global variable for Helpers instance
    
    protected $payment_helper; // Global variable for PaymentHelper instance

    /**
     * Constructor to Set PaymentHelper instance in Global variable
     *
     * @param array $payment   Instance of PaymentHelper
     */
    public function __construct(PaymentHelper $payment)
    {
        $this->payment_helper = $payment;
        $this->helper = new Helpers;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['reservation_id'] = $request->id;

        $read_count   = Messages::where('reservation_id',$request->id)->where('user_to',auth()->id())->where('read','0')->count();

        if($read_count !=0) {
            Messages::where('reservation_id',$request->id)->where('user_to',auth()->id())->update(['read' =>'1']);  
        }

        $data['result']         = Reservation::with('space','users','reservation_times')->host()->findOrFail($request->id);

        return view('reservation.reservation_detail', $data);
    }

    /**
     * Reservation Request Accept by Host
     *
     * @param array $request Input values
     * @return redirect to Reservation Request page
     */
    public function accept(Request $request, EmailController $email_controller)
    {
        $reservation_details = Reservation::find($request->id);
        if($reservation_details->status == 'Cancelled') {
            flash_message('success', trans('messages.your_trips.guest_cancelled_reservation'));
            return redirect()->route('reservation_request',[$request->id]);
        }

        $reservation_details->status      = 'Pre-Accepted';
        $reservation_details->accepted_at = date('Y-m-d H:m:s');

        $reservation_details->save();

        $friends_email = explode(',', $reservation_details->friends_email);
        if(count($friends_email) > 0){
            foreach($friends_email as $email) {
                if($email != '') {
                   $email_controller->itinerary($reservation_details->id, $email);
                }
            }
        }

        $messages = new Messages;

        $messages->space_id       = $reservation_details->space_id;
        $messages->reservation_id = $reservation_details->id;
        $messages->user_to        = $reservation_details->user_id;
        $messages->user_from      = auth()->id();
        $messages->message        = removeEmailNumber($request->message);
        $messages->message_type   = 12;

        $messages->save();

        $email_controller->pre_accepted($reservation_details->id);

        flash_message('success', trans('messages.your_trips.reservation_request_accepted')); 
        return redirect()->route('reservation_request',[$request->id]);
    }

    /**
     * Reservation Request Decline by Host
     *
     * @param array $request Input values
     * @return redirect to Reservation Request page
     */
    public function decline(Request $request, EmailController $email_controller)
    {
        $reservation_details = Reservation::find($request->id);
        if($reservation_details->status == 'Cancelled') {
          flash_message('success', trans('messages.your_trips.guest_cancelled_reservation')); // Call flash message 
          return redirect('reservation/'.$request->id);
        }
        else
        $reservation_details->status          = 'Declined';
        $reservation_details->decline_reason  = ($request->decline_reason == 'other') ? $request->decline_reason_other : $request->decline_reason;
        $reservation_details->declined_at     = date('Y-m-d H:m:s');

        $reservation_details->save();

        $messages = new Messages;

        $messages->space_id        = $reservation_details->space_id;
        $messages->reservation_id = $reservation_details->id;
        $messages->user_to        = $reservation_details->user_id;
        $messages->user_from      = auth()->id();
        $messages->message        =$this->helper->phone_email_remove($request->message);;
        $messages->message_type   = 3;

        $messages->save();

        //send mail to admin cancel this request
        $email_controller->cancel_host($reservation_details->id);

        flash_message('success', trans('messages.your_reservations.declined_successfully')); // Call flash message function
        return redirect('reservation/'.$request->id);
    }

    /**
     * Reservation Request Expire
     *
     * @param array $request Input values
     * @return redirect to Reservation Request page
     */
    public function expire(Request $request)
    {
        $reservation_details = Reservation::findOrFail($request->id);
        
        // Expire penalty
        $cancel_count = Reservation::where('host_id', auth()->id())->where('cancelled_by', 'Host')->where('cancelled_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 6 MONTH)'))->where('host_penalty','1')->count();
        
        // penalty management admin panel
        $host_penalty        = Fees::find(3)->value;
        $penalty_currency    = Fees::find(4)->value;
        $penalty_before_days = Fees::find(5)->value;
        $penalty_after_days  = Fees::find(6)->value;
        $penalty_cancel_limits_count  = Fees::find(7)->value;
        // penalty management admin panel

        $to_time   = strtotime($reservation_details->getOriginal('created_at'));
        $from_time = strtotime(date('Y-m-d H:i:s'));
        $diff_mins = round(abs($to_time - $from_time) / 60,2);

        if($diff_mins >= 1440) {
            $reservation_details->status       = 'Expired';
            $reservation_details->expired_at   = date('Y-m-d H:m:s');
            $reservation_details->save();

            if($cancel_count >= $penalty_cancel_limits_count && $host_penalty == 1) {
                $host_penalty_amount  = currency_convert($penalty_currency,$reservation_details->currency_code,$penalty_before_days);
                $this->payment_helper->payout_refund_processing($reservation_details, 0, 0, $host_penalty_amount);
            }

            $messages = new Messages;

            $messages->space_id       = $reservation_details->space_id;
            $messages->reservation_id = $reservation_details->id;
            $messages->user_to        = $reservation_details->user_id;
            $messages->user_from      = auth()->id();
            $messages->message        = '';
            $messages->message_type   = 4;

            $messages->save();

            $email_controller = new EmailController;
            $email_controller->reservation_expired_admin($reservation_details->id);
            $email_controller->reservation_expired_guest($reservation_details->id);

            flash_message('success', trans('messages.your_reservations.expired_successfully'));
            return redirect()->route('reservation_request',[$request->id]);
        }
        flash_message('danger', trans('messages.your_reservations.reservation_has_time'));
        return redirect()->route('reservation_request',[$request->id]);
    }

    /**
     * Show Host Reservations
     *
     * @param array $request Input values
     * @return redirect to My Reservations page
     */
    public function my_reservations(Request $request)
    {
        if($request->all == 1) {
            $data['code'] = '1';
            $data['reservations'] = Reservation::with('reservation_times', 'users', 'host_users','space')
            ->host()->exceptContact()
            ->orderBy('id','desc')
            ->get();
        }
        else {
            $data['code'] = '0';

            $data['past_reservations'] = Reservation::with('reservation_times', 'users', 'host_users','space')
            ->host()->exceptContact()
            ->orderBy('id','desc')
            ->count();

            $data['reservations'] = Reservation::with('reservation_times', 'users', 'host_users','space')
            ->host()->exceptContact()
            ->whereHas('reservation_times', function($query) {
                $query->where('start_date','>=',date('Y-m-d'));
            })
            ->orderBy('id','desc')
            ->get();
        }

        
        $data['print'] = $request->print;

        return view('reservation.my_reservations', $data);
    }

    /**
     * Load Reservation Itinerary Print Page
     *
     * @param array $request Input values
     * @return view Itinerary file
     */
    public function print_confirmation(Request $request)
    {
        $data['reservation_details'] = Reservation::with('space','users')->where('code',$request->code)->firstOrFail();

        $data['additional_title'] = $request->code;

        if($data['reservation_details']->host_id == auth()->id()) {
            $data['penalty'] = optional($data['reservation_details']->payouts)->total_penalty_amount;
            return view('reservation.print_confirmation', $data);
        }
        if($data['reservation_details']->user_id == auth()->id()) {
            return view('trips.itinerary', $data);
        }
    }

    /**
     * Load Reservation Requested Page for After Payment
     *
     * @param array $request Input values
     * @return view Reservation Requested file
     */
    public function requested(Request $request)
    {
        $data['reservation_details'] = Reservation::where('code', $request->code)->firstOrFail();
        return view('reservation.requested', $data);
    }

    /**
     * Store Itinerary Friends
     *
     * @param array $request Input values 
     * @return redirect to Trips page
     */
    public function itinerary_friends(Request $request, EmailController $email_controller)
    {
        $friends_email = '';

        for($i=0; $i<count($request->friend_address); $i++)
        {
            if($request->friend_address[$i] != '') {
                $friends_email .= trim($request->friend_address[$i]).',';
            }
        }

        $reservation = Reservation::where('code',$request->code)->update(['friends_email'=>rtrim($friends_email,',')]);

        $reservation_details = Reservation::whereCode($request->code)->first();

        if($reservation_details->status == 'Accepted') {
            $friends_email = explode(',', $reservation_details->friends_email);
            if(count($friends_email) > 0) {
                foreach($friends_email as $email) {
                    if($email != '') {
                        $email_controller->itinerary($reservation_details->id, $email);
                    }
                }
            }
        }
        
        return redirect()->route('current_bookings')->with(['popup_reservation'=>'true']); 
    }
    
    /**
     * Reservation Cancel by Host
     *
     * @param array $request Input values
     * @return redirect to My Reservations page
     */
    public function host_cancel_reservation(Request $request,EmailController $email_controller)
    {
        $reservation_details = Reservation::find($request->id);

        // Status check start
        if($reservation_details->status=='Cancelled') {
            return redirect()->route('my_bookings');
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
        $datetime1 = new DateTime(date('Y-m-d H:i:s')); 
        $datetime2 = new DateTime(date('Y-m-d H:i:s', strtotime($reservation_details->checkin)));
        $interval_diff = $datetime1->diff($datetime2);
        //$interval = $interval_diff->days;
        $interval = ceil(((($interval_diff->d * 24 + $interval_diff->h) * 60 + $interval_diff->i)*60 + $interval_diff->s)/(24 * 60 * 60));
      
        // $per_night_price   = $reservation_details->per_hour;
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
            $per_hour_amount=$reservation_details->subtotal/$total_hour;
            $pay_to_guest=$spend_hour*$per_hour_amount;
            $remain_to_host=($total_hour*$per_hour_amount)-$pay_to_guest;
            // $total_nights      = $reservation_details->hours;
        // Additional guest price is added to the per night price for calculation
            // $additional_guest_per_night     = 0;
            // $per_night_price                = $per_night_price+$additional_guest_per_night;

        // $total_night_price = $per_night_price * $total_nights;
        if($interval_diff->invert && $reservation_details->status == 'Accepted') // To check the check in is less than today date
        {
            $spend_night_price = round($pay_to_guest);
            $remain_night_price= round($remain_to_host);
            // $remain_night_price= $per_night_price * (($total_nights - $interval) > 0 ? ($total_nights - $interval) : 0);
        }
        else
        {
            $spend_night_price = 0;
            $remain_night_price= $reservation_details->subtotal;
            // $remain_night_price= $total_night_price;
        }
        
        $cleaning_fees              = $reservation_details->cleaning;
        $coupon_amount              = $reservation_details->coupon_amount;
        $service_fee                = $reservation_details->service;
        $host_payout_ratio          = (1 - ($host_fee_percentage / 100));

        if(!$interval_diff->invert) // Cancel before checkin
        {
            $refund_night_price = $reservation_details->subtotal;
            $guest_refundable_amount = array_sum([
                $refund_night_price,
                $cleaning_fees,
                -$coupon_amount,
                $service_fee
            ]);

            $payout_night_price = 0;
            $host_payout_amount = array_sum([
                $payout_night_price,
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
            $refund_night_price = $remain_night_price;
            $guest_refundable_amount = array_sum([
                $refund_night_price,
                -$coupon_amount,
            ]);

            $payout_night_price = $spend_night_price;
            $host_payout_amount = array_sum([
                $payout_night_price,
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

        $cancel = Reservation::find($request->id);
        $cancel->host_fee = currency_convert($reservation_details->currency_code,$reservation_details->original_currency_code,$host_fee);
        $cancel->cancelled_by = "Host";
        $cancel->cancelled_reason = $request->cancel_reason;
        $cancel->cancelled_at = date('Y-m-d H:m:s');
        $cancel->status = "Cancelled";
        $cancel->updated_at = date('Y-m-d H:m:s');
        $cancel->save();

        $email_controller->cancel_host($cancel->id);

        flash_message('success', trans('messages.your_reservations.cancelled_successfully'));
        return redirect()->route('my_bookings');
    }
}