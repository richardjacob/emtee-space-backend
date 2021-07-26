<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Factory as Auth;
use Session;

class Authenticate
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $guard = @$guards[0] ?: 'user';

        $redirect_to = 'login';

        if($guard == 'admin') {
            $redirect_to = ADMIN_URL.'/login';
        }
        $is_admin_path = $request->segment(1) == ADMIN_URL;

        if (!$this->auth->guard($guard)->check() && ($guard != 'admin' || $is_admin_path)) {
            // Save the payment data in session
            if($request->route()->uri == 'payments/book/{id?}') {
                $s_key = $request->s_key ?: time().$request->id.str_random(4);

                if($request->s_key) {
                    $payment = Session::get('payment.'.$request->s_key);
                }
                else {
                    $payment = array(
                        'payment_space_id'          => $request->space_id, 
                        'payment_cancellation'      => $request->cancellation,
                        'payment_number_of_guests'  => $request->number_of_guests,
                        'payment_special_offer_id'  => $request->special_offer_id,
                        'payment_reservation_id'    => $request->reservation_id,
                        'booking_period'            => $request->booking_period ?? '',
                    );

                    if($request->method() == 'POST') {
                        $payment['payment_booking_type']= $request->booking_type;
                        $payment['booking_date_times']  = json_decode($request->booking_date_times,true);
                        $payment['payment_event_type']  = json_decode($request->event_type,true);
                    }
                    if($request->method() == 'GET') {
                        $payment['payment_booking_type']    = 'instant_book';
                        $payment['booking_date_times']      = json_decode($request->booking_date_times,true);
                        $payment['payment_event_type']      = json_decode($request->event_type,true);
                    }

                    session(['payment.'.$s_key => $payment]);
                }
                Session::put('url.intended', url('payments/book').'?s_key='.$s_key);
            }
            else if(strpos($request->url(), 'manage_listing')) {
                $redirect_url='manage_listing/'.$request->id.'/basics';
                if($request->ajax()) {
                    Session::put('ajax_redirect_url',$redirect_url);
                    return response('Unauthorized', 300);
                }
            }
            else {
                $intend_url = url()->full();
                session(['url.intended' => $intend_url]);
            }
            return redirect($redirect_to);
        }
        else if($guard == 'admin' && !$is_admin_path) {
            return redirect('about/'.$request->segment(1));
        }
        if($this->auth->guard($guard)->user()->status == 'Inactive') {
            $this->auth->guard($guard)->logout();
            return redirect($redirect_to);
        }

        return $next($request);
    }
}
