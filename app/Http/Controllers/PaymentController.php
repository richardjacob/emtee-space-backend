<?php 

/**
 * Payment Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Payment
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\EmailController;
use App\Models\SpacePrice;
use App\Models\Space;
use App\Models\SpaceRule;
use App\Models\Currency;
use App\Models\Country;
use App\Models\PaymentGateway;
use App\Models\Reservation;
use App\Models\ReservationTimes;
use App\Models\Calendar;
use App\Models\Messages;
use App\Models\Payouts;
use App\Models\CouponCode;
use App\Models\Referrals;
use App\Models\AppliedTravelCredit;
use App\Models\HostPenalty;
use App\Models\SpecialOffer;
use App\Models\Fees;
use App\Http\Helper\PaymentHelper;
use Omnipay\Omnipay;
use Validator;
use App\Http\Start\Helpers;
use App\Repositories\StripePayment;
use DateTime;
use Session;
use Auth;
use DB;
use JWTAuth;



class PaymentController extends Controller 
{
    protected $omnipay; // Global variable for Omnipay instance

    protected $payment_helper; // Global variable for Helpers instance

    /**
     * Constructor to Set PaymentHelper instance in Global variable
     *
     * @param array $payment   Instance of PaymentHelper
     */
    public function __construct(PaymentHelper $payment)
    {
        $this->middleware('auth');
        $this->payment_helper = $payment;

        $paypal_credentials = PaymentGateway::where('site', 'PayPal')->get();
        $this->payment_mode = ($paypal_credentials[3]->value == 'sandbox') ? true : false;
    }

    /**
     * Setup the Omnipay PayPal API credentials
     *
     * @param string $gateway  PayPal Payment Gateway Method as PayPal_Express/PayPal_Pro
     * PayPal_Express for PayPal account payments, PayPal_Pro for CreditCard payments
     */
    public function setup($gateway = 'PayPal_Express')
    {
        // Create the instance of Omnipay
        $this->omnipay  = Omnipay::create($gateway);

        // Get PayPal credentials from payment_gateway table
        $paypal_credentials = PaymentGateway::where('site', 'PayPal')->get();

        $this->omnipay->setUsername($paypal_credentials[0]->value);
        $this->omnipay->setPassword($paypal_credentials[1]->value);
        $this->omnipay->setSignature($paypal_credentials[2]->value);
        $this->omnipay->setTestMode($this->payment_mode);
        $this->omnipay->setLandingPage('Login');
    }

    /**
     * Load Payment view file
     *
     * @param $request  Input values
     * @return payment page view
     */
    public function index(Request $request)
    {
        $mobile_web_auth_user_id = $this->getUserId();
        if(session('get_token')!= '') {
            $user = JWTAuth::toUser(session('get_token'));
            $currency_details = Currency::where('code', $user->currency_code)->first();
            session(['currency_symbol' =>  $currency_details->original_symbol]);
            session(['currency' => $currency_details->code]);
        }

        $s_key = $request->s_key ?: time().$request->id.str_random(4);
        $data   = array();
        $data['user_id']            = $mobile_web_auth_user_id;
        $data['s_key']              = $s_key;
        $data['special_offer_id']   = '';
        $data['special_offer_type'] = '';

        if($request->s_key) {
            $payment = $this->setPaymentData($request,$request->s_key,'s_key');
        }
        else {
            $payment = $this->setPaymentData($request,$s_key, $request->method());
        }

        if(!$payment) {
            return redirect(404);
        }

        $special_offer_id = isset($payment['payment_special_offer_id']) ? $payment['payment_special_offer_id'] : '';
        $reservation_id = isset($payment['payment_reservation_id']) ? $payment['payment_reservation_id'] : '';

        if($special_offer_id != '') {
            $special_offer_data   = SpecialOffer::where('id', $special_offer_id)->where('user_id', $mobile_web_auth_user_id)->first();
            if(!$special_offer_data) {
                $host_name = Space::find($payment['payment_space_id'])->host_name;
                flash_message('danger', trans('messages.inbox.type_removed_by_host',['type'=>trans('messages.inbox.special_offer'),'host_name'=>$host_name]));
                if(\URL::previous()) {
                    return back();
                }
                return redirect()->route('inbox');
            }

            $already_booked = Reservation::where('special_offer_id',$special_offer_id)->where('status','Accepted')->first();

            if($already_booked) {
                flash_message('danger', trans('messages.inbox.already_booked'));
                session()->forget('payment.'.$s_key);
                if(\URL::previous()) {
                    return back();
                }
                return redirect()->route('current_bookings');
            }
            $data['special_offer_id']   = $special_offer_id;
            $data['special_offer_type'] = $special_offer_data->type;
        }
        else if($reservation_id != '') {
            $reservation = Reservation::with('reservation_times')->where('user_id',$mobile_web_auth_user_id)->find($reservation_id);
            if(!$reservation) {
                $status_message = trans('messages.rooms.dates_not_available');
                return $this->checkReturnResponse($status_message,'current_bookings');
            }
            else {
                /* check reservation status is already booked or cancelled
                * if Accepted - redirect user to rooms detail page with dates not available flash
                * if Cancelled - redirect user to search page with your reservation has been cancelled flash
                */
                if($reservation->status == 'Accepted') {
                    flash_message('danger', trans('messages.rooms.dates_not_available'));
                    return redirect()->route('space_details',$reservation->space_id); 
                }
                else if($reservation->status == 'Cancelled' && $reservation->cancelled_by == 'Host') {
                    flash_message('danger', trans('messages.email.sorry_book_some_other_dates'));
                    return redirect()->route('search_page');
                }
                else if($reservation->status == 'Cancelled' && $reservation->cancelled_by == 'Guest') {
                    flash_message('danger', trans('messages.email.sorry_book_some_other_dates_guest'));
                    return redirect()->route('search_page');
                }
            }

            if($request->segment(1) != 'api_payments') {
                $payment = $this->setPaymentData($reservation,$s_key, 'Reservation');
            }
        }

        $data['result'] = $space_details = Space::findOrFail(session('payment')[$s_key]['payment_space_id']);
        $space_rules    = explode(',', $space_details->space_rules);
        $data['space_rules'] = SpaceRule::whereIn('id', $space_rules)->get();

        if(isset(session('payment')[$s_key]['payment_cancellation'])) {
            $cancellation = session('payment')[$s_key]['payment_cancellation'];
        }
        else {
            $cancellation = $space_details->cancellation_policy;
            session(['payment.'.$s_key.'.payment_cancellation' => $cancellation]);
        }
        $data['s_key']            = $s_key;
        $data['space_id']         = session('payment')[$s_key]['payment_space_id'];        
        $data['booking_type']     = session('payment')[$s_key]['payment_booking_type'];
        $data['reservation_id']   = session('payment')[$s_key]['payment_reservation_id'];
        $data['cancellation']     = $cancellation;
        $data['special_offer_id'] = session('payment')[$s_key]['payment_special_offer_id'];
        $data['number_of_guests'] = session('payment')[$s_key]['payment_number_of_guests'];
        $data['booking_date_times'] = session('payment')[$s_key]['booking_date_times'];
        $data['booking_period']   = session('payment')[$s_key]['booking_period'];
        $data['event_type']       = session('payment')[$s_key]['payment_event_type'];

        // Prevent Host booking their own list
        if($data['result']->user_id == $data['user_id']) {
            return redirect()->route('space_details',$data['space_id']);
        }

        $travel_credit_result = Referrals::whereUserId($mobile_web_auth_user_id)->get();
        $travel_credit_friend_result = Referrals::whereFriendId($mobile_web_auth_user_id)->get();
        $travel_credit = 0;

        foreach($travel_credit_result as $row) {
            $travel_credit += $row->credited_amount;
        }

        foreach($travel_credit_friend_result as $row) {
            $travel_credit += $row->friend_credited_amount;
        }

        if($travel_credit && session('remove_coupon') != 'yes' && session('manual_coupon') != 'yes' && ($data['reservation_id']!='' || $data['booking_type'] == 'instant_book')) {
            session(['coupon_code' => 'Travel_Credit']);
            session(['coupon_amount' => $travel_credit]);
        }

        $data['travel_credit']      = $travel_credit;

        $price_data = array('space_id' => $data['space_id'], 'event_type' => $data['event_type'], 'booking_date_times' => $data['booking_date_times'], 'number_of_guests' => $data['number_of_guests'], 'booking_period' => $data['booking_period']);

        $additional_data = array('special_offer_id' => $data['special_offer_id'], 'reservation_id' => $data['reservation_id']);

        $price_list = $this->payment_helper->price_calculation($data['space_id'], (Object)$price_data, (Object)$additional_data);

        $data['price_list'] = json_decode($price_list);

        $pending_reservation_check  = Reservation::where(['space_id' => $data['space_id'],'id' => $data['reservation_id'], 'user_id' => $mobile_web_auth_user_id, 'status' => 'Pending'])->count();

        if($data['price_list']->status == 'Not available' || $pending_reservation_check > 0) {
            flash_message('danger', trans('messages.rooms.dates_not_available'));
            session()->forget('payment.'.$s_key);
            if(\URL::previous() && \URL::full() != \URL::previous()) {
                return back();
            }
            return redirect()->route('space_details',$data['space_id']);
        }

        $data['space_currency_code'] = $space_currency_code = $data['price_list']->original_code;
        $currency_details = Currency::whereCode($space_currency_code)->first();
        $data['currency_symbol'] = $currency_details->symbol;
        $data['currency_code'] = $currency_details->currency_code;
        $data['space_currency'] = $currency_details->original_symbol;
        $data['total_hours'] = $data['price_list']->total_hours;

        session(['payment.'.$s_key.'.payment_price_list' => $data['price_list']]);

        $data['paypal_price'] = currency_convert($space_currency_code, PAYPAL_CURRENCY_CODE, $data['price_list']->total_price);

        $from_currency = Currency::whereCode($space_currency_code)->first();
        $from_rate = optional($from_currency)->rate;
        $to_currency = Currency::whereCode(PAYPAL_CURRENCY_CODE)->first();
        $to_rate = optional($to_currency)->rate;

        $data['paypal_price_rate']  = number_format(($from_rate/$to_rate), 2);

        // Get First Default Currency from currency table
        $data['country']          = Country::all()->pluck('long_name', 'short_name');

        return view('payment.payment', $data);
    }

    /**
     * send Pre Accept Request to Host
     *
     * @param array $request Input values
     * @return redirect to Rooms Detail page
     */
    public function pre_accept(Request $request)
    {
        if(!isset($request->session_key) || $request->session_key =='') {
            return redirect('404');
        }

        $s_key = $request->session_key;

        if(!isset(session('payment')[$s_key])) {
            return redirect(404);
        }

        $mobile_web_auth_user_id = $this->getUserId();

        $country = @session('payment.'.$s_key.'.mobile_payment_counry_code')=='' ? 'US': session('payment.'.$s_key.'.mobile_payment_counry_code');
        $country_data = Country::where('short_name', $country)->first();

        if (!$country_data) {
            $status_message = trans('messages.lys.service_not_available_country');
            return $this->checkReturnResponse($status_message);
        }

        $space_details = Space::with('users')->findOrFail(session('payment')[$s_key]['payment_space_id']);

        $host_user_id = $space_details->user_id;
        $space_id     = $space_details->id;

        // to prevent host book their own list
        if($host_user_id == $mobile_web_auth_user_id) {
            return redirect()->route('space_details',$space_id);
        }

        $price_data = array(
            'space_id' => $space_id,
            'event_type' => json_decode($request->event_type,true),
            'booking_date_times' => json_decode($request->booking_date_times,true),
            'number_of_guests' => $request->number_of_guests,
            'booking_period' => $request->booking_period,
        );

        $additional_data = array(
            'special_offer_id' => $request->special_offer_id,
            'reservation_id' => $request->reservation_id,
        );

        $price_list = $this->payment_helper->price_calculation($space_id, (Object)$price_data, (Object)$additional_data);

        $data['price_list']       = json_decode($price_list);

        if($data['price_list']->status == 'Not available') {
            flash_message('danger', trans('messages.rooms.dates_not_available'));
            return redirect()->route('space_details',$space_id);
        }

        //session and request value are equal or not 
        $reservation_data['space_id']           = $space_id;
        $reservation_data['country']            = $country;
        $reservation_data['host_id']            = $host_user_id;
        $reservation_data['number_of_guests']   = @session('payment')[$s_key]['payment_number_of_guests'];
        $reservation_data['price_list']         = $data['price_list'];
        $reservation_data['cancellation']       = @session('payment')[$s_key]['payment_cancellation'];
        $reservation_data['status']             = 'Pending';
        $reservation_data['host_name']          = $space_details->users->first_name;
        $reservation_data['s_key']              = $s_key;
        $reservation_data['booking_date_times'] = json_decode($request->booking_date_times,true);

        $reservation   = $this->updateReservation($reservation_data,'inquiry',$s_key);

        $question = removeEmailNumber($request->message_to_host);

        $message = new Messages;

        $message->space_id       = $reservation_data['space_id'];
        $message->reservation_id = $reservation->id;
        $message->user_to        = $reservation_data['host_id'];
        $message->user_from      = $mobile_web_auth_user_id;
        $message->message        = $question;
        $message->message_type   = '1';
        $message->read           = '0';

        $message->save();

        $mail_data['id']        = $reservation->id;
        $mail_data['question']  = $question;

        $this->notifyUserViaMail('inquiry',(Object)$mail_data);
        
        if(session('get_token')!='') {
            $result = array('success_message' => 'Request Booking Send to Host','status_code'=>'1');
            return view('json_response.json_response',array('result' => json_encode($result)));
        }

        flash_message('success', trans('messages.rooms.pre-accept_request',['first_name'=> $reservation_data['host_name']]));

        return redirect()->route('current_bookings');
    }

    /**
     * Appy Coupen Code Function
     *
     * @param array $request    Input values
     * @return redirect to Payemnt Page
     */
    public function apply_coupon(Request $request)
    {
        $coupon_code      = $request->coupon_code;
        $s_key            = $request->s_key;
        $result           = CouponCode::where('coupon_code', $coupon_code)->where('status','Active')->get();
        $coupon_status    = "Invalid_coupon";

        if($result->count()) {
            // get user id
            $user_id = @auth()->user()->id;

            // check if coupon already used by the user
            $reservation_result = Reservation::where('user_id', $user_id)->where('coupon_code', $coupon_code)->get();
            if($reservation_result->count()) {
                $data['message']  = trans('messages.payments.coupon_already_used');
                return json_encode($data);
            }

            $datetime1 = getDateObject(date('Y-m-d')); 
            $datetime2 = getDateObject(custom_strtotime($result[0]->expired_at));

            $coupon_status = "Expired_coupon";
            if($datetime1 <= $datetime2) {
                $coupon_status = "Valid_coupon";
            }
        }

        if($coupon_status == "Valid_coupon") {
            $id               = session('payment')[$s_key]['payment_space_id'];
            $price_list       = session('payment')[$s_key]['payment_price_list'];
            $code             = session('currency');

            $data['coupon_amount']  = currency_convert($result[0]->currency_code,$code,$result[0]->amount);
            $coupon_applied_total = ($price_list->subtotal + $price_list->service_fee ) - $data['coupon_amount'];
            $data['coupen_applied_total']  = $coupon_applied_total > 0 ? $coupon_applied_total: 0;
            $this->forgetCoupon();
            session(['coupon_code' => $coupon_code]);
            session(['coupon_amount' => $data['coupon_amount']]);
            session(['manual_coupon' => 'yes']);
        }
        else {
            $data['message']  = trans('messages.payments.invalid_coupon');
            if($coupon_status == "Expired_coupon") {
                $data['message']  = trans('messages.payments.expired_coupon');  
            }
        }

        return json_encode($data);
    }

    public function remove_coupon(Request $request)
    {
        $this->forgetCoupon();
        session(['remove_coupon' => 'yes']);
    }

    /**
     * Payment Submit Function
     *
     * @param array $request    Input values
     * @return redirect to Dashboard Page
     */

    public function create_booking(Request $request)
    {
        if(!isset($request->session_key) || $request->session_key == '' || !isset(session('payment')[$request->session_key])) {
            return redirect('404');
        }

        $s_key = $request->session_key;
        $mobile_web_auth_user_id = $this->getUserId();
        
        $space_details = Space::findOrFail(session('payment')[$s_key]['payment_space_id']);

        // to prevent host book their own list
        if($space_details->user_id == $mobile_web_auth_user_id) {
            return redirect('rooms/'.session('payment')[$s_key]['payment_space_id']);
        }

        $reservation_id = @session('payment')[$s_key]['payment_reservation_id'];
        $space_id       = $request->space_id;        

        $price_data = array(
            'space_id' => $space_id,
            'event_type' => json_decode($request->event_type,true),
            'booking_date_times' => json_decode($request->booking_date_times,true),
            'number_of_guests' => $request->number_of_guests,
            'booking_period' => $request->booking_period,
        );

        $additional_data = array(
            'special_offer_id' => $request->special_offer_id,
            'reservation_id' => $request->reservation_id,
        );

        $price_list = $this->payment_helper->price_calculation($space_id, (Object)$price_data, (Object)$additional_data);

        $price_list = json_decode($price_list);

        if($price_list->status == 'Not available') {
            flash_message('danger', trans('messages.rooms.dates_not_available'));
            return redirect()->route('current_bookings');
        }

        $amount         = currency_convert($request->currency, PAYPAL_CURRENCY_CODE, $price_list->payment_total);

        $country = $request->payment_country;
        $country_data = Country::where('short_name', $country)->first();

        if (!$country_data && $price_list->coupon_code != 'Travel_Credit') {
            $status_message = trans('messages.lys.service_not_available_country');
            return $this->checkReturnResponse($status_message);
        }

        $message_to_host    = removeEmailNumber($request->message_to_host);
        $space_id           = session('payment')[$s_key]['payment_space_id'];
        $booking_date_times = session('payment')[$s_key]['booking_date_times'];
        $event_type         = session('payment')[$s_key]['payment_event_type'];
        $number_of_guests   = session('payment')[$s_key]['payment_number_of_guests'];
        $reservation_id     = @session('payment')[$s_key]['payment_reservation_id'];
        $payment_description= $space_details->name.' '.$price_list->display_name;

        $purchaseData   = array(
            'testMode'  => $this->payment_mode,
            'amount'    => $amount,
            'description' => $payment_description,
            'currency'  => PAYPAL_CURRENCY_CODE,
            'returnUrl' => url('payments/success?s_key='.$s_key),
            'cancelUrl' => url('payments/cancel?s_key='.$s_key),
        );

        //mobile redirect
        if(session('get_token') != '') {
            $purchaseData['returnUrl'] = url('api_payments/success?s_key='.$s_key);
            $purchaseData['cancelUrl'] = url('api_payments/cancel?s_key='.$s_key);
        }

        session(['payment.'.$s_key.'.amount' => $amount]);
        session(['payment.'.$s_key.'.payment_country' => $country]);
        session(['payment.'.$s_key.'.message_to_host_'.$mobile_web_auth_user_id => $message_to_host]);
        session()->save();

        if(session('payment.'.$s_key.'.payment_card_type') != '') {
            $payment_mode = ($request->payment_type =='cc') ? 'Credit Card' : 'PayPal';
            session(['payment.'.$s_key.'.payment_card_type' => $payment_mode]);
        }

        if($request->payment_type =='cc') {

            $rules = [
                'cc_number'        => 'required|numeric|digits_between:12,20|validateluhn',
                'cc_expire_month'  => 'required|expires:cc_expire_month,cc_expire_year',
                'cc_expire_year'   => 'required|expires:cc_expire_month,cc_expire_year',
                'cc_security_code' => 'required|numeric|digits_between:0,4',
                'first_name'       => 'required',
                'last_name'        => 'required',
                'zip'              => 'required',
            ];

            $niceNames = [
                'cc_number'        => 'Card number',
                'cc_expire_month'  => 'Expires',
                'cc_expire_year'   => 'Expires',
                'cc_security_code' => 'Security code',
                'first_name'       => 'First name',
                'last_name'        => 'Last name',
                'zip'              => 'Postal code',
            ];

            $messages = [
                'expires'      => 'Card has expired',
                'validateluhn' => 'Card number is invalid'
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $validator->setAttributeNames($niceNames);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }

            $purchaseData   =   [
                'amount'              => ($amount * 100),
                'description'         => $payment_description,
                'currency'            => PAYPAL_CURRENCY_CODE,
                'confirmation_method' => 'manual',
                'confirm'             => true,
            ];

            $card = [
                'firstName'       => $request->first_name,
                'lastName'        => $request->last_name,
                'number'          => $request->cc_number, 
                'expiryMonth'     => $request->cc_expire_month, 
                'expiryYear'      => $request->cc_expire_year, 
                'cvv'             => $request->cc_security_code, 
                'billingAddress1' => $request->payment_country,
                'billingCountry'  => $request->payment_country,
                'billingCity'     => $request->payment_country,
                'billingPostcode' => $request->zip,
                'billingState'    => $request->payment_country
            ];

            $stripe_card =  array(
                "number"    => $request->cc_number,
                "exp_month" => $request->cc_expire_month,
                "exp_year"  => $request->cc_expire_year,
                "cvc"       => $request->cc_security_code,
            );

            $stripe_payment = new StripePayment();
        }
        else {
            $this->setup();
        }

        $data = [
            'space_id'         => $request->space_id,
            'host_id'          => $space_details->user_id,
            'reservation_id'   => $reservation_id,
            'booking_date_times'=> json_decode($request->booking_date_times,true),
            'event_type'       => json_decode($request->event_type,true),
            'number_of_guests' => $request->number_of_guests,
            'transaction_id'   => '',
            'price_list'       => $price_list,
            'paymode'          => 'Credit Card',
            'cancellation'     => $request->cancellation,
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'postal_code'      => $request->zip,
            'country'          => $request->payment_country,
            'message_to_host'  => session('payment')[$s_key]['message_to_host_'.$mobile_web_auth_user_id],
            's_key'            => $s_key,
            'status'           => (@session('payment')[$s_key]['payment_booking_type'] == 'instant_book') ? 'Accepted' : 'Pending',
        ];

        if($amount > 0) {

            if($request->payment_type =='cc') {
                if($request->payment_intent_id != '') {
                    $stripe_response = $stripe_payment->CompletePayment($request->payment_intent_id);
                }
                else {
                    $payment_method = $stripe_payment->createPaymentMethod($stripe_card);
                    if($payment_method->status != 'success') {
                        flash_message('danger', $payment_method->status_message);
                        return back();
                    }
                    $purchaseData['payment_method'] = $payment_method->payment_method_id;
                    $stripe_response = $stripe_payment->CreatePayment($purchaseData);
                }
                if($stripe_response->status == 'success') {

                    $data['transaction_id'] = $stripe_response->transaction_id;

                    $code = $this->completeReservation($data);

                    if(session('get_token')!='') {
                        $result = array('success_message'=>'Payment Successfully Paid','status_code'=>'1');
                        return view('json_response.json_response',array('result' =>json_encode($result)));
                    }

                    flash_message('success', trans('messages.payments.payment_success'));
                    return redirect()->route('reservation_requested',['code' => $code]);
                }
                else if($stripe_response->status == 'requires_action') {
                    session(['payment.'.$s_key.'.payment_intent_client_secret' => $stripe_response->payment_intent_client_secret]);
                    return redirect()->route('payment.home',['id' => $request->space_id, 's_key' => $s_key])->withInput();
                }
                else {
                    session(['s_key' => $s_key]);
                    //payment failed
                    if(session('get_token')!='') {
                        $result=array('success_message'=>'Payment Failed','status_code'=>'0','error', $stripe_response->status_message);
                        return view('json_response.json_response',array('result' =>json_encode($result)));  
                    } 
                    flash_message('danger', $stripe_response->status_message);
                    return redirect()->route('payment.home',['id' => $request->space_id, 's_key' => $s_key]);
                }
            }
            else {
                try {
                    $response = $this->omnipay->purchase($purchaseData)->send();
                }
                catch(\Exception $e) {
                    flash_message('danger', $e->getMessage());
                    return redirect('payments/book/'.$request->space_id.'?s_key='.$s_key);
                }
                // Process response
                if ($response->isSuccessful()) {
                    // Payment was successful 
                    $result = $response->getData();

                    $data['transaction_id'] = @$result['TRANSACTIONID'];

                    $code = $this->completeReservation($data);

                    if(session('get_token')!='') {
                        $result = array('success_message'=>'Payment Successfully Paid','status_code'=>'1');
                        return view('json_response.json_response',array('result' =>json_encode($result)));
                    }

                    flash_message('success', trans('messages.payments.payment_success'));
                    return redirect()->route('reservation_requested',['code' => $code]);
                }
                elseif ($response->isRedirect()) {
                    // Redirect to offsite payment gateway
                    return $response->redirect();
                }
                else {
                    session(['s_key' => $s_key]);
                    //payment failed
                    if(session('get_token')!='') {
                        $result=array('success_message'=>'Payment Failed','status_code'=>'0','error', $response->getMessage());
                        return view('json_response.json_response',array('result' =>json_encode($result)));  
                    } 
                    flash_message('danger', $response->getMessage());
                    return redirect()->route('payment.home',['id' => $request->space_id, 's_key' => $s_key]);
                }
            }
        }

        $code = $this->completeReservation($data);

        if(session('get_token') != '') {
            $result = array('success_message'=>'Payment Successfully Paid','status_code'=>'1');
            return view('json_response.json_response', array('result' => json_encode($result)));
        }
        flash_message('success', trans('messages.payments.payment_success'));
        return redirect()->route('reservation_requested',['code' => $code]);
    }

    /**
     * Callback function for Payment Success
     *
     * @param array $request    Input values
     * @return redirect to Payment Success Page
     */
    public function success(Request $request)
    {
        if(!@session('payment')[$request->s_key]) {
            if(session('get_token') != '') {
                $result=array('success_message'=>'Payment Successfully Paid','status_code'=>'1');
                return view('json_response.json_response',array('result' =>json_encode($result)));
            }

            return redirect()->route('current_bookings');
        }
        $s_key = $request->s_key;
        $mobile_web_auth_user_id = $this->getUserId();

        $this->setup();

        $transaction = $this->omnipay->completePurchase(array(
            'payer_id'              => $request->PayerID,
            'transactionReference'  => $request->token,
            'amount'                => session('payment')[$s_key]['amount'],
            'currency'              => PAYPAL_CURRENCY_CODE
        ));

        try {
            $response = $transaction->send();
        }
        catch(\Exception $e) {
            flash_message('danger', @$e->getMessage());
            return redirect('payments/book?s_key='.$s_key);
        }

        $result = $response->getData();

        if(@$result['ACK'] == 'Success') {
            $data = [
                'space_id'         => session('payment')[$s_key]['payment_space_id'],
                'reservation_id'   => session('payment')[$s_key]['payment_reservation_id'],
                'booking_date_times'=> session('payment')[$s_key]['booking_date_times'],
                'event_type'       => session('payment')[$s_key]['payment_event_type'],
                'number_of_guests' => session('payment')[$s_key]['payment_number_of_guests'],
                'transaction_id'   => @$result['PAYMENTINFO_0_TRANSACTIONID'],
                'price_list'       => session('payment')[$s_key]['payment_price_list'],
                'paymode'          => 'PayPal',
                'country'          => session('payment')[$s_key]['payment_country'],
                'cancellation'     => session('payment')[$s_key]['payment_cancellation'],
                'message_to_host'  => session('payment')[$s_key]['message_to_host_'.$mobile_web_auth_user_id],
                's_key'            => $s_key,
                'status'           => (@session('payment')[$s_key]['payment_booking_type'] == 'instant_book') ? 'Accepted' : 'Pending',
            ];

            $space_id = $data['space_id'];
            $space_details = Space::findOrFail($space_id);
            $data['host_id'] = $space_details->user_id;

            $count_reservation = 0;
            if($count_reservation > 0) {
                // Refund To User for Same Time Booking
                $refund = $this->omnipay->refund(array(
                    'payer_id'              => $request->PayerID,
                    'transactionReference'  => $data['transaction_id'],
                    'amount'                => session('payment')[$s_key]['amount'],
                    'currency'              => PAYPAL_CURRENCY_CODE
                ));
                $response = $refund->send();

                $refundresult = $response->getData();

                $data['status'] = 'Declined';
                $code = $this->declineReservation($data);

                $return_message = trans('messages.payments.refundpayment_cancel');
                if(@$refundresult['ACK'] == 'Success') {
                    $return_message = trans('messages.payments.refundpayment');
                }

                //mobile changes
                if(session('get_token')!='') {
                    $result=array(
                        'success_message'=>'Payment Failed',
                        'status_code'=>'0',
                        'error'=> trans('messages.payments.refundpayment')
                    );
                    return view('json_response.json_response',array('result' =>json_encode($result)));
                } 
                flash_message('danger', $return_message);
                return redirect()->route('current_bookings');
            }

            //mobile changes
            $code = $this->completeReservation($data);

            if(session('get_token')!='') {
                $result=array('success_message'=>'Payment Successfully Paid','status_code'=>'1');
                return view('json_response.json_response',array('result' =>json_encode($result)));
            }
            //end mobile changes

            flash_message('success', trans('messages.payments.payment_success')); 
            return redirect('reservation/requested?code='.$code);
        }
        session(['s_key' => $s_key]);

        //mobile changes
        if(session('get_token')!='') {
            if($result['L_SHORTMESSAGE0']=='Duplicate Request') {
                $result = array('success_message'=>'Payment Successfully Paid','status_code'=>'1');
            }
            else {
                $result = array(
                    'success_message'=>'Payment Failed',
                    'status_code'=>'0',
                    'error'=>$result['L_LONGMESSAGE0']
                );
            }
            return view('json_response.json_response',array('result' =>json_encode($result)));
        }

        // Payment failed
        flash_message('danger', $result['L_SHORTMESSAGE0']); // Call flash message function
        return redirect('payments/book/'.session('payment')[$s_key]['payment_space_id'].'?s_key='.$s_key);
    }

    /**
     * Callback function for Payment Failed
     *
     * @param array $request    Input values
     * @return redirect to Payments Booking Page
     */
    public function cancel(Request $request)
    {
        $s_key = $request->s_key;
        $redirect_to = '404';
        session(['s_key' => $s_key]);

        // Payment failed
        if(session('get_token')!='') {
            $result=array(
                'success_message' => 'The payment process was cancelled.',
                'status_code'    => '0'
            );
            return view('json_response.json_response',array('result' =>json_encode($result)));
        }

        if(isset(session('payment')[$s_key])) {
            $redirect_to = 'payments/book/'.session('payment')[$s_key]['payment_space_id'].'?s_key='.$s_key;
        }

        flash_message('danger', trans('messages.payments.payment_cancelled'));

        return redirect($redirect_to);
    }

    /**
     * Contact Request send to Host
     *
     * @param array $request Input values
     * @return redirect to Rooms Detail page
     */
    public function contact_request(Request $request)
    {
        $space_id = $request->id;
        $price_data = $request->only('event_type','booking_date_times','number_of_guests','booking_period');
        $price_data['event_type'] = json_decode($price_data['event_type'],true);
        $price_data['booking_date_times'] = json_decode($price_data['booking_date_times'],true);

        $price_list = $this->payment_helper->price_calculation($space_id, (Object)$price_data);
        $price_list = json_decode($price_list);

        if($price_list->status == 'Not available') {
            $status_message = $price_list->status_message;
            flash_message('danger', $status_message);
            return redirect()->route('space_details',Arr::wrap($space_id));
        }

        $space_details = Space::findOrFail($space_id);

        $reservation_data['space_id']           = $space_id;
        $reservation_data['country']            = 'US';
        $reservation_data['host_id']            = $space_details->user_id;
        $reservation_data['number_of_guests']   = $request->number_of_guests;
        $reservation_data['cancellation']       = $space_details->cancellation_policy;
        $reservation_data['price_list']         = $price_list;
        $reservation_data['host_name']          = $space_details->users->first_name;
        $reservation_data['booking_date_times'] = $price_data['booking_date_times'];

        $reservation   = $this->updateReservation($reservation_data,'contact_host');

        $question = removeEmailNumber($request->question);

        $message = new Messages;
        $message->space_id       = $space_id;
        $message->reservation_id = $reservation->id;
        $message->user_to        = $space_details->user_id;
        $message->user_from      = auth()->id();
        $message->message        = $question;
        $message->message_type   = 9;
        $message->read           = 0;
        $message->save();

        $mail_data['id']        = $reservation->id;
        $mail_data['question']  = $question;

        $this->notifyUserViaMail('inquiry',(Object)$mail_data);

        flash_message('success', __('messages.rooms.contact_request_has_sent',['first_name'=> $space_details->users->first_name ]));
        return redirect()->route('space_details',Arr::wrap($space_id));
    }

    /**
     * Create Reservation After paypal refund Done when same time booking
     *
     * @param array $data    Payment Data
     * @return string $code  Reservation Code
     */
    protected function declineReservation($data)
    {
        $s_key          = $data['s_key'];
        $reservation_id = session('payment')[$s_key]['payment_reservation_id'] ?? '';

        $reservation = $this->updateReservation($data,'decline',$s_key);

        $messages = new Messages;
        $messages->space_id       = $reservation->space_id;
        $messages->reservation_id = $reservation->id;
        $messages->user_to        = auth()->user()->id;
        $messages->user_from      = $reservation->host_id;
        $messages->message        = '';
        $messages->message_type   = 10;

        $messages->save();

        $this->forgetCoupon();
        session()->forget('s_key');
        session()->forget('payment.'.$s_key);

        return true;
    }

    /**
     * Create Reservation After Payment Successfully Done
     *
     * @param array $data    Payment Data
     * @return string $code  Reservation Code
     */
    protected function completeReservation($data)
    {
        $reservation = $this->updateReservation($data,'confirm_booking',$data['s_key']);

        $message = new Messages;
        $message->space_id       = $data['space_id'];
        $message->reservation_id = $reservation->id;
        $message->user_to        = $reservation->host_id;
        $message->user_from      = $reservation->user_id;
        $message->message        = @$data['message_to_host'];
        $message->message_type   = 2;
        $message->read           = '0';

        $message->save();

        $mail_data['id']        = $reservation->id;
        $this->notifyUserViaMail('booking_success', (Object)$mail_data);

        $this->forgetCoupon();
        session()->forget('s_key');
        session()->forget('payment.'.$data['s_key']);

        return $reservation->code;
    }

    protected function getUserId()
    {
        if(session('get_token')!='') {
            $user = JWTAuth::toUser(session('get_token'));
            $user_id = $user->id; 
        }
        else {
            $user_id = @auth()->user()->id; 
        }
        return $user_id;
    }

    protected function checkReturnResponse($status_message, $target_route = '')
    {
        if(session('get_token') == '') {
            flash_message('danger', $status_message);
            if($target_route != '') {
                return redirect()->route($target_route);
            }
            return back();
        }
        else {
            return response()->json(['success_message'=> $status_message, 'status_code'=>'0']);
        }
    }

    protected function setPaymentData($request_data, $s_key, $type)
    {
        if($type == 's_key') {
            $payment = session('payment.'.$s_key);
        }
        else {
            $payment = array(
                'payment_space_id'          => $request_data->space_id,
                'payment_cancellation'      => $request_data->cancellation,
                'payment_number_of_guests'  => $request_data->number_of_guests,
                'payment_special_offer_id'  => $request_data->special_offer_id,
                'payment_reservation_id'    => $request_data->reservation_id,
            );

            if($request_data->reservation_id) {
                return $payment;
            }

            if($type == 'POST') {
                $payment['payment_booking_type']= $request_data->booking_type;
                $payment['booking_date_times']  = json_decode($request_data->booking_date_times,true);
                $payment['booking_period']      = $request_data->booking_period;
                $payment['payment_event_type']  = json_decode($request_data->event_type,true);
            }
            if($type == 'GET') {
                $special_offer_id = $request_data->special_offer_id;                
                $special_offer = SpecialOffer::find($special_offer_id);

                $booking_date_times = array();
                $booking_times = optional($special_offer)->special_offer_times;
                
                if(isset($booking_times)) {
                    $booking_date_times['id']           = $booking_times->id;
                    $booking_date_times['start_date']   = $booking_times->checkin_formatted;
                    $booking_date_times['formatted_start_date'] = $booking_times->start_date;
                    $booking_date_times['end_date']     = $booking_times->checkout_formatted;
                    $booking_date_times['formatted_end_date'] = $booking_times->end_date;
                    $booking_date_times['week_day']     = date('w',strtotime($booking_times->start_date));
                    $booking_date_times['start_time']   = $booking_times->start_time;
                    $booking_date_times['end_time']     = $booking_times->end_time;
                }

                $payment['payment_booking_type']    = 'instant_book';
                $payment['booking_period']          = $request_data->booking_period;
                $payment['payment_special_offer_id']= $special_offer_id;
                $payment['payment_event_type']      = ['activity_type' => $special_offer->activity_type, 'activity' => $special_offer->activity, 'sub_activity' => $special_offer->sub_activity];
                $payment['booking_date_times']      = $booking_date_times;
            }
            if($type == 'Reservation') {
                $prev_session_data = session('payment.'.$s_key) ?? array();
                $booking_date_times = array();
                $booking_times = $request_data->reservation_times;

                $booking_date_times['id'] = $booking_times->id;
                $booking_date_times['start_date'] = $booking_times->checkin_formatted;
                $booking_date_times['formatted_start_date'] = $booking_times->start_date;
                $booking_date_times['end_date'] = $booking_times->checkout_formatted;
                $booking_date_times['formatted_end_date'] = $booking_times->end_date;
                $booking_date_times['week_day'] = date('w',strtotime($booking_times->start_date));
                $booking_date_times['start_time'] = $booking_times->start_time;
                $booking_date_times['end_time'] = $booking_times->end_time;

                $event_type = ['activity_type' => $request_data->activity_type, 'activity' => $request_data->activity, 'sub_activity' => $request_data->sub_activity];
                $payment['payment_reservation_id']  = $request_data->id;
                $payment['payment_booking_type']    = 'instant_book';
                $payment['payment_card_type']       = $request_data->paymode;
                $payment['booking_date_times']      = $booking_date_times;
                $payment['booking_period']          = ($booking_times->start_date == $booking_times->end_date) ? 'Single' : 'Multiple';
                $payment['payment_event_type']      = $event_type;
                // Merge with previous session data to re store stripe intent key
                $payment = array_merge($prev_session_data,$payment);
            }
            session(['payment.'.$s_key => $payment]);
        }

        return $payment;
    }

    /**
     * To store the payment details in reservation
     * 
     * @return App\Models\Reservation $reservation Created reservation 
     */
    protected function updateReservation($reservation_data, $type, $s_key = '')
    {
        $mobile_web_auth_user_id = $this->getUserId();
        $host_penalty            = Fees::find(3)->value;

        $price_list     = $reservation_data['price_list'];
        $reservation_id = isset($reservation_data['reservation_id']) ? $reservation_data['reservation_id'] : '';
        $space_id       = $reservation_data['space_id'];

        $reservation = Reservation::findOrNew($reservation_id);

        if($type == 'confirm_booking') {
            // $this->updateCalendar($space_id,$reservation_data['booking_date_times']);
        }

        $reservation->space_id         = $space_id;
        $reservation->host_id          = $reservation_data['host_id'];
        $reservation->user_id          = $mobile_web_auth_user_id;
        $reservation->number_of_guests = $reservation_data['number_of_guests'];        
        $reservation->activity_type    = $price_list->activity_type;
        $reservation->activity         = $price_list->activity;
        $reservation->sub_activity     = $price_list->sub_activity;
        $reservation->hours            = $price_list->count_total_hour;
        $reservation->days            = $price_list->count_total_days;
        $reservation->weeks            = $price_list->count_total_week;
        $reservation->months            = $price_list->count_total_month;
        $reservation->per_hour         = $price_list->hour_amount;
        $reservation->per_day         = $price_list->full_day_amount;
        $reservation->per_week         = $price_list->weekly_amount;
        $reservation->per_month         = $price_list->monthly_amount;
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
        $reservation->paymode          = @session('payment.'.$s_key.'.payment_card_type');
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
                $reservation->coupon_amount = $coupon_amount = $price_list->coupon_amount;
            }
            if(@session('payment')[$s_key]['payment_special_offer_id']) {
                $reservation->special_offer_id = session('payment')[$s_key]['payment_special_offer_id'];
            }

            $reservation->transaction_id    = $reservation_data['transaction_id'];
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
                $this->applyTravelCredit($mobile_web_auth_user_id, $price_list->coupon_amount,$reservation->id,$price_list->currency_code);
            }
        }

        if($reservation->status == 'Accepted') {
            $host_payout_amount  = $reservation->host_payout;
            $this->payment_helper->payout_refund_processing($reservation, 0, $host_payout_amount);
        }

        session()->forget('s_key');
        session()->forget('payment.'.@$reservation_data['s_key']);

        return $reservation;
    }

    protected function updateCalendar($space_id,$booking_date_times)
    {
        foreach($booking_date_times as $booking_date_time) {
            //
        }
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

    // For coupon code destroy
    protected function forgetCoupon()
    {
        session()->forget('coupon_code');
        session()->forget('coupon_amount');
        session()->forget('remove_coupon');
        session()->forget('manual_coupon');
    }
}