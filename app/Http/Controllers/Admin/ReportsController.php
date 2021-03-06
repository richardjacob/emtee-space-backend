<?php

/**
 * Reports Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Reports
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Space;
use App\Models\Reservation;
use Excel;
use DB;
use App\Exports\ArrayExport;

class ReportsController extends Controller
{

    /**
     * Load Datatable for Reports
     *
     * @return view file
     */
    public function index(Request $request)
    {
        if($request->isMethod('get')) {
            return view('admin.reports');
        }

        $from = date('Y-m-d H:i:s', custom_strtotime($request->from));
        $to = date('Y-m-d H:i:s', custom_strtotime($request->to." 23:59:59"));
        
        $category = $request->category;
        if($category == '') {
            $result = User::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();
            
        }
        if($category == 'space') {
            $result = Space::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();
        }
        if($category == 'reservations') {
            $result = Reservation::where('reservation.created_at', '>=', $from)->where('reservation.created_at', '<=', $to)   ->join('space', function($join) {
                    $join->on('space.id', '=', 'reservation.space_id');
                })
            ->join('users', function($join) {
                    $join->on('users.id', '=', 'reservation.user_id');
                })
            ->join('currency', function($join) {
                    $join->on('currency.code', '=', 'reservation.currency_code');
                })
            ->leftJoin('users as u', function($join) {
                    $join->on('u.id', '=', 'reservation.host_id');
                })
            ->select(['reservation.id as id', 'u.first_name as host_name', 'users.first_name as guest_name', 'space.name as space_name','space.sq_ft as sq_ft','space.size_type as size_type', DB::raw('CONCAT(currency.symbol, reservation.total) AS total_amount'), 'reservation.status', 'reservation.created_at as created_at', 'reservation.updated_at as updated_at', 'reservation.*'])->get();
        }

        $final['from']      = $request->from == '' ? 'all' : date('Y-m-d',custom_strtotime($request->from));
        $final['to']        = $request->to == '' ? 'all' : date('Y-m-d',custom_strtotime($request->to));
        $final['result']    = $result;

        return $final;
    }

    public function export(Request $request)
    {
        $from = $request->from == 'all' ? '' : date('Y-m-d H:i:s', custom_strtotime($request->from));
        $to = $request->to == 'all' ? '' : date('Y-m-d H:i:s', custom_strtotime($request->to." 23:59:59"));
        
        if($request->category == 'users') {            
            $results = User::select('id','first_name','last_name','email','status','created_at as registered_at','created_at','updated_at','languages');
            if($from != '')
                $results =  $results->where('created_at', '>=', $from);
            if($to != '')
                 $results =  $results->where('created_at', '<=', $to);

            $results = $results->get()->toArray();

            foreach($results as $k => $res){
                $result_values = array_except($res, ['languages', 'dob_dmy','age','full_name','languages_name','primary_phone_number_protected', 'primary_phone_number', 'created_at', 'updated_at']);
                $result[$k]['Id'] = @$result_values['id'];
                $result[$k]['First Name'] = @$result_values['first_name'];
                $result[$k]['Last Name'] = @$result_values['last_name'];
                $result[$k]['Email'] = @$result_values['email'];
                $result[$k]['Status'] = @$result_values['status'];
                $result[$k]['Registered At'] = @$result_values['registered_at'];
            }
        }
        if($request->category == 'space') {
            $results = Space::select('id','name','summary','status','created_at','updated_at','views_count','popular','space_type','user_id','sq_ft','size_type');
            
            if($from != '')
                $results =  $results->where('created_at', '>=', $from);
            if($to != '')
                 $results =  $results->where('created_at', '<=', $to);

            $results = $results->get()->toArray();
            
            foreach($results as $i => $res){
                $result_values = array_except($res, ['space_type','user_id','photo_name','overall_star_rating','steps_count','reviews_count', 'popular', 'views_count', 'updated_at']);
                $result[$i]['Id'] = @$result_values['id'];
                $result[$i]['Name'] = @$result_values['name'];
                $result[$i]['Host Name'] = @$result_values['host_name'];
                $result[$i]['Space Type'] = @$result_values['space_type_name'];
                $result[$i]['Status'] = @$result_values['status'];
                $result[$i]['Created At'] = @$result_values['created_at'];
            }
        }
        if($request->category == 'reservations') {
            $results = Reservation::join('space', function($join) {
                        $join->on('space.id', '=', 'reservation.space_id');
                    })
                    ->join('users', function($join) {
                        $join->on('users.id', '=', 'reservation.user_id');
                    })
                    ->join('currency', function($join) {
                        $join->on('currency.code', '=', 'reservation.currency_code');
                    })
                    ->leftJoin('users as u', function($join) {
                        $join->on('u.id', '=', 'reservation.host_id');
                    })
                    ->select(['reservation.id as id', 'u.first_name as host_name', 'users.first_name as guest_name', 'space.name as space_name', DB::raw('CONCAT(currency.symbol, reservation.total) AS total_amount'), 'reservation.status', 'reservation.created_at as created_at', 'reservation.updated_at as updated_at', 'reservation.*','currency.symbol as currency_symbol']);
            
            if($from != '')
                $results =  $results->where('reservation.created_at', '>=', $from);
            if($to != '')
                 $results =  $results->where('reservation.created_at', '<=', $to);

            $results = $results->get()->toArray();

            foreach($results as $i => $res){
                $result_values = array_except($res, ['space_id','host_id','user_id','checkin','checkout','number_of_guests','hours','per_hour','subtotal','cleaning','additional_guest','security','service','host_fee','total','coupon_code','coupon_amount','currency_code','transaction_id','paymode','cancellation','first_name','last_name','postal_code','country','type','friends_email','cancelled_by','cancelled_reason','decline_reason','host_remainder_email_sent','special_offer_id','accepted_at','expired_at','declined_at','cancelled_at','date_check','created_at_timer','status_color','receipt_date','dates_subject','checkin_arrive','checkout_depart','guests','host_payout','guest_payout','admin_host_payout','admin_guest_payout','checkin_md','checkout_md','checkin_mdy','checkout_mdy','checkout_mdy','check_total','checkin_site_date_format','checkout_site_date_format','review_end_date','grand_total','space_category','checkin_formatted','checkout_formatted','status_language','avablity']);
                $result[$i]['Id'] = @$result_values['id'];
                $result[$i]['Host Name'] = @$result_values['host_name'];
                $result[$i]['Guest Name'] = @$result_values['guest_name'];
                $result[$i]['Space Name'] = @$result_values['space_name'];
                $result[$i]['Total Amount'] = html_entity_decode(@$result_values['total_amount'], ENT_COMPAT, 'UTF-8');
                $result[$i]['Status'] = @$result_values['status'];
                $result[$i]['Created At'] = @$result_values['created_at'];
            }
        }
        
        if(count($result) == 0) {
            return '';
        }
        return \Excel::download(new ArrayExport($result),$request->category . '-report.csv');


    }
}
