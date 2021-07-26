<?php

/**
 * Messages Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Messages
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Messages;
use App\Models\SessionReservation;
use App\Models\Reservation;
use App\Models\SpecialOffer;
use App\Models\ProfilePicture;
use App\Models\Currency;
use App\Http\Controllers\Controller;
use App\Http\Start\Helpers;
use App\Http\Helper\PaymentHelper;
use Validator;
use DB;
use JWTAuth;

class MessagesController extends Controller
{
  public function __construct(PaymentHelper $payment)
    {
        $this->payment_helper = $payment;
        $this->helper = new Helpers;
    }

    protected function getMessageDateText($created_at,$created_time)
    {
      $createDate = getDateObject(strtotime($created_at));
    $today_date = date('Y-m-d');
    if ($today_date == $createDate->format('Y-m-d')) {
      $message_date = $created_time;
    }
    else {
      $message_date = $createDate->format(PHP_DATE_FORMAT);
    }
    return $message_date;
    }

    protected function getMessageTypeText($message_type)
    {
      $message_type_text = '';
      if ($message_type == 1) {
      $message_type_text = 'Pre-Accept the Request?';
    }
    else if($message_type == 2) {
      $message_type_text = 'Reservation Accept';
    }
    else if($message_type == 3) {
      $message_type_text = 'Reservation Decline';
    }
    else if($message_type == 4) {
      $message_type_text = 'Reservation Expire';
    }
    else if($message_type == 5) {
      $message_type_text = 'Reservation Discuss';
    }
    else if($message_type == 6) {
      $message_type_text = 'Pre-Approval';
    }
    else if($message_type == 7) {
      $message_type_text = 'Special Offer';
    }
    else if($message_type == 8) {
      $message_type_text = 'Unavailable';
    }
    else if($message_type == 9) {
      $message_type_text = 'Contact Request';
    }
    else if($message_type == 10) {
      $message_type_text = 'Cancel Reservation by Guest';
    }
    else if($message_type == 11) {
      $message_type_text = 'Cancel Reservation by Host';
    }
    else if($message_type == 13) {
      $message_type_text = 'Resubmit';
    }
    return $message_type_text;
    }

    protected function getCommonMessageData()
  {
    $reservation_data = array(
      'reservation_id'  => '',
      'space_id'      => '',
      'booking_status'  => '',
      'reservation_status'=> 'Resubmit',
    );

    $other_user = array(
      'other_user_id'   => '0',
      'other_user_name'   => 'Admin',
      'other_thumb_image' => asset('admin_assets/dist/img/avatar04.png'),
      'other_user_location'=> '',
      'member_from'     => '',
    );

    $space = array(
      'space_name'    => '',
      'space_type_name' => '',
      'space_image'     => '',
      'space_location'  => '',
    );

    $reservation_price = array(
      'reservation_date'  => '',
      'checkin'       => '',
      'checkout'      => '',
      'start_time'    => '',
      'end_time'      => '',
      'number_of_guests'  => '',
      'currency_symbol' => '',
      'total_hours'   => '',
      'total_cost'    => '',
      'review_count'    => '',
    );

    $special_offer_data = array(
      'special_offer_id'    => '',
      'special_offer_status'  => 'No',
    );

    $other_details = array(
      'can_view_receipt'  => '',
      'payment_recieved_date' => '',
      'expire_timer'    => '',
    );

    return array_merge($reservation_data,$space,$other_user,$reservation_price,$special_offer_data, $other_details);
  }

  /**
     * Get Payment Data Based On Given Key
     *
     * @param Int $key Either session_key or reservation_id
     * @param String $type Either session or reservation
     */
    protected function getPaymentData($key, $type)
    {
        if($type == 'session') {

            $reservation = SessionReservation::find($key);

            $price_list = $reservation->price_list;

            $price = array(
                'hours'             => $price_list->total_hours,
                'base_per_hour'     => $price_list->base_hour_price,
                'total_hour_price'  => $price_list->total_hour_price,
                'host_fee'      => $price_list->host_fee,
                'service'         => $price_list->service_fee,
                'security'          => $price_list->security_fee,
                'currency_code'     => $price_list->currency_code,
                'currency_symbol'   => Currency::original_symbol($price_list->currency_code),
                'penalty_amount'    => '',
                'guest_total'       => $price_list->total_price,
                'full_day_amount'   => $price_list->full_day_amount,
                'hour_amount'   => $price_list->hour_amount,
                'weekly_amount'   => $price_list->weekly_amount,
                'monthly_amount'    => $price_list->monthly_amount,
                'additional_total_price' => $price_list->additional_total_price,
                'count_total_hour'  => $price_list->count_total_hour,
                'count_total_days'  => $price_list->count_total_days,
                'count_total_week'  => $price_list->count_total_week,
                'count_total_month' => $price_list->count_total_month,
                'payment_total'     => $price_list->payment_total,
                'coupon_code'       => $price_list->coupon_code,
                'coupon_amount'     => $price_list->coupon_amount,
            );
        }
        else {
            $reservation = Reservation::exceptContact()
        ->with('currency','hostPayouts','reservation_times')
        ->find($key);
            if(!$reservation) {
                return array();
            }
           
            $price = array(
                'hours'             => $reservation->hours,
                'base_per_hour'     => $reservation->base_per_hour,
                'total_hour_price'  => $reservation->subtotal,
                'host_fee'      => $reservation->host_fee,
                'service'         => $reservation->service,
                'security'          => $reservation->security,
                'currency_code'     => $reservation->currency->code,
                'currency_symbol'   => $reservation->currency->symbol,
                'penalty_amount'    => optional($reservation->hostPayouts)->total_penalty_amount,
                'guest_total'       => $reservation->subtotal,
                'host_total'        => $reservation->check_total,
                'full_day_amount'   => $reservation->per_day,
                'hour_amount'   => $reservation->per_hour,
                'weekly_amount'   => $reservation->per_week,
                'monthly_amount'    => $reservation->per_month,
                'additional_total_price' => $reservation->subtotal,
                'count_total_hour'  => $reservation->hours,
                'count_total_days'  => $reservation->days,
                'count_total_week'  => $reservation->weeks,
                'count_total_month' => $reservation->months,
                'payment_total'     => $reservation->total,
                'coupon_code'       => $reservation->coupon_code,
                'coupon_amount'     => $reservation->coupon_amount,
            );
        }

        return $price;
    }

    protected function checkValue($value)
    {
      return ($value != '');
    }

    /**
     *Send Message
     *
     * @param  Get method inputs
     * @return Response in Json Format
     */
    public function send_message(Request $request)
    {
    $rules = array(
      'space_id'    => 'required|exists:space,id',
      'host_user_id'  => 'required|exists:users,id',
      'message_type'  => 'required|exists:message_type,id',
      'reservation_id'=> 'required|exists:reservation,id',
      'message'   => 'required'
    );
    $attributes = array();
    $messages  = array('required' => ':attribute is required.');
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);

    if ($validator->fails()) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
    }

    $user = JWTAuth::parseToken()->authenticate();
    //set user token to session for getting user time based on time zone.
    session(['get_token' => $request->token]); 

    //Prevent Host Sending Message to Host 
    if($user->id==$request->host_user_id) {
      return response()->json([
        'status_code'   => '0',
        'success_message'   => 'You Can Not Send Messages to Your Own Reservation',
      ]);
    }

    $user_id = $user->id;
    $host_id = $request->host_user_id;

    $reservation_count = Reservation::where('id',$request->reservation_id)
            ->where('space_id',$request->space_id)
            ->where(function ($query) use ($user_id, $host_id) {
                $query->where('host_id',$user_id)->orWhere('host_id',$host_id);
            })
            ->where(function ($query) use ($user_id,$host_id) {
        $query->where('user_id','=',$user_id)->orWhere('user_id', '=',$host_id);
            })->count(); 
    
    if($reservation_count == 0) {
      return response()->json([
        'status_code'     => '0',
        'success_message' => 'Reservation details Mismatch',
      ]);
    }

    $messages = new Messages;
    $messages->space_id       = $request->space_id;
    $messages->reservation_id = $request->reservation_id;
    $messages->user_to        = $request->host_user_id;
    $messages->user_from      = $user->id;
    $messages->message        = removeEmailNumber($request->message); 
    $messages->message_type   = $request->message_type;
    $messages->save();

    return response()->json([
      'status_code'       => '1',
      'success_message'   => 'Message Send Successfully',
      'message'           => $messages->message,
      'message_time'    => $messages->created_time
    ]);
    }

    /**
   *Display Conversation List
   *
   * @param  Get method inputs
   * @return Response in Json
   */
  public function conversation_list(Request $request)
  {
    $user = JWTAuth::parseToken()->authenticate();

    $rules = array(
      'host_user_id' => 'required|exists:users,id',
      'reservation_id' => 'required',
    );

    $attributes = array('host_user_id' => 'Host User Id');
    $messages = array('required' => ':attribute is required.');
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);

    if ($validator->fails()) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
    }

    //set user token to session for getting  user time based on time zone.
    session(['get_token' => $request->token]);

    // Load user profile image
    $user->load('profile_picture');
    $user_id = $user->id;
    //get host user details
    $host_user = User::with('profile_picture')->find($request->host_user_id);
    $host_id = $host_user->id;

    $result = Messages::with('user_details')
      ->where('reservation_id', $request->reservation_id)
      ->where(function ($query) use ($user_id) {
        $query->where('user_to', '=', $user_id)->orWhere('user_from', '=', $user_id);
      })
      ->where(function ($query) use ($host_id) {
        $query->where('user_to', '=', $host_id)->orWhere('user_from', '=', $host_id);
      })
      ->get();

    if ($result->count() == 0) {
      return response()->json([
        'success_message' => 'Reservation Not Found',
        'status_code' => '0'
      ]);
    }

    $result_data = $result->map(function($message) use ($host_id, $user_id, $user) {
      $return_data = array();
      $message->read = '1';
      $message->save();
      $message->load('reservation.space');
      $reservation = $message->reservation;

      $message_type_text  = $this->getMessageTypeText($message->message_type);
      $message_date     = $this->getMessageDateText($message->created_at,$message->created_time);

      $sender_details = array(
        'status'    => $message_type_text,
        'date/time'   => $message_date,
        'inquiry_title' => '',
        'date'      => '',
        'amount'    => '',
      );

      // For Admin Message
      if($message->user_to == $message->user_from) {
        $return_data = array(
          'sender_thumb_image'  => '',
          'sender_user_name'    => '',
          'sender_message_status' => '',
          'sender_details'    => (object)array(),
          'sender_messages'     => '',
          'receiver_thumb_image'  => asset('admin_assets/dist/img/avatar04.png'),
          'receiver_user_name'  => $message->user_details->full_name,
          'receiver_message_status'=> $message->read,
          'receiver_details'    => $sender_details,
          'receiver_messages'   => $message->message,
          'conversation_time'   => $message_date,
        );
        return $return_data;
      }

      // User To Host Messages
      if($message->user_from == $user_id && $message->user_to == $host_id) {
        $profile_image_src = ProfilePicture::where('user_id', $user_id)->first()->src;
        if($message->message_type == 12) {
          $sender_details = array(
            'status'    => 'Pre-Accept',
            'inquiry_title' => 'Inquiry about ' . $reservation->space->name,
            'date'      => $reservation->dates_subject.' '.$reservation->number_of_guests.' Guest',
            'amount'    => 'You will earn '.$reservation->space->activity_price->hourly.' '.$user->currency_code,
          );
        }

        $return_data = array(
          'sender_thumb_image'  => $profile_image_src,
          'sender_user_name'    => $message->user_details->full_name,
          'sender_message_status' => $message->read,
          'sender_details'    => $sender_details,
          'sender_messages'     => $message->message,
          'receiver_thumb_image'  => '',
          'receiver_user_name'  => '',
          'receiver_message_status' => '',
          'receiver_details'    => (object)array(),
          'receiver_messages'   => '',
          'conversation_time'   => $message_date,
        );
      }

      // Host To User Messages
      if ($message->user_to == $user_id && $message->user_from == $host_id) {
        $profile_image_src = ProfilePicture::where('user_id', $host_id)->first()->src;
        $return_data = array(
          'sender_thumb_image'  => '',
          'sender_user_name'    => '',
          'sender_message_status' => '',
          'sender_details'    => (object)array(),
          'sender_messages'     => '',          
          'receiver_thumb_image'  => $profile_image_src,
          'receiver_user_name'  => $message->user_details->full_name,
          'receiver_message_status' => $message->read,
          'receiver_details'    => $sender_details,
          'receiver_messages'   => $message->message,
          'conversation_time'   => $message_date,
        );
      }
      return $return_data;
    });
    
    if(count($result_data->first()) == 0) {
      $result_data = array();
    }

    return response()->json([
      'status_code'       => '1',
      'success_message'     => __('messages.api.listed_successful'),
      'sender_user_name'    => $user->full_name,
      'sender_thumb_image'  => $user->profile_picture->src,
      'receiver_user_name'  => $host_user->full_name,
      'receiver_thumb_image'  => $host_user->profile_picture->src,
      'data'          => $result_data,
    ]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function inbox(Request $request)
  {
    $user = JWTAuth::parseToken()->authenticate();
    $user_id = $user->id;

    $all_message = Messages::whereIn('id', function ($query) use ($user_id) {
      $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
    })
    ->with('space','space_address','reservation.currency','reservation.reservation_times','user_details.profile_picture','other_user_details.profile_picture')
    ->orderBy('id', 'desc')
    ->get();

    //check message count
    if ($all_message->count() < 0) {
      return response()->json([
        'status_code'     => '0',
        'success_message'   => 'No Data Found',
      ]);
    }

    $unread_count = $all_message->where('read','0')->count();

    $message_data = $all_message->map(function($message) use($user_id) {
      $space_address  = $message->space_address;
      $reservation  = $message->reservation;
      $space_id     = optional($message->reservation)->space_id;
      $c_date     = date('Y-m-d');

      $return_data    = $this->getCommonMessageData();

      // For Admin Messages
      if($message->user_from == $message->user_to) {

        $return_data['reservation_id'] = $message->reservation_id;
        $return_data['space_id']    = $message->space_id;
        $return_data['other_user_id'] = $message->user_to;
        $return_data['request_user_id'] = $message->user_to;

        $message_data = array(
          'is_message_read'   => $message->read,
          'last_message'    => $message->message,
        );

        return array_merge($return_data,$message_data);
      }

      $other_user_data = ($message->user_from == $user_id) ? $message->other_user_details : $message->user_details;

      $expire_timer = '';
      if(isset($reservation) && $reservation->status == 'Pending') {
        $from_time  = strtotime($reservation->created_at_timer);
        $to_time  = strtotime(date('Y/m/d H:i:s'));
        $diff     = abs($from_time - $to_time);
        $expire_timer = sprintf('%02d:%02d:%02d', ($diff / 3600), ($diff / 60 % 60), $diff % 60);
      }
      $special_offer = SpecialOffer::where('reservation_id', $message->reservation_id)->orderBy('id', 'desc')->first();

      $booking_status = 'Not available';
      if(optional($special_offer)->type == 'pre-approval' || optional($special_offer)->type == 'special_offer') {
        $event_type = [];
        if($special_offer != '') {
          $event_type = ['activity_type' => $special_offer->activity_type, 'activity' => $special_offer->activity, 'sub_activity' => $special_offer->sub_activity];
        }
        $booking_date_times = array();
                $booking_times = optional($special_offer)->special_offer_times;
                
                $booking_date_times['id']           = $booking_times->id;
                $booking_date_times['start_date']   = $booking_times->checkin_formatted;
                $booking_date_times['formatted_start_date'] = $booking_times->start_date;
                $booking_date_times['end_date']     = $booking_times->checkout_formatted;
                $booking_date_times['formatted_end_date'] = $booking_times->end_date;
                $booking_date_times['week_day']     = date('w',strtotime($booking_times->start_date));
                $booking_date_times['start_time']   = $booking_times->start_time;
                $booking_date_times['end_time']     = $booking_times->end_time;

        $price_data = array('space_id' => $special_offer->space_id, 'event_type' => $event_type, 'booking_date_times' => $booking_date_times, 'number_of_guests' => $special_offer->number_of_guests, 'booking_period' => $special_offer->booking_period);
            $additional_data = array('special_offer_id' => $special_offer->special_offer_id, 'reservation_id' => $special_offer->reservation_id);
            $price_list = $this->payment_helper->price_calculation($space_id, (Object)$price_data, (Object)$additional_data);

            $price_list = json_decode($price_list);

        if($price_list->status == 'Available' && $booking_times->start_date > $c_date) {
          $booking_status = 'Available';
        }
      }
      if(isset($reservation) && $reservation->status == 'Pre-Accepted') {
        if($reservation->checkin > $c_date) {
          $booking_status = 'Available';
        }
      }

      if(optional($special_offer)->type == 'special_offer') {
        $return_data['special_offer_id']  = $special_offer->id;
        $return_data['special_offer_status']= 'Yes';
      }

      if(isset($reservation)) {
        $coupon_amount = $reservation->coupon_code != 'Travel_Credit' ? $reservation->coupon_amount : '0';
        $travel_credit = $reservation->coupon_code == 'Travel_Credit' ? $reservation->coupon_amount : '0';        
      }

      $return_data['reservation_id']  = $reservation['id'];
      $return_data['space_id']    = $reservation['space_id'];
      $return_data['booking_status']  = $booking_status;
      $return_data['reservation_status']= $reservation['status'];


      $space = array(
        'space_name'    => $reservation['space']['name'],
        'space_type_name' => $reservation['space']['space_type_name'],
        'space_image'     => $reservation['space']['photo_name'],
        'space_location'  => $space_address->address_line,
      );

      $other_user = array(
        'other_user_id'   => $other_user_data->id,
        'other_user_name'   => $other_user_data->full_name,
        'other_thumb_image' => $other_user_data->profile_picture->email_src,
        'other_user_location'=> $other_user_data->live,
        'member_from'     => $other_user_data->since,
      );

      $reservation_price = array(
        'reservation_date'  => $reservation['dates_subject'],
        'checkin'       => $reservation['checkin'],
        'checkout'      => $reservation['checkout'],
        'start_time'    => optional($reservation['reservation_times'])->start_time_formatted,
        'end_time'      => optional($reservation['reservation_times'])->end_time_formatted,
        'number_of_guests'  => $reservation['number_of_guests'],
        'currency_symbol' => $reservation['currency']['symbol'],
        'total_hours'   => $reservation['hours'],
        'total_cost'    => $reservation['check_total'],
        'review_count'    => $message->space->reviews_count,
      );

      $other_details = array(
        'request_user_id'   => $reservation['host_id'],
        'can_view_receipt'  => ($reservation['status'] == 'Accepted'),
        'payment_recieved_date' => ($reservation['transaction_id'] != '') ? $reservation['updated_at'] : '',
        'expire_timer'    => $expire_timer,
      );

      $message_data = array(
        'is_message_read'   => $message->read,
        'last_message'    => $message->message,
      );

      $reservation_price = array_map('strval', $reservation_price);

      return array_merge($return_data,$message_data,$space,$other_user,$reservation_price, $other_details);
    });

    return response()->json([
      'status_code'     => '1',
      'success_message'   => __('messages.api.listed_successful'),
      'unread_count'    => strval($unread_count),
      'data'        => $message_data,
    ]);
  }

  /**
     *Send Message
     *
     * @param  Get method inputs
     * @return Response in Json Format
     */
    public function price_breakdown(Request $request)
    {
      $rules = array(
      'user_type'   => 'required|in:guest,host',
    );

      if($request->reservation_id != '') {
        $rules['reservation_id'] = 'required|exists:reservation,id';
      }
      else {
        $rules['s_key'] = 'required|exists:session_reservations,id';
    }

    $attributes = array('s_key' => 'Session Key');
    $messages  = array('required' => ':attribute is required.');
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);

    if ($validator->fails()) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
    }

    $user = JWTAuth::parseToken()->authenticate();

    $s_key = $request->s_key;
    $user_type = $request->user_type;

        if($request->reservation_id != '') {
            $payment_data = $this->getPaymentData($request->reservation_id,'reservation');
        }
        else {
            $payment_data = $this->getPaymentData($s_key,'session');
        }

        if(!$payment_data) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => __('messages.api.invalid_payment_data'),
            ]);
        }

        $payment_data = arrayToObject($payment_data);
      $currency_symbol = html_entity_decode($payment_data->currency_symbol);     
     
      // $result[] = array(
      //   'key'   => 'Total Hours',
      //   'value' => strval($payment_data->hours),
      // );
      if($payment_data->count_total_hour)
      {
      $result[] = array(
        'key'   => $currency_symbol.$payment_data->hour_amount.' x '.$payment_data->count_total_hour." ".($payment_data->count_total_hour>1?'Hours':"Hour"),
        'value' => strval($payment_data->count_total_hour*$payment_data->hour_amount),
      );
      }
      if($payment_data->count_total_days)
      {
      $result[] = array(
        'key'   => $currency_symbol.$payment_data->full_day_amount.' x '.$payment_data->count_total_days." ".($payment_data->count_total_days>1?'Days':"Day"),
        'value' => strval($payment_data->count_total_days*$payment_data->full_day_amount),
      );
      }
      if($payment_data->count_total_week)
      {
      $result[] = array(
        'key'   => $currency_symbol.$payment_data->weekly_amount.' x '.$payment_data->count_total_week." ".($payment_data->count_total_week>1?'Weeks':"Week"),
        'value' => strval($payment_data->count_total_week*$payment_data->weekly_amount),
      );
      }
      if($payment_data->count_total_month)
      {
      $result[] = array(
        'key'   => $currency_symbol.$payment_data->monthly_amount.' x '.$payment_data->count_total_month." ".($payment_data->count_total_month>1?'Months':"Month"),
        'value' => strval($payment_data->count_total_month*$payment_data->monthly_amount),
      );
      }
      $result[] = array(
        // 'key'   => $currency_symbol.$payment_data->base_per_hour.' x '.$payment_data->hours,
        'key'   => 'Total Amount',

        'value' => $currency_symbol.($payment_data->additional_total_price),
      );

      if($user_type == 'guest') {
        if($this->checkValue($payment_data->service)) {
          $result[] = array(
            'key'   => 'Service Fee',
            'value' => $currency_symbol.($payment_data->service),
          );
        }

        /*$result[] = array(
          'key'   => 'Total Price',
          'value' => $currency_symbol.($payment_data->guest_total),
        );*/

        if($this->checkValue($payment_data->coupon_amount)) {
          $result[] = array(
            'key'   => 'Coupon Amount',
            'value' => '-'.$currency_symbol.($payment_data->coupon_amount),
          );
        }

        $result[] = array(
          'key'   => 'Total Payment Amount',
          'value' => $currency_symbol.($payment_data->payment_total),
        );

      }
      else {

        if($this->checkValue($payment_data->host_fee)) {
          $result[] = array(
            'key'   => 'Host Fee',
            'value' => $currency_symbol.($payment_data->host_fee),
          );
        }

        if($this->checkValue($payment_data->penalty_amount)) {
          $result[] = array(
            'key'   => 'Host Penalty Amount',
            'value' => $currency_symbol.($payment_data->penalty_amount),
          );
        }

        $result[] = array(
          'key'   => 'Total Payout',
          'value' => $currency_symbol.($payment_data->host_total),
        );
      }

    if($this->checkValue($payment_data->security)) {
        $result[] = array(
          'key'   => 'Security Fee',
          'value' => $currency_symbol.($payment_data->security),
        );
      }

    return response()->json([
      'status_code'   => '1',
      'success_message' => __('messages.api.listed_successful'),
      'data'      => $result,
    ]);   
    }
}