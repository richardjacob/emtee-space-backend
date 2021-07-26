<?php

/**
 * Inbox Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Inbox
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Models\Messages;
use App\Models\Reservation;
use App\Models\Space;
use App\Models\Fees;
use App\Models\SpecialOffer;
use App\Models\SpecialOfferTimes;
use App\Models\SpaceCalendar;
use App\Models\Currency;
use App\Models\Activity;
use App\Http\Helper\PaymentHelper;
use App\Http\Start\Helpers;
use Validator;
use DateTime;
use Session;
use DB;

class InboxController extends Controller
{
    protected $helper; // Global variable for Helpers instance

    /**
     * Constructor to Set Helpers instance in Global variable
     *
     * @param array $helper   Instance of Helpers
     */
     protected $payment_helper; // Global variable for Helpers instance
    
    public function __construct(PaymentHelper $payment)
    {
        $this->payment_helper = $payment;
        $this->helper = new Helpers;
    }

    /**
     * Load Inbox View
     *
     * @return Inbox page view file
     */
    public function index()
    {
        $data['user_id']     = auth()->id();
        $data['all_message'] = Messages::all_messages($data['user_id']);
        return view('users.inbox', $data);
    }

    /**
     * Ajax function for update Archive messages
     *
     * @param array $request  Input values
     */
    public function archive(Request $request)
    {
        $id     = $request->id;
        $msg_id = $request->msg_id;
        $type   = trim($request->type);

        if($type == "Archive")
            Messages::where('user_from', $id)->where('id', $msg_id )->update(['archive' =>'1']);

        if($type == "Unarchive")
            Messages::where('user_from', $id)->where('id', $msg_id )->update(['archive' =>'0']);

        // update read to 1 and archive to 1 for all messages related to reservation
        $message = Messages::find($msg_id);
        Messages::where('reservation_id',$message->reservation_id)->where('user_to',auth()->id())->update(['archive' => $type == 'Archive' ? '1' : '0']);
    }

    /**
     * Ajax function for update Star messages
     *
     * @param array $request  Input values
     */
    public function star(Request $request)
    {
        $id     = $request->id;
        $msg_id = $request->msg_id;
        $type   = trim($request->type);

        if($type == "Star")
            Messages::where('user_from', $id)->where('id', $msg_id )->update(['star' =>'1']);

        if($type == "Unstar")
            Messages::where('user_from', $id)->where('id', $msg_id )->update(['star' =>'0']);

    }

    /**
     * Ajax function for Message counts
     *
     * @param array $request  Input values
     * @return json message counts
     */
    public function message_count(Request $request)
    {
        $data['user_id'] = $user_id = auth()->id();

        $all_message     = Messages::all_messages($data['user_id']);

        $all    =   Messages::whereIn('id', function($query) use($user_id) {   
                        $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                    })
                    ->where('archive','0')
                    ->count();

        $star   =   Messages::whereIn('id', function($query) use($user_id) {   
                        $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                    })
                    ->where('archive','0')
                    ->where('star', '1')
                    ->count();

        $unread  =  Messages::whereIn('id', function($query) use($user_id) {   
                        $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                    })
                    ->where('archive','0')
                    ->where('read','0')
                    ->count();

        $reserve =  Messages::whereIn('id', function($query) use($user_id) {
                        $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->where('reservation_id','!=', 0)->groupby('reservation_id');
                    })
                    ->with(['reservation'])
                    ->whereHas('reservation' ,function($query) {
                        $query->where('status','!=','')->where('status','!=','Pending');
                    })
                    ->where('archive','0')
                    ->count();

        $pending =  Messages::whereIn('id', function($query) use($user_id) {
                        $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                    })
                    ->with(['reservation'])
                    ->whereHas('reservation' ,function($query) {
                        $query->where('status','Pending');
                    })
                    ->where('archive','0')
                    ->count();

        $archive =  Messages::whereIn('id', function($query) use($user_id) {
                        $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                    })
                    ->where('archive', '1')
                    ->count();

        $admin_messages = Messages::whereIn('id', function($query) use($user_id) {   
                            $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                        })
                        ->where('user_to',$user_id)
                        ->where('user_from',$user_id)
                        ->where('archive','0')
                        ->orderBy('id','desc')
                        ->count();

        if(count($all_message) != 0 ) {
            $data['all_message_count'] = $all;
            $data['stared_count']      = $star;
            $data['unread_count']      = $unread;
            $data['reservation_count'] = $reserve;
            $data['archived_count']    = $archive;
            $data['pending_count']     = $pending;
            $data['admin_messages']    = $admin_messages;
        }
        else {
            $data['all_message_count'] = 0;
            $data['stared_count']      = 0;
            $data['unread_count']      = 0;
            $data['reservation_count'] = 0;
            $data['archived_count']    = 0;
            $data['pending_count']     = 0;
            $data['admin_messages']    = 0;
        }

        return json_encode($data);
    }

    /**
     * Ajax function for Message Filters
     *
     * @param array $request  Input values
     * @return json message counts
     */
    public function message_by_type(Request $request)
    {
        $user_id = auth()->id();
        $type    = trim($request->type);

        if($type == "starred") {
            $result =   Messages::whereIn('id', function($query) use($user_id) {   
                            $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                        })
                        ->with(['user_details' => function($query) {
                            $query->with('profile_picture');
                        }])
                        ->with(['reservation' => function($query) {
                            $query->with('currency');
                        }])
                        ->with(['space' => function($query) {
                            $query->with('space_address');
                        }])
                        ->where('archive','0')
                        ->where('star', '1')
                        ->orderBy('id','desc');
        }
        else if($type == "hidden") {
            $result =   Messages::whereIn('id', function($query) use($user_id) {   
                            $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                        })
                        ->with(['user_details' => function($query) {
                            $query->with('profile_picture');
                        }])
                        ->with(['reservation' => function($query) {
                            $query->with('currency');
                        }])
                        ->with(['space' => function($query) {
                            $query->with('space_address');
                        }])
                        ->where('archive', '1')
                        ->orderBy('id','desc');
        }
        else if($type == "unread") {
            $result =   Messages::whereIn('id', function($query) use($user_id) {   
                            $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                        })
                        ->with(['user_details' => function($query) {
                            $query->with('profile_picture');
                        }])
                        ->with(['reservation' => function($query) {
                            $query->with('currency');
                        }])
                        ->with(['space' => function($query) {
                            $query->with('space_address');
                        }])
                        ->where('read','0')
                        ->where('archive','0')
                        ->orderBy('id','desc');
        }
        else if($type == "reservations") {
            $result =   Messages::whereIn('id', function($query) use($user_id) {   
                            $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                        })
                        ->with(['user_details' => function($query) {
                            $query->with('profile_picture');
                        }])
                        ->with(['reservation' => function($query) {
                            $query->with('currency');
                        }])
                        ->whereHas('reservation' ,function($query) {
                            $query->where('status','!=','')->where('status','!=','Pending');
                        })
                        ->with(['space' => function($query) {
                            $query->with('space_address');
                        }])
                        ->where('reservation_id','!=', '0')
                        ->where('archive','0')
                        ->orderBy('id','desc');
        }
        else if($type == "admin_messages") {
            $result = Messages::whereIn('id', function($query) use($user_id) {   
                            $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                        })
                        ->where('user_to',$user_id)
                        ->where('user_from',$user_id)
                        ->where('archive','0')
                        ->orderBy('id','desc');
        }
        else if($type == "pending_requests") {
            $result =   Messages::whereIn('id', function($query) use($user_id) {
                            $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                        })
                        ->with(['user_details' => function($query) {
                            $query->with('profile_picture');
                        },'reservation'=>function($query){
                            $query->with('currency');
                        }])
                        ->whereHas('reservation' ,function($query) {
                            $query->where('status','Pending');
                        })
                        ->with(['space' => function($query) {
                            $query->with('space_address');
                        }])
                        ->where('archive','0')
                        ->orderBy('id','desc');
        }
        else {
            // All Messages
            $result =   Messages::whereIn('id', function($query) use($user_id) {   
                            $query->select(DB::raw('max(id)'))->from('messages')->where('user_to', $user_id)->groupby('reservation_id');
                        })
                        ->with(['user_details' => function($query) {
                            $query->with('profile_picture');
                        }])
                        ->with(['reservation' => function($query) {
                            $query->with('currency');
                        }])
                        ->with(['space' => function($query) {
                            $query->with('space_address');
                        }])
                        ->where('archive','0')
                        ->orderBy('id','desc');
        }

        $result =  $result->paginate(10)->toJson();

        return $result;
    }

    /**
     * Load Guest Conversation Page with Messages
     *
     * @param array $request  Input values
     * @return view Guest Conversation View File
     */
    public function guest_conversation(Request $request)
    {
        $reservation = Reservation::where('id', $request->id)->userRelated()->firstOrFail();

        $read_count   = Messages::where('reservation_id',$request->id)->where('user_to',auth()->id())->where('read','0')->count();

        if($read_count !=0 ) {
            Messages::where('reservation_id',$request->id)->where('user_to',auth()->id())->update(['read' =>'1']);
        }

        $data['messages'] = Messages::with('user_details','reservation.space')->where('reservation_id',$request->id)->orderBy('created_at','desc')->get();

        $data['special_offer'] = SpecialOffer::where('id',@$data['messages'][0]['special_offer_id'])->get();

        if(@$data['messages'][0]->reservation->space->user_id == auth()->id())
            abort('404');
        // check avablity in special offer
        $data['avablity']=0;
        if(@$data['messages'][0]->special_offer_id != '') {
            $special_offer      = $data['messages'][0]->special_offer;
            $checkin      = $special_offer->checkin;
            $checkout     = $special_offer->checkout;
            $start_time   = $special_offer->special_offer_times->start_time;
            $end_time     = $special_offer->special_offer_times->end_time;

            $space_id = $special_offer->space_id;

            $price_data = array(
                'event_type' => json_decode($request->event_type,true),
                'booking_date_times' => json_decode($request->booking_date_times,true),
                'number_of_guests' => $special_offer->number_of_guests,
                'booking_period' => $special_offer->booking_period,
            );

            $additional_data = array(
                'special_offer_id' => $data['messages'][0]->special_offer_id,
            );

            $price_list = $this->payment_helper->price_calculation($space_id, (Object)$price_data, (Object)$additional_data);
            $data['price_list'] = json_decode($price_list);

            $data['checkin']    = date(PHP_DATE_FORMAT,strtotime($checkin));
            $data['checkout']   = date(PHP_DATE_FORMAT,strtotime($checkout));
        }
         $data["page_language"] = session::get('language');
        return view('users.guest_conversation', $data);
    }

    /**
     * Ajax function for Conversation reply
     *
     * @param array $request  Input values
     * @return html Reply message html
     */
    public function reply(Request $request, EmailController $email_controller)
    {
        $reservation_details = Reservation::with('space','reservation_times')->find($request->id);

        if($request->template == 'NOT_AVAILABLE' || $request->template == '9') {
            $reservation_details->decline_reason = $request->template == '9' ? $request->template_message : $request->template;

            $reservation_details->status = 'DECLINED';
            $reservation_details->save();
        }

        $message = removeEmailNumber($request->message);

        if($reservation_details->user_id == auth()->id()) {
            $messages = new Messages;

            $messages->space_id        = $reservation_details->space_id;
            $messages->reservation_id = $reservation_details->id;
            $messages->user_to        = $reservation_details->space->user_id;
            $messages->user_from      = auth()->id();
            $messages->message        = $message;
            $messages->message_type   = 5;

            $messages->save();

            echo '<div class="row my-4"> <div class="col-10"> <div class="card"> <div class="card-body custom-arrow right"> <span class="message-text">'.$message.'</span> </div> </div> <div class="my-2 time text-right"> <span> '.$messages->created_time.' </span> </div> </div> <div class="col-2 pl-0 profile-img"> <img src="'.auth()->user()->profile_picture->src.'" class="user-profile-photo"> </div> </div>';
        }
        else if($reservation_details->space->user_id == auth()->id()) {
            if($request->template == 1) {
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
                $special_offer->currency_code   = Currency::first()->session_code;
                $special_offer->type            = 'pre-approval';
                $special_offer->created_at      = date('Y-m-d H:i:s');
                $special_offer->save();

                $reservation_times              = $reservation_details->reservation_times;
                $spl_offer_times = $reservation_times->only(['space_id','start_date','end_date','start_time','end_time']);
                $spl_offer_times['special_offer_id'] = $special_offer->id;

                SpecialOfferTimes::create($spl_offer_times);

                //pre approval status change
                if($reservation_details->type == 'contact') {
                    $reservation =Reservation::find($reservation_details->id);
                    $reservation->status     = 'pre-approved';
                    $reservation->created_at = date('Y-m-d H:i:s');
                    $reservation->save();
                }

                $special_offer_id = $special_offer->id;

                $email_controller->preapproval($reservation_details->id, $message);
            }
            else if($request->template == 2) {
                $message_type = 7;

                $rules = array(
                    'price' => 'required|numeric'
                );

                $validator = Validator::make($request->all(), $rules);
                if($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $minimum_amount = currency_convert(DEFAULT_CURRENCY, session('currency'), MINIMUM_AMOUNT); 
                $currency_symbol = Currency::whereCode(session('currency'))->first()->original_symbol;

                $night_price = $request->price;

                if($night_price < $minimum_amount && $night_price != '') {
                  return json_encode(['success'=>'false','msg' => trans('validation.min.numeric', ['attribute' => 'price', 'min' => $currency_symbol.$minimum_amount]), 'attribute' => 'price']);
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
            else if($request->template == 'NOT_AVAILABLE') {
                $message_type = 8;

                $blocked_days = getDays($reservation_details->checkin, $reservation_details->checkout);

                // Update Calendar
                for($j=0; $j<count($blocked_days)-1; $j++)
                {
                    $calendar_data = [
                            'space_id' => $reservation_details->space_id,
                            'date'    => $blocked_days[$j],
                            'status'  => 'Not available',
                            'source'  => 'Calendar',
                            ];

                    Calendar::updateOrCreate(['space_id' => $reservation_details->space_id, 'date' => $blocked_days[$j]], $calendar_data);
                }
            }
            else if($request->template == 9)
                $message_type = 8;
            else
                $message_type = 5;

            $messages = new Messages;

            $messages->space_id          = $reservation_details->space_id;
            $messages->reservation_id   = $reservation_details->id;
            $messages->user_to          = $reservation_details->user_id;
            $messages->user_from        = auth()->id();
            $messages->message          = $message;
            $messages->message_type     = $message_type;
            $messages->special_offer_id = @$special_offer_id;

            $messages->save();
            $messages->load('reservation');

            $space_url = route('space_details',[$messages->reservation->space_id]);

            $html = '<li id="question2_post_'.$messages->id.'">';

            if($message_type == 6) {
                $hprice=$messages->special_offer->price-$reservation_details->host_fee;
                $html .= '<div class="card my-4"> <div class="card-header"> <h5>'.$messages->reservation->users->first_name.' '. trans('messages.inbox.pre_approved_stay_at').' <a href="'.$space_url.'"> '.$messages->special_offer->space->name .' </a> </h5> <p> '.$messages->special_offer->dates_subject.'·'.$messages->special_offer->number_of_guests.' '.trans_choice('messages.home.guest',$messages->special_offer->number_of_guests).'·'.  html_entity_decode($messages->special_offer->currency->symbol).$hprice.' '.$messages->special_offer->currency->session_code.'</p> </div> <div class="card-body"> <a href="'.url('/').'/messaging/remove_special_offer/'.$messages->special_offer_id.'" class="btn" data-confirm="Are you sure?" data-method="post" rel="nofollow"> '. trans('messages.inbox.remove_pre_approval') .' </a> </div> </div>';
            }
            else if($message_type == 7) {
                $html .= '<div class="card my-4"> <div class="card-header"> <span class="label label-info">'.trans('messages.inbox.special_offer').'</span> <h5>'.$messages->reservation->users->first_name.' '.trans('messages.inbox.pre_approved_stay_at').' <a href="'.$space_url.'"> '.$messages->special_offer->space->name.' </a> </h5> <p>'. $messages->special_offer->dates_subject.' · '.$messages->special_offer->number_of_guests.' '.trans_choice('messages.home.guest', $messages->special_offer->number_of_guests).'<br> <strong>'.trans('messages.inbox.you_could_earn').' '.html_entity_decode($messages->special_offer->currency->symbol).$messages->special_offer->price.' '.$messages->special_offer->currency->session_code.'</strong> ('.trans('messages.inbox.once_reservation_made').') </p> </div> <div class="card-body"> <a href="'.url('/').'/messaging/remove_special_offer/'.$messages->special_offer_id.'" class="btn" data-confirm="Are you sure?" data-method="post" rel="nofollow"> '.trans('messages.inbox.remove_special_offer').' </a> </div> </div>';
            }

            echo $html.'<div class="row my-4"> <div class="col-3 col-md-2 pr-0 text-center"> <a aria-label="'.auth()->user()->first_name.'" data-behavior="tooltip" href="'.route('show_profile',[auth()->id()]).'"> <img title="'.auth()->user()->first_name.'" src="'.auth()->user()->profile_picture->src.'" alt="'.auth()->user()->first_name.'"> </a> </div> <div class="col-9 col-md-10"> <div class="card custom-arrow left"> <div class="card-body p-3"> <p>'.$message.'</p> </div> </div> <div class="time-container"> <small title="'.$messages->created_at.'" class="time">'. $messages->created_time .'</small> <small class="exact-time d-none">"'.$messages->created_at.'"</small> </div> </div> </div> </li>';
        }
    }

    /**
     * Load Host Conversation Page with Messages
     *
     * @param array $request  Input values
     * @return view Host Conversation View File
     */
    public function host_conversation(Request $request, CalendarController $calendar)
    {
        $data['user_time_zone'] = (\Auth::guest())?'':\Auth::user()->timezone;
        $reservation = Reservation::with('space.space_activities')->userRelated()->findOrFail($request->id);

        $read_count   = Messages::where('reservation_id',$request->id)->where('user_to',auth()->id())->where('read','0')->count();

        if($read_count != 0) {
            Messages::where('reservation_id',$request->id)->where('user_to',auth()->id())->update(['read' =>'1']);
        }

        $data['messages'] = Messages::with('user_details','reservation','special_offer')->where('reservation_id',$request->id)->orderBy('created_at','desc')->get();   
        //rooms name changed using language based (dropdown) 
        $space = Space::user()->listed()->verified()->get();
        $data['space'] =  $space->pluck('name','id');

        $space_unlist = Space::user()->where('name','!=' ,'')->viewOnly()->get();

        $data['space_unlist'] =  $space_unlist->pluck('name','id');

        if($data['messages'][0]->reservation->user_id == auth()->id()) {
            abort('404');
        }

        $data['edit_calendar_link'] = route('manage_space',['space_id' => $data['messages'][0]->space_id, 'page' => 'ready_to_host', 'step_num' => 5]);

        $data['calendar_data']  = $calendar->generate($data['messages'][0]->reservation->space_id);

        $space_details = $reservation->space;
        $activity_types = $space_details->space_activities->implode('activity_type_id',',');
        $activities     = $space_details->space_activities->implode('activities',',');
        $sub_activities = $space_details->space_activities->implode('sub_activities',',');

        $activity_types = explode(',',$activity_types);
        $activities     = explode(',',$activities);
        $sub_activities = explode(',',$sub_activities);

        $data['activities']  = Activity::with(['sub_activities' => function($query) use ($sub_activities) {
            $query->whereIn('id',$sub_activities);
        }])->whereIn('activity_type_id',$activity_types)->whereIn('id',$activities)->activeOnly()->get();

        $data['booking_date_times'] = array('start_date' => '');
        $data['activity_type_selected'] = $reservation->activity_type;
        $data['guests']             = $reservation->number_of_guests;
        $data['booking_period']     = $reservation->booking_period;
        $data['minimum_amount']     = currency_convert(DEFAULT_CURRENCY, session('currency'), MINIMUM_AMOUNT); 
 
        $data['status'] =  $reservation->status;
         $data["page_language"] = session::get('language');
        return view('users.host_conversation', $data);
    }

    public function admin_messages(Request $request)
    {
        if($request->id != auth()->id())
        abort(404);

        $read_count   = Messages::where('reservation_id',Null)->where('user_to',auth()->id())->where('read','0')->count();

        if($read_count !=0)
            Messages::where('reservation_id',Null)->where('user_to',auth()->id())->update(['read' =>'1']);

        $data['messages'] = Messages::where('reservation_id',Null)->where('user_to',$request->id)->where('user_from',$request->id)->orderBy('created_at','desc')->get();
        $data['title'] = 'Admin Messages';

        return view('users.admin_conversation', $data);
    }

    /**
     * Get Calendar Data
     *
     * @param array $request Input values
     * @param $calendar  Calendar Controller Instance
     * @return Json $calendar_data
     */
    public function calendar(Request $request, CalendarController $calendar)
    {
        $space_id         = $request->space_id;
        $year             = $request->year;
        $month            = $request->month;

        $calendar_data  = $calendar->generate($space_id, $year, $month);

        return json_encode(compact('calendar_data'));
    }

    /**
     * Remove Special Offer
     *
     * @param array $request  Input values
     * @return redirect to Conversation page
     */
    public function remove_special_offer(Request $request)
    {
        $id = $request->id;
        $special_offer  = SpecialOffer::find($id);
        $reservation_id = $special_offer->reservation_id;
        $type           = $special_offer->type;

        // Already paid
        $already = Reservation::where('special_offer_id',$id)->where('status','Accepted')->first();
        if($already) {
            $this->helper->flash_message('error', 'Already Booked');
            return redirect()->route('host_conversation',['id' => $reservation_id]);
        }
        $special_offer->load('special_offer_times');
        $special_offer->special_offer_times->delete();
        $special_offer->delete();
        $messages = Messages::where('special_offer_id',$id)->delete();
        $type_name = ($type=='pre-approval') ? 'Pre-Approval' : 'Special offer';

        //remove status reservation
        $reservation = Reservation::find($special_offer->reservation_id);

        if($reservation->type == 'contact') {
            if(!$reservation->special_offer || @$reservation->special_offer->type == 'special_offer') {
                $reservation->status           = Null;
                $reservation->created_at       = date('Y-m-d H:i:s');
                $reservation->save();
            }
        }

        flash_message('success', trans('messages.inbox.type_has_removed',['type'=>$type_name]));
        return redirect()->route('host_conversation',['id' => $reservation_id]);
   }

    /**
     * Load Admin Resubmit message reason
     *
     * @param array $request  Input values
     * @return view Admin Resubmit View File
     */
    public function admin_message(Request $request)
    {
        Messages::where('reservation_id',$request->id)->where('user_to',auth()->id())->where('read','0')->update(['read' =>'1']);

        $data['messages'] = Messages::where('reservation_id',$request->id)->orderBy('created_at','desc')->get(); 

        if ($data['messages']->count() <= 0 ) {
            abort(404);
        }
        $data['title'] = 'Admin Messages';
        return view('users.admin_resubmit', $data);
    }

}
