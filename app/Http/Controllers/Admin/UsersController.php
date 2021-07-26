<?php

/**
 * Users Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Users
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\UsersDataTable;
use App\Models\User;
use App\Models\ProfilePicture;
use App\Models\UsersVerification;
use App\Models\Space;
use App\Models\Reservation;
use App\Models\Referrals;
use App\Models\SavedWishlists;
use App\Models\Wishlists;
use App\Models\PayoutPreferences;
use App\Models\UsersVerificationDocuments;
use App\Models\UsersPhoneNumbers;
use App\Models\Messages;
use App\Http\Controllers\EmailController;
use Validator;
use DB;
use Carbon\Carbon;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->view_data['main_title'] = $this->main_title = 'User';
        $this->view_data['base_url'] = $this->base_url = route('users');
        $this->view_data['add_url']  = route('create_user');
        $this->view_data['base_view_path'] = $this->base_view_path = 'admin.users.';
    }

    /**
     * Load Datatable for Users
     *
     * @param array $dataTable  Instance of UsersDataTable
     * @return datatable
     */
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('admin.common.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->base_view_path.'add', $this->view_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $now = new Carbon();
        $format = view()->shared('php_format_date');
        $before = $now->subYears(18)->format($format);

        // Add User Validation Rules
        $rules = array(
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:8',
            'dob'        => 'required|date_format:'.$format.'|before:' . $before,
            'status'     => 'required'
        );

        // Add User Validation Custom Names
        $attributes = array(
            'first_name' => 'First name',
            'last_name'  => 'Last name',
            'email'      => 'Email',
            'password'   => 'Password',
            'dob'        => 'DOB',
            'status'     => 'Status'
        );

        $messages = array(
            'dob.before' => 'User must be 18 or older',
        );

        $request->validate($rules,$messages,$attributes);

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->password   = bcrypt($request->password);
        $user->dob        = date('Y-m-d', custom_strtotime($request->dob));
        $user->status     = $request->status;
        $user->save();

        $user_pic = new ProfilePicture;
        $user_pic->user_id      =   $user->id;
        $user_pic->src          =   "";
        $user_pic->photo_source =   'Local';
        $user_pic->save();

        $users_verification = new UsersVerification;
        $users_verification->user_id      =   $user->id;
        $users_verification->email        =   "yes";
        $users_verification->save();

        flash_message('success', 'New '.$this->main_title.' Added Successfully');
        return redirect($this->base_url);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['result']      = User::find($id);
        $this->view_data['id_documents'] = UsersVerificationDocuments::whereType('id_document')->where('user_id', $id)->get();

        return view($this->base_view_path.'edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,EmailController $email_controller)
    {
        $user = User::find($request->id);
        if(!$user) {
            flash_message('error', 'Invalid user.');
            return redirect($this->base_url);
        }
        // Edit User Validation Rules
        $rules = array(
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users,email,'.$request->id,
            'dob'        => 'required',
            'status'     => 'required'
        );

        if($user->verification_status != 'Connect') {
            $rules += array(
                'id_document_verification_status'  => 'required'
            );
        }

        if($request->id_document_verification_status == 'Resubmit') {
            $rules['id_resubmit_reason'] = 'required';
        }

        // Edit User Validation Custom Fields Name
        $attributes = array(
            'first_name' => 'First name',
            'last_name'  => 'Last name',
            'email'      => 'Email',
            'dob'        => 'DOB',
            'status'     => 'Status',
            'id_document_verification_status'     => 'ID Document Status',
            'id_resubmit_reason'     => 'Resubmit Reason',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributes); 

        if ($validator->errors()->first('dob')==null) {
            $today = new Carbon();
            $before_18_years = $today->subYears(18)->format('U');
            $date_of_birth = custom_strtotime($request->dob);
            if ($date_of_birth>$before_18_years) {
                $validator->errors()->add('dob', 'User must be 18 or older');
            }
        }
        if(count($validator->errors())>0) {
            return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
        }

        $user = User::find($request->id);

        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->dob        = date('Y-m-d', custom_strtotime($request->dob));
        $user->status     = $request->status;

        if($request->id_document_verification_status == 'Verified') {
            $email_controller->document_verified($user);
        }

        if($request->id_document_verification_status == 'Resubmit' && $request->id_resubmit_reason != '' && ( $request->id_resubmit_reason != $user->id_resubmit_reason || $request->id_document_verification_status != $user->id_document_verification_status) ) {
            // send resubmit message to user 
            $message = new Messages;
            $message->user_to = $request->id;
            $message->user_from = $request->id;
            $message->reservation_id = NULL;
            $message->message_type = 13;
            $message->message = $request->id_resubmit_reason;
            $message->save();
        }

        if($user->id_document_verification_status != '') {
            UsersVerificationDocuments::whereType('id_document')->where('user_id', $request->id)->update(['status' => $request->id_document_verification_status]);
        }

        if($user->verification_status != 'Connect') {
            $verification_doc = UsersVerificationDocuments::where('user_id', $request->id)->first();
            $user->verification_status = $verification_doc->user_verification_status;
        }

        if($request->password != '') {
            $user->password = bcrypt($request->password);
            User::clearUserSession($request->id);
        }

        $user->save();

        flash_message('success', 'Updated Successfully');

        return redirect($this->base_url);
    }

    /**
     * Delete User
     *
     * @param array $request    Input values
     * @return redirect     to Users View
     */
    public function destroy(Request $request)
    {
        $user_id = $request->id;
        $can_destory = $this->canDestroy($user_id);
        
        if($can_destory['status'] == 0) {
            flash_message('error',$can_destory['message']);
            return redirect($this->base_url);
        }

        SavedWishlists::where('user_id', $user_id)->delete();
        Wishlists::where('user_id', $user_id)->delete();
        PayoutPreferences::where('user_id', $user_id)->delete();
        UsersVerificationDocuments::where('user_id', $user_id)->delete();
        UsersPhoneNumbers::where('user_id', $user_id)->delete();
        User::find($user_id)->forceDelete();
        flash_message('success', 'Deleted Successfully');

        return redirect($this->base_url);
    }

    public function canDestroy($user_id)
    {
        $user = User::find($user_id);
        if(!$user) {
            return ['status' => 0, 'message' => 'This User already Deleted.'];
        }

        $space_count = Space::whereUserId($user_id)->count();
        if($space_count) {
            return ['status' => 0, 'message' => 'This User has some Space. Please delete that Space, before deleting this User.'];
        }

        $reservation_count = Reservation::where('user_id' , $user_id)->count();
        if($reservation_count) {
            return ['status' => 0, 'message' => 'This User has some Bookings. We can\'t delete this User'];
        }

        $referrals_count = Referrals::where('user_id', $user_id)->orWhere('friend_id', $user_id)->count();
        if($referrals_count) {
            return ['status' => 0, 'message' => 'This User has some referrals. We can\'t delete this User'];
        }

        return ['status' => 1, 'message' => ''];
    }
}
