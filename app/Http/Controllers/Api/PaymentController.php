<?php

/**
 * Payment Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    API Payment
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Http\Helper\PaymentHelper;
use App\Models\SpaceCalendar;
use App\Models\Country;
use App\Models\CouponCode;
use App\Models\Referrals;
use App\Models\AppliedTravelCredit;
use App\Models\Currency;
use App\Models\Messages;
use App\Models\PaymentGateway;
use App\Models\Payouts;
use App\Models\ProfilePicture;
use App\Models\SessionReservation;
use App\Models\Reservation;
use App\Models\ReservationTimes;
use App\Models\Fees;
use App\Models\Space;
use App\Models\SiteSettings;
use App\Models\SpecialOffer;
use App\Models\User;
use DB;
use JWTAuth;
use Validator;

class PaymentController extends Controller
{
    protected $payment_helper;

    public function __construct(PaymentHelper $payment)
    {
        $this->payment_helper = $payment;
    }

    /**
     * Send Notification mail to User
     *
     * @param String $space_id
     * @param String $type
     */
    protected function notifyUserViaMail($type, $mail_data)
    {
        $email_controller = new EmailController;

        if($type == 'inquiry') {
            $email_controller->inquiry($mail_data->id, $mail_data->question);
        }
        else if($type == 'booking_success') {
            $email_controller->accepted($mail_data->id);
            $email_controller->booking_confirm_host($mail_data->id);
            $email_controller->booking_confirm_admin($mail_data->id);
        }
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
            $session_reservation = SessionReservation::addSelect('id','space_id','event_type','booking_date_times','number_of_guests','special_offer_id','reservation_id','coupon_code')->find($key);
            if(!$session_reservation) {
                return array();
            }

            $payment = $session_reservation->append('price_list')->toArray();
            $payment['s_key'] = $session_reservation['id'];            
        }
        else if($type == 'special_offer') {
            $special_offer = SpecialOffer::with('space','special_offer_times')->find($key);
            if(!$special_offer) {
                return array();
            }

            $user = JWTAuth::parseToken()->authenticate();
            $booking_date_times = array();
            $booking_times = $special_offer->special_offer_times;
            $booking_date_times['id']           = $booking_times->id;
            $booking_date_times['start_date']   = $booking_times->start_date;
            $booking_date_times['start_time']   = $booking_times->start_time;
            $booking_date_times['end_date']     = $booking_times->end_date;
            $booking_date_times['end_time']     = $booking_times->end_time;
            $booking_date_times['formatted_end_date'] = $booking_times->end_date;
            $booking_date_times['formatted_start_date'] = $booking_times->start_date;

            $event_type = ['activity_type' => $special_offer->activity_type, 'activity' => $special_offer->activity, 'sub_activity' => $special_offer->sub_activity];
            $booking_period = ($booking_times->start_date == $booking_times->end_date) ? 'Single' : 'Multiple';

            $price_data = array('space_id' => $special_offer->space_id, 'event_type' => $event_type, 'booking_date_times' => $booking_date_times, 'number_of_guests' => $special_offer->number_of_guests, 'booking_period' => $booking_period);

            $additional_data = array('special_offer_id' => $special_offer->id, 'reservation_id' => '');

            $price_list = $this->payment_helper->price_calculation($special_offer->space_id, (Object)$price_data, (Object)$additional_data);

            $price_list = json_decode($price_list);

            $session_data = array(
                'space_id'          => $special_offer->space_id,
                'number_of_guests'  => $special_offer->number_of_guests,
                'special_offer_id'  => $special_offer->id,
                'reservation_id'    => '',
                'event_type'        => $event_type,
                'booking_date_times'=> $booking_date_times,
            );

            $session_reservation = SessionReservation::updateOrCreate(['user_id' => $user->id],$session_data);

            $payment = array(
                'space_id'          => $special_offer->space_id,
                'event_type'        => $event_type,
                'booking_date_times'=> $booking_date_times,
                'number_of_guests'  => $special_offer->number_of_guests,
                'special_offer_id'  => $special_offer->id,
                'reservation_id'    => '',
                'coupon_code'       => $special_offer->coupon_code,
                'price_list'        => $price_list,
                's_key'             => $session_reservation->id,
            );
        }
        else {
            $reservation = Reservation::with('space','reservation_times')->find($key);
            if(!$reservation) {
                return array();
            }

            $user = JWTAuth::parseToken()->authenticate();
            $booking_date_times = array();
            $booking_times = $reservation->reservation_times;
            $booking_date_times['id']           = $booking_times->id;
            $booking_date_times['start_date']   = $booking_times->start_date;
            $booking_date_times['start_time']   = $booking_times->start_time;
            $booking_date_times['end_date']     = $booking_times->end_date;
            $booking_date_times['end_time']     = $booking_times->end_time;
            $booking_date_times['formatted_end_date'] = $booking_times->end_date;
            $booking_date_times['formatted_start_date'] = $booking_times->start_date;

            $event_type = ['activity_type' => $reservation->activity_type, 'activity' => $reservation->activity, 'sub_activity' => $reservation->sub_activity];
            $booking_period = ($booking_times->start_date == $booking_times->end_date) ? 'Single' : 'Multiple';

            $price_data = array('space_id' => $reservation->space_id, 'event_type' => $event_type, 'booking_date_times' => $booking_date_times, 'number_of_guests' => $reservation->number_of_guests, 'booking_period' => $booking_period);

            $additional_data = array('special_offer_id' => '', 'reservation_id' => $reservation->id);

            $price_list = $this->payment_helper->price_calculation($reservation->space_id, (Object)$price_data, (Object)$additional_data);

            $price_list = json_decode($price_list);

            $session_data = array(
                'space_id'          => $reservation->space_id,
                'number_of_guests'  => $reservation->number_of_guests,
                'special_offer_id'  => '',
                'reservation_id'    => $reservation->id,
                'event_type'        => $event_type,
                'booking_date_times'=> $booking_date_times,
            );

            $session_reservation = SessionReservation::updateOrCreate(['user_id' => $user->id],$session_data);

            $payment = array(
                'space_id'          => $reservation->space_id,
                'event_type'        => $event_type,
                'booking_date_times'=> $booking_date_times,
                'number_of_guests'  => $reservation->number_of_guests,
                'special_offer_id'  => '',
                'reservation_id'    => $reservation->id,
                'coupon_code'       => $reservation->coupon_code,
                'price_list'        => $price_list,
                's_key'             => $session_reservation->id,
            );
        }

        $space_details = Space::with('users.profile_picture','space_address')->find($payment['space_id']);

        $payment['space_name'] = $space_details->name;
        $payment['space_type_name'] = $space_details->space_type_name;
        $payment['space_address'] = $space_details->space_address->formatted_address;

        $host_data = array(
            'host_user_id'     => $space_details->users->id,
            'host_user_name'   => $space_details->users->full_name,
            'host_thumb_image' => $space_details->users->profile_picture->email_src,
        );
        $return_data = array_merge($payment,$host_data);
        return $return_data;
    }

    /**
     * Get All Payment Credentials
     * 
     * @return Array $payment_credentials
     */
    protected function getPaymentCredentials()
    {
        $payment_gateway = resolve('payment_gateway');
        $paypal_details = $payment_gateway->where('site', 'PayPal')->pluck('value','name');
        $stripe_details = $payment_gateway->where('site', 'Stripe')->pluck('value','name');

        $payment_credentials['PAYPAL_LIVE_MODE'] = ($paypal_details['mode'] != 'sandbox');
        $payment_credentials['PAYPAL_CLIENT_ID'] = $paypal_details['client'];
        $payment_credentials['STRIPE_PUBLISH_KEY'] = $stripe_details['publish'];

        return $payment_credentials;
    }

    protected function applyTravelCredit($user_id, $coupon_amount, $reservation_id,$currency_code)
    {
        $referral_friend = Referrals::whereFriendId($user_id)->get();
        foreach($referral_friend as $row) {
            $friend_credit = $row->friend_credited_amount;
            if($coupon_amount != 0) {
                if($friend_credit <= $coupon_amount) {
                    $referral = Referrals::find($row->id);
                    $referral->friend_credited_amount = 0;
                    $referral->save();
                    $coupon_amount = $coupon_amount - $friend_credit;

                    $applied_referral = new AppliedTravelCredit;
                    $applied_referral->reservation_id = $reservation_id;
                    $applied_referral->referral_id = $row->id;
                    $applied_referral->amount = $friend_credit;
                    $applied_referral->type = 'friend';
                    $applied_referral->currency_code = $currency_code;
                    $applied_referral->save();
                }
                else {
                    $referral = Referrals::find($row->id);
                    $remain = $friend_credit - $coupon_amount;
                    $referral->friend_credited_amount = $referral->convert($remain);
                    $referral->save();

                    $applied_referral = new AppliedTravelCredit;
                    $applied_referral->reservation_id = $reservation_id;
                    $applied_referral->referral_id = $row->id;
                    $applied_referral->amount = $coupon_amount;
                    $applied_referral->type = 'friend';
                    $applied_referral->currency_code = $currency_code;
                    $applied_referral->save();
                    $coupon_amount = 0;
                }
            }
        }
        $referral_user = Referrals::whereUserId($user_id)->get();
        foreach($referral_user as $row) {
            $user_credit = $row->credited_amount;
            if($coupon_amount != 0) {
                if($user_credit <= $coupon_amount) {
                    $referral = Referrals::find($row->id);
                    $referral->credited_amount = 0;
                    $referral->save();
                    $coupon_amount = $coupon_amount - $user_credit;

                    $applied_referral = new AppliedTravelCredit;
                    $applied_referral->reservation_id = $reservation_id;
                    $applied_referral->referral_id = $row->id;
                    $applied_referral->amount = $user_credit;
                    $applied_referral->type = 'main';
                    $applied_referral->currency_code = $currency_code;
                    $applied_referral->save();
                }
                else {
                    $referral = Referrals::find($row->id);
                    $referral->credited_amount = $user_credit - $coupon_amount;
                    $referral->save();

                    $applied_referral = new AppliedTravelCredit;
                    $applied_referral->reservation_id = $reservation_id;
                    $applied_referral->referral_id = $row->id;
                    $applied_referral->amount = $coupon_amount;
                    $applied_referral->type = 'main';
                    $applied_referral->currency_code = $currency_code;
                    $applied_referral->save();
                    $coupon_amount = 0;
                }
            }
        }
    }

    /**
     * Check Stripe Payment Intent Status
     * 
     * @return App\Models\Reservation $reservation Created reservation 
     */
    protected function getStripeTransactionId($payment_intent_id)
    {
        $secret_key = view()->shared('stripe_secret_key');
        \Stripe\Stripe::setApiKey($secret_key);

        $intent = \Stripe\PaymentIntent::retrieve(
            $payment_intent_id
        );
        if($intent->status == 'succeeded') {
            return arrayToObject(['status' => true,'intent_id' => $intent->id]);
        }
        return arrayToObject(['status' => false,'status_message' => 'Payment not yet Completed.']);
    }

    /**
     * Get Transaction Id of Given Paypal payment key
     * 
     * @return App\Models\Reservation $reservation Created reservation 
     */
    protected function getPaypalTransactionId($pay_key)
    {
        $payment_gateway = resolve('payment_gateway');
        $paypal_credentials = $payment_gateway->where('site', 'PayPal')->pluck('value','name');
        $gateway = \Omnipay\Omnipay::create('PayPal_Rest');

        // Initialise the gateway
        $gateway->initialize(array(
            'clientId'  => $paypal_credentials['client'],
            'secret'    => $paypal_credentials['secret'],
            'testMode'  => ($paypal_credentials['mode'] == 'sandbox'),
        ));

        $transaction_id = $pay_key ?: '';

        try {
            $purchase_response = $gateway->fetchPurchase(['transactionReference' => $pay_key])->send();
            $transaction_id = $purchase_response->getTransactionReference();
        }
        catch(\Exception $e) {
            // 
        }
        return $transaction_id;
    }

    /**
     * To store the payment details in reservation
     * 
     * @return App\Models\Reservation $reservation Created reservation 
     */
    protected function updateReservation($reservation_data, $type, $s_key = '')
    {
        $user           = JWTAuth::parseToken()->authenticate();
        $user_id        = $user->id;
        $host_penalty   = Fees::find(3)->value;

        $price_list     = $reservation_data['price_list'];

        $reservation_id = isset($reservation_data['reservation_id']) ? $reservation_data['reservation_id'] : '';
        $space_id       = $reservation_data['space_id'];

        $reservation = Reservation::findOrNew($reservation_id);

        $reservation->space_id         = $space_id;
        $reservation->host_id          = $reservation_data['host_id'];
        $reservation->user_id          = $user_id;
        $reservation->number_of_guests = $reservation_data['number_of_guests'];
        $reservation->activity_type    = $price_list->activity_type;
        $reservation->activity         = $price_list->activity;
        $reservation->sub_activity     = $price_list->sub_activity;
        $reservation->hours            = $price_list->count_total_hour;
        $reservation->days            = $price_list->count_total_days;
        $reservation->weeks            = $price_list->count_total_week;
        $reservation->months            = $price_list->count_total_month;
        $reservation->per_hour         = $price_list->hour_amount;
        $reservation->per_week         = $price_list->weekly_amount;
        $reservation->per_month         = $price_list->monthly_amount;
        $reservation->per_day         = $price_list->full_day_amount;
        $reservation->base_per_hour    = $price_list->base_hour_price;
        $reservation->subtotal         = $price_list->subtotal;
        $reservation->security         = $price_list->security_fee;
        $reservation->service          = $price_list->service_fee;
        $reservation->host_fee         = $price_list->host_fee;
        $reservation->total            = $price_list->payment_total;
        $reservation->currency_code    = $price_list->currency_code;
        $reservation->paypal_currency  = PAYPAL_CURRENCY_CODE;
        $reservation->type             = ($type == 'contact_host') ? 'contact' : 'reservation';
        $reservation->status           = $reservation_data['status'] ?? NULL;
        $reservation->country          = $reservation_data['country'];
        $reservation->paymode          = $reservation_data['paymode'] ?? NULL;
        $reservation->cancellation     = $reservation_data['cancellation'];

        if($type == 'confirm_booking') {
            if($price_list->special_offer == '') {
                $reservation->cleaning     = $price_list->cleaning ?? 0;
            }
            else {
                $reservation->cleaning     = 0;
            }

            if($price_list->coupon_amount > 0) {
                $reservation->coupon_code   = $price_list->coupon_code;
                $reservation->coupon_amount = $price_list->coupon_amount;
            }
            if(@session('payment')[$s_key]['special_offer_id']) {
                $reservation->special_offer_id = session('payment')[$s_key]['special_offer_id'];
            }

            $reservation->transaction_id    = $reservation_data['transaction_id'] ?? '';
            $reservation->paymode           = $reservation_data['paymode'];

            if($reservation_data['paymode'] == 'Credit Card') {
                $reservation->first_name   = $reservation_data['first_name'];
                $reservation->last_name    = $reservation_data['last_name'];
                $reservation->postal_code  = $reservation_data['postal_code'];
            }
        }
        else {
            $reservation->host_penalty     = $host_penalty;
        }

        $reservation->save();

        if(count($reservation_data['booking_date_times'])) {
            $booking_date_times = $reservation_data['booking_date_times'];

            $reserve_time_id   = (isset($booking_date_times['id']) && $price_list->special_offer == '') ? $booking_date_times['id'] : '';
            $status            = (@$reservation_data['status'] == 'Accepted') ? 'Not available' : 'Available';
            $reserve_date_time = ReservationTimes::findOrNew($reserve_time_id);
            $reserve_date_time->space_id    = $reservation_data['space_id'];
            $reserve_date_time->reservation_id = $reservation->id;
            $reserve_date_time->start_date  = $booking_date_times['formatted_start_date'];
            $reserve_date_time->end_date    = $booking_date_times['formatted_end_date'];
            $reserve_date_time->start_time  = $booking_date_times['start_time'];
            $reserve_date_time->end_time    = $booking_date_times['end_time'];
            $reserve_date_time->status      = $status;
            $reserve_date_time->save();
        }

        if($type == 'confirm_booking') {
            do {
                $code = getCode(6, $reservation->id);
                $check_code = Reservation::where('code', $code)->get();
            }
            while(empty($check_code));

            $reservation = Reservation::find($reservation->id);
            $reservation->code = $code;
            $reservation->save();

            if($price_list->coupon_code == 'Travel_Credit') {
                $this->applyTravelCredit($user_id, $price_list->coupon_amount,$reservation->id,$price_list->currency_code);
            }
        }

        if($reservation->status == 'Accepted') {
            $host_payout_amount  = $reservation->host_payout;
            $this->payment_helper->payout_refund_processing($reservation, 0, $host_payout_amount);
        }

        SessionReservation::where('id',$s_key)->delete();

        return $reservation;
    }

    /**
     * Contact Request send to Host
     *
     * @param array $request Input values
     * @return redirect to Rooms Detail page
     */
    public function contact_request(Request $request)
    {
        $rules = array(
            'space_id'           => 'required|exists:space,id',
            'number_of_guests'   => 'required',
            'event_type'         => 'required',
            'booking_date_times' => 'required',
            'message'            => 'required',
        );

        $attributes = array(
            'space_id'      => 'Space Id',
            'event_type'    => 'Event Id',
            'number_of_guests'  => 'Number of Guests',
            'booking_date_times'  => 'Date And Times',
        );

        $messages = array('required' => ':attribute is required.');
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if($validator->fails()) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
        }

        $user       = JWTAuth::parseToken()->authenticate();
        $space_id   = $request->space_id;
        $space_details = Space::find($space_id);
        $event_type = json_decode($request->event_type,true);
        $booking_date_times = json_decode($request->booking_date_times,true);

        if($event_type == null || $booking_date_times == null) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => __('messages.api.invalid_request'),
            ]);
        }

        $booking_date_times['formatted_start_date'] = $booking_date_times['start_date'];
        $booking_date_times['formatted_end_date'] = $booking_date_times['end_date'];
        $booking_period = ($booking_date_times['start_date'] == $booking_date_times['end_date']) ? 'Single' : 'Multiple';

        $price_data = array('space_id' => $space_id, 'event_type' => $event_type, 'booking_date_times' => $booking_date_times, 'number_of_guests' => $request->number_of_guests, 'booking_period' => $booking_period);
        $additional_data = array('special_offer_id' => $request->special_offer_id, 'reservation_id' => $request->reservation_id);

        $price_list = $this->payment_helper->price_calculation($space_id, (Object)$price_data, (Object)$additional_data);

        $price_list = json_decode($price_list);

        if($price_list->status == 'Not available') {
            return response()->json([
                'status_code'       => '0',
                'success_message'   => $price_list->status_message,
            ]);
        }

        $reservation_data['space_id']           = $space_id;
        $reservation_data['country']            = 'US';
        $reservation_data['host_id']            = $space_details->user_id;
        $reservation_data['number_of_guests']   = $request->number_of_guests;
        $reservation_data['cancellation']       = $space_details->cancellation_policy;
        $reservation_data['price_list']         = $price_list;
        $reservation_data['host_name']          = $space_details->users->first_name;
        $reservation_data['booking_date_times'] = $price_data['booking_date_times'];

        $reservation   = $this->updateReservation($reservation_data,'contact_host');

        $question = removeEmailNumber($request->message);

        $message = new Messages;
        $message->space_id       = $space_id;
        $message->reservation_id = $reservation->id;
        $message->user_to        = $space_details->user_id;
        $message->user_from      = $user->id;
        $message->message        = $question;
        $message->message_type   = 9;
        $message->read           = 0;
        $message->save();

        $mail_data['id']        = $reservation->id;
        $mail_data['question']  = $question;

        $this->notifyUserViaMail('inquiry',(Object)$mail_data);

        return response()->json([
            'status_code'       => '1',
            'success_message'   => __('messages.rooms.contact_request_has_sent',['first_name'=> $space_details->users->first_name ]),
        ]);
    }

    /**
     * Store Payment Data
     *
     * @param $request Input values
     * @return redirect to Rooms Detail page
     */
    public function store_payment_data(Request $request)
    {
        $rules = array(
            'space_id'          => 'required|exists:space,id',
            'event_type'        => 'required',
            'booking_date_times'=> 'required',
            'number_of_guests'  => 'required',
            'booking_type'      => 'required',
        );

        $attributes = array(
            'space_id'          => 'Space Id',
            'event_type'        => 'Event Type',
            'booking_date_times'=> 'Date and Time',
            'number_of_guests'  => 'Number of Guests',
            'booking_type'      => 'Booking Type',
        );

        $messages = array('required' => ':attribute is required.');
        $validator = Validator::make($request->all(), $rules, $messages,$attributes);
        if($validator->fails()) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
        }

        $user       = JWTAuth::parseToken()->authenticate();
        $space_id   = $request->space_id;
        $space_details = Space::findOrFail($space_id);
        $event_type = json_decode($request->event_type,true);
        $booking_date_times = json_decode($request->booking_date_times,true);
        
        if($event_type == null || $booking_date_times == null) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => __('messages.api.invalid_request'),
            ]);
        }

        $booking_date_times['formatted_start_date'] = $booking_date_times['start_date'];
        $booking_date_times['formatted_end_date'] = $booking_date_times['end_date'];
        $booking_period = ($booking_date_times['start_date'] == $booking_date_times['end_date']) ? 'Single' : 'Multiple';

        $price_data = array('space_id' => $space_id, 'event_type' => $event_type, 'booking_date_times' => $booking_date_times, 'number_of_guests' => $request->number_of_guests, 'booking_period' => $booking_period);
        $additional_data = array('special_offer_id' => $request->special_offer_id, 'reservation_id' => $request->reservation_id);

        $price_list = $this->payment_helper->price_calculation($space_id, (Object)$price_data, (Object)$additional_data);

        $price_list = json_decode($price_list);

        $pending_reservation_check  = Reservation::where(['space_id' => $space_id,'id' => $request->reservation_id, 'user_id' => $user->id, 'status' => 'Pending'])->count();

        if($price_list->status == 'Not available' || $pending_reservation_check > 0) {
            return response()->json([
                'status_code'       => '0',
                'success_message'   => $price_list->status_message,
            ]);
        }

        $session_data = array(
            'space_id'          => $space_id,
            'number_of_guests'  => $request->number_of_guests,
            'special_offer_id'  => $request->special_offer_id,
            'reservation_id'    => $request->reservation_id,
            'event_type'        => $event_type,
            'booking_date_times'=> $booking_date_times,
        );

        $session_reservation = SessionReservation::updateOrCreate(['user_id' => $user->id],$session_data);

        return response()->json([
            'status_code'       => '1',
            'success_message'   => __('messages.api.update_success'),
            's_key'             => $session_reservation->id,
        ]);
    }

    /**
     * Get Payment Data
     *
     * @param $request Input values
     * @return redirect to Rooms Detail page
     */
    public function get_payment_data(Request $request)
    {
        if($request->special_offer_id != '') {
            $rules['special_offer_id'] = 'required|exists:special_offer,id';
        }
        else if($request->reservation_id != '') {
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
        $s_key = $request->s_key;
        
        if($request->special_offer_id != '') {
            $payment_data = $this->getPaymentData($request->special_offer_id,'special_offer');
        }
        else if($request->reservation_id != '') {
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

        $price_list  = $payment_data['price_list'];

        $boooking_date_times  = $payment_data['booking_date_times'];
        $remove_keys = ['price_list', 'event_type'];
        $payment_data = array_diff_key($payment_data, array_flip($remove_keys));

        if($price_list->status == 'Not available') {
            return response()->json([
                'status_code'     => '2',
                'success_message' => $price_list->status_message,
            ]);
        }

        $payment_data['activity_type']  = $price_list->display_name;
        $payment_data['boooking_date_time']  = getDatesSubject($boooking_date_times);
        $payment_data['total_hours']    = $price_list->total_hours;
        // $payment_data['total_price']    = $price_list->total_price;
        $payment_data['payment_total']  = $price_list->payment_total;
        $payment_data['coupon_applied'] = ($price_list->coupon_amount > 0);
        $payment_data['currency_code']  = $price_list->currency_code;
        $payment_data['currency_symbol']= Currency::original_symbol($price_list->currency_code);


        $from_currency = Currency::whereCode($price_list->currency_code)->first();
        $from_rate = optional($from_currency)->rate;
        $to_currency = Currency::whereCode(PAYPAL_CURRENCY_CODE)->first();
        $to_rate = optional($to_currency)->rate;

        $payment_data['payment_price_rate']  = number_format(($from_rate/$to_rate), 2);
        $payment_data['payment_currency'] = PAYPAL_CURRENCY_CODE;
        $payment_data['payment_price'] = currency_convert($price_list->currency_code, PAYPAL_CURRENCY_CODE, $price_list->total_price);

        $return_data        = array(
            'status_code'       => '1',
            'success_message'   => __('messages.api.update_success'),
            'data'              => $payment_data,
            'payment_credentials' => $this->getPaymentCredentials(),
        );

        return response()->json($return_data);
    }

     /**
     * Appy Coupen Code Function
     *
     * @param array $request    Input values
     * @return redirect to Payemnt Page
     */
    public function apply_coupon(Request $request)
    {
        $rules = array(
            's_key'         => 'required|exists:session_reservations,id',
            'coupon_code'   => 'required',
        );

        $attributes = array(
            's_key'      => 'Session Key',
            'coupon_code'=> 'Coupon Code',
        );

        $messages = array('required' => ':attribute is required.');
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        $coupon_code      = $request->coupon_code;
        $s_key            = $request->s_key;
        
        $session_reservation = SessionReservation::find($s_key);
        $coupon_status = $session_reservation->validateCouponCode($coupon_code);

        $return_data        = array(
            'status_code'       => '1',
            'success_message'   => __('messages.api.coupon_code_applied'),
        );
        
        if($coupon_status->status) {
            $session_reservation->coupon_code = $coupon_code;
            $session_reservation->save();
            $price_list = $session_reservation->price_list;
            $currency_symbol = Currency::original_symbol($price_list->currency_code);

            $return_data['payment_total'] = html_entity_decode($currency_symbol).$price_list->payment_total;
        }
        else {
            $return_data        = array(
                'status_code'       => '0',
                'success_message'   => $coupon_status->status_message,
            );
        }

        return response()->json($return_data);
    }

    public function remove_coupon(Request $request)
    {
        $rules = array(
            's_key'         => 'required|exists:session_reservations,id',
        );

        $attributes = array(
            's_key'      => 'Session Key',
        );

        $messages = array('required' => ':attribute is required.');
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        $session_reservation = SessionReservation::find($request->s_key);
        $session_reservation->coupon_code = '';
        $session_reservation->save();

        $return_data        = array(
            'status_code'       => '1',
            'success_message'   => __('messages.api.coupon_code_removed'),
        );

        $price_list = $session_reservation->price_list;
        $currency_symbol = Currency::original_symbol($price_list->currency_code);

        $return_data['payment_total'] = html_entity_decode($currency_symbol).$price_list->payment_total;
        return response()->json($return_data);
    }

    /**
     * Get Stripe client_secret For Given Payment Method
     * 
     * @param Get method request inputs
     * @return Response in Json
     */
    public function generate_stripe_key(Request $request)
    {
        $rules = array(
            's_key'         => 'required|exists:session_reservations,id',
            'currency_code'  => 'required|exists:currency,code',
            'amount'        => 'required',
            'payment_method_id' => 'required',
        );

        $attributes = array(
            's_key'  => 'Session Key',
            'currency_code'  => 'Currency Code',
            'amount' => 'Amount',
            'payment_method_id'  => 'Payment Method Id',
        );

        $messages = array('required' => ':attribute is required.');
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->fails()) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
        }

        $session_reservation = SessionReservation::with('space')->find($request->s_key);

        $payment_description = 'Payment For '.$session_reservation->space->name;

        $purchaseData   =   [
            'amount'              => ($request->amount * 100),
            'description'         => $payment_description,
            'currency'            => $request->currency_code,
            'payment_method'      => $request->payment_method_id,
        ];

        $secret_key = view()->shared('stripe_secret_key');
        \Stripe\Stripe::setApiKey($secret_key);
        
        try {
            $payment_intent = \Stripe\PaymentIntent::create($purchaseData);
        }
        catch(\Exception $e) {
            return response()->json([
                'status_code'     => '0',
                'success_message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status_code'     => '1',
            'success_message' => __('messages.api.success'),
            'client_secret'   => $payment_intent->client_secret,
        ]);
    }

    /**
     * Complete Either Request to Book or Instant Book
     * 
     * @param Get method request inputs
     * @return Response in Json
     */
    public function complete_booking(Request $request)
    {
        $rules = array(
            's_key' => 'required|exists:session_reservations,id',
            'booking_type'  => 'required|in:request_book,instant_book',
            'country'  => 'required|exists:country,short_name',
        );

        if($request->booking_type == 'instant_book') {
            $rules['currency_code'] = 'required';
            $rules['pay_key'] = 'required';
            $rules['paymode'] = 'required';
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

        $session_reservation = SessionReservation::find($request->s_key);

        $reservation_data = $session_reservation->only(['space_id','user_id','cancellation','special_offer_id','reservation_id','number_of_guests','booking_date_times','event_type']);

        $space_details = Space::find($reservation_data['space_id']);

        $price_list = $session_reservation->price_list;
        if($price_list->status == 'Not available') {
            return response()->json([
                'status_code'       => '0',
                'success_message'   => $price_list->status_message,
            ]);
        }

        //session and request value are equal or not 
        $reservation_data['country']            = $request->country;
        $reservation_data['host_id']            = $space_details->user_id;
        $reservation_data['price_list']         = $price_list;
        $reservation_data['host_name']          = $space_details->users->first_name;
        $reservation_data['status']             = ($request->booking_type == 'instant_book')  ? 'Accepted' : 'Pending';
        $reservation_data['paymode']            = $request->paymode;

        if($request->booking_type == 'instant_book') {

            if($request->paymode == 'paypal') {
                $reservation_data['transaction_id'] = $this->getPaypalTransactionId($request->pay_key);
            }
            else {
                $validate_stripe = $this->getStripeTransactionId($request->pay_key);
                if($validate_stripe->status) {
                    $reservation_data['transaction_id'] = $validate_stripe->intent_id;
                }
                else {
                    return response()->json([
                        'status_code'     => '0',
                        'success_message' => __('messages.api.payment_not_completed'),
                    ]);                    
                }
            }


            $reservation   = $this->updateReservation($reservation_data,'confirm_booking',$request->s_key);
            $question = removeEmailNumber($request->message);
           
            $message = new Messages;
            $message->space_id       = $reservation_data['space_id'];
            $message->reservation_id = $reservation->id;
            $message->user_to        = $reservation_data['host_id'];
            $message->user_from      = $reservation_data['user_id'];
            $message->message        = $question;
            $message->message_type   = '2';
            $message->read           = '0';
            $message->save();

            $mail_data['id']        = $reservation->id;
            $mail_data['question']  = $question;
            $this->notifyUserViaMail('booking_success', (Object)$mail_data);
        }
        else{
            $reservation   = $this->updateReservation($reservation_data,'inquiry',$request->s_key);
            $question = removeEmailNumber($request->message);
           
            $message = new Messages;
            $message->space_id       = $reservation_data['space_id'];
            $message->reservation_id = $reservation->id;
            $message->user_to        = $reservation_data['host_id'];
            $message->user_from      = $reservation_data['user_id'];
            $message->message        = $question;
            $message->message_type   = '1';
            $message->read           = '0';
            $message->save();

            $mail_data['id']        = $reservation->id;
            $mail_data['question']  = $question;

            $this->notifyUserViaMail('inquiry',(Object)$mail_data);
        }

        return response()->json([
            'status_code'     => '1',
            'success_message' => __('messages.api.booking_successful'),
        ]);
    }
}