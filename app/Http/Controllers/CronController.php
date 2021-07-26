<?php

/**
 * Cron Controller
 *
 * @package     Makent
 * @subpackage  Controller
 * @category    Cron
 * @author      Trioangle Product Team
 * @version     1.6
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IcalController;
use App\Http\Controllers\EmailController;
use App\Http\Helper\PaymentHelper;
use App\Http\Start\Helpers;
use Auth;
use App\Models\Currency;
use App\Models\ImportedIcal;
use App\Models\Calendar;
use App\Models\Reservation;
use App\Models\ReservationTimes;
use App\Models\Payouts;
use App\Models\Messages;
use App\Models\Fees;
use App\Models\HostPenalty;
use App\Models\Referrals;
use DateTime;
use Swap;
use DB;
use Session;

class CronController extends Controller
{
  /**
     * Constructor
     *
     */
  public function __construct(PaymentHelper $payment)
  {
    $this->payment_helper = $payment;
  }

  public function currency()
  {
        // Get all currencies from Currency table
    $result = Currency::all();

        // Update Currency rate by using Code as where condition
    foreach($result as $row) {
      $rate = 1;
      if($row->code != DEFAULT_CURRENCY) {
        $rate = Swap::latest(DEFAULT_CURRENCY.'/'.$row->code);
        $rate = $rate->getValue();
      }
      Currency::where('code',$row->code)->update(['rate' => $rate]);
    }
  }

    /**
     * Update Expired Reservations
     *
     * @return redirect     to Home page
     */
    public function expire(EmailController $email_controller)
    {
      $reservation_all = Reservation::where('status', 'Pending')->get();
      foreach($reservation_all as $row) {
        $reservation_details = Reservation::find($row->id);

            // penalty management admin panel
        $host_penalty        = Fees::find(3)->value;
        $penalty_currency    = Fees::find(4)->value;
        $penalty_before_days = Fees::find(5)->value;
        $penalty_after_days  = Fees::find(6)->value;
        $penalty_cancel_limits_count  = Fees::find(7)->value;
            // penalty management admin panel

            // Expire penalty
        $to_time   = strtotime($reservation_details->getOriginal('created_at'));
        $from_time = strtotime(date('Y-m-d H:i:s'));
        $diff_mins = round(abs($to_time - $from_time) / 60,2);

        if($diff_mins >= 1440) {
          $reservation_details->status       = 'Expired';
          $reservation_details->expired_at   = date('Y-m-d H:m:s');
          $reservation_details->save();

          $cancel_count = Reservation::where('host_id', $reservation_details->host_id)->where('cancelled_by', 'Host')->where('cancelled_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 6 MONTH)'))->where('host_penalty','1')->count();

          if($cancel_count >= $penalty_cancel_limits_count && $host_penalty == 1) {
            $host_penalty_amount  = currency_convert($penalty_currency,$reservation_details->currency_code,$penalty_before_days);
            $this->payment_helper->payout_refund_processing($reservation_details, 0, 0, $host_penalty_amount);
          }

          $email_controller->reservation_expired_admin($reservation_details->id);
          $email_controller->reservation_expired_guest($reservation_details->id);

          $messages = new Messages;
          $messages->space_id       = $reservation_details->space_id;
          $messages->reservation_id = $reservation_details->id;
          $messages->user_to        = $reservation_details->user_id;
          $messages->user_from      = @Auth::user()->id;
          $messages->message        = '';
          $messages->message_type   = 4;
          $messages->save();
        }
      }
    }

    public function host_remainder_pending_reservaions(EmailController $email_controller)
    {
    $pending_reservations = Reservation::where('status', 'Pending')->get(); 
    foreach($pending_reservations as $pending_reservation) {
      $reservation_created_at_time = strtotime($pending_reservation->getOriginal('created_at')); 
      $now_time = time(); 
      $passed_hours = round(($now_time - $reservation_created_at_time)/3600, 1);
      $sent_email = $pending_reservation->host_remainder_email_sent; 

      if($passed_hours > 5 && $sent_email == 0) {
        $remaining_hours = 19;
        $email_controller->booking_response_remainder($pending_reservation->id, $remaining_hours);
        $reservation  = Reservation::find($pending_reservation->id); 
        $reservation->host_remainder_email_sent = 1; 
        $reservation->save(); 
      }
      elseif($passed_hours > 10 && $sent_email == 1) {
        $remaining_hours = 14;
        $email_controller->booking_response_remainder($pending_reservation->id, $remaining_hours);
        $reservation  = Reservation::find($pending_reservation->id); 
        $reservation->host_remainder_email_sent = 2; 
        $reservation->save(); 
      }
    }
    return;
    }

    /**
     * Update Travel Credit After Checkin
     *
     */
    public function travel_credit()
  {
    $reservation_all = Reservation::where('status', '=', 'Accepted')->get();

    foreach($reservation_all as $row) {
      if($row->checkin_cross == 0) {
        $guest_referral = Referrals::whereFriendId($row->user_id)->where('if_friend_guest_amount', '!=', 0)->first();
        $guest_amount = @$guest_referral->if_friend_guest_amount_original;
        $prev_credited_amount = @$guest_referral->credited_amount;

        if(@$guest_referral->id) {
          $referral = Referrals::find($guest_referral->id);
          $referral->credited_amount = $prev_credited_amount + $guest_amount;
          $referral->if_friend_guest_amount = 0;
          $referral->save();
        }

        $host_referral = Referrals::whereFriendId($row->host_id)->where('if_friend_host_amount', '!=', 0)->first();
        $host_amount = @$host_referral->if_friend_host_amount_original;
        $prev_credited_amount = @$host_referral->credited_amount;

        if(@$host_referral->id) {
          $referral = Referrals::find($host_referral->id);
          $referral->credited_amount = $prev_credited_amount + $host_amount;
          $referral->if_friend_host_amount = 0;
          $referral->save();
        }

        Referrals::whereIfFriendGuestAmount(0)->whereIfFriendHostAmount(0)->update(['status'=>'Completed']);
      }
    }
  }

  /**
     * Send Review Remainder email to host and guest
     *
     * @return void
     */
  public function review_remainder(EmailController $email)
    {
    $yesterday = date('Y-m-d',strtotime("-1 days"));
    $result    = ReservationTimes::whereHas('reservation',function($query) {
      $query->whereStatus('Accepted');
    })->where('end_date', $yesterday)->get();

    foreach($result as $row) {
      $reservation = Reservation::find($row->reservation_id);
      logger($row->reservation_id);
      $email->review_remainder($reservation, 'guest');
      $email->review_remainder($reservation, 'host');
    }
    return;
    }
}