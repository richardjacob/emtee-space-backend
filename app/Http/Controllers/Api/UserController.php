<?php

/**
 * User Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    User
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProfilePicture;
use App\Models\HomeCities;
use App\Models\PayoutPreferences;
use App\Models\Country;
use App\Http\Start\Helpers;
use JWTAuth;
use DB;
use Validator;

class UserController extends Controller
{
	public function user_details()
	{
		$user = JWTAuth::parseToken()->authenticate();
		$user = User::with(['profile_picture'])->whereId($user->id)->first();
		return response()->json(compact('user'));
	}

	public function signup_details()
    {
        $user = JWTAuth::parseToken()->authenticate();
		$user = User::with(['profile_picture'])->whereId($user->id)->select('id','first_name','last_name','email','dob')->first();
		$token = JWTAuth::getToken();

		$user = array(
			'status'			=> '1',
			'success_message'	=> '1',
			'token'				=> $token,
			'id'				=> $user->id,
			'first_name'		=> $user->first_name,
			'last_name'			=> $user->last_name,
			'email'				=> $user->email,
			'dob'				=> $user->dob,
			'image_url'			=> $user->profile_picture->src
		);

		return response()->json(compact('user'));
    }

    /**
     * User Logout
     *
     * @param  Get method inputs
     * @return Response in Json 
     */
    public function logout(Request $request)
    {
    	//Deactive the Access Token
        JWTAuth::invalidate($request->token);
        session()->flush();
        return response()->json([
            'status_code'     => '1',
            'success_message' => 'Logout Successfully',
        ]);
    }

    /**
     * Edit User Profile
     *
     * @param  Get method inputs
     * @return  Response in Json 
     */
  	public function edit_profile(Request $request)
   	{
		$user_token = JWTAuth::parseToken()->authenticate();
		$id         = $user_token->id;
		
		$rules      = array(
			'first_name'  =>  'required|max:255',
			'last_name'   =>  'required|max:255',
			'dob'         =>  'required|date_format:"d-m-Y"|date',
			'gender'      =>  'required|In:female,male,other,Male,Female,Other',
			'email'       =>  'required|email|unique:users,email,'.$id,
		);
		$messages  = array(
			'required'     => __('messages.api.field_is_required',['attr'=>':attribute']),
			'email'        => __('messages.api.invalid_mail'),
			'regex'        => __('messages.api.inavlid_thumb')
		);
		$validator  = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		$from 	= getDateObject(custom_strtotime($request->dob));
		$to 	= getDateObject(custom_strtotime(date('Y-m-d')));
		$age  = $from->diff($to)->y; 

		if($age < 18) {
			return response()->json([
				'status_code'     => '0',
				'success_message' => trans('messages.api.must_18_old'),
			]);
		}

		$user_data = array(
			'first_name' => $request->first_name,
			'last_name'  => $request->last_name,
			'dob'        => date("Y-m-d", strtotime($request->dob)),
			'live'       => $request->user_location,
			'about'      => $request->about_me,
			'school'     => $request->school,
			'gender'     => $request->gender,
			'email'      => $request->email,
			'work'       => $request->work
		);

		User::find($id)->update($user_data);

		return response()->json([
			'status_code'     => '1',
			'success_message' => __('messages.api.user_detail_updated'),
		]);
   	}

   	/**
     * Display User Profile
     *
     * @param  Get method inputs
     * @return Response in Json 
     */
    public function view_profile(Request $request)
    {
		$user_token   = JWTAuth::parseToken()->authenticate();
		$user_details = User::where('id',$user_token->id)->first();
		$details_user = array();
		
		//Check dob empty or not
		if($user_details->dob=='0000-00-00') {
			$dob = '';
		}
		else {  
			$c_dob = getDateObject(custom_strtotime($user_details->dob));
			$dob   = $c_dob->format('d M Y');
		}

		$pro_pic  = ProfilePicture::where('user_id',$user_token->id)->first();

		if($pro_pic->src != '') {
			if($pro_pic->photo_source=='Local') {
				$img       = $pro_pic->src;
				$file_name = basename($img);
				$type      = pathinfo($img, PATHINFO_EXTENSION);
				$basename  = basename($img,'.'.$type);
				$pro_img   = $pro_pic->src;
				$small     = $pro_pic->src;
				$large     = $pro_pic->header_src510;
			}
			else {
				$pro_img = $pro_pic->src;
				$small   = $pro_pic->src;
				$large   = $pro_pic->src;
			}
		}
		else {
			$pro_img = asset('/images/user_pic-225x225.png');
			$small   = asset('/images/user_pic-225x225.png');
			$large   = asset('/images/user_pic-225x225.png');
		}

		$user_phone = DB::table('users_phone_numbers')
		->where('user_id',$user_details->id)
		->where('status','Confirmed')
		->pluck('phone_number');

		$social_details = DB::table('users_verification')
		->where('user_id',$user_details->id)
		->first();

		$payout_count = PayoutPreferences::where('user_id',$user_details->id)->count();

		$details_user['payout_count']  		 = $payout_count;
		$details_user['first_name']          = $user_details->first_name != '' ? $user_details->first_name : '';
		$details_user['last_name']           = $user_details->last_name != '' ? $user_details->last_name : '';
		$details_user['dob']                 = $dob != '' ? $dob : '';
		$details_user['user_location']       = $user_details->live != '' ? $user_details->live : '';
		$details_user['member_from']         = $user_details->since != '' ? $user_details->since : '';
		$details_user['about_me']            = $user_details->about != '' ? $user_details->about : '';
		$details_user['school']              = $user_details->school != ''? $user_details->school : '';
		$details_user['gender']              = $user_details->gender != ''? $user_details->gender : '';
		$details_user['email']               = $user_details->email != '' ? $user_details->email : '';
		$details_user['phone_number']        = $user_phone != '' ? $user_phone : '';
		$details_user['work']                = $user_details->work != '' ? $user_details->work : '';
		$details_user['is_email_connect']    = $social_details->email != '' ? $social_details->email : '';
		$details_user['is_facebook_connect'] = $social_details->facebook != '' ? $social_details->facebook : '';
		$details_user['is_google_connect']   = $social_details->google != '' ? $social_details->google : '';
		$details_user['is_linkedin_connect'] = $social_details->linkedin != '' ? $social_details->linkedin : '';
		$details_user['normal_image_url'] 	 = $pro_img;
		$details_user['small_image_url'] 	 = $small;
		$details_user['large_image_url'] 	 = $large;

		return  response()->json([
			'status_code'     => '1',
			'success_message' => __('messages.api.listed_successful'),
			'user_details'    => $details_user,
		]); 
    }

    /*
     *Profile Image Upload
     *
     * @param  Post method inputs
     * @return Response in Json
     */
    public function upload_profile_image(Request $request)
	{
		$this->helper = new Helpers;
		$user         = JWTAuth::toUser($_POST['token']);
		$user_id      = $user->id;
		//ceck uploaded image is set or not
		if(isset($_FILES['image'])) {
			$errors    = array();
			$file_name = time().'_'.$_FILES['image']['name'];
			$type      = pathinfo($file_name, PATHINFO_EXTENSION);
			$file_tmp  = $_FILES['image']['tmp_name'];
			$dir_name = dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user_id;
			$f_name   = dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user_id.'/'.$file_name;

			//check file directory is created or not
			if(!file_exists($dir_name)) {
				mkdir(dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user_id, 0777, true);
			}
			if(UPLOAD_DRIVER=='cloudinary') {
				$c=$this->helper->cloud_upload($file_tmp);
				if($c['status']!="error") {
					$file_name=$c['message']['public_id'];    
				}
				else {
					return response()->json([
						'status_code'         => '0',
						'success_message'     => $c['message'],
					]);
				}
			}
			else {
				//upload image from temp_file  to server file
				if(move_uploaded_file($file_tmp,$f_name)) {
					//change compress image in 1440*960 
					$this->helper->compress_image("images/users/".$user_id."/".$file_name, "images/users/".$user_id."/".$file_name, 80, 1440, 960);
					//change compress image in 225*225 
					$li=$this->helper->compress_image("images/users/".$user_id."/".$file_name, "images/users/".$user_id."/".$file_name, 80, 225, 225);
					//change compress image in 510*510
					$this->helper->compress_image("images/users/".$user_id."/".$file_name, "images/users/".$user_id."/".$file_name, 80, 510, 510);
					//change compress image in 1349*402
					$this->helper->compress_image("images/users/".$user_id."/".$file_name, "images/users/".$user_id."/".$file_name, 80, 1349, 402);
					//change compress image in 450*250
					$this->helper->compress_image("images/users/".$user_id."/".$file_name, "images/users/".$user_id."/".$file_name, 80, 450, 250);
				}
			}

			ProfilePicture::where('user_id',$user_id)->update(['src' => $file_name,'photo_source'=>'Local']);
			$pro_pic  = ProfilePicture::where('user_id',$user_id)->first();
			$normal   = optional($pro_pic)->src;
			$small    = optional($pro_pic)->src;
			$large    = optional($pro_pic)->header_src510;
			return response()->json([
				'status_code'      => "1",
				'success_message'  => "Profile Image Upload Successfully",
				'normal_image_url' => $normal,
				'small_image_url'  => $small,
				'large_image_url'  => $large,
				'file_name'        => $file_name
			]);
		}
	}

    /**
     *Display user profile
     *
     * @param  Get method inputs
     * @return Response in Json
     */
    public function user_profile_details(Request $request)
	{
		$rules    = array('user_id' => 'required|exists:users,id');
		$messages = array('required' => ':attribute is required.');
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		//get host user details
		$host_user    = User::with('profile_picture')->find($request->user_id);
		$user_details = array(
			'large_image'   =>  $host_user->profile_picture->header_src510,
			'first_name'    =>  $host_user->first_name,
			'last_name'     =>  $host_user->last_name,
			'about_me'      =>  $host_user->about,
			'member_from'   =>  $host_user->since,
			'user_location' =>  $host_user->live
		);

		return response()->json([
			'status_code'     => '1',
			'success_message' => 'User Details Listed Successfully',
			'user_details'    => $user_details,
		]);
	}

 	/**
     *Display payout details
     *   
     * @param  Get method request inputs
     * @return Response in Json
     */
    public function payout_details(Request $request)
    {
    	$user_details = JWTAuth::parseToken()->authenticate();
		$payout_details = PayoutPreferences::where('user_id',$user_details->id)->get();

		if($payout_details->count() == 0) {
			return response()->json([
				'status_code'	  => '0',
				'success_message' => 'No Data Found',
			]);
		}

		$data = $payout_details->map(function($payout_detail) {
			return [
				'payout_id'     =>  $payout_detail->id,
				'user_id'       =>  $payout_detail->user_id,
				'payout_method' =>  $payout_detail->payout_method ?? '',
				'paypal_email'  =>  $payout_detail->paypal_email ?? '',
				'set_default'   =>  ucfirst($payout_detail->default),
			];
		});

		return response()->json([
			'status_code'    => '1',
			'success_message'=> __('messages.api.listed_successful'),
			'payout_details' => $data,
		]);
    }

   	/**
     *Payout Set Default and Delete
     *   
     * @param  Get method request inputs
     * @param  Type  Default   Set Default payout 
     * @param  Type  Delete    Delete payout Details
     * @return Response in Json
     */
   	public function payout_changes(Request $request,EmailController $email_controller)
	{
		$rules     = array(
			'payout_id' => 'required|exists:payout_preferences,id',
			'type' => 'required'
		);
		$attributes = array('payout_id' => 'Payout Id'); 
		$messages  = array('required' => ':attribute is required.');
		$validator = Validator::make($request->all(), $rules, $messages);
		$validator->setAttributeNames($attributes); 
		if ($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		//check valid user or not
		$check_user = PayoutPreferences::where('id',$request->payout_id)
		->where('user_id',JWTAuth::parseToken()->authenticate()->id)
		->count();

		if($check_user < 1 ) {
			return response()->json([
				'status_code'     => '0',
				'success_message' => 'Permission Denied',
			]);
		}  

		//check valid type or not
		if($request->type!='default' && $request->type !='delete') {
			return response()->json([
				'status_code'     => '0',
				'success_message' => 'The Selected Type Is Invalid',
			]);
		}

		//set default payout
		if($request->type == 'default') {
			$payout = PayoutPreferences::where('id',$request->payout_id)->first();
			if($payout->default == 'yes') {
				return response()->json([
					'status_code'		=> '0',
					'success_message' 	=> 'The Given Payout Id is Already Defaulted',
				]);
			}
			//Changed default option No in all Payout based on user id
			$payout_all = PayoutPreferences::where('user_id',JWTAuth::parseToken()->authenticate()->id)->update(['default'=>'no']);
			$payout->default = 'yes';
			$payout->save();
			$email_controller->payout_preferences($payout->id, 'default_update');
			return response()->json([
				'status_code'     => '1',
				'success_message' => 'Payout Preferences is Successfully Selected Default',
			]);
		}
		if($request->type=='delete') {
			$payout = PayoutPreferences::where('id',$request->payout_id)->first();
			if($payout->default == 'yes') {
				return response()->json([
					'status_code'     => '0',
					'success_message' => 'Permission Denied to Delete the Default Payout',
				]);
			}
			$payout->delete();
			$email_controller->payout_preferences($request->payout_id, 'delete');
			return response()->json([
				'status_code'     => '1',
				'success_message' => 'Payout Details Deleted Successfully',
			]);
		}
	}

	/**
	 * Add payout Preferences
	 *
	 * @param  Get method inputs
	 * @return Response in Json
	 */
	Public function add_payout_perference(Request $request)
	{
		if($request->getMethod() == 'GET') {
			$user_token = $user = JWTAuth::parseToken()->authenticate();
		}

		if ($request->getMethod() == 'POST') {
			$user = $user_token = JWTAuth::toUser($_POST['token']);
			if ($user) {
				$user_id = $user->id;
			}
			else {
				return response()->json([
					'status_code' 	  => '0',
					'success_message' => 'user_not_found',
				]);
			}
		}

		// first get payout method and country validation
		if($request->getMethod() == 'POST') {
			$rules = array(
				'payout_method' => 'required|in:stripe,paypal,Stripe,Paypal',
				'country' => 'required|exists:country,short_name',
			);

			$messages 	= array('required' => ':attribute is required.');
			$validator 	= Validator::make($request->all(), $rules, $messages);

			if($validator->fails()) {
	          	return response()->json([
	                'status_code'     => '0',
	                'success_message' => $validator->messages()->first(),
	            ]);
			}
		}
		/*** Add payout preference for Stripe  ***/
		if (strtolower($request->payout_method) == 'stripe') {
			if (empty($request->document)) {
				return response()->json([
					'status_code' 		=> '0',
					'success_message' 	=> 'document required',
				]);
			}

			if (empty($request->additional_document)) {
				return response()->json([
					'status_code' 		=> '0',
					'success_message' 	=> 'additional document required',
				]);
			}

			$country = $request->country;

			/*** required field validation ***/
			$rules = PayoutPreferences::getMandatory($country);

			$rules['address1'] = 'required';
			$rules['city'] = 'required';
			$rules['state'] = 'required';
			$rules['postal_code'] = 'required';
			$rules['document'] = 'required';
			$rules['additional_document'] = 'required';
			$rules['phone_number'] = 'required';
			if($country == 'JP') {
				$rules['phone_number'] = 'required';
				$rules['bank_name'] = 'required';
				$rules['branch_name'] = 'required';
				$rules['address1'] = 'required';
				$rules['kanji_address1'] = 'required';
				$rules['kanji_address2'] = 'required';
				$rules['kanji_city'] = 'required';
				$rules['kanji_state'] = 'required';
				$rules['kanji_postal_code'] = 'required';
				if(!$user->gender) {
					$rules['gender'] = 'required|in:male,female';
				}
			}
	        else if($country == 'US') {
				// custom required validation for US country
	            $rules['ssn_last_4'] = 'required|digits:4';
	        }

			$messages = array('required' => ':attribute is required.');
			$validator = Validator::make($request->all(), $rules, $messages);

			if($validator->fails()) {
	          	return response()->json([
	                'status_code'     => '0',
	                'success_message' => $validator->messages()->first(),
	            ]);
			}
			/*** required field validation ***/

	        \Stripe\Stripe::setApiKey(view()->shared('stripe_secret_key'));
	        $account_holder_type = 'individual';

	        // create account token use to create account 
	        if($country  != 'JP') {
	            $individual = [ 
	                "address" => array(
	                    "line1" => $request->address1,
	                    "city" => $request->city,
	                    "postal_code" => $request->postal_code,
	                    "state" => $request->state
	                ),
	                "dob" => array(
	                    "day" => @$user->dob_array[2],
	                    "month" => @$user->dob_array[1],
	                    "year" => @$user->dob_array[0],
	                ),
	                "first_name" => $user->first_name,
	                "last_name" => $user->last_name,
	                "phone" => ($request->phone_number) ? $request->phone_number : "",
	                "email" => $user->email,
	            ];

	            if($country == 'US') {
	                $individual['ssn_last_4'] = $request->ssn_last_4;
	            }

	            if(in_array($country,['SG','CA'])) {
	                $individual['id_number'] =  $request->personal_id;
	            }
	        }
	        else {
	            // for Japan country //
	            $address_kana = array(
	                'line1'         => $request->address1,
	                'town'         => $request->address2,
	                'city'          => $request->city,
	                'state'         => $request->state,
	                'postal_code'   => $request->postal_code,
	                 'country'       => $country,
	            );
	            $address_kanji = array(
	                'line1'         => $request->kanji_address1,
	                'town'         => $request->kanji_address2,
	                'city'          => $request->kanji_city,
	                'state'         => $request->kanji_state,
	                'postal_code'   => $request->kanji_postal_code,
	                'country'       => $country,
	            );
	            $individual = array(
	                "first_name_kana" => $user->first_name,
	                "last_name_kana" => $user->last_name,
	                "first_name_kanji" => $user->first_name,
	                "last_name_kanji" => $user->last_name,
	                "phone" => ($request->phone_number) ? $request->phone_number : "",
	                // "type" => $account_holder_type,
	                "address" => array(
	                    "line1" => @$request->address1,
	                    "line2" => @$request->address2 ? @$request->address2  : null,
	                    "city" => @$request->city,
	                    "country" => @$country,
	                    "state" => @$request->state ? @$request->state : null,
	                    "postal_code" => @$request->postal_code,
	                    ),
	                "address_kana" => $address_kana,
	                "address_kanji" => $address_kanji,
	                // "phone_number" => @$request->phone_number ? $request->phone_number : null,
	            );
	        }
	        /*** create stripe account ***/

	         $url = url('/');
	         if(strpos($url, "localhost") > 0) {
	            $url = 'http://makentspace.trioangle.com';
	         } 
			$verification = array(
				"country" => $country,
				"business_type" => "individual",
				"business_profile" => array(
		              'mcc' => 6513,
		              'url' => $url,
		         ),
				"tos_acceptance" => array(
					"date" => time(),
					"ip"    => $_SERVER['REMOTE_ADDR']
				),
				"type"    => "custom",
				"individual" => $individual,
			);

			$capability_countries = ['US','AU','AT','BE','CZ','DK','EE','FI','FR','DE','GR','IE','IT','LV','LT','LU','NL','NZ','NO','PL','PT','SK','SI','ES','SE','CH','GB'];

	        if(in_array($country, $capability_countries)) {
	            $verification["requested_capabilities"] = ["transfers","card_payments"];
	        }

	        try {
	            $recipient = \Stripe\Account::create($verification);
	            // verification document upload for stripe account //
	            $document = $request->file('document');
	            $additional_document = $request->file('additional_document');
	            if($document) {
	                $extension =   $document->getClientOriginalExtension();
	                $filename  =   $user_id.'_user_document_'.time().'.'.$extension;
	                $filenamepath = dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user_id.'/uploads';

	                if(!file_exists($filenamepath)) {
	                    mkdir(dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user_id.'/uploads', 0777, true);
	                }
	                $success   =   $document->move('images/users/'.$user_id.'/uploads/', $filename);

	               $a_extension =   $additional_document->getClientOriginalExtension();
               	   $a_filename  =   $user_id.'additional_document'.time().'.'.$extension;
                   $a_success   =   $additional_document->move('images/users/'.$user_id.'/uploads/', $a_filename);


	                if($a_success) {
	                    $document_path = dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user_id.'/uploads/'.$filename;

	                    $a_document_path = dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user_id.'/uploads/'.$a_filename;

	                    try {
	                        $stripe_file_details = \Stripe\FileUpload::create(
	                          array(
	                            "purpose" => "identity_document",
	                            "file" => fopen($document_path, 'r')
	                          ),
	                          array('stripe_account' => $recipient->id)
	                        );

	                        $stripe_a_file_details = \Stripe\FileUpload::create(
	                          array(
	                            "purpose" => "identity_document",
	                            "file" => fopen($a_document_path, 'r')
	                          ),
	                          array('stripe_account' => $recipient->id)
	                        );


	                        $individual['verification'] = array(
	                                "document" => $stripe_file_details->id
	                            );
	                        $stripe_document = $stripe_file_details->id;

	                        $stripe_document = $stripe_file_details->id;
	                        $stripe_a_document = $stripe_a_file_details->id;

	                        $stripe_document_update = \Stripe\Account::updatePerson($recipient->id,
	                            $recipient->individual->id,
	                          array('verification' => array(
	                                    'document' =>array(
	                                       'front' => $stripe_document       
	                                    ),
	                                    'additional_document' =>array(
	                                       'front' => $stripe_a_document       
	                                    )                             
	                                )
	                              )
	                        );

	                    }
	                    catch(\Exception $e) {
	                        return response()->json([
								'status_code' => '0',
								'success_message' => $e->getMessage(),
							]);
	                    }
	                }
	            }
	            // verification document upload for stripe account //
	        }
	        catch(\Exception $e) {
	            return response()->json([
					'status_code' => '0',
					'success_message' => $e->getMessage(),
				]);
	        }

	        $recipient->email = auth()->user()->email;

	        // create external account using stripe token //
	        try {
				$routing_number = $request->routing_number ? $request->routing_number : '';

				$iban_supported_country = Country::getIbanRequiredCountries();
				if(in_array($country, $iban_supported_country)) {

					$account_number = $request->iban;
					$stripe_token = \Stripe\Token::create(array(
						"bank_account" => array(
							"country" => $country,
							"currency" => $request->currency,
							"account_holder_name" => $request->account_holder_name,
							"account_holder_type" => $account_holder_type,
							// "routing_number" => $routing_number,
							"account_number" => $account_number,
						),
					));
				}
				else {
					$account_number = $request->account_number;
					if($country == 'AU') {
						$routing_number = $request->bsb;
					}
					else if ($country == 'HK') {
						$routing_number = $request->clearing_code . '-' . $request->branch_code;
					}
					else if ($country == 'JP' || $country == 'SG') {
						$routing_number = $request->bank_code . $request->branch_code;
					}
					else if ($country == 'GB') {
						$routing_number = $request->sort_code;
					}

					$stripe_token = \Stripe\Token::create(array(
						"bank_account" => array(
							"country" => $country,
							"currency" => $request->currency,
							"account_holder_name" => $request->account_holder_name,
							"account_holder_type" => $account_holder_type,
							"routing_number" => $routing_number,
							"account_number" => $request->account_number,
						),
					));
				}
			}
			catch (\Exception $e) {
				return response()->json([
					'status_code' => '0',
					'success_message' => $e->getMessage(),
				]);
			}

	        try {
	            $recipient->external_accounts->create(array(
	                "external_account" => $stripe_token,
	            ));
	        }
	        catch(\Exception $e) {
	            return response()->json([
					'status_code' => '0',
					'success_message' => $e->getMessage(),
				]);
	        }
	        $recipient->save();

	        // document upload to create stripe custome account //

			//check payoutpreferences is selected or not
			$payout_default_count = PayoutPreferences::where('user_id', $user->id)->where('default', '=', 'yes');

			$payout_perference = new PayoutPreferences;
			$payout_perference->user_id = $user_token->id;
			$payout_perference->paypal_email = @$recipient->id;
			
			$payout_perference->country = $country;
			$payout_perference->default = $payout_default_count->count() == 0 ? 'yes' : 'no';
			$payout_perference->currency_code = $request->currency != null? $request->currency : DEFAULT_CURRENCY;
			$payout_perference->routing_number = $routing_number ? $routing_number : '';
			$payout_perference->account_number = $account_number ? $account_number : '';
			$payout_perference->holder_name = $request->account_holder_name;
			$payout_perference->holder_type = $account_holder_type;

			$payout_perference->address1 = $request->address1 != ''? $request->address1 : '';
			$payout_perference->address2 = $request->address2 != ''? $request->address2 : '';
			$payout_perference->city = $request->city != ''? $request->city : '';
			
			$payout_perference->document_id = @$stripe_document;
			$payout_perference->document_image = @$filename;
			$payout_perference->phone_number = @$request->phone_number?$request->phone_number:'';
			$payout_perference->branch_code = @$request->branch_code? $request->branch_code : '';
			$payout_perference->bank_name = @$request->bank_name ? $request->bank_name : '';
			$payout_perference->branch_name = @$request->branch_name? $request->branch_name : '';
			$payout_perference->postal_code = $request->postal_code != ''? $request->postal_code : '';
			$payout_perference->state = $request->state != ''? $request->state : '';

			$payout_perference->payout_method = 'Stripe';
			$payout_perference->ssn_last_4 = @$country == 'US' ? $request->ssn_last_4 : '';
			$payout_perference->address_kanji = @$address_kanji ? json_encode(@$address_kanji) : json_encode([]);
			$payout_perference->save();

			return response()->json([
				'status_code' 		=> '1',
				'success_message' 	=> 'Payout Details Is Added Successfully',
			]);
	    }
		/*** Stripe Payout Preference End ***/

		$rules = array(
			'address1' => 'required|max:255',
			'city' => 'required',
			'country' => 'required|exists:country,short_name',
			'postal_code' => 'required',
			'paypal_email' => 'required|email',
		);
		$messages = array('required' => ':attribute is required.');
		$validator = Validator::make($request->all(), $rules, $messages);

		if($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		//Get Default PayPal Currency code
		$site_settings = resolve('site_settings');
		$paypal_currency = $site_settings->where('name', 'paypal_currency')->first()->value;

		//check payoutpreferences is selected or not
		$payout_default_count = PayoutPreferences::where('user_id', $user->id)->where('default', '=', 'yes');
		$payout_perference = new PayoutPreferences;
		$payout_perference->user_id = $user_token->id;
		$payout_perference->paypal_email = $request->paypal_email;
		$payout_perference->address1 = $request->address1 != '' ? $request->address1 : '';
		$payout_perference->address2 = $request->address2 != '' ? $request->address2 : '';
		$payout_perference->city = $request->city != '' ? $request->city : '';
		$payout_perference->state = $request->state != '' ? $request->state : '';
		$payout_perference->country = $request->country;
		$payout_perference->default = $payout_default_count->count() == 0 ? 'yes' : 'no';
		$payout_perference->postal_code = $request->postal_code != '' ? $request->postal_code : '';
		$payout_perference->currency_code = $paypal_currency != null ? $paypal_currency : DEFAULT_CURRENCY;
		$payout_perference->payout_method = 'Paypal';
		$payout_perference->save();

		return response()->json([
			'status_code' 		=> '1',
			'success_message' 	=> 'Payout Details Is Added Successfully',
		]);
	}

	/**
	 * Change User Language
	 *
	 * @param Get method request inputs
	 * @return Response in Json
	 */
	public function change_language(Request $request)
    {
    	$rules 		= array('language' => 'required|exists:language,value');
		$attributes = array('language' => 'Language');
		$messages 	= array('required' => ':attribute is required.');
		$validator 	= Validator::make($request->all(), $rules, $messages, $attributes);

		if($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		$user_details = JWTAuth::parseToken()->authenticate();

		User::find($user_details->id)->update(['email_language' => $request->language]);

		\App::setLocale($request->language);

		return response()->json([
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.update_success'),
		]);
    }

	/**
	 * Change User Currency
	 *
	 * @param Get method request inputs
	 * @return Response in Json
	 */
	public function change_currency(Request $request)
	{
		$rules 		= array('currency_code' => 'required|exists:currency,code');
		$attributes = array('currency_code' => 'Currency Code');
		$messages 	= array('required' => ':attribute is required.');
		$validator 	= Validator::make($request->all(), $rules, $messages, $attributes);

		if($validator->fails()) {
          	return response()->json([
                'status_code'     => '0',
                'success_message' => $validator->messages()->first(),
            ]);
		}

		$user = JWTAuth::parseToken()->authenticate();
		$currency_code = $request->currency_code;
		
		User::find($user->id)->update(['currency_code' => $currency_code]);
		
		return response()->json([
			'status_code' 		=> '1',
			'success_message' 	=> __('messages.api.update_success'),
		]);
	}
}