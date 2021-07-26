<?php

/**
 * Email Controller
 *
 * @package     Makent
 * @subpackage  Controller
 * @category    Email
 * @author      Trioangle Product Team
 * @version     1.6
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mail;
use Config;
use Auth;
use DateTime;
use DateTimeZone;
use App\Models\PasswordResets;
use App\Models\User;
use App\Models\Space;
use App\Models\Disputes;
use App\Models\DisputeMessages;
use App\Models\Reservation;
use App\Models\SiteSettings;
use App\Models\PayoutPreferences;
use App\Models\ReferralSettings;
use App\Models\Currency;
use App\Models\Reviews;
use App\Models\Admin;
use App\Models\Language;
use App;
use JWTAuth;

use App\Mail\MailQueue;

class EmailController extends Controller
{
    /**
     * Send Welcome Mail to Users with Confirmation Link
     *
     * @param array $user  User Details
     * @return true
     */
    public function welcome_email_confirmation($user)
    {
        $data['first_name'] = $user->first_name;
        $data['email'] = $user->email;
        $data['token'] = str_random(100); // Generate random string values - limit 100
        $data['type'] = 'welcome';
        $data['url'] = url('/').'/';
        $data['locale'] = $user->user_email_language;

        $password_resets = new PasswordResets;

        $password_resets->email      = $user->email;
        $password_resets->token      = $data['token'];
        $password_resets->created_at = date('Y-m-d H:i:s');

        $password_resets->save();
        
        $data['subject'] = trans('messages.email.confirm_email_address',[],$data['locale']);
        $data['view_file'] = 'emails.email_confirm';

        // return view($data['view_file'],$data);
        Mail::to($data['email'], $data['first_name'])->queue(new MailQueue($data));
        return;
    }

    /**
     * Send Welcome Mail to Users with Confirmation Link
     *
     * @param array $user  User Details
     * @return true
     */
    public function contact_email_confirmation($user_contact)
    {
        $admin = Admin::first();
        $data['admin_email'] = $admin->email;
        $data['admin_name'] =  'Admin';
        
        $data['contact_name'] = $user_contact->name;
        $data['contact_email'] = $user_contact->email;
        $data['contact_feedback'] = $user_contact->feedback;
        $data['url'] = url('/').'/';
        $data['locale']       = Language::where('default_language',1)->first()->value;

        $data['subject'] = trans('messages.email.contact_us_email');
        $data['view_file'] = 'emails.email_contact';
        
        // return view($data['view_file'],$data);
        Mail::to($data['admin_email'], $data['admin_name'])->queue(new MailQueue($data));
        return;
    }

    /**
     * Send Forgot Password Mail with Confirmation Link
     *
     * @param array $user  User Details
     * @return true
     */
    public function forgot_password($user)
    {  
        $data['first_name'] = $user->first_name;

        $data['token'] = str_random(100); // Generate random string values - limit 100
        $data['url'] = url('/').'/';
        $data['locale'] = $user->user_email_language;

        $password_resets = new PasswordResets;

        $password_resets->email      = $user->email;
        $password_resets->token      = $data['token'];
        $password_resets->created_at = date('Y-m-d H:i:s');
        
        $password_resets->save();

        $data['subject'] = trans('messages.email.reset_your_pass',[], $data['locale']);
        $data['view_file'] = 'emails.forgot_password';
        
        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Send Email Change Mail with Confirmation Link
     *
     * @param array $user  User Details
     * @return true
     */
    public function change_email_confirmation($user)
    {
        $data['first_name'] = $user->first_name;
        $data['token'] = str_random(100); // Generate random string values - limit 100
        $data['type'] = 'change';
        $data['url'] = url('/').'/';
        $data['locale'] = $user->user_email_language;

        $password_resets = new PasswordResets;

        $password_resets->email      = $user->email;
        $password_resets->token      = $data['token'];
        $password_resets->created_at = date('Y-m-d H:i:s');

        $password_resets->save();

        $data['subject']=trans('messages.email.confirm_email_address',[],$data['locale']);
        $data['view_file'] = 'emails.email_confirm';

        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Send New Email Change Mail with Confirmation Link
     *
     * @param array $user  User Details
     * @return true
     */
    public function new_email_confirmation($user)
    {
        $data['first_name'] = $user->first_name;
        $data['token'] = str_random(100); // Generate random string values - limit 100
        $data['type'] = 'confirm';
        $data['url'] = url('/').'/';
        $data['locale'] = $user->user_email_language;

        $password_resets = new PasswordResets;

        $password_resets->email      = $user->email;
        $password_resets->token      = $data['token'];
        $password_resets->created_at = date('Y-m-d H:i:s');

        $password_resets->save();

        $data['subject']=trans('messages.email.confirm_email_address',[],$data['locale']);
        $data['view_file'] = 'emails.email_confirm';

        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Send Inquiry Mail to Host
     *
     * @param array $reservation_id Contact Request Details
     * @return true
     */
    public function inquiry($reservation_id , $question )
    {
        $data['question'] = $question;
        $data['url'] = url('/').'/';

        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency')->find($reservation_id);
        $user           = $reservation->host_users;
        $data['locale'] = $user->user_email_language;

        $data['result'] = $reservation->toArray();
        $data['subject'] = trans('messages.email.inquiry_at',[],$data['locale']).' '.$data['result']['space']['name'].' '.trans('messages.email.for',[],$data['locale']).' '.$data['result']['dates_subject'];
        $data['view_file'] = 'emails.inquiry';

        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Send Booking Mail to Host
     *
     * @param array $reservation_id Request Details
     * @return true
     */
    public function booking($reservation_id)
    {
        $data['hide_header'] = true;
        $data['url'] = url('/').'/';

        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency')->find($reservation_id);
        $user           = $reservation->host_users;
        $data['locale'] = $user->user_email_language;

        $data['result'] = Reservation::where('reservation.id', $reservation_id)->with(['users' => function($query) {
                $query->with('profile_picture')->with('users_verification')->with('reviews');
            }, 'space', 'host_users' => function($query) {
                $query->with('profile_picture')->with('users_verification')->with('reviews');
            }, 'currency', 'messages']);

        $data['result'] = $data['result']->first()->toArray();

        $data['subject'] = trans('messages.email.booking_inquiry_for',[],$data['locale']).' '.$data['result']['space']['name'].' '.trans('messages.email.for',[],$data['locale']).' '.$data['result']['dates_subject'];

        $data['view_file'] = 'emails.booking';

        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Send itinerary Mail to Host
     *
     * @param string $reservation_id Reservation Id
     * @param string $email Friend Email
     * @return true
     */
    public function itinerary($reservation_id , $email )
    {
        $data['hide_header']= true;
        $data['url']        = url('/').'/';
        $data['email']      = $email;
        $data['map_key']    = view()->shared('map_key');

        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency')->find($reservation_id);

        $user           = $reservation->users;
        $data['locale'] = $user->user_email_language;
        
        $data['contact'] = SiteSettings::where('name','support_number')->first()->value;

        $data['result'] = $reservation->toArray();
        $data['subject'] = trans('messages.email.an_itinerary_shared',[],$data['locale']);

        $data['view_file'] = 'emails.itinerary';

        // return view($data['view_file'],$data);
        Mail::to($data['email'], $data['subject'])->queue(new MailQueue($data));
        return;
    }

    /**
     * Send preapproval Mail to Host
     *
     * @param array $reservation_id Reservation Id
     * @param string $preapproval_message Message from Host when pre-approving
     * @param type for Checking Pre-approval or Special-Offer
     * @return true
     */
    public function preapproval($reservation_id, $preapproval_message, $type = 'pre-approval')
    {
        $data['result']              = Reservation::find($reservation_id);
        $user                        = $data['result']->users;
        $data['first_name']          = $user->first_name;
        $data['preapproval_message'] = $preapproval_message;
        $data['type']                = $type;
        $data['url'] = url('/').'/';
        $data['locale'] = $user->user_email_language;

        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency')->with(['special_offer' => function($query) {
                $query->orderby('id','desc')->limit(1)->with('space');
            }])->find($reservation_id);
        
        if($type == 'pre-approval') {
            $subject = $reservation->host_users->first_name.' '.__('messages.email.reservation_itinerary_from').' ';
            if(isset($reservation->special_offer)) {
                $subject .= $reservation->special_offer->space->name." for ".$reservation->special_offer->dates_subject;
            }
            else {
                $subject .= $reservation->space->name." for ".$reservation->dates_subject;
            }
        }
        else if($type == 'special_offer') {
            $subject = $reservation->host_users->first_name.' '.__('messages.email.sent_Special_Offer_for').' '.$reservation->special_offer->space->name." for ".$reservation->special_offer->dates_subject;
        }

        $data['result'] = $reservation->toArray();
        $data['subject'] = $subject;
        $data['view_file'] = 'emails.preapproval';

        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Send Listed Mail to Host
     *
     * @param array $space_id Room Details
     * @return true
     */
    public function listed($space_id)
    {
        $result               = Space::find($space_id);
        $user                 = $result->users;
        $data['first_name']   = $user->first_name;
        $data['space_name']   = $result->name;
        $data['created_time'] = $result->created_time;
        $data['space_id']     = $result->id;
        $data['link']         = $result->link;
        $data['edit_calendar_link'] = route('manage_space',['space_id' => $space_id, 'page' => 'ready_to_host', 'step_num' => 5]);
        $data['url']          = url('/').'/';
        $data['locale'] = $user->user_email_language;
        $data['subject'] = trans('messages.email.your_space_listed',[],$data['locale']).' '.SITE_NAME;

        $data['view_file'] = 'emails.listed';

        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Send Unlisted Mail to Host
     *
     * @param array $space_id Room Details
     * @return true
     */
    public function unlisted($space_id)
    {
        $result = Space::find($space_id);
        $user = $result->users;
        $data['first_name'] = $user->first_name;
        $data['created_time'] = $result->created_time;
        $data['space_id'] = $result->id;
        $data['url'] = url('/').'/';
        $data['locale'] = $user->user_email_language;

        $data['subject'] = trans('messages.email.listing_deactivated',[],$data['locale']).' '.SITE_NAME.' '.trans('messages.email.account');

        $data['view_file'] = 'emails.unlisted';

        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Send Updated Payout Information Mail to Host
     *
     * @param array $payout_preference_id Payout Preference Details
     * @return true
     */
    public function payout_preferences($payout_preference_id, $type = 'update')
    {
        if($type != 'delete') {
            $result = PayoutPreferences::find($payout_preference_id);
            $user = $result->users;
            $data['first_name'] = $user->first_name;
            $data['updated_time'] = $result->updated_time;
            $data['updated_date'] = $result->updated_date;
            $data['deleted_time'] = $result->deleted_time;
        }
        else {
            if(request()->segment(1) == 'api') {
                $user=JWTAuth::parseToken()->authenticate();
                $data['first_name'] = $user->first_name;
                $new_str = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone(Config::get('app.timezone')));
                $new_str->setTimeZone(new DateTimeZone($user->timezone));
            }
            else {
                $user = Auth::user();
                $data['first_name'] = $user->first_name;
                $new_str = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone(Config::get('app.timezone')));
                $new_str->setTimeZone(new DateTimeZone(Auth::user()->timezone));

            }
            $data['deleted_time'] = $new_str->format('d M').' at '.$new_str->format('H:i');
        }
        $data['type'] = $type;
        $data['url'] = url('/').'/';
        $data['locale'] = $user->user_email_language;

        if($type == 'update')
            $subject = trans('messages.email.your',[],$data['locale']).' '.SITE_NAME." ".trans('messages.email.payout_information_updated',[],$data['locale']);
        else if($type == 'delete')
            $subject = trans('messages.email.your',[],$data['locale']).' '.SITE_NAME." ".trans('messages.email.payout_information_deleted',[],$data['locale']);
        else if($type == 'default_update')
            $subject = trans('messages.email.payout_information_changed',[],$data['locale']);
       
        $data['subject'] = $subject;
        $data['view_file'] = 'emails.payout_preferences';

        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Send Need Payout Information Mail to Host/Guest
     *
     * @param array $reservation_id Reservation Details
     * @return true
     */
    public function need_payout_info($reservation_id, $type)
    {
        $result       = Reservation::find($reservation_id);
        $data['type'] = $type;
        
        if($type == 'guest') {
            $user = $result->users;
            $data['payout_amount'] = $result->admin_guest_payout;
        }
        else {
            $user = $result->host_users;
            $data['payout_amount'] = $result->admin_host_payout;
        }

        $data['currency_symbol'] = $result->currency->symbol;
        $data['first_name']      = $user->first_name;
        $data['user_id']         = $user->id;
        $data['url'] = url('/').'/';
        $data['locale'] = $user->user_email_language;      
        $data['subject'] = trans('messages.email.information_needed',[],$data['locale']);

        $data['view_file'] = 'emails.need_payout_info';

        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Space Details Updated to Admin
     *
     * @param Int $space_id
     * @param String $field
     * @return true
     */
    public function space_details_updated($space_id, $field)
    {
        $data['space_id'] = $space_id;
        $data['result'] = Space::find($space_id)->toArray(); 
        $data['field'] = $field; 
        $data['user'] = User::find($data['result']['user_id']); 
        
        $data['url'] = url('/').'/';
        $data['locale']       = Language::where('default_language',1)->first()->value;

        $data['admin'] = Admin::whereStatus('Active')->first(); 
        $data['first_name'] = $data['admin']->username;
        $data['subject'] = trans('messages.email.rooms_details_updated',[], $data['locale']).' '.$data['result']['name'];
        if($data['result']['status'] == 'Listed') {
            $data['view_file'] = 'emails.space_details_updated';
            // return view($data['view_file'],$data);
            Mail::to($data['admin']->email, $data['admin']->username)->queue(new MailQueue($data));
        }
        return;
    }

    /**
     * Send Need Payout Sent Mail to Host/Guest
     *
     * @param array $reservation_id Reservation Details
     * @return true
     */
    public function payout_sent($reservation_id, $type)
    {
        $data['result'] = Reservation::find($reservation_id);
        $data['type'] = $type;
        
        if($type == 'guest') {
            $user = $data['result']->users;
            $data['full_name'] = $data['result']->host_users->full_name;
            $data['payout_amount'] = $data['result']->admin_guest_payout;
            $payout_amount=html_entity_decode($data['result']['refund_currency']['symbol'], ENT_NOQUOTES, 'UTF-8').$data['payout_amount'];
        }
        else{
            $user = $data['result']->host_users;
            $data['full_name'] = $data['result']->users->full_name;
            $data['payout_amount'] = $data['result']->admin_host_payout;
            $payout_amount=html_entity_decode($data['result']['currency']['symbol'], ENT_NOQUOTES, 'UTF-8').$data['payout_amount'];
        }

        $data['result'] = Reservation::where('reservation.id',$reservation_id)->with(['space', 'currency'])->first()->toArray();
        $data['first_name'] = $user->first_name;
        $data['url'] = url('/').'/';
        $data['locale'] = $user->user_email_language;      

        $data['subject'] = trans('messages.email.payout_of',[],$data['locale']).' '.$payout_amount." ".trans('messages.email.sent',[],$data['locale']);

        $data['view_file'] = 'emails.payout_sent';
        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * Referral Email Share
     *
     * @param array $emails Friend Emails
     * @return true
     */
    public function referral_email_share($emails)
    {
        $user_id = Auth::user()->id;

        $data['result'] = $user = User::with(['profile_picture'])->whereId($user_id)->first()->toArray();

        $data['travel_credit'] = ReferralSettings::value(4);
        $data['symbol'] = Currency::first()->symbol;

        $data['url'] = url('/').'/';
        $data['locale'] = $user['user_email_language']; 

        $emails = explode(',', $emails);

        $data['subject'] = $user['full_name']." ".trans('messages.email.invited_you_to',[],$data['locale']).' '.SITE_NAME;
        foreach($emails as $email) {
            $email = trim($email);
            $data['view_file'] = 'emails.referral_email_share';
            // return view($data['view_file'],$data);
            Mail::to($email)->queue(new MailQueue($data));
        }
        return;
    }

    /**
     * Review Remainder
     *
     * @param array $reservation
     * @param string $type
     * @return true
     */
    public function review_remainder($reservation, $type='guest')
    {
        $data['url'] = SiteSettings::where('name', 'site_url')->first()->value.'/';

        if($type == 'guest') {
            $email = $reservation->host_users->email;
            $user = $reservation->users;
        }
        else {
            $email = $reservation->users->email;
            $user = $reservation->host_users;
        }

        $data['users'] = $user;
        $data['result'] = $reservation->toArray();

        $data['locale'] = $user->user_email_language; 
        $data['profile_picture'] = $user->profile_picture->email_src;
        $data['review_name'] = $user->first_name;

        $data['subject'] = trans('messages.email.write_review_about',[],$data['locale'])." ".$user->first_name;

        $data['view_file'] = 'emails.review_remainder';
        // return view($data['view_file'],$data);
        Mail::to($email)->queue(new MailQueue($data));
        return;
    }

    /**
     * Review Wrote
     *
     * @param int $review_id
     * @param string $type
     * @return true
     */
    public function wrote_review($review_id, $type ='guest')
    {
        $data['url'] = url('/').'/';

        $reviews = Reviews::find($review_id);

        $data['locale'] = $reviews->users->user_email_language; 
        $email = $reviews->users->email;

        $user = $reviews->users_from;

        $data['users'] = $user;
        $data['result'] = $reviews->toArray();

        $data['review_end_date'] = Reservation::find($reviews->reservation_id)->review_end_date;

        $data['profile_picture'] = $user->profile_picture->src;
        $data['review_name'] = $user->first_name;

        $data['view_url']= Reservation::find($reviews->reservation_id)->review_link;
        $data['subject'] = $user->first_name.' '.trans('messages.email.wrote_you_review',[],$data['locale']);

        $data['view_file'] = 'emails.wrote_review';
        // return view($data['view_file'],$data);
        Mail::to($email)->queue(new MailQueue($data));
    }

    /**
     * Review Read
     *
     * @param int $review_id
     * @param string $type
     * @return true
     */
    public function read_review($review_id, $type ='guest')
    {
        $data['url'] = url('/').'/';
        $reviews = Reviews::find($review_id);
        $data['locale'] = $reviews->users->user_email_language;
        
        $email = $reviews->users->email;
        $user = $reviews->users_from;

        $data['users'] = $user;
        $data['result'] = $reviews->toArray();

        $data['review_end_date'] = Reservation::find($reviews->reservation_id)->review_end_date;

        $data['profile_picture'] = $user->profile_picture->src;
        $data['review_name'] = $user->first_name;
        $data['view_url']= Reservation::find($reviews->reservation_id)->review_link;
        $data['subject'] = trans('messages.email.read',[],$data['locale']).' '.$user->first_name."'s ".trans('messages.email.review',[],$data['locale']);
        $data['view_file'] = 'emails.read_review';
        // return view($data['view_file'],$data);
        Mail::to($email)->queue(new MailQueue($data));
    }

    /**
     * Send accepted Mail to Host
     *
     * @param string $reservation_id Reservation Code
     * @param string $email Friend Email
     * @return true
     */
    public function accepted($reservation_id)
    {
        $data['hide_header']= true;
        $data['url']        = url('/').'/';
        $data['map_key']    = view()->shared('map_key');

        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency')->find($reservation_id);

        $user           = $reservation->users;
        $data['locale'] = $user->email_language;
        
        $data['contact'] = SiteSettings::where('name','support_number')->first()->value;

        $data['result'] = $reservation->toArray();

        $data['subject'] = trans('messages.email.reservation_confirmed',[],$data['locale']).' '.$data['result']['host_users']['full_name'];

        $data['view_file'] = 'emails.accepted';
        
        // return view($data['view_file'],$data);
        Mail::to($data['result']['users']['email'], '')->queue(new MailQueue($data));
        return;
    }

     /**
     * Send accepted Mail to Host
     *
     * @param string $reservation_id Reservation ID
     * @param string $email Friend Email
     * @return true
     */
    public function pre_accepted($reservation_id)
    {
        $data['hide_header']= true;
        $data['url']        = url('/').'/';
        $data['map_key']    = view()->shared('map_key');

        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency','reservation_times')->find($reservation_id);

        $user           = $reservation->users;
        $data['locale'] = $user->email_language;
        
        $data['contact'] = SiteSettings::where('name','support_number')->first()->value;

        $data['result'] = $reservation->toArray();
        $data['subject'] = __('messages.inbox.reservations').' '.__('messages.inbox.pre_accepted').' '.$reservation->host_users->full_name;

        $data['view_file'] = 'emails.pre_accepted';
        // return view($data['view_file'],$data);
        Mail::to($data['result']['users']['email'],'')->queue(new MailQueue($data));
        return;
    }

    /**
     * Booking Confirmed Email to Host
     *
     * @param array $reservation_id
     * @return true
     */
    public function booking_confirm_host($reservation_id)
    { 
        $data['hide_header']= true;
        $data['url']        = url('/').'/';
        $data['map_key']    = view()->shared('map_key');

        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency','reservation_times','messages')->find($reservation_id);

        $user           = $reservation->host_users;
        $data['locale'] = $user->email_language;
        
        $data['contact'] = SiteSettings::where('name','support_number')->first()->value;

        $data['result'] = $reservation->toArray();

        $data['subject'] = trans('messages.email.booking_confirmed',[], $data['locale'])." ".$data['result']['space']['name']." ".trans('messages.email.for',[], $data['locale'])." ".$data['result']['dates_subject'];
        $data['view_file'] = 'emails.booking_confirm_host';
        
        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    public function booking_confirm_admin($reservation_id)
    {
        $data['hide_header']= true;
        $data['url']        = url('/').'/';
        $data['map_key']    = view()->shared('map_key');

        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency','reservation_times','messages')->find($reservation_id);

        $user           = $reservation->users;
        $data['locale'] = $user->email_language;
        
        $data['contact'] = SiteSettings::where('name','support_number')->first()->value;
        $data['result'] = $reservation->toArray();

        $data['admin'] = Admin::whereStatus('Active')->first(); 

        $data['check_in_time']  = $reservation->reservation_times->start_time;;
        $data['check_out_time'] = $reservation->reservation_times->end_time;;

        $data['subject'] = trans('messages.email.booking_confirmed',[], $data['locale']).' '.$data['result']['space']['name'].' '.trans('messages.email.for',[], $data['locale']).' '.$data['result']['dates_subject'];
        
        $data['view_file'] = 'emails.booking_confirm_admin';

        // return view($data['view_file'],$data);
        Mail::to($data['admin']->email, $data['admin']->username)->queue(new MailQueue($data));
        return;
    }

    public function cancel_guest($reservation_id)
    {
        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency','reservation_times')->find($reservation_id);

        $user           = $reservation->host_users;
        $data['hide_header']    = true;
        
        $data['url']            = url('/').'/';
        $data['map_key']        = view()->shared('map_key');

        $data['result'] = $reservation->toArray();

        $data['check_in_time'] = $reservation->reservation_times->start_time;
        $data['check_out_time'] = $reservation->reservation_times->end_time;

        // Send Cancel mail to Admin
        $data['admin'] = Admin::whereStatus('Active')->first();
        $data['locale'] = Language::where('default_language',1)->first()->value;
        $data['subject']= trans('messages.email.reservation_cancelled_by',[],$data['locale']).' '.$data['result']['users']['full_name'];
        $data['view_file'] = 'emails.guest_cancel_confirm_admin';
        
        // return view($data['view_file'],$data);
        Mail::to($data['admin']->email, $data['admin']->username)->queue(new MailQueue($data));

        // Send Cancel mail to Host
        $data['locale'] = $user->user_email_language;
        $data['subject']= trans('messages.email.reservation_cancelled_by',[],$data['locale']).' '.$data['result']['users']['full_name'];
        $data['view_file'] = 'emails.guest_cancel_confirm_host';        
        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    public function cancel_host($reservation_id)
    {
        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency','reservation_times')->find($reservation_id);

        $user           = $reservation->host_users;
        $data['hide_header']    = true;
        
        $data['url']            = url('/').'/';
        $data['map_key']        = view()->shared('map_key');

        $data['result'] = $reservation->toArray();

        $data['check_in_time'] = $reservation->reservation_times->start_time;
        $data['check_out_time'] = $reservation->reservation_times->end_time;

        $data['locale'] = Language::where('default_language',1)->first()->value;
        if($data['result']['status'] == 'Declined') {
           $subjects = trans('messages.email.request_cancelled_by',[],$data['locale']);
        }
        else {
           $subjects = trans('messages.email.reservation_cancelled_by',[],$data['locale']);
        }
        $data['subject'] = $subjects.' '.$data['result']['host_users']['full_name'];

        // Send Cancel Mail to Admin
        $data['admin'] = Admin::whereStatus('Active')->first();
        $data['view_file'] = 'emails.host_cancel_confirm_admin';
        
        // return view($data['view_file'],$data);
        Mail::to($data['admin']->email, $data['admin']->username)->queue(new MailQueue($data));

        // Send Cancel Mail to Host
        $data['locale'] = $user->user_email_language;
        $data['view_file'] = 'emails.host_cancel_confirm_guest';

        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    public function reservation_expired_admin($reservation_id)
    {
        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency','reservation_times')->find($reservation_id);

        $user           = $reservation->host_users;
        $data['hide_header']    = true;
        
        $data['url']            = url('/').'/';
        $data['map_key']        = view()->shared('map_key');

        $data['result'] = $reservation->toArray();

        $data['check_in_time'] = $reservation->reservation_times->start_time;
        $data['check_out_time'] = $reservation->reservation_times->end_time;

        $data['locale'] = Language::where('default_language',1)->first()->value;
        if($reservation->status == 'Declined') {
           $subjects = trans('messages.email.request_cancelled_by',[],$data['locale']);
        }
        else {
           $subjects = __('messages.email.reservation_cancelled_by',[],$data['locale']);
        }

        $data['admin'] = Admin::whereStatus('Active')->first();
        $data['subject'] = __('messages.email.reservation_expired',[], $data['locale']).' '.$reservation->space->name." for ".$reservation->dates_subject;
        $data['view_file'] = 'emails.cancel_confirm_admin';
        
        // return view($data['view_file'], $data); 
        Mail::to($data['admin']->email, $data['admin']->username)->queue(new MailQueue($data));
        return;
    }

    public function reservation_expired_guest($reservation_id)
    {
        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency','reservation_times')->find($reservation_id);

        $user           = $reservation->host_users;
        $data['hide_header']    = true;
        
        $data['url']            = url('/').'/';
        $data['map_key']        = view()->shared('map_key');

        $data['result'] = $reservation->toArray();

        $data['check_in_time'] = $reservation->reservation_times->start_time;
        $data['check_out_time'] = $reservation->reservation_times->end_time;

        $data['locale']       = $user->user_email_language;
        $data['subject'] = trans('messages.email.reservation_expired',[], $data['locale']).' '.$reservation->space->name." for ".$reservation->dates_subject;
        $data['view_file'] = 'emails.reservation_expire_guest';

        // return view($data['view_file'],$data);
        Mail::to($reservation->users->email, $reservation->users->first_name)->queue(new MailQueue($data));
        return;
    }


    public function booking_response_remainder($reservation_id, $hours)
    {
        $reservation = Reservation::with('host_users.profile_picture', 'host_users.users_verification', 'host_users.reviews', 'users.profile_picture', 'users.users_verification', 'users.reviews','space.space_address','currency','reservation_times')->find($reservation_id);

        $user           = $reservation->host_users;
        $data['hide_header']    = true;
        $data['hours'] = $hours;

        $data['url']            = url('/').'/';
        $data['map_key']        = view()->shared('map_key');

        $data['result'] = $reservation->toArray();

        $data['check_in_time'] = $reservation->reservation_times->start_time;
        $data['check_out_time'] = $reservation->reservation_times->end_time;

        $data['locale']       = $user->user_email_language;

        $data['subject'] = trans('messages.email.booking_inquiry_expire',[],  $data['locale']).' '.$reservation->space->name.' '.trans('messages.email.for',[],  $data['locale']).' '.$reservation->dates_subject;

        $data['view_file'] = 'emails.booking_response_remainder';
        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;

    }

    /**
     * Send Document Verified Successfully email to user
     *
     * @param array $user  User Details
     * @return true
     */
    public function document_verified($user)
    {
        $data['first_name'] = $user->first_name;
        $data['url']        = url('/').'/';
        $data['locale']     = App::getLocale();
        $data['subject']    = __('messages.email.document_verified');
        $data['view_file']  = 'emails.document_verified';
        
        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    }

    /*Disputes Email Functions*/

    /**
     * Send Dispute Requested mail
     *
     * @return bool
     * @param $dispute_id 
     **/
    function dispute_requested($dispute_id)
    {
        $dispute = Disputes::where('id', $dispute_id)->with('dispute_user')->first();
        if(!$dispute) {
            return;
        }
        $dispute->set_user_or_dispute_user('DisputeUser');

        $dispute_message = $dispute->dispute_messages->first();
        $dispute_message->dispute->set_user_or_dispute_user('DisputeUser');

        $data['url'] = url('/').'/';
        $receiver_details   = $dispute_message->receiver_details;
          $data['locale'] = $receiver_details->user_email_language;
        $data['hide_header'] = true;

        $data['result'] = $dispute->reservation;
        $data['dispute'] = $dispute->toArray();
        $data['dispute_message'] = $dispute_message->toArray();
        $data['subject']  = $dispute_message['sub_text'];        
       
        $data['view_file'] = 'emails.dispute_requested';

        // return view($data['view_file'],$data);
        Mail::to($dispute->dispute_user->email, $dispute->dispute_user->first_name)->queue(new MailQueue($data));

        $admin = $data['admin'] = Admin::whereStatus('Active')->first();
        $data['subject']  = $dispute_message->admin_sub_text;
        $data['locale'] = Language::where('default_language',1)->first()->value;
        $data['view_file'] = 'emails.dispute_requested_admin';

        // return view($data['view_file'],$data);
        Mail::to($data['admin']->email, $data['admin']->username)->queue(new MailQueue($data));
        return;
    }
    
    /**
     * Send dispute conversation Email
     *
     * @return bool
     * @param $dispute_id 
     **/
    function dispute_admin_conversation($dispute_message_id)
    {
        $dispute_message = DisputeMessages::where('id', $dispute_message_id)->where(function($query){
            $query->where('message_by', 'Admin')->orWhere('message_for', 'Admin');
        })->first();

        if(!$dispute_message) {
            return;
        }

        $data['url'] = url('/').'/';
        $data['locale']       = App::getLocale();  
        $data['dispute_message'] = $dispute_message;

        if($dispute_message['message_by'] == 'Admin') {
            $sender_details     = Admin::whereStatus('Active')->first();
            $receiver_details   = $dispute_message->receiver_details;
            $data['first_name'] = ucfirst($receiver_details->first_name);
            $data['firstname']  = ucfirst($sender_details->username);
            $data['link']       = 'dispute_details/'.$dispute_message->dispute_id;
            $data['locale'] = $receiver_details->user_email_language;
        }   
        else {
            $sender_details     = $dispute_message->sender_details;
            $receiver_details   = Admin::whereStatus('Active')->first();
            $data['first_name'] = ucfirst($receiver_details->username);
            $data['firstname']  = ucfirst($sender_details->first_name);
            $data['link']       = ADMIN_URL.'/dispute/details/'.$dispute_message->dispute_id;
        }     
        $data['email']   = $receiver_details->email;

        $data['subject']    = trans('messages.disputes.user_sent_message_to_you', ['first_name' => $data['firstname']],$data['locale']);
        
        $data['view_file'] = 'emails.dispute_conversation_admin';
        
        // return view($data['view_file'],$data);
        Mail::to($data['email'], $data['first_name'])->queue(new MailQueue($data));
        return;
    }
    /**
     * Send dispute closed Email
     *
     * @return bool
     * @param $dispute_id 
     **/
    function dispute_closed($dispute_id)
    {
        $dispute = Disputes::where('id', $dispute_id)->first();
        if(!$dispute) {
            return;
        }
        $data['url'] = url('/').'/';
        $data['link'] = 'dispute_details/'.$dispute_id;

        $data['result'] = $dispute->reservation;
        $data['dispute'] = $dispute;
        $data['locale'] = $data['dispute']->dispute_user->user_email_language;

        foreach(['Host', 'Guest'] as $user) {
            $data['subject']  = $dispute->dispute_by == $user ? trans('messages.disputes.your_dispute_request_closed') : trans('messages.disputes.dispute_request_closed_by_you');
            $data['final_dispute_data'] = $dispute->final_dispute_data;
            $data['dispute_currency']   = $dispute->currency;

            $data['view_file'] = 'emails.dispute_closed';
            // return view($data['view_file'],$data);
            Mail::to($data['dispute']->dispute_user->email, $data['dispute']->dispute_user->first_name)->queue(new MailQueue($data));
        }
        return;
    }

    /**
    * send listing awaiting for admin approval email to admin
    *
    * @param array $space_id
    * @return true
    */
    public function awaiting_approval_admin($space_id)
    {
      $data['space_id'] = $space_id;
      $space            = Space::with('users')->find($space_id); 
      $data['name']     = $space->name;

      $data['url']      = url('/').'/';
      $data['locale']   = App::getLocale();

      $admin            = Admin::whereStatus('Active')->first();
      $data['first_name'] = $admin->username;
      $data['subject']  = __('messages.email.awaiting_approval_admin');

      $data['view_file']= 'emails.awaiting_approval_admin';

      // return view($data['view_file'],$data);
      Mail::to($admin->email, $admin->username)->queue(new MailQueue($data));
      return;
    }

    /**
     * send listing awaiting for admin approval email to host
     *
     * @param array $space_id
     * @return true
     */
    public function awaiting_approval_host($space_id)
    {
        $data['space_id'] = $space_id;
        $data['result'] = Space::find($space_id)->toArray(); 
        $data['user'] = User::find($data['result']['user_id']); 
        
        $data['url'] = url('/').'/';
        $data['locale'] = App::getLocale();
        $data['subject'] = trans('messages.email.awaiting_approval_host',[], null,  $data['locale']);
        $data['view_file'] = 'emails.awaiting_approval_host';
        $data['first_name'] = $data['user']['first_name'];

        // return view($data['view_file'],$data);
        Mail::to($data['user']->email, $data['user']->first_name)->queue(new MailQueue($data));
        return;
    }

    /**
     * send listing approved by admin email to host
     *
     * @param array $space_id Room Details
     * @return true
     */
    public function listing_approved_by_admin($space_id)
    {
        $result               = Space::with('users')->find($space_id);
        $user                 = $result->users;
        $data['first_name']   = $user->first_name;
        $data['space_name']   = $result->name;
        $data['space_link']   = $result->link;
        $data['calendar_link'] = route('manage_space',['space_id' => $result->id, 'page' => 'ready_to_host', 'step_num' => 5]);
        $data['created_time'] = $result->created_time;
        $data['space_id']     = $result->id;
        $data['url']          = url('/').'/';
        $data['locale']       = App::getLocale();   
        $data['subject'] = trans('messages.email.your_space_listed').' '.SITE_NAME;

        $data['view_file'] = 'emails.listing_approved_by_admin';
        // return view($data['view_file'],$data);
        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));
        return;
    } 
}